// Core WiFi and MQTT Libraries
#include <WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include <time.h>
#include <Preferences.h>

// -- Firmware Version --
#define FIRMWARE_VERSION "1.0.1"

// -- API Configuration --
#define API_URL "172.16.100.63:8000/api/v1"

// -- MQTT Configuration --
#define MQTT_HOST "172.16.100.63"
#define MQTT_PORT 1883

// TODO: These should be unique for each device
#define DEVICE_ID "33"
#define API_TOKEN "zT944wjUkYCO2JYVyYXdgEJeVHA60rxVoL1HLX6UDbU38SmEbkRKLlNQ5IZ9c2kA"

// MQTT Topics
#define MQTT_TOPIC_DATA "devices/" DEVICE_ID "/data"
#define MQTT_TOPIC_COMMANDS "devices/" DEVICE_ID "/commands"
#define MQTT_TOPIC_STATUS "devices/" DEVICE_ID "/status"

// -- WIFI CREDENTIALS --
#define WIFI_SSID      "anak kost 2.4G"
#define WIFI_PASSWORD  "sekotique"

// -- ESP32-C3 MINI PIN DEFINITIONS --
// Voltage and Current Sensors (ADC1)
#define PIN_VOLTAGE_ADC    1   // GPIO1 (ADC1_CH1) - Shared voltage sensor
#define PIN_CURRENT1_ADC   2   // GPIO2 (ADC1_CH2) - Current sensor 1
#define PIN_CURRENT2_ADC   3   // GPIO3 (ADC1_CH3) - Current sensor 2
#define PIN_CURRENT3_ADC   4   // GPIO4 (ADC1_CH4) - Current sensor 3

// Relay Controls
#define RELAY1_PIN         5   // GPIO5 - Relay 1
#define RELAY2_PIN         7   // GPIO7 - Relay 2
#define RELAY3_PIN         10  // GPIO10 - Relay 3

#define MSG_PUBLISH_INTERVAL 30000 // 30 seconds

// Preferences object to store credentials
Preferences preferences;
WiFiClient espClient;
PubSubClient mqttClient(espClient);
unsigned long lastMsg = 0;

// Forward declarations
void reconnectMQTT();
void publishData();
const char* getTimestamp();

#define DEBUG_PRINTLN(x) Serial.println(x)
#define DEBUG_PRINT(x) Serial.print(x)

// -- SENSOR CALIBRATION CONSTANTS --
// Voltage Sensor (ZMPT101B) - This is a scaling factor applied after RMS calculation.
const float VOLTAGE_CALIBRATION = 145.58; 

// Current Sensor (ACS712) - Sensitivity in V/A.
const float ACS_SENS = 0.185; // For 5A model. Use 0.100 for 20A, 0.066 for 30A.


// --- RMS AC SENSOR READING ---

// Function to calculate the RMS value of a signal from an ADC pin.
// It first determines the DC offset (zero-point) of the signal,
// then samples the signal for a given duration to calculate the RMS value.
double getRMS(int pin, unsigned int sampleDuration = 20) {
    long adc_sum = 0;
    int num_samples_offset = 200;
    for (int i = 0; i < num_samples_offset; i++) {
        adc_sum += analogRead(pin);
        delayMicroseconds(100);
    }
    int dc_offset = adc_sum / num_samples_offset;

    unsigned long startTime = millis();
    int numberOfSamples = 0;
    double squaredSum = 0;

    while (millis() - startTime < sampleDuration) {
        double sample = analogRead(pin) - dc_offset;
        squaredSum += sample * sample;
        numberOfSamples++;
        delayMicroseconds(250); // Adjust for sampling frequency
    }

    if (numberOfSamples == 0) return 0;

    double meanSquare = squaredSum / numberOfSamples;
    return sqrt(meanSquare);
}

// Read RMS voltage
static inline float readVoltageV(uint16_t &rawOffset) {
    double rms_adc = getRMS(PIN_VOLTAGE_ADC);
    
    // The rawOffset for voltage is not as meaningful as for current,
    // but we can store the RMS ADC value for debugging.
    rawOffset = rms_adc;

    // V_rms = RMS_ADC_delta * (V_supply / ADC_resolution) * Calibration_Factor
    float vRms = rms_adc * (3.3f / 4095.0f) * VOLTAGE_CALIBRATION;
    
    // Filter for noise when no voltage is present
    if (vRms < 10.0) {
        return 0.0;
    }
    return vRms;
}

// Read RMS current
static inline float readCurrentA(int pin, uint16_t &rawOffset) {
    double rms_adc = getRMS(pin);
    rawOffset = rms_adc; // Store the RMS ADC value for debugging

    // Convert RMS ADC value to voltage, then to current
    float vRms = rms_adc * (3.3f / 4095.0f);
    float current_Amps = vRms / ACS_SENS;

    // Filter for noise when no current is flowing
    if (current_Amps < 0.05) { // 50mA threshold
        return 0.0;
    }
    return current_Amps;
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
  DEBUG_PRINT("Message arrived on topic: ");
  DEBUG_PRINT(topic);
  DEBUG_PRINTLN(". Message: ");
  
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
      // Expected payload: {"command": "set_relay_state", "channel": 1-3, "state": "on"/"off"}
      int relayNum = doc["channel"];
      const char* state = doc["state"];
      
      int relayPin = -1;
      switch(relayNum) {
        case 1: relayPin = RELAY1_PIN; break;
        case 2: relayPin = RELAY2_PIN; break;
        case 3: relayPin = RELAY3_PIN; break;
        default:
          DEBUG_PRINTLN("Invalid relay number");
          sendAck(command, "error: invalid relay");
          return;
      }
      
      if (strcmp(state, "on") == 0) {
          digitalWrite(relayPin, LOW);
          DEBUG_PRINT("Relay ");
          DEBUG_PRINT(relayNum);
          DEBUG_PRINTLN(" turned ON (Active LOW)");
      } else if (strcmp(state, "off") == 0) {
          digitalWrite(relayPin, HIGH);
          DEBUG_PRINT("Relay ");
          DEBUG_PRINT(relayNum);
          DEBUG_PRINTLN(" turned OFF (Active LOW)");
      }
      sendAck(command, "success");
  }
  else if (strcmp(command, "set_all_relays") == 0) {
      // Turn all relays on or off
      const char* state = doc["payload"]["state"];
      bool setState = (strcmp(state, "on") == 0);
      
      digitalWrite(RELAY1_PIN, setState);
      digitalWrite(RELAY2_PIN, setState);
      digitalWrite(RELAY3_PIN, setState);
      
      DEBUG_PRINT("All relays turned ");
      DEBUG_PRINTLN(state);
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
}

void reconnectMQTT() {
  while (!mqttClient.connected()) {
    DEBUG_PRINTLN("Attempting MQTT connection...");
    const char* lwt_payload = "offline";
    
    if (mqttClient.connect(DEVICE_ID, DEVICE_ID, API_TOKEN, MQTT_TOPIC_STATUS, 1, true, lwt_payload)) {
      DEBUG_PRINTLN("MQTT connected.");
      DEBUG_PRINTLN("Publishing online status...");
      mqttClient.publish(MQTT_TOPIC_STATUS, "online", true);
      
      DEBUG_PRINTLN("Subscribing to commands topic...");
      mqttClient.subscribe(MQTT_TOPIC_COMMANDS);
    } else {
      DEBUG_PRINT("MQTT connection failed, rc=");
      DEBUG_PRINT(mqttClient.state());
      DEBUG_PRINTLN(". Retrying in 5 seconds...");
      delay(5000);
    }
  }
}

const char* getWiFiStatusString(int status) {
  switch(status) {
    case WL_IDLE_STATUS: return "IDLE";
    case WL_NO_SSID_AVAIL: return "NO_SSID_AVAILABLE";
    case WL_SCAN_COMPLETED: return "SCAN_COMPLETED";
    case WL_CONNECTED: return "CONNECTED";
    case WL_CONNECT_FAILED: return "CONNECT_FAILED";
    case WL_CONNECTION_LOST: return "CONNECTION_LOST";
    case WL_DISCONNECTED: return "DISCONNECTED";
    default: return "UNKNOWN";
  }
}

void setup()
{
  // CRITICAL: For ESP32-C3 USB CDC, initialize Serial FIRST
  Serial.begin(115200);
  
  // Wait for Serial Monitor to connect (optional - remove for production)
  unsigned long serialTimeout = millis() + 3000;
  while (!Serial && millis() < serialTimeout) {
    delay(10);
  }
  
  delay(100);
  
  DEBUG_PRINTLN("\n\n==================================");
  DEBUG_PRINTLN("ESP32-C3 Mini 3-Channel Power Monitor");
  DEBUG_PRINTLN("==================================");
  DEBUG_PRINT("Firmware Version: ");
  DEBUG_PRINTLN(FIRMWARE_VERSION);
  DEBUG_PRINTLN("\n--- Pin Configuration ---");
  DEBUG_PRINT("Voltage Sensor: GPIO");
  DEBUG_PRINTLN(PIN_VOLTAGE_ADC);
  DEBUG_PRINT("Current Sensor 1: GPIO");
  DEBUG_PRINTLN(PIN_CURRENT1_ADC);
  DEBUG_PRINT("Current Sensor 2: GPIO");
  DEBUG_PRINTLN(PIN_CURRENT2_ADC);
  DEBUG_PRINT("Current Sensor 3: GPIO");
  DEBUG_PRINTLN(PIN_CURRENT3_ADC);
  DEBUG_PRINT("Relay 1: GPIO");
  DEBUG_PRINTLN(RELAY1_PIN);
  DEBUG_PRINT("Relay 2: GPIO");
  DEBUG_PRINTLN(RELAY2_PIN);
  DEBUG_PRINT("Relay 3: GPIO");
  DEBUG_PRINTLN(RELAY3_PIN);
  DEBUG_PRINTLN("==================================\n");
  
  // Initialize relay pins
  pinMode(RELAY1_PIN, OUTPUT);
  pinMode(RELAY2_PIN, OUTPUT);
  pinMode(RELAY3_PIN, OUTPUT);
  
  // Ensure all relays start OFF (Active LOW, so set to HIGH)
  digitalWrite(RELAY1_PIN, HIGH);
  digitalWrite(RELAY2_PIN, HIGH);
  digitalWrite(RELAY3_PIN, HIGH);
  DEBUG_PRINTLN("All relays initialized to OFF state (Active LOW)");
  
  // Configure ADC
  analogReadResolution(12); // 0..4095
  analogSetAttenuation(ADC_11db); // ~0-3.3V range
  
  // ===== IMPROVED WIFI CONNECTION =====
  DEBUG_PRINTLN("\n--- WiFi Setup ---");
  
  // Set WiFi to station mode and disconnect from any previous connection
  WiFi.mode(WIFI_STA);
  WiFi.disconnect();
  delay(100);
  
  DEBUG_PRINT("Connecting to WiFi SSID: ");
  DEBUG_PRINTLN(WIFI_SSID);
  DEBUG_PRINT("Password length: ");
  DEBUG_PRINTLN(strlen(WIFI_PASSWORD));
  DEBUG_PRINTLN("Note: ESP32-C3 only supports 2.4GHz WiFi");
  
  // Start WiFi connection
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  
  // Extended timeout with detailed status reporting
  int timeout_counter = 0;
  int max_attempts = 40; // 20 seconds total
  
  DEBUG_PRINT("Connecting");
  while (WiFi.status() != WL_CONNECTED && timeout_counter < max_attempts) {
    delay(500);
    DEBUG_PRINT(".");
    
    // Print detailed status every 5 seconds
    if (timeout_counter % 10 == 0 && timeout_counter > 0) {
      int status = WiFi.status();
      DEBUG_PRINT("\n[");
      DEBUG_PRINT(timeout_counter/2);
      DEBUG_PRINT("s] WiFi Status: ");
      DEBUG_PRINT(status);
      DEBUG_PRINT(" (");
      DEBUG_PRINT(getWiFiStatusString(status));
      DEBUG_PRINT(")");
      
      // Provide helpful hints based on status
      if (status == WL_NO_SSID_AVAIL) {
        DEBUG_PRINTLN("\n  ⚠ Cannot find network. Check:");
        DEBUG_PRINTLN("     - SSID spelling is correct");
        DEBUG_PRINTLN("     - Router is on and broadcasting");
        DEBUG_PRINTLN("     - Router is on 2.4GHz (not 5GHz)");
        DEBUG_PRINTLN("     - Device is in range");
      } else if (status == WL_CONNECT_FAILED) {
        DEBUG_PRINTLN("\n  ⚠ Connection failed. Check:");
        DEBUG_PRINTLN("     - Password is correct");
        DEBUG_PRINTLN("     - Router security is WPA2 (not WPA3)");
        DEBUG_PRINTLN("     - MAC filtering not blocking device");
      }
      DEBUG_PRINT("Continuing");
    }
    timeout_counter++;
  }
  
  DEBUG_PRINTLN("");
  
  if (WiFi.status() == WL_CONNECTED) {
    DEBUG_PRINTLN("\n✓✓✓ WiFi Connected Successfully! ✓✓✓");
    DEBUG_PRINT("IP address: ");
    DEBUG_PRINTLN(WiFi.localIP());
    DEBUG_PRINT("Gateway: ");
    DEBUG_PRINTLN(WiFi.gatewayIP());
    DEBUG_PRINT("Subnet: ");
    DEBUG_PRINTLN(WiFi.subnetMask());
    DEBUG_PRINT("DNS: ");
    DEBUG_PRINTLN(WiFi.dnsIP());
    DEBUG_PRINT("RSSI: ");
    DEBUG_PRINT(WiFi.RSSI());
    DEBUG_PRINTLN(" dBm");
    DEBUG_PRINT("MAC Address: ");
    DEBUG_PRINTLN(WiFi.macAddress());
    
    // Initialize time
    DEBUG_PRINTLN("\nContacting time server...");
    configTime(0, 0, "pool.ntp.org", "time.nist.gov");
    
    struct tm timeinfo;
    int retries = 0;
    while(!getLocalTime(&timeinfo) && retries < 10) {
      DEBUG_PRINT(".");
      delay(500);
      retries++;
    }
    
    if(retries < 10) {
      char timeStr[25];
      strftime(timeStr, sizeof(timeStr), "%Y-%m-%d %H:%M:%S", &timeinfo);
      DEBUG_PRINT("\n✓ Current time: ");
      DEBUG_PRINTLN(timeStr);
    } else {
      DEBUG_PRINTLN("\n⚠ Failed to obtain time. Continuing anyway...");
    }
    
    DEBUG_PRINT("\nConfiguring MQTT broker: ");
    DEBUG_PRINT(MQTT_HOST);
    DEBUG_PRINT(":");
    DEBUG_PRINTLN(MQTT_PORT);
    
    mqttClient.setServer(MQTT_HOST, MQTT_PORT);
    mqttClient.setBufferSize(1024);
    mqttClient.setCallback(mqttCallback);
    
    DEBUG_PRINTLN("\n✓✓✓ Setup Complete - Entering Main Loop ✓✓✓\n");
  } else {
    DEBUG_PRINTLN("\n✗✗✗ WiFi Connection FAILED ✗✗✗");
    DEBUG_PRINT("Final Status: ");
    DEBUG_PRINT(WiFi.status());
    DEBUG_PRINT(" (");
    DEBUG_PRINT(getWiFiStatusString(WiFi.status()));
    DEBUG_PRINTLN(")");
    
    DEBUG_PRINTLN("\n=== Troubleshooting Steps ===");
    DEBUG_PRINTLN("1. Verify WiFi credentials are correct");
    DEBUG_PRINTLN("2. Ensure router is on 2.4GHz (ESP32-C3 doesn't support 5GHz)");
    DEBUG_PRINTLN("3. Check router security is WPA2, not WPA3");
    DEBUG_PRINTLN("4. Verify device is within WiFi range");
    DEBUG_PRINTLN("5. Check if MAC filtering is enabled on router");
    DEBUG_PRINTLN("6. Try power cycling the router");
    DEBUG_PRINTLN("\nDevice will restart in 10 seconds...");
    delay(10000);
    ESP.restart();
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
    DEBUG_PRINTLN("WiFi disconnected! Attempting reconnection...");
    DEBUG_PRINT("Status: ");
    DEBUG_PRINTLN(getWiFiStatusString(WiFi.status()));
    WiFi.disconnect();
    delay(1000);
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    delay(5000);
  }
}

void publishData() {
  DEBUG_PRINTLN("\n======= Publishing Sensor Data =======");
  
  // Read shared voltage sensor
  uint16_t rawVoltage = 0;
  float voltageV = readVoltageV(rawVoltage);
  
  DEBUG_PRINT("Voltage: ");
  DEBUG_PRINT(voltageV);
  DEBUG_PRINT(" V (raw: ");
  DEBUG_PRINT(rawVoltage);
  DEBUG_PRINTLN(")");
  
  // Read current sensors
  uint16_t rawCurrent1 = 0, rawCurrent2 = 0, rawCurrent3 = 0;
  float current1A = readCurrentA(PIN_CURRENT1_ADC, rawCurrent1);
  float current2A = readCurrentA(PIN_CURRENT2_ADC, rawCurrent2);
  float current3A = readCurrentA(PIN_CURRENT3_ADC, rawCurrent3);
  
  // Calculate power for each channel
  float power1W = voltageV * current1A;
  float power2W = voltageV * current2A;
  float power3W = voltageV * current3A;

  // Get relay states (Active LOW logic)
  bool isRelay1On = (digitalRead(RELAY1_PIN) == LOW);
  bool isRelay2On = (digitalRead(RELAY2_PIN) == LOW);
  bool isRelay3On = (digitalRead(RELAY3_PIN) == LOW);

  // Create JSON payload
  StaticJsonDocument<768> jsonDoc;

  jsonDoc["firmware_version"] = FIRMWARE_VERSION;
  jsonDoc["timestamp"] = getTimestamp();
  jsonDoc["voltage"] = voltageV;
  jsonDoc["voltage_raw"] = rawVoltage;

  JsonArray channels = jsonDoc.createNestedArray("channels");

  // Channel 1
  JsonObject ch1 = channels.createNestedObject();
  ch1["channel"] = 1;
  ch1["current"] = current1A;
  ch1["current_raw"] = rawCurrent1;
  ch1["power"] = power1W;
  ch1["relay_state"] = isRelay1On ? "on" : "off";
  
  // Channel 2
  JsonObject ch2 = channels.createNestedObject();
  ch2["channel"] = 2;
  ch2["current"] = current2A;
  ch2["current_raw"] = rawCurrent2;
  ch2["power"] = power2W;
  ch2["relay_state"] = isRelay2On ? "on" : "off";

  // Channel 3
  JsonObject ch3 = channels.createNestedObject();
  ch3["channel"] = 3;
  ch3["current"] = current3A;
  ch3["current_raw"] = rawCurrent3;
  ch3["power"] = power3W;
  ch3["relay_state"] = isRelay3On ? "on" : "off";

  char jsonBuffer[768];
  serializeJson(jsonDoc, jsonBuffer);
  
  DEBUG_PRINTLN("\n--- MQTT Publish ---");
  DEBUG_PRINT("Topic: ");
  DEBUG_PRINTLN(MQTT_TOPIC_DATA);
  DEBUG_PRINT("Payload: ");
  DEBUG_PRINTLN(jsonBuffer);
  
  if (mqttClient.publish(MQTT_TOPIC_DATA, jsonBuffer)) {
    DEBUG_PRINTLN("✓ Published successfully");
  } else {
    DEBUG_PRINTLN("✗ Publish failed!");
  }
  
  DEBUG_PRINTLN("======================================\n");
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