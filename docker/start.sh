#!/bin/bash
set -e

echo "==> Creating required directories..."
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache

# ── Generate .env dari environment variables Railway ──────────────────────────
# .env tidak ikut di-commit (ada di .gitignore), jadi kita generate dari env vars
echo "==> Writing .env from Railway environment variables..."
cat > /var/www/html/.env << EOF
APP_NAME=${APP_NAME:-heartfit}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

APP_LOCALE=${APP_LOCALE:-en}
APP_FALLBACK_LOCALE=${APP_FALLBACK_LOCALE:-en}
APP_FAKER_LOCALE=${APP_FAKER_LOCALE:-en_US}
APP_MAINTENANCE_DRIVER=${APP_MAINTENANCE_DRIVER:-file}
BCRYPT_ROUNDS=${BCRYPT_ROUNDS:-12}

LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_STACK=${LOG_STACK:-single}
LOG_DEPRECATIONS_CHANNEL=${LOG_DEPRECATIONS_CHANNEL:-null}
LOG_LEVEL=${LOG_LEVEL:-debug}

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-mysql.railway.internal}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-railway}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}
SESSION_ENCRYPT=${SESSION_ENCRYPT:-false}
SESSION_PATH=${SESSION_PATH:-/}
SESSION_DOMAIN=${SESSION_DOMAIN}
SESSION_SECURE_COOKIE=${SESSION_SECURE_COOKIE:-true}
SESSION_SAME_SITE=${SESSION_SAME_SITE:-lax}

BROADCAST_CONNECTION=${BROADCAST_CONNECTION:-log}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
CACHE_STORE=${CACHE_STORE:-database}

MIDTRANS_SERVER_KEY=${MIDTRANS_SERVER_KEY}
MIDTRANS_CLIENT_KEY=${MIDTRANS_CLIENT_KEY}
MIDTRANS_IS_PRODUCTION=${MIDTRANS_IS_PRODUCTION:-false}

MAIL_MAILER=${MAIL_MAILER:-log}
MAIL_SCHEME=${MAIL_SCHEME}
MAIL_HOST=${MAIL_HOST}
MAIL_PORT=${MAIL_PORT:-587}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-noreply@example.com}
MAIL_FROM_NAME="${MAIL_FROM_NAME:-HeartFit}"

FONNTE_TOKEN=${FONNTE_TOKEN}

VITE_APP_NAME=${APP_NAME:-heartfit}
EOF

echo "==> .env written successfully"

# ── Fix permissions ────────────────────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown www-data:www-data /var/www/html/.env
chmod 644 /var/www/html/.env

# ── Clear old cache dulu (jangan sampai cache lama pakai key kosong) ──────────
echo "==> Clearing old cache..."
php artisan config:clear  || echo "[WARN] config:clear failed"
php artisan cache:clear   || echo "[WARN] cache:clear failed"
php artisan route:clear   || echo "[WARN] route:clear failed"
php artisan view:clear    || echo "[WARN] view:clear failed"

# ── Artisan commands ──────────────────────────────────────────────────────────
echo "==> Caching config..."
php artisan config:cache  || echo "[WARN] config:cache failed"

echo "==> Caching routes..."
php artisan route:cache   || echo "[WARN] route:cache failed"

echo "==> Caching views..."
php artisan view:cache    || echo "[WARN] view:cache failed"

echo "==> Creating storage symlink..."
php artisan storage:link --force || echo "[WARN] storage:link failed"

echo "==> Running migrations..."
php artisan migrate --force || echo "[WARN] migrate failed"

# ── Fix permissions after artisan ─────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ── Apache setup ──────────────────────────────────────────────────────────────
echo "==> Forcing Apache MPM prefork..."
rm -f /etc/apache2/mods-enabled/mpm_*.load
rm -f /etc/apache2/mods-enabled/mpm_*.conf
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

if [ -n "$PORT" ] && [ "$PORT" != "8080" ]; then
    echo "==> Configuring Apache to also listen on PORT=$PORT..."
    echo "Listen $PORT" >> /etc/apache2/ports.conf
    cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/001-railway.conf
    sed -i "s/:8080>/:$PORT>/g" /etc/apache2/sites-available/001-railway.conf
    a2ensite 001-railway.conf
else
    echo "==> Keeping Apache on port 8080..."
fi

echo "==> Starting Apache..."
exec apache2-foreground
