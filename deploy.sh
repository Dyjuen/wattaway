#!/bin/bash

# WattAway Deployment Script
# Usage: ./deploy.sh production

set -e # Exit on error

ENVIRONMENT=$1

if [ -z "$ENVIRONMENT" ]; then
    echo "Usage: ./deploy.sh {production|staging}"
    exit 1
fi

echo "🚀 Deploying WattAway to $ENVIRONMENT..."

# Pull latest code
echo "📥 Pulling latest code..."
git pull origin main

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Clear and cache config
echo "⚙️  Optimizing configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
echo "👷 Restarting queue workers..."
php artisan queue:restart

# Restart services
echo "🔄 Restarting services..."
sudo systemctl restart wattaway-mqtt-listener
sudo systemctl restart wattaway-scheduler

# Run post-deployment tests
echo "🧪 Running smoke tests..."
php artisan test --testsuite=Smoke

echo "✅ Deployment complete!"
