# WattAway Deployment Guide

## Server Requirements
- Ubuntu 22.04 LTS
- 2GB+ RAM
- 20GB+ Storage
- PHP 8.2 with extensions (mysql, redis, mbstring, xml, curl)
- MySQL 8.0
- Redis 7
- Mosquitto MQTT Broker
- Supervisor (for queue workers)

## Initial Server Setup

### 1. Install Dependencies
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-redis php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip -y

# Install MySQL
sudo apt install mysql-server -y

# Install Redis
sudo apt install redis-server -y

# Install Mosquitto
sudo apt install mosquitto mosquitto-clients -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Supervisor
sudo apt install supervisor -y
```

### 2. Configure MySQL
```bash
sudo mysql
CREATE DATABASE wattaway_prod;
CREATE USER 'wattaway_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON wattaway_prod.* TO 'wattaway_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Configure Mosquitto
```bash
# Edit config
sudo nano /etc/mosquitto/mosquitto.conf

# Add these lines:
listener 1883
allow_anonymous false
password_file /etc/mosquitto/passwd

# Create password file
sudo mosquitto_passwd -c /etc/mosquitto/passwd laravel_backend
sudo mosquitto_passwd /etc/mosquitto/passwd device_user

# Restart Mosquitto
sudo systemctl restart mosquitto
```

### 4. Deploy Application
```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/yourusername/wattaway.git
cd wattaway

# Set permissions
sudo chown -R www-data:www-data /var/www/wattaway
sudo chmod -R 755 /var/www/wattaway
sudo chmod -R 775 storage bootstrap/cache

# Install dependencies
composer install --no-dev --optimize-autoloader

# Configure environment
cp .env.production .env
php artisan key:generate

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Configure Nginx
```nginx
server {
    listen 80;
    server_name wattaway.yourdomain.com;
    root /var/www/wattaway/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 6. Configure Supervisor
```ini
# /etc/supervisor/conf.d/wattaway-queue.conf
[program:wattaway-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/wattaway/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/wattaway/storage/logs/queue.log

# /etc/supervisor/conf.d/wattaway-mqtt.conf
[program:wattaway-mqtt]
command=php /var/www/wattaway/artisan mqtt:listen
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/wattaway/storage/logs/mqtt.log

# /etc/supervisor/conf.d/wattaway-scheduler.conf
[program:wattaway-scheduler]
command=php /var/www/wattaway/artisan schedule:work
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/wattaway/storage/logs/scheduler.log
```

```bash
# Reload Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

### 7. SSL Certificate (Let\'s Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d wattaway.yourdomain.com
```

## Deployment Process

### Manual Deployment
```bash
cd /var/www/wattaway
./deploy.sh production
```

### Automated Deployment (GitHub Actions)
Push to main branch triggers automatic deployment.

## Monitoring

### Check Service Status
```bash
# Supervisor services
sudo supervisorctl status

# MQTT broker
sudo systemctl status mosquitto

# Check logs
tail -f storage/logs/laravel.log
tail -f storage/logs/mqtt.log
```

### Database Backup
```bash
# Create backup script: /usr/local/bin/backup-wattaway.sh
#!/bin/bash
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
mysqldump -u wattaway_user -p'secure_password' wattaway_prod > /backups/wattaway_$TIMESTAMP.sql
find /backups -name "wattaway_*.sql" -mtime +7 -delete

# Add to crontab
0 2 * * * /usr/local/bin/backup-wattaway.sh
```

## Troubleshooting

### MQTT Connection Issues
```bash
# Test MQTT connection
mosquitto_sub -h localhost -p 1883 -u laravel_backend -P password -t 'devices/#' -v

# Check Mosquitto logs
sudo tail -f /var/log/mosquitto/mosquitto.log
```

### Queue Not Processing
```bash
# Restart queue workers
sudo supervisorctl restart wattaway-queue:*

# Check queue status
php artisan queue:failed
```

### High Memory Usage
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart services
sudo supervisorctl restart all
```
