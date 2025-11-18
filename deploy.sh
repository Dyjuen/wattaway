#!/bin/bash

# WattAway Deployment Script
# Usage: ./deploy.sh production

set -e # Exit on error

# --- Safety and Maintenance Mode ---
# Ensure the application is brought back up even if the script fails.
# The 'trap' command ensures 'php artisan up' is executed when the script exits,
# for any reason (success or failure).
trap 'echo "Bringing application back online..."; php artisan up' EXIT
# --- End Safety ---

ENVIRONMENT=$1

if [ -z "$ENVIRONMENT" ]; then
    echo "Usage: ./deploy.sh {production|staging}"
    exit 1
fi

echo "🚀 Deploying WattAway to $ENVIRONMENT..."

# Activate maintenance mode
echo "🔒 Activating maintenance mode..."
php artisan down

# Pull latest code
echo "📥 Pulling latest code..."
git pull origin main

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader
npm install
npm run build

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

echo "✅ Deployment complete! The trap will now bring the application online."
