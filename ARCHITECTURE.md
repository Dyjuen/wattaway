# WattAway System Architecture

## Overview
WattAway is a full-stack IoT platform designed for smart socket management with real-time monitoring, remote control, and automated scheduling capabilities.

## High-Level Architecture

```
┌─────────────┐         ┌──────────────┐         ┌─────────────┐
│   ESP32     │◄───────►│ MQTT Broker  │◄───────►│   Laravel   │
│   Devices   │  MQTT   │  (Mosquitto) │  MQTT   │   Backend   │
└─────────────┘         └──────────────┘         └─────────────┘
                                                         │
                                                         ▼
                        ┌──────────────────────────────────────┐
                        │         Storage Layer                │
                        ├──────────────┬───────────┬───────────┤
                        │    MySQL     │   Redis   │  Storage  │
                        │   (Primary)  │  (Cache)  │  (Files)  │
                        └──────────────┴───────────┴───────────┘
                                                         │
                                                         ▼
                        ┌──────────────────────────────────────┐
                        │      Frontend Clients                │
                        ├──────────────┬───────────────────────┤
                        │  Web App     │   Mobile App          │
                        │  (Browser)   │   (Future)            │
                        └──────────────┴───────────────────────┘
```

## Component Details

### 1. ESP32 Device Layer
**Technology:** Arduino C++ on ESP32

**Responsibilities:**
- Sensor data collection (voltage, current, power, etc.)
- Relay control for on/off functionality
- MQTT communication with broker
- OTA firmware update capability
- WiFi connection management with BLE provisioning

**Communication Flow:**
1. Device connects to WiFi
2. Authenticates with MQTT broker using device API token
3. Subscribes to command topic: `devices/{id}/commands`
4. Publishes sensor data to: `devices/{id}/data` every 30 seconds
5. Receives and executes commands from server

### 2. MQTT Broker (Mosquitto)
**Purpose:** Message broker for bidirectional IoT communication

**Features:**
- Lightweight publish/subscribe protocol
- QoS levels for message reliability
- Persistent connections
- Topic-based message routing
- Authentication and ACL

**Topics Structure:**
- `devices/{device_id}/data` - Device-to-server telemetry
- `devices/{device_id}/commands` - Server-to-device commands
- `devices/{device_id}/status` - Device status and acknowledgments

### 3. Laravel Backend
**Technology:** Laravel 11, PHP 8.2

**Core Services:**

#### MqttListenCommand
- Long-running Artisan command
- Subscribes to device data topics
- Dispatches jobs for data processing
- Handles reconnection logic

#### DeviceDataService
- Processes incoming sensor data
- Validates data integrity
- Stores data in database
- Updates device last_seen timestamp
- Triggers alerts for anomalies

#### MqttPublishService
- Sends commands to devices
- Publishes to device command topics
- Handles command acknowledgment
- Logs command history

#### DeviceControlService
- Orchestrates device control operations
- Implements business logic for scheduling
- Manages device configurations

### 4. Data Layer

#### MySQL Database
**Schema:**
- `users` - User accounts
- `devices` - Registered smart sockets
- `esp32messagelogs` - Time-series sensor data
- `device_configurations` - Device settings
- `device_schedules` - Automation schedules
- `firmware_versions` - OTA firmware management
- `audit_logs` - Security and action audit trail

**Indexes:**
- Compound index on (`device_id`, `created_at`) for time-series queries
- Individual indexes on frequently queried columns

#### Redis Cache
**Usage:**
- Device latest data (TTL: 30s)
- User device list (TTL: 5m)
- Daily statistics (TTL: 10m)
- Queue backend for async jobs
- Session storage

### 5. Queue System
**Technology:** Laravel Queues with Redis

**Jobs:**
- `ProcessIncomingDeviceData` - Store sensor readings
- `SendDeviceCommand` - Publish MQTT commands
- `ProcessSchedule` - Execute scheduled actions
- `SendAlertNotification` - User notifications

**Workers:** 2 concurrent workers managed by Supervisor

### 6. Background Processes

#### MQTT Listener
- Managed by Supervisor
- Auto-restart on failure
- Logs to `storage/logs/mqtt.log`

#### Scheduler
- Runs Laravel scheduler: `schedule:work`
- Executes `schedule:process` every minute
- Processes device automation schedules

#### Queue Workers
- Process background jobs
- 3 retry attempts with exponential backoff
- Dead letter queue for failed jobs

## Security Architecture

### Authentication Layers

#### Device Authentication
- Unique 64-character API token per device
- Stored in `devices.api_token` column
- Sent as Bearer token in HTTP headers
- Used as MQTT password

#### User Authentication
- Laravel Sanctum for SPA authentication
- Session-based for web clients
- API token for mobile clients
- CSRF protection for state-changing operations

### Authorization
- Policy-based authorization
- Users can only access their own devices
- Admin role for firmware management
- Device-level permissions

### Security Measures
- Rate limiting on all API endpoints
- Input validation with Form Requests
- SQL injection prevention via Eloquent ORM
- XSS protection with output escaping
- Security headers middleware
- HTTPS enforcement in production
- Audit logging for sensitive actions

## Scalability Considerations

### Current Capacity
- Single VPS deployment
- ~1000 devices supported
- 30-second data interval

### Scaling Strategy

#### Horizontal Scaling
1. **Load Balancer:** Nginx for API requests
2. **Multiple App Servers:** Stateless Laravel instances
3. **Centralized Redis:** Shared cache and queue
4. **Database Replication:** Read replicas for queries

#### Vertical Optimization
1. **Database Partitioning:** Time-series data by month
2. **Data Archival:** Move old data to cold storage
3. **CDN:** Static assets and firmware files
4. **Query Optimization:** Proper indexing and caching

### Monitoring
- Laravel Telescope (development)
- Log aggregation (production)
- MQTT broker metrics
- Database query performance
- Queue job processing times
- API response times
- Device connection status

## Deployment Architecture

### Production Environment
- **Web Server:** Nginx
- **Application:** PHP-FPM 8.2
- **Database:** MySQL 8.0
- **Cache:** Redis 7
- **MQTT:** Mosquitto
- **Process Manager:** Supervisor
- **SSL:** Let'''s Encrypt

### CI/CD Pipeline
1. Code push to GitHub
2. GitHub Actions runs tests
3. If tests pass, deploy to VPS
4. Run migrations
5. Clear and rebuild cache
6. Restart services
7. Run smoke tests

## Future Enhancements

### Phase 8 (Planned)
- WebSocket support for real-time web UI updates
- Mobile application (React Native)
- Multi-tenancy for B2B deployments
- Advanced analytics and reporting
- Machine learning for predictive maintenance
- Integration with third-party services (Alexa, Google Home)
- Grafana dashboards for monitoring
