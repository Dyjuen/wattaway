# WattAway API Documentation

Base URL: `https://api.wattaway.com/api/v1`

## Authentication

### Device Authentication
ESP32 devices use Bearer token authentication:
```
Authorization: Bearer {device_api_token}
```

### User Authentication
Web/mobile clients use Laravel Sanctum:
```
Authorization: Bearer {user_api_token}
```

## Endpoints

### Device Endpoints (Device Auth Required)

#### Submit Device Data
```http
POST /device/data
Content-Type: application/json
Authorization: Bearer {device_token}

{
  "voltage": 220.5,
  "current": 5.2,
  "power": 1146.6,
  "energy": 25.5,
  "frequency": 50.0,
  "power_factor": 0.95
}
```

**Response:**
```json
{
  "message": "Data received successfully",
  "stored": true
}
```

#### Check for OTA Updates
```http
GET /ota/check
Authorization: Bearer {device_token}
X-Firmware-Version: 1.0.0
```

**Response (Update Available):**
```json
{
  "update_available": true,
  "version": "1.1.0",
  "size": 654321,
  "checksum": "abc123...",
  "download_url": "https://api.../ota/download/5",
  "release_notes": "Bug fixes and improvements"
}
```

**Response (No Update):**
```json
{
  "update_available": false
}
```

### User Endpoints (User Auth Required)

#### List Devices
```http
GET /devices
Authorization: Bearer {user_token}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Living Room Socket",
      "type": "socket",
      "status": "online",
      "last_seen": "2025-10-22T10:30:00Z",
      "latest_data": {
        "voltage": 220.5,
        "current": 5.2,
        "power": 1146.6,
        "energy": 25.5
      }
    }
  ]
}
```

#### Control Device
```http
POST /devices/{id}/control
Authorization: Bearer {user_token}
Content-Type: application/json

{
  "action": "on"  // "on", "off", or "toggle"
}
```

**Response:**
```json
{
  "message": "Command sent successfully",
  "device_id": 1,
  "action": "on"
}
```

#### Get Device History
```http
GET /devices/{id}/history?hours=24
Authorization: Bearer {user_token}
```

**Response:**
```json
{
  "data": [
    {
      "timestamp": "2025-10-22T10:30:00Z",
      "voltage": 220.5,
      "current": 5.2,
      "power": 1146.6,
      "energy": 25.5
    }
  ],
  "meta": {
    "total": 2880,
    "per_page": 50,
    "current_page": 1
  }
}
```

#### Create Schedule
```http
POST /devices/{id}/schedule
Authorization: Bearer {user_token}
Content-Type: application/json

{
  "name": "Morning ON",
  "action": "on",
  "schedule_type": "daily",
  "scheduled_time": "06:00",
  "days_of_week": [1, 2, 3, 4, 5]  // Optional for weekly
}
```

**Response:**
```json
{
  "message": "Schedule created successfully",
  "schedule": {
    "id": 1,
    "name": "Morning ON",
    "action": "on",
    "schedule_type": "daily",
    "scheduled_time": "06:00:00",
    "is_enabled": true
  }
}
```

## Rate Limits

| Endpoint Type | Rate Limit |
|--------------|------------|
| Device Data | 120 req/min |
| User API | 60 req/min |
| Control Commands | 30 req/min |
| OTA Downloads | 10 req/hour |
| Public Endpoints | 10 req/min |

## Error Responses

### 400 Bad Request
```json
{
  "message": "Validation failed",
  "errors": {
    "voltage": ["The voltage must be between 0 and 300."]
  }
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthorized"
}
```

### 403 Forbidden
```json
{
  "message": "You do not have permission to access this device"
}
```

### 429 Too Many Requests
```json
{
  "message": "Too many requests. Please try again later.",
  "retry_after": 60
}
```

### 500 Internal Server Error
```json
{
  "message": "An error occurred. Please try again later."
}
```

## MQTT Topics (For Real-time Communication)

### Device Publishes To:
- `devices/{device_id}/data` - Sensor readings
- `devices/{device_id}/status` - Status updates and acknowledgments

### Device Subscribes To:
- `devices/{device_id}/commands` - Control commands from server

### Command Message Format:
```json
{
  "command": "set_relay_state",
  "payload": {
    "state": "on"
  },
  "timestamp": "2025-10-22T10:30:00Z"
}
```

### Available Commands:
- `set_relay_state` - Turn device on/off
- `get_status` - Request immediate status report
- `update_config` - Update device configuration
- `restart` - Restart device
- `ota_update` - Trigger OTA firmware update
