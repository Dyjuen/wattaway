# WattAway - Smart Socket IoT Platform

## Overview
WattAway is a complete IoT platform for smart socket management, featuring real-time power monitoring, remote control, scheduling, and OTA firmware updates.

## Tech Stack
- **Backend**: Laravel 11, MySQL 8, Redis
- **IoT Communication**: MQTT (Mosquitto broker)
- **Hardware**: ESP32 with custom Arduino firmware
- **Deployment**: Ubuntu VPS (Hostinger)

## Features
- ✅ Real-time power consumption monitoring
- ✅ Remote device control (on/off)
- ✅ Scheduling and automation
- ✅ Multi-device management
- ✅ OTA firmware updates
- ✅ User authentication and authorization
- ✅ RESTful API with rate limiting
- ✅ Comprehensive audit logging

## Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+
- Redis 7+
- Mosquitto MQTT broker
- Node.js 18+ (for frontend assets)

### Installation
```bash
# Clone repository
git clone https://github.com/yourusername/wattaway.git
cd wattaway

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate --seed

# Start services
php artisan serve
php artisan mqtt:listen
php artisan queue:work
```

## Project Structure
```
wattaway/
├── app/
│   ├── Console/Commands/      # Artisan commands (MQTT listener, schedulers)
│   ├── Http/
│   │   ├── Controllers/       # API and web controllers
│   │   ├── Middleware/        # Custom middleware (auth, security)
│   │   └── Requests/          # Form request validation
│   ├── Models/                # Eloquent models
│   ├── Services/              # Business logic services
│   └── Traits/                # Reusable traits (Auditable)
├── database/
│   ├── migrations/            # Database schema
│   └── factories/             # Test data factories
├── esp32_firmware/            # Arduino code for ESP32
├── routes/
│   ├── api.php               # API routes
│   └── web.php               # Web routes
└── tests/                    # PHPUnit tests
```

## API Documentation
See [API.md](API.md) for complete API reference.

## Deployment
See [DEPLOYMENT.md](DEPLOYMENT.md) for deployment instructions.

## Architecture
See [ARCHITECTURE.md](ARCHITECTURE.md) for system architecture details.

## Contributing
1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m '''Add amazing feature'''`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## License
MIT License - See LICENSE file for details.