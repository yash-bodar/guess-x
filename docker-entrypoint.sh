#!/bin/bash
set -e

# Port configuration (Render passes PORT environment variable)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Generate app key if not already set in environment
if [ -z "$APP_KEY" ]; then
    echo "Notice: APP_KEY is not set. Generating a new application key..."
    php artisan key:generate --force
fi

# Ensure storage link exists
php artisan storage:link --quiet || true

# Cache configurations and routes
php artisan config:clear
php artisan route:cache
php artisan view:cache

# If a database host is provided, execute migrations & seed dictionary
if [ -n "$DB_HOST" ] || [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "Running migrations..."
    php artisan migrate --force || true
    echo "Ensuring initial database seeds..."
    php artisan db:seed --force || true
fi

echo "Starting Apache on port ${PORT}..."
exec apache2-foreground
