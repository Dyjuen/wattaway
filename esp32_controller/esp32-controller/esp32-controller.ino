// Core WiFi and MQTT Libraries
#include <WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include <time.h>
// #include <HTTPUpdate.h>
// #include <Update.h>

// Libraries for BLE Provisioning (using NimBLE to reduce flash size)
#include <NimBLEDevice.h> // Single include is sufficient; exposes NimBLEServer, NimBLE2902, etc.
#include <Preferences.h> // Used for saving credentials to Non-Volatile Storage (NVS)

// -- Firmware Version --
#define FIRMWARE_VERSION "1.0.0"

// -- API Configuration --
#define API_URL "202.10.61.100/api/v1"

// -- MQTT Configuration --
#define MQTT_HOST "202.10.61.100" // Replace with your broker's IP or hostname
#define MQTT_PORT 1883

// TODO: These should be unique for each device
#define DEVICE_ID "1"
#define API_TOKEN "zT944wjUkYCO2JYVyYXdgEJeVHA60rxVoL1HLX6UDbU38SmEbkRKLlNQ5IZ9c2kA"

// MQTT Topics
#define MQTT_TOPIC_DATA "devices/" DEVICE_ID "/data"
#define MQTT_TOPIC_COMMANDS "devices/" DEVICE_ID "/commands"
#define MQTT_TOPIC_STATUS "devices/" DEVICE_ID "/status"

// -- DEFAULT (HARDCODED) WIFI CREDENTIALS --
#define DEFAULT_WIFI_SSID      "anak kost 2.4G"
#define DEFAULT_WIFI_PASSWORD  "sekotique"

// -- BLE DEFINITIONS FOR PROVISIONING --
#define SERVICE_UUID           "4fafc201-1fb5-459e-8fcc-c5c9c331914b"
#define CHARACTERISTIC_UUID_WIFI "beb5483e-36e1-4688-b7f5-ea07361b26a8"
// Read-only + notify status characteristic to report provisioning state to clients
#define CHARACTERISTIC_UUID_STATUS "6e400003-b5a3-f393-e0a9-e50e24dcca9e"
// SSID scan command (write) and streaming results (read/notify)
#define CHARACTERISTIC_UUID_SCAN_CMD     "6e400010-b5a3-f393-e0a9-e50e24dcca9e"
#define CHARACTERISTIC_UUID_SCAN_RESULTS "6e400011-b5a3-f393-e0a9-e50e24dcca9e"

// -- ANALOG SENSOR PINS (ADC1 ONLY; RELIABLE WITH WIFI) --
#define PIN_CURRENT_ADC   34   // GPIO34 (ADC1)
#define PIN_VOLTAGE_ADC   35   // GPIO35 (ADC1)
#define RELAY_PIN 26

// Preferences object to store credentials
Preferences preferences;

WiFiClient espClient;
PubSubClient mqttClient(espClient);

unsigned long lastMsg = 0;
#define MSG_PUBLISH_INTERVAL 30000 // 30 seconds

// unsigned long lastOtaCheck = 0;
// #define OTA_CHECK_INTERVAL 86400000 // 24 hours

// Global status characteristic pointer so we can update status from anywhere
NimBLECharacteristic* gStatusChar = nullptr;
// Scan characteristics
NimBLECharacteristic* gScanResultsChar = nullptr;
volatile bool gScanRequested = false;
volatile bool gScanBusy = false;

// Forward decl
void performWifiScanAndNotify();
void reconnectMQTT();
void publishData();
const char* getTimestamp();
// void checkForUpdate();
// void performOTAUpdate(const char* url);

#define DEBUG_PRINTLN(x)
// Helper to set and (if subscribed) notify status updates
void setProvisioningStatus(const char* statusMsg) {
  DEBUG_PRINTLN(String("[STATUS] ") + statusMsg);
  if (gStatusChar != nullptr) {
    gStatusChar->setValue((uint8_t*)statusMsg, strlen(statusMsg));
    // Notify if a client has subscribed
    gStatusChar->notify();
  }
}

// --- SIMPLE ANALOG SENSOR READS (PLACEHOLDER APPROXIMATIONS) ---
static inline uint16_t readAdcAveraged(int pin, int samples = 16) {
  uint32_t sum = 0;
  for (int i = 0; i < samples; ++i) {
    sum += analogRead(pin);
    delayMicroseconds(500);
  }
  return (uint16_t)(sum / (uint32_t)samples);
}

// Convert ADC counts to voltage at ADC pin. With 11dB attenuation, full-scale is approx ~3.3–3.6V; we'll use 3.3V.
static inline float adcCountsToVolts(uint16_t counts) {
  return (counts / 4095.0f) * 3.3f;
}

// Approximate current (A) from analog sensor connected to PIN_CURRENT_ADC.
// Without sensor specifics, we return raw and an uncalibrated centered estimate.
// Assumes midpoint ~1.65V corresponds to 0 A (typical hall sensors); sensitivity placeholder 0.1 V/A.
static inline float readCurrentApproxA(uint16_t &rawCounts) {
  rawCounts = readAdcAveraged(PIN_CURRENT_ADC);
  float v = adcCountsToVolts(rawCounts);
  const float VREF = 1.65f; // midpoint for 3.3V supply
  const float SENS = 0.10f; // V/A placeholder (adjust per sensor)
  return (v - VREF) / SENS;
}

// Approximate external voltage using a simple divider to ADC pin.
// Without known divider, we return the ADC pin voltage as "approx".
static inline float readVoltageApproxV(uint16_t &rawCounts) {
  rawCounts = readAdcAveraged(PIN_VOLTAGE_ADC);
  float vAdc = adcCountsToVolts(rawCounts);
  // If using a divider: Vext = vAdc * ((R1 + R2) / R2). For now, report vAdc as approximate.
  return vAdc;
}

// Callback class to handle incoming BLE data
class MyCharacteristicCallbacks: public NimBLECharacteristicCallbacks {
    void onWrite(NimBLECharacteristic *pCharacteristic) {
        const char* receivedData = pCharacteristic->getValue().c_str();

        if (strlen(receivedData) > 0) {
            // Data should be in the format: "ssid,password"
            const char* comma = strchr(receivedData, ',');
            if (comma != nullptr) {
                int ssidLen = comma - receivedData;
                char ssid[ssidLen + 1];
                strncpy(ssid, receivedData, ssidLen);
                ssid[ssidLen] = '\0';

                const char* password = comma + 1;

                // Save credentials to NVS
                preferences.begin("wifi-creds", false);
                preferences.putString("ssid", ssid);
                preferences.putString("password", password);
                preferences.end();

                setProvisioningStatus("Received Credentials");

                setProvisioningStatus("Restarting");
                delay(3000);
                ESP.restart();
            } else {
                setProvisioningStatus("Invalid Format");
            }
        }
    }
};

// Callback for scan command writes
class MyScanCmdCallbacks: public NimBLECharacteristicCallbacks {
    void onWrite(NimBLECharacteristic *pCharacteristic) {
      const char* cmd = pCharacteristic->getValue().c_str();
      if (gScanBusy) {
        if (gScanResultsChar) {
          const char* busy = "SCAN_BUSY";
          gScanResultsChar->setValue((uint8_t*)busy, strlen(busy));
          gScanResultsChar->notify();
        }
        return;
      }
      if (strcasecmp(cmd, "SCAN") == 0 || strcasecmp(cmd, "START") == 0 || strlen(cmd) == 0) {
        gScanRequested = true; // handled in loop()
      } else {
        if (gScanResultsChar) {
          char msg[128];
          snprintf(msg, sizeof(msg), "SCAN_ERROR:Unknown command '%s'", cmd);
          gScanResultsChar->setValue((uint8_t*)msg, strlen(msg));
          gScanResultsChar->notify();
        }
      }
    }
};

void startBleProvisioning(const char* initialStatus = "Waiting for Credentials") {
  DEBUG_PRINTLN("Starting BLE Server for WiFi Configuration");
  
  NimBLEDevice::init("ESP32_WiFi_Config");
  NimBLEServer *pServer = NimBLEDevice::createServer();
  NimBLEService *pService = pServer->createService(SERVICE_UUID);
  
  NimBLECharacteristic *pCharacteristic = pService->createCharacteristic(
                                         CHARACTERISTIC_UUID_WIFI,
                                         NIMBLE_PROPERTY::WRITE
                                       );

  pCharacteristic->setCallbacks(new MyCharacteristicCallbacks());

  // Create status characteristic (READ + NOTIFY)
  gStatusChar = pService->createCharacteristic(
                    CHARACTERISTIC_UUID_STATUS,
                    (NIMBLE_PROPERTY::READ | NIMBLE_PROPERTY::NOTIFY)
                 );
  gStatusChar->setValue(initialStatus);

  // Create scan command characteristic (WRITE)
  NimBLECharacteristic* pScanCmd = pService->createCharacteristic(
                                   CHARACTERISTIC_UUID_SCAN_CMD,
                                   NIMBLE_PROPERTY::WRITE
                               );
  pScanCmd->setCallbacks(new MyScanCmdCallbacks());

  // Create scan results characteristic (READ + NOTIFY)
  gScanResultsChar = pService->createCharacteristic(
                         CHARACTERISTIC_UUID_SCAN_RESULTS,
                         (NIMBLE_PROPERTY::READ | NIMBLE_PROPERTY::NOTIFY)
                      );
  gScanResultsChar->setValue("SCAN_IDLE");
  pService->start();

  NimBLEAdvertising *pAdvertising = NimBLEDevice::getAdvertising();
  pAdvertising->addServiceUUID(SERVICE_UUID);
  NimBLEDevice::startAdvertising();
  
  DEBUG_PRINTLN("Characteristic defined. Ready for BLE connection.");
  DEBUG_PRINTLN("Send WiFi credentials in the format: Your_SSID,Your_Password");

  // Broadcast initial status
  setProvisioningStatus(initialStatus);
}

void sendAck(const char* command, const char* result) {
    StaticJsonDocument<128> ackDoc;
    ackDoc["command"] = command;
    ackDoc["result"] = result;
    ackDoc["timestamp"] = getTimestamp();
    
    char ackBuffer[128];
    serializeJson(ackDoc, ackBuffer);
    mqttClient.publish(MQTT_TOPIC_STATUS, ackBuffer);
}

void mqttCallback(char* topic, byte* payload, unsigned int length) {
  DEBUG_PRINTLN(String("Message arrived on topic: ") + topic + ". Message: ");
  char message[length + 1];
  memcpy(message, payload, length);
  message[length] = '\0';
  DEBUG_PRINTLN(message);

    StaticJsonDocument<256> doc;
    DeserializationError error = deserializeJson(doc, payload, length);
    
    if (error) {
        DEBUG_PRINTLN("Failed to parse command JSON");
        return;
    }
    
    const char* command = doc["command"];
    
    if (strcmp(command, "set_relay_state") == 0) {
        const char* state = doc["payload"]["state"];
        if (strcmp(state, "on") == 0) {
            digitalWrite(RELAY_PIN, HIGH);
        } else if (strcmp(state, "off") == 0) {
            digitalWrite(RELAY_PIN, LOW);
        }
        sendAck(command, "success");
    }
    else if (strcmp(command, "get_status") == 0) {
        publishData();
        sendAck(command, "success");
    }
    else if (strcmp(command, "restart") == 0) {
        sendAck(command, "success");
        delay(1000);
        ESP.restart();
    }
    else if (strcmp(command, "ota_update") == 0) {
        // checkForUpdate();
    }
}

void reconnectMQTT() {
  while (!mqttClient.connected()) {
    DEBUG_PRINTLN("Attempting MQTT connection...");
    // Attempt to connect
    if (mqttClient.connect(DEVICE_ID, DEVICE_ID, API_TOKEN)) {
      DEBUG_PRINTLN("connected");
      // Subscribe
      mqttClient.subscribe(MQTT_TOPIC_COMMANDS);
    } else {
      DEBUG_PRINTLN(String("failed, rc=") + mqttClient.state() + " try again in 5 seconds");
      // Wait 5 seconds before retrying
      delay(5000);
    }
  }
}

bool connectToWiFi(const char* ssid, const char* password, const char* status) {
    DEBUG_PRINTLN(String("Connecting to: ") + ssid);
    if (gStatusChar == nullptr) {
      startBleProvisioning(status);
    } else {
      setProvisioningStatus(status);
    }
    WiFi.begin(ssid, password);
    
    int timeout_counter = 0;
    while (WiFi.status() != WL_CONNECTED && timeout_counter < 30) {
      delay(500);
      DEBUG_PRINTLN(".");
      timeout_counter++;
    }

    if (WiFi.status() == WL_CONNECTED) {
      setProvisioningStatus("Connected");
      NimBLEAdvertising* adv = NimBLEDevice::getAdvertising();
      if (adv) adv->stop();
      return true;
    } else {
      setProvisioningStatus("Connection Failed");
      return false;
    }
}

void setup()
{
  Serial.begin(115200);
  DEBUG_PRINTLN("\nESP32 Starting...");
  
  pinMode(RELAY_PIN, OUTPUT);

  // Configure ADC for analog sensors (approximate; fine-tune later)
  analogReadResolution(12); // 0..4095
  analogSetPinAttenuation(PIN_CURRENT_ADC, ADC_11db); // wider range (~3.3V)
  analogSetPinAttenuation(PIN_VOLTAGE_ADC, ADC_11db);

  // Try to connect to WiFi with saved credentials
  preferences.begin("wifi-creds", true); // Read-only mode
  char ssid[64];
  char password[64];
  preferences.getString("ssid", ssid, sizeof(ssid));
  preferences.getString("password", password, sizeof(password));
  preferences.end();
  
  bool connected = false;
  if (strlen(ssid) > 0) {
    connected = connectToWiFi(ssid, password, "Connecting...");
  } else {
    // No saved credentials; try default hardcoded ones first
    strcpy(ssid, DEFAULT_WIFI_SSID);
    strcpy(password, DEFAULT_WIFI_PASSWORD);
    connected = connectToWiFi(ssid, password, "Connecting (Default)...");
  }

  // Check if connection was successful
  if (connected) {
    // ---- NORMAL OPERATION ----
    DEBUG_PRINTLN("\nWiFi connected!");
    DEBUG_PRINTLN(String("IP address: ") + WiFi.localIP());
    
    // Initialize time
    DEBUG_PRINTLN("Contacting time server...");
    configTime(0, 0, "pool.ntp.org", "time.nist.gov");
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo)){
      DEBUG_PRINTLN("Failed to obtain time. Using default time.");
    } else {
      char timeStr[25];
      strftime(timeStr, sizeof(timeStr), "%Y-%m-%d %H:%M:%S", &timeinfo);
      DEBUG_PRINTLN(String("Current time: ") + timeStr);
    }
    
    mqttClient.setServer(MQTT_HOST, MQTT_PORT);
    mqttClient.setCallback(mqttCallback);

  } else {
    // ---- PROVISIONING MODE ----
    DEBUG_PRINTLN("\nCould not connect to WiFi.");
    WiFi.disconnect(); // Ensure WiFi is off
    // Start/ensure BLE is running and show waiting state for new credentials
    if (gStatusChar == nullptr) {
      startBleProvisioning("Waiting for Credentials");
    } else {
      setProvisioningStatus("Waiting for Credentials");
    }
  }
}

void loop()
{
  if (WiFi.status() == WL_CONNECTED) {
    if (!mqttClient.connected()) {
      reconnectMQTT();
    }
    mqttClient.loop();

    unsigned long now = millis();
    if (now - lastMsg > MSG_PUBLISH_INTERVAL) {
      lastMsg = now;
      publishData();
    }

    // if (now - lastOtaCheck > OTA_CHECK_INTERVAL) {
    //   lastOtaCheck = now;
    //   checkForUpdate();
    // }

  } else {
    // If not connected, we are in BLE provisioning mode.
    // The device will restart automatically once credentials are received.
    // Handle deferred Wi-Fi scans requested via BLE
    if (gScanRequested && !gScanBusy) {
      gScanRequested = false;
      performWifiScanAndNotify();
    }
    DEBUG_PRINTLN("In BLE provisioning mode. Waiting for credentials...");
    delay(5000);
  }
}

void publishData() {
  // Read sensors and create JSON payload
  uint16_t rawCurrent = 0, rawVoltage = 0;
  float currentA = readCurrentApproxA(rawCurrent);
  float voltageV = readVoltageApproxV(rawVoltage);
  float powerW = voltageV * currentA;
  float energyWh = powerW * (MSG_PUBLISH_INTERVAL / 1000.0 / 3600.0);

  StaticJsonDocument<256> jsonDoc;
  jsonDoc["voltage"] = voltageV;
  jsonDoc["current"] = currentA;
  jsonDoc["power"] = powerW;
  jsonDoc["energy"] = energyWh;

  char jsonBuffer[512];
  serializeJson(jsonDoc, jsonBuffer);

  mqttClient.publish(MQTT_TOPIC_DATA, jsonBuffer);
  DEBUG_PRINTLN("Published MQTT message");
}

const char* getTimestamp() {
    static char timeStr[25];
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo)){
        strcpy(timeStr, "1970-01-01T00:00:00Z");
        return timeStr;
    }
    strftime(timeStr, sizeof(timeStr), "%Y-%m-%dT%H:%M:%SZ", &timeinfo);
    return timeStr;
}

// void checkForUpdate() {
//     HTTPClient http;
//     char url[128];
//     snprintf(url, sizeof(url), "%s/ota/check", API_URL);
//     http.begin(url);
//     char authHeader[100];
//     snprintf(authHeader, sizeof(authHeader), "Bearer %s", API_TOKEN);
//     http.addHeader("Authorization", authHeader);
//     http.addHeader("x-firmware-version", FIRMWARE_VERSION);
    
//     int httpCode = http.GET();
    
//     if (httpCode == 200) {
//         String payload = http.getString();
//         StaticJsonDocument<512> doc;
//         deserializeJson(doc, payload);
        
//         if (doc["update_available"]) {
//             const char* downloadUrl = doc["download_url"];
//             const char* newVersion = doc["version"];
            
//             DEBUG_PRINTLN(String("Update available: ") + newVersion);
//             performOTAUpdate(downloadUrl);
//         }
//     }
    
//     http.end();
// }

// void performOTAUpdate(const char* url) {
//     DEBUG_PRINTLN("Starting OTA update...");
    
//     WiFiClient client;
    
//     httpUpdate.setLedPin(LED_BUILTIN, LOW);
    
//     t_httpUpdate_return ret = httpUpdate.update(client, url);
    
//     switch(ret) {
//         case HTTP_UPDATE_FAILED:
//             DEBUG_PRINTLN(String("Update failed: ") + httpUpdate.getLastErrorString());
//             break;
//         case HTTP_UPDATE_NO_UPDATES:
//             DEBUG_PRINTLN("No updates available");
//             break;
//         case HTTP_UPDATE_OK:
//             DEBUG_PRINTLN("Update successful! Restarting...");
//             ESP.restart();
//             break;
//     }
// }

// Perform a Wi-Fi scan and stream results over BLE scan results characteristic
void performWifiScanAndNotify() {
  if (gScanResultsChar == nullptr) return;
  gScanBusy = true;
  setProvisioningStatus("Scanning...");

  // Announce start
  const char* startMsg = "SCAN_START";
  gScanResultsChar->setValue((uint8_t*)startMsg, strlen(startMsg));
  gScanResultsChar->notify();

  // Ensure station mode for scanning
  WiFi.mode(WIFI_STA);
  WiFi.disconnect(true);
  delay(100);

  int n = WiFi.scanNetworks(/*async=*/false, /*hidden=*/true);
  if (n < 0) {
    char err[32];
    snprintf(err, sizeof(err), "SCAN_ERROR:%d", WiFi.status());
    gScanResultsChar->setValue((uint8_t*)err, strlen(err));
    gScanResultsChar->notify();
  } else {
    for (int i = 0; i < n; ++i) {
      const char* ssid = WiFi.SSID(i).c_str();
      if (strlen(ssid) == 0) {
        // skip hidden
        continue;
      }
      int32_t rssi = WiFi.RSSI(i);
      wifi_auth_mode_t auth = (wifi_auth_mode_t)WiFi.encryptionType(i);
      const char* sec;
      switch (auth) {
        case WIFI_AUTH_OPEN: sec = "OPEN"; break;
        case WIFI_AUTH_WEP: sec = "WEP"; break;
        case WIFI_AUTH_WPA_PSK: sec = "WPA"; break;
        case WIFI_AUTH_WPA2_PSK: sec = "WPA2"; break;
        case WIFI_AUTH_WPA_WPA2_PSK: sec = "WPA/WPA2"; break;
        case WIFI_AUTH_WPA2_ENTERPRISE: sec = "WPA2-ENT"; break;
        case WIFI_AUTH_WPA3_PSK: sec = "WPA3"; break;
        case WIFI_AUTH_WPA2_WPA3_PSK: sec = "WPA2/WPA3"; break;
        default: sec = "UNKNOWN"; break;
      }
      // Build line: SSID|RSSI|SEC
      char line[128];
      snprintf(line, sizeof(line), "%s|%d|%s", ssid, rssi, sec);
      gScanResultsChar->setValue((uint8_t*)line, strlen(line));
      gScanResultsChar->notify();
      delay(30);
    }
  }

  // Done marker
  const char* doneMsg = "SCAN_DONE";
  gScanResultsChar->setValue((uint8_t*)doneMsg, strlen(doneMsg));
  gScanResultsChar->notify();

  // Reset busy and restore waiting status
  gScanBusy = false;
  setProvisioningStatus("Waiting for Credentials");
}