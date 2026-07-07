#!/bin/bash

echo "==> Creating required directories..."
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> Caching Laravel configuration..."
php artisan config:cache || echo "[WARN] config:cache failed"

echo "==> Caching routes..."
php artisan route:cache || echo "[WARN] route:cache failed"

echo "==> Caching views..."
php artisan view:cache || echo "[WARN] view:cache failed"

echo "==> Creating storage symlink..."
php artisan storage:link --force || echo "[WARN] storage:link failed"

echo "==> Running migrations..."
php artisan migrate --force || echo "[WARN] migrate failed"

echo "==> Starting Apache..."
exec apache2-foreground
