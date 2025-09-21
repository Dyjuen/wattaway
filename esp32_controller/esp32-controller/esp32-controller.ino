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
*/

// Core WiFi and HTTP Libraries
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <time.h>

// Libraries for BLE Provisioning
#include <BLEDevice.h>
#include <BLEServer.h>
#include <BLEUtils.h>
#include <BLE2902.h>
#include <Preferences.h> // Used for saving credentials to Non-Volatile Storage (NVS)

#define HOST "https://30f3479327fa.ngrok-free.app"  // No trailing slash

// -- BLE DEFINITIONS FOR PROVISIONING --
#define SERVICE_UUID           "4fafc201-1fb5-459e-8fcc-c5c9c331914b"
#define CHARACTERISTIC_UUID_WIFI "beb5483e-36e1-4688-b7f5-ea07361b26a8"

// Preferences object to store credentials
Preferences preferences;

// --- YOUR ORIGINAL FUNCTIONS (UNCHANGED) ---
StaticJsonDocument<200> doc;
StaticJsonDocument<200> postJson;


// Callback class to handle incoming BLE data
class MyCharacteristicCallbacks: public BLECharacteristicCallbacks {
    void onWrite(BLECharacteristic *pCharacteristic) {
        std::string value = pCharacteristic->getValue();

        if (value.length() > 0) {
            String receivedData = String(value.c_str());
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

                Serial.println("Credentials saved. Restarting device in 3 seconds...");
                delay(3000);
                ESP.restart();
            } else {
                Serial.println("Invalid data format. Should be: ssid,password");
            }
            Serial.println("*********");
        }
    }
};

void startBleProvisioning() {
  Serial.println("Starting BLE Server for WiFi Configuration");
  
  BLEDevice::init("ESP32_WiFi_Config");
  BLEServer *pServer = BLEDevice::createServer();
  BLEService *pService = pServer->createService(SERVICE_UUID);
  
  BLECharacteristic *pCharacteristic = pService->createCharacteristic(
                                         CHARACTERISTIC_UUID_WIFI,
                                         BLECharacteristic::PROPERTY_WRITE
                                       );

  pCharacteristic->setCallbacks(new MyCharacteristicCallbacks());
  pService->start();

  BLEAdvertising *pAdvertising = BLEDevice::getAdvertising();
  pAdvertising->addServiceUUID(SERVICE_UUID);
  pAdvertising->setScanResponse(true);
  pAdvertising->setMinPreferred(0x06);
  pAdvertising->setMinPreferred(0x12);
  BLEDevice::startAdvertising();
  
  Serial.println("Characteristic defined. Ready for BLE connection.");
  Serial.println("Send WiFi credentials in the format: Your_SSID,Your_Password");
}


void setup()
{
  Serial.begin(115200);
  Serial.println("\nESP32 Starting...");
  
  // Try to connect to WiFi with saved credentials
  preferences.begin("wifi-creds", true); // Read-only mode
  String ssid = preferences.getString("ssid", "");
  String password = preferences.getString("password", "");
  preferences.end();
  
  bool connected = false;
  if (ssid.length() > 0) {
    Serial.printf("Found saved credentials. Connecting to: %s\n", ssid.c_str());
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
    
    Serial.println("Ready to send data to server");

  } else {
    // ---- PROVISIONING MODE ----
    Serial.println("\nCould not connect to WiFi.");
    WiFi.disconnect(); // Ensure WiFi is off
    startBleProvisioning();
  }
}

void loop()
{
  if (WiFi.status() == WL_CONNECTED) {
    // Only run the main logic if connected to WiFi
    jsonDataPost();
    delay(30000);
  } else {
    // If not connected, we are in BLE provisioning mode.
    // The device will restart automatically once credentials are received.
    Serial.println("In BLE provisioning mode. Waiting for credentials...");
    delay(5000);
  }
}

void httPost() {
  HTTPClient httPost;
  httPost.begin(String(HOST) + "/api/http-post");
  httPost.addHeader("Content-Type", "application/json");
  int httpResponceCode = httPost.POST("{\n\"sensor\":\"gps\",\n\"time\":1351824120,\n\"data\":[\n48.756080,\n2.302038\n]\n}");
  if (httpResponceCode > 0) {
    String response = httPost.getString();
    Serial.println(httpResponceCode);
    Serial.println(response);
  } else {
    Serial.print("err:");
    Serial.println(httpResponceCode);
  }
  httPost.end();
}

void httpGet() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(String(HOST) + "/api/http-get");
    int httpResponceCode = http.GET();
    if (httpResponceCode > 0) {
      String response = http.getString();
      Serial.println(httpResponceCode);
      Serial.println(response);
      DeserializationError error = deserializeJson(doc, response);
      if (error) {
        Serial.print("deserializeJson() failed: ");
        Serial.println(error.c_str());
        return;
      }
      const char* retval1 = doc["data"][0];
      const char* retval2 = doc["data"][1];
      Serial.println("Parsed data : ");
      Serial.println(retval1);
      Serial.println(retval2);

    } else {
      Serial.print("err:");
      Serial.println(httpResponceCode);
    }
    http.end();
  } else {
    Serial.println("wifi err");
  }
}

void jsonDataPost() {
  // Create JSON data
  StaticJsonDocument<128> jsonDoc;
  JsonObject stateObj = jsonDoc.createNestedObject("state");
  JsonObject reportedObj = stateObj.createNestedObject("reported");
  
  // Add current time and LED state
  struct tm timeinfo;
  if(!getLocalTime(&timeinfo)){
    Serial.println("Failed to obtain time");
    reportedObj["time"] = "1970-01-01T00:00:00Z";
  } else {
    char timeStr[25];
    strftime(timeStr, sizeof(timeStr), "%Y-%m-%dT%H:%M:%SZ", &timeinfo);
    reportedObj["time"] = timeStr;
  }
  reportedObj["led_state"] = "off";
  
  JsonObject locationObj = reportedObj.createNestedObject("location");
  locationObj["latitude"] = 22.54;
  locationObj["longitude"] = 88.72;
  
  char jsonBuffer[512];
  serializeJson(jsonDoc, jsonBuffer);
  
  HTTPClient http;
  String url = String(HOST) + "/api/arduino-json";
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  
  Serial.print("Sending data to ");
  Serial.println(url);
  
  int httpResponseCode = http.POST(jsonBuffer);
  
  if (httpResponseCode > 0) {
    Serial.print("Server response code: ");
    Serial.println(httpResponseCode);
  } else {
    Serial.print("Error sending POST: ");
    Serial.println(http.errorToString(httpResponseCode).c_str());
  }
  
  http.end();
  Serial.println("Waiting for next update...\n");
}