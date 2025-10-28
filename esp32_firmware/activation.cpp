#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <EEPROM.h>
#include <esp_system.h>

// --- Constants ---
#define SERIAL_NUMBER "WS20250001234"  // Hardcoded during manufacturing
#define API_URL "http://wattaway.test" // Use your local Laravel URL for testing
#define FIRMWARE_VERSION "1.0.0"

// --- Global Variables (Assumed to be defined elsewhere) ---
bool isActivated = false;
// extern WiFiClient espClient; // If using WiFiClient for MQTT
// extern PubSubClient mqtt; // If using PubSubClient for MQTT

// --- Forward Declarations (Assumed to be defined elsewhere) ---
void connectToWiFi();
void connectToMQTT();
void reconnectMQTT();
void loadCredentialsFromEEPROM();

// --- EEPROM Configuration ---
#define EEPROM_SIZE 512
#define EEPROM_DEVICE_ID_ADDR 0
#define EEPROM_API_TOKEN_ADDR 10
#define EEPROM_MQTT_HOST_ADDR 74
#define EEPROM_MQTT_PORT_ADDR 138
#define EEPROM_MQTT_USERNAME_ADDR 142
#define EEPROM_MQTT_PASSWORD_ADDR 206
#define EEPROM_MQTT_PUBLISH_TOPIC_ADDR 270
#define EEPROM_MQTT_SUBSCRIBE_TOPIC_ADDR 334
#define EEPROM_ACTIVATED_FLAG_ADDR 400

// --- Helper functions for EEPROM (simplified for example) ---
void saveStringToEEPROM(int address, String data) {
    for (int i = 0; i < data.length(); i++) {
        EEPROM.write(address + i, data.charAt(i));
    }
    EEPROM.write(address + data.length(), 0); // Null terminator
    EEPROM.commit();
}

String readStringFromEEPROM(int address) {
    char data[65]; // Max string length + null terminator
    int i = 0;
    while (i < 64 && EEPROM.read(address + i) != 0) {
        data[i] = EEPROM.read(address + i);
        i++;
    }
    data[i] = 0; // Null terminator
    return String(data);
}

void saveIntToEEPROM(int address, int value) {
    EEPROM.put(address, value);
    EEPROM.commit();
}

int readIntFromEEPROM(int address) {
    int value;
    EEPROM.get(address, value);
    return value;
}

// --- ESP32 MAC Address ---
String getESP32MacAddress() {
    uint8_t mac[6];
    esp_read_mac(mac, ESP_MAC_WIFI_STA);
    
    char macStr[18];
    sprintf(macStr, "ESP32-%02X%02X%02X%02X%02X%02X",
            mac[0], mac[1], mac[2], mac[3], mac[4], mac[5]);
    
    return String(macStr);
}

// --- Handle Activation Success ---
void handleActivationSuccess(String response) {
    Serial.println("Activation successful!");
    const size_t capacity = JSON_OBJECT_SIZE(4) + JSON_OBJECT_SIZE(6) + JSON_OBJECT_SIZE(3) + 500; // Adjust capacity as needed
    DynamicJsonDocument doc(capacity);

    DeserializationError error = deserializeJson(doc, response);

    if (error) {
        Serial.print(F("deserializeJson() failed: "));
        Serial.println(error.f_str());
        return;
    }

    int device_id = doc["device_id"];
    String api_token = doc["api_token"].as<String>();
    String mqtt_host = doc["mqtt_credentials"]["host"].as<String>();
    int mqtt_port = doc["mqtt_credentials"]["port"].as<int>();
    String mqtt_username = doc["mqtt_credentials"]["username"].as<String>();
    String mqtt_password = doc["mqtt_credentials"]["password"].as<String>();
    String mqtt_publish_topic = doc["mqtt_credentials"]["topics"]["publish"].as<String>();
    String mqtt_subscribe_topic = doc["mqtt_credentials"]["topics"]["subscribe"].as<String>();
    String user_email = doc["user"]["email"].as<String>();

    // Save to EEPROM
    saveIntToEEPROM(EEPROM_DEVICE_ID_ADDR, device_id);
    saveStringToEEPROM(EEPROM_API_TOKEN_ADDR, api_token);
    saveStringToEEPROM(EEPROM_MQTT_HOST_ADDR, mqtt_host);
    saveIntToEEPROM(EEPROM_MQTT_PORT_ADDR, mqtt_port);
    saveStringToEEPROM(EEPROM_MQTT_USERNAME_ADDR, mqtt_username);
    saveStringToEEPROM(EEPROM_MQTT_PASSWORD_ADDR, mqtt_password);
    saveStringToEEPROM(EEPROM_MQTT_PUBLISH_TOPIC_ADDR, mqtt_publish_topic);
    saveStringToEEPROM(EEPROM_MQTT_SUBSCRIBE_TOPIC_ADDR, mqtt_subscribe_topic);
    EEPROM.write(EEPROM_ACTIVATED_FLAG_ADDR, 1); // Set activated flag
    EEPROM.commit();

    Serial.printf("Device ID: %d, User: %s\n", device_id, user_email.c_str());
    isActivated = true;
    // connectToMQTT(); // Call connectToMQTT after loading credentials
}

// --- Check Activation Status ---
void checkActivationStatus() {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("WiFi not connected, skipping activation check.");
        return;
    }

    HTTPClient http;
    String serialNumber = SERIAL_NUMBER;
    String hardwareId = getESP32MacAddress();

    Serial.printf("Attempting activation for Serial: %s, Hardware ID: %s\n", serialNumber.c_str(), hardwareId.c_str());

    http.begin(API_URL + String("/api/v1/device/activate"));
    http.addHeader("Content-Type", "application/json");

    StaticJsonDocument<256> doc;
    doc["serial_number"] = serialNumber;
    doc["hardware_id"] = hardwareId;
    doc["firmware_version"] = FIRMWARE_VERSION;

    String requestBody;
    serializeJson(doc, requestBody);

    int httpResponseCode = http.POST(requestBody);

    if (httpResponseCode == HTTP_CODE_OK) { // 200
        handleActivationSuccess(http.getString());
    } else if (httpResponseCode == HTTP_CODE_NOT_FOUND) { // 404
        Serial.println("Device not paired yet. Waiting for pairing...");
    } else if (httpResponseCode == HTTP_CODE_FORBIDDEN) { // 403
        Serial.printf("CRITICAL ERROR: Hardware verification failed! Response: %s\n", http.getString().c_str());
    } else {
        Serial.printf("Activation check failed with error: %d - %s\n", httpResponseCode, http.errorToString(httpResponseCode).c_str());
    }

    http.end();
}

// --- Load Credentials from EEPROM ---
void loadCredentialsFromEEPROM() {
    // Load device_id, api_token, mqtt credentials etc.
    // This function would be called from setup() if isActivated is true
    // For this example, we'll just set isActivated to true if the flag is set
    if (EEPROM.read(EEPROM_ACTIVATED_FLAG_ADDR) == 1) {
        isActivated = true;
        Serial.println("Device previously activated. Loading credentials...");
        // In a real scenario, you would load all saved credentials here
        // For example:
        // int device_id = readIntFromEEPROM(EEPROM_DEVICE_ID_ADDR);
        // String api_token = readStringFromEEPROM(EEPROM_API_TOKEN_ADDR);
        // ... and so on for MQTT credentials
    } else {
        isActivated = false;
    }
}

// --- Setup Function Integration (Example) ---
void setup() {
    Serial.begin(115200);
    EEPROM.begin(EEPROM_SIZE);
    
    connectToWiFi(); // Connect to WiFi first

    loadCredentialsFromEEPROM(); // Check if already activated

    if (isActivated) {
        Serial.println("Device already activated. Connecting to MQTT...");
        // connectToMQTT(); // Connect to MQTT with loaded credentials
    } else {
        Serial.println("Device not activated. Waiting for pairing...");
    }
}

// --- Loop Function Integration (Example) ---
void loop() {
    if (!isActivated) {
        // Check for activation every 30 seconds
        static unsigned long lastCheck = 0;
        if (millis() - lastCheck > 30000) {
            lastCheck = millis();
            checkActivationStatus();
        }
    } else {
        // Normal operation (assumes mqtt object exists and is handled)
        // if (!mqtt.connected()) {
        //     reconnectMQTT();
        // }
        // mqtt.loop();
        
        // Send data, handle commands, etc.
        // ... your existing code ...
    }
}
