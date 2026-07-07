#!/bin/sh
set -e

echo "==> Caching Laravel config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

echo "==> Running migrations..."
php artisan migrate --force 2>/dev/null || true

echo "==> Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
