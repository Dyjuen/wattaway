#!/bin/bash

# Set proper permissions for Laravel storage and cache directories

# Set ownership to www-data (or your web server user)
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache

# Set permissions for storage directory
chmod -R 755 storage

# Set specific permissions for sensitive directories
chmod -R 750 storage/app
chmod -R 750 storage/framework
chmod -R 750 storage/logs

# Set permissions for cache directory
chmod -R 755 bootstrap/cache

# Ensure .env file has proper permissions (readable by web server)
if [ -f ".env" ]; then
    chmod 644 .env
fi

# Create storage link if it doesn't exist
if [ ! -L "public/storage" ]; then
    php artisan storage:link
fi

echo "Storage permissions have been set successfully!"
