#!/bin/sh
# Ministry of Health Course Application
# Docker Entrypoint Script

set -e

echo "=========================================="
echo "MOH Course Application - Starting..."
echo "=========================================="

# Wait for database to be ready (if using MySQL)
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Waiting for MySQL to be ready..."
    while ! mysqladmin ping -h"$DB_HOST" --silent; do
        echo "MySQL is unavailable - sleeping"
        sleep 2
    done
    echo "MySQL is ready!"
fi

# Create storage directories if they don't exist
echo "Setting up storage directories..."
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set correct permissions
echo "Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Create SQLite database if using SQLite
if [ "$DB_CONNECTION" = "sqlite" ]; then
    if [ ! -f database/database.sqlite ]; then
        echo "Creating SQLite database..."
        touch database/database.sqlite
        chown www-data:www-data database/database.sqlite
        chmod 664 database/database.sqlite
    fi
fi

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache configuration
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Create storage link
echo "Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# Cache configuration for Spatie Permission
echo "Caching permissions..."
php artisan permission:cache-reset 2>/dev/null || true

# Run database seeders (only on first run)
if [ ! -f /var/www/html/storage/.seeded ]; then
    echo "Running database seeders..."
    php artisan db:seed --force 2>/dev/null || true
    touch /var/www/html/storage/.seeded
fi

echo "=========================================="
echo "MOH Course Application - Ready!"
echo "=========================================="
echo "App URL: ${APP_URL:-http://localhost}"
echo "Environment: ${APP_ENV:-production}"
echo "=========================================="

# Execute the main command
exec "$@"
