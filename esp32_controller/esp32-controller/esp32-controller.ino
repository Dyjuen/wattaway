/**
  This code is an example of sending and recieving json data from server(Laravel is used Here)
  Deserialize json data
  posting json data as json string in server in post method with httpclient
  by Debarun Saha
*/
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <time.h>
#define HOST "https://30f3479327fa.ngrok-free.app"  // No trailing slash

const char* ssid       = "erwin";
const char* password   = "Denardy2006J";

StaticJsonDocument<200> doc;
StaticJsonDocument<200> postJson;
void setup()
{
  Serial.begin(115200);
  Serial.println("\nESP32 Starting...");
  
  // Connect to WiFi
  Serial.printf("Connecting to WiFi: %s", ssid);
  WiFi.begin(ssid, password);
  
  // Wait for connection
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  
  // Connection established
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
}

void loop()
{


  //  httpGet();
  //  Serial.println("----------");
  //  httPost();
  //  delay(300000);
  jsonDataPost();
  delay(30000);
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
    //    http.addHeader("Content-Type","text/plain");
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
  reportedObj["led_state"] = "off";  // Default LED state
  
  // Add location data (kept for backward compatibility)
  JsonObject locationObj = reportedObj.createNestedObject("location");
  locationObj["latitude"] = 22.54;
  locationObj["longitude"] = 88.72;
  
  // Serialize JSON to buffer
  char jsonBuffer[512];
  serializeJson(jsonDoc, jsonBuffer);
  
  // Send data to server
  HTTPClient http;
  String url = String(HOST) + "/api/arduino-json";
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  
  Serial.print("Sending data to ");
  Serial.println(url);
  
  int httpResponseCode = http.POST(jsonBuffer);
  
  if (httpResponseCode > 0) {
    Serial.print("Server response: ");
    if (httpResponseCode == HTTP_CODE_OK) {
      String response = http.getString();
      Serial.println("Success!");
    } else {
      Serial.print("HTTP ");
      Serial.println(httpResponseCode);
    }
  } else {
    Serial.print("Error: ");
    Serial.println(http.errorToString(httpResponseCode).c_str());
  }
  
  http.end();
  Serial.println("Waiting for next update...\n");
}

