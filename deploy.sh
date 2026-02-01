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

# Install/Update Composer dependencies (no dev)
echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Install/Update NPM dependencies
echo "📦 Installing NPM dependencies..."
npm install --production

# Build assets
echo "🏗️ Building production assets..."
npm run build

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Clear and cache configs
echo "🧹 Clearing and caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Clear old caches
php artisan cache:clear

# Restart queue workers (if supervisor is configured)
if command -v supervisorctl &> /dev/null; then
    echo "🔄 Restarting queue workers..."
    supervisorctl restart asynchronousdigital-worker:* 2>/dev/null || echo "⚠️ Queue workers not configured with supervisor"
fi

echo "✅ Deployment completed successfully!"
echo "📧 Remember: Queue worker must be running for emails to send!"

# Log deployment
echo "$(date '+%Y-%m-%d %H:%M:%S') - Deployment completed" >> storage/logs/deployment.log
