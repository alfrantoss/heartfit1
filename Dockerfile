# ============================================================
# Stage 1: Build assets (Node.js)
# ============================================================
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

# ============================================================
# Stage 2: PHP Application (Railway production)
# ============================================================
FROM php:8.3-fpm-alpine AS app

# Install system dependencies + PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    bash \
    libpng \
    libpng-dev \
    libjpeg-turbo \
    libjpeg-turbo-dev \
    freetype \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        mbstring \
        xml \
        zip \
        bcmath \
        opcache \
        pcntl \
        intl \
    && apk del libpng-dev libjpeg-turbo-dev freetype-dev

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy built frontend assets from Node stage
COPY --from=node-builder /app/public/build ./public/build

# Install PHP dependencies (production, skip scripts — artisan runs in start.sh)
RUN composer install \
    --optimize-autoloader \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# OPcache configuration for production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Copy config files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
