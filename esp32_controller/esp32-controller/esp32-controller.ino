/**
  This code is an example of sending and recieving json data from server(Laravel is used Here)
  Deserialize json data
  posting json data as json string in server in post method with httpclient
  by Debarun Saha

  UPDATED:
  - Added BLE Provisioning for WiFi credentials.
  - Hardcoded SSID and password are removed.
  - Device checks for saved credentials in NVS on boot.
  - If connection fails or no credentials exist, it starts a BLE server
    to allow configuration from a phone or laptop.
  - Migrated from HTTP to MQTT for real-time communication.
*/

// Core WiFi and MQTT Libraries
#include <WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include <time.h>

// Libraries for BLE Provisioning (using NimBLE to reduce flash size)
#include <NimBLEDevice.h> // Single include is sufficient; exposes NimBLEServer, NimBLE2902, etc.
#include <Preferences.h> // Used for saving credentials to Non-Volatile Storage (NVS)

// -- MQTT Configuration --
#define MQTT_HOST "your_mqtt_broker_ip" // Replace with your broker's IP or hostname
#define MQTT_PORT 1883

// TODO: These should be unique for each device
#define DEVICE_ID "123"
#define API_TOKEN "YOUR_64_CHAR_API_TOKEN"

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
String getTimestamp();

// Helper to set and (if subscribed) notify status updates
void setProvisioningStatus(const char* statusMsg) {
  Serial.print("[STATUS] ");
  Serial.println(statusMsg);
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
        // Build Arduino String from underlying buffer for compatibility across core versions
        String receivedData = String(pCharacteristic->getValue().c_str());

        if (receivedData.length() > 0) {
            Serial.println("*********");
            Serial.print("Received Value: ");
            Serial.println(receivedData);

            // Data should be in the format: "ssid,password"
            int commaIndex = receivedData.indexOf(',');
            if (commaIndex > 0) {
                String ssid = receivedData.substring(0, commaIndex);
                String password = receivedData.substring(commaIndex + 1);

                Serial.print("New SSID: ");
                Serial.println(ssid);
                Serial.print("New Password: ");
                Serial.println(password);

                // Save credentials to NVS
                preferences.begin("wifi-creds", false);
                preferences.putString("ssid", ssid);
                preferences.putString("password", password);
                preferences.end();

                setProvisioningStatus("Received Credentials");

                Serial.println("Credentials saved. Restarting device in 3 seconds...");
                setProvisioningStatus("Restarting");
                delay(3000);
                ESP.restart();
            } else {
                Serial.println("Invalid data format. Should be: ssid,password");
                setProvisioningStatus("Invalid Format");
            }
            Serial.println("*********");
        }
    }
};

// Callback for scan command writes
class MyScanCmdCallbacks: public NimBLECharacteristicCallbacks {
    void onWrite(NimBLECharacteristic *pCharacteristic) {
      String cmd = String(pCharacteristic->getValue().c_str());
      cmd.trim();
      if (gScanBusy) {
        if (gScanResultsChar) {
          const char* busy = "SCAN_BUSY";
          gScanResultsChar->setValue((uint8_t*)busy, strlen(busy));
          gScanResultsChar->notify();
        }
        return;
      }
      if (cmd.equalsIgnoreCase("SCAN") || cmd.equalsIgnoreCase("START") || cmd.length() == 0) {
        gScanRequested = true; // handled in loop()
      } else {
        if (gScanResultsChar) {
          String msg = String("SCAN_ERROR:Unknown command '") + cmd + "'";
          gScanResultsChar->setValue((uint8_t*)msg.c_str(), msg.length());
          gScanResultsChar->notify();
        }
      }
    }
};

void startBleProvisioning(const char* initialStatus = "Waiting for Credentials") {
  Serial.println("Starting BLE Server for WiFi Configuration");
  
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
  
  Serial.println("Characteristic defined. Ready for BLE connection.");
  Serial.println("Send WiFi credentials in the format: Your_SSID,Your_Password");

  // Broadcast initial status
  setProvisioningStatus(initialStatus);
}

void sendAck(String command, String result) {
    StaticJsonDocument<128> ackDoc;
    ackDoc["command"] = command;
    ackDoc["result"] = result;
    ackDoc["timestamp"] = getTimestamp();
    
    char ackBuffer[128];
    serializeJson(ackDoc, ackBuffer);
    mqttClient.publish(MQTT_TOPIC_STATUS, ackBuffer);
}

void mqttCallback(char* topic, byte* payload, unsigned int length) {
  Serial.print("Message arrived on topic: ");
  Serial.print(topic);
  Serial.print(". Message: ");
  String message;
  for (int i = 0; i < length; i++) {
    message += (char)payload[i];
  }
  Serial.println(message);

    StaticJsonDocument<256> doc;
    DeserializationError error = deserializeJson(doc, payload, length);
    
    if (error) {
        Serial.println("Failed to parse command JSON");
        return;
    }
    
    String command = doc["command"];
    
    if (command == "set_relay_state") {
        String state = doc["payload"]["state"];
        if (state == "on") {
            digitalWrite(RELAY_PIN, HIGH);
        } else if (state == "off") {
            digitalWrite(RELAY_PIN, LOW);
        }
        sendAck(command, "success");
    }
    else if (command == "get_status") {
        publishData();
        sendAck(command, "success");
    }
    else if (command == "restart") {
        sendAck(command, "success");
        delay(1000);
        ESP.restart();
    }
}

void reconnectMQTT() {
  while (!mqttClient.connected()) {
    Serial.print("Attempting MQTT connection...");
    // Attempt to connect
    if (mqttClient.connect(DEVICE_ID, DEVICE_ID, API_TOKEN)) {
      Serial.println("connected");
      // Subscribe
      mqttClient.subscribe(MQTT_TOPIC_COMMANDS);
    } else {
      Serial.print("failed, rc=");
      Serial.print(mqttClient.state());
      Serial.println(" try again in 5 seconds");
      // Wait 5 seconds before retrying
      delay(5000);
    }
  }
}

void setup()
{
  Serial.begin(115200);
  Serial.println("\nESP32 Starting...");
  
  pinMode(RELAY_PIN, OUTPUT);

  // Configure ADC for analog sensors (approximate; fine-tune later)
  analogReadResolution(12); // 0..4095
  analogSetPinAttenuation(PIN_CURRENT_ADC, ADC_11db); // wider range (~3.3V)
  analogSetPinAttenuation(PIN_VOLTAGE_ADC, ADC_11db);

  // Try to connect to WiFi with saved credentials
  preferences.begin("wifi-creds", true); // Read-only mode
  String ssid = preferences.getString("ssid", "");
  String password = preferences.getString("password", "");
  preferences.end();
  
  bool connected = false;
  if (ssid.length() > 0) {
    Serial.printf("Found saved credentials. Connecting to: %s\n", ssid.c_str());
    // Initialize BLE so client can see status while attempting to connect
    if (gStatusChar == nullptr) {
      startBleProvisioning("Connecting...");
    } else {
      setProvisioningStatus("Connecting...");
    }
    WiFi.begin(ssid.c_str(), password.c_str());
    
    // Wait for connection with a 15-second timeout
    int timeout_counter = 0;
    while (WiFi.status() != WL_CONNECTED && timeout_counter < 30) {
      delay(500);
      Serial.print(".");
      timeout_counter++;
    }

    if (WiFi.status() == WL_CONNECTED) {
      connected = true;
      setProvisioningStatus("Connected");
      // Optional: stop advertising to save power once connected
      NimBLEAdvertising* adv = NimBLEDevice::getAdvertising();
      if (adv) adv->stop();
    } else {
      setProvisioningStatus("Connection Failed");
    }
  } else {
    // No saved credentials; try default hardcoded ones first
    ssid = String(DEFAULT_WIFI_SSID);
    password = String(DEFAULT_WIFI_PASSWORD);
    Serial.printf("No saved credentials. Trying default SSID: %s\n", ssid.c_str());
    if (gStatusChar == nullptr) {
      startBleProvisioning("Connecting (Default)...");
    } else {
      setProvisioningStatus("Connecting (Default)...");
    }
    WiFi.begin(ssid.c_str(), password.c_str());

    // Wait for connection with a 15-second timeout
    int timeout_counter = 0;
    while (WiFi.status() != WL_CONNECTED && timeout_counter < 30) {
      delay(500);
      Serial.print(".");
      timeout_counter++;
    }

    if (WiFi.status() == WL_CONNECTED) {
      connected = true;
      setProvisioningStatus("Connected (Default)");
      NimBLEAdvertising* adv = NimBLEDevice::getAdvertising();
      if (adv) adv->stop();
    } else {
      setProvisioningStatus("Connection Failed (Default)");
    }
  }

  // Check if connection was successful
  if (connected) {
    // ---- NORMAL OPERATION ----
    Serial.println("\nWiFi connected!");
    Serial.print("IP address: ");
    Serial.println(WiFi.localIP());
    
    // Initialize time
    Serial.println("Contacting time server...");
    configTime(0, 0, "pool.ntp.org", "time.nist.gov");
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo)){
      Serial.println("Failed to obtain time. Using default time.");
    } else {
      char timeStr[25];
      strftime(timeStr, sizeof(timeStr), "%Y-%m-%d %H:%M:%S", &timeinfo);
      Serial.print("Current time: ");
      Serial.println(timeStr);
    }
    
    mqttClient.setServer(MQTT_HOST, MQTT_PORT);
    mqttClient.setCallback(mqttCallback);

  } else {
    // ---- PROVISIONING MODE ----
    Serial.println("\nCould not connect to WiFi.");
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
  } else {
    // If not connected, we are in BLE provisioning mode.
    // The device will restart automatically once credentials are received.
    // Handle deferred Wi-Fi scans requested via BLE
    if (gScanRequested && !gScanBusy) {
      gScanRequested = false;
      performWifiScanAndNotify();
    }
    Serial.println("In BLE provisioning mode. Waiting for credentials...");
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
  Serial.println("Published MQTT message");
}

String getTimestamp() {
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo)){
        return "1970-01-01T00:00:00Z";
    }
    char timeStr[25];
    strftime(timeStr, sizeof(timeStr), "%Y-%m-%dT%H:%M:%SZ", &timeinfo);
    return String(timeStr);
}

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
    String err = String("SCAN_ERROR:") + WiFi.status();
    gScanResultsChar->setValue((uint8_t*)err.c_str(), err.length());
    gScanResultsChar->notify();
  } else {
    for (int i = 0; i < n; ++i) {
      String ssid = WiFi.SSID(i);
      if (ssid.length() == 0) {
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
      String line = ssid + "|" + String(rssi) + "|" + String(sec);
      gScanResultsChar->setValue((uint8_t*)line.c_str(), line.length());
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