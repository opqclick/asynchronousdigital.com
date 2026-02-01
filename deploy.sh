#!/bin/bash
# Hostinger Deployment Script
# This script runs automatically after git webhook push

set -e # Exit on error

echo "🚀 Starting deployment..."

# Navigate to project directory
cd /home/u264230334/domains/asynchronousdigital.com/public_html

# Pull latest changes
echo "📥 Pulling latest code..."
git pull origin main

# Check if .env exists
if [ ! -f .env ]; then
    echo "⚠️ .env file not found! Copying from .env.prod..."
    cp .env.prod .env
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Verify APP_KEY exists
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 APP_KEY not set, generating..."
    php artisan key:generate --force
fi

# Install/Update Composer dependencies (no dev) without scripts first
echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# Now run package discovery
echo "🔍 Running package discovery..."
php artisan package:discover --ansi

# Install/Update NPM dependencies and build assets
if command -v npm &> /dev/null; then
    echo "📦 Installing NPM dependencies..."
    npm install --production
    
    echo "🏗️ Building production assets..."
    npm run build
else
    echo "⚠️ NPM not found - skipping asset build"
    echo "ℹ️  Assets should be built locally and committed to git"
fi

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Publish Telescope assets if needed
if [ ! -d "public/vendor/telescope" ]; then
    echo "📡 Publishing Telescope assets..."
    php artisan telescope:publish
fi

# Clear and cache configs
echo "🧹 Clearing and caching..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Clear old caches
php artisan cache:clear

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework
# Skip chown on shared hosting (user/group ownership is already correct)

# Restart queue workers (if supervisor is configured)
if command -v supervisorctl &> /dev/null; then
    echo "🔄 Restarting queue workers..."
    supervisorctl restart asynchronousdigital-worker:* 2>/dev/null || echo "⚠️ Queue workers not configured with supervisor"
fi

echo "✅ Deployment completed successfully!"
echo "📧 Remember: Queue worker must be running for emails to send!"

# Log deployment
echo "$(date '+%Y-%m-%d %H:%M:%S') - Deployment completed" >> storage/logs/deployment.log

# Show recent logs if there are errors
if [ -f storage/logs/laravel.log ]; then
    echo "📋 Recent logs (last 10 lines):"
    tail -n 10 storage/logs/laravel.log 2>/dev/null || true
fi
