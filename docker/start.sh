#!/bin/sh
# DO NOT use set -e — artisan failures should not kill the container

echo "==> Creating required directories..."
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> Caching Laravel configuration..."
php artisan config:cache || echo "[WARN] config:cache failed, continuing..."

echo "==> Caching routes..."
php artisan route:cache || echo "[WARN] route:cache failed, continuing..."

echo "==> Caching views..."
php artisan view:cache || echo "[WARN] view:cache failed, continuing..."

echo "==> Creating storage symlink..."
php artisan storage:link --force || echo "[WARN] storage:link failed, continuing..."

echo "==> Running migrations..."
php artisan migrate --force || echo "[WARN] migrate failed, continuing..."

echo "==> Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
