# ---------- Composer Phase ----------
FROM composer:latest AS composer-base
WORKDIR /app

COPY composer.json composer.lock artisan ./
COPY bootstrap bootstrap
COPY routes routes

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# ---------- Production Image ----------
FROM php:8.5-alpine AS production
WORKDIR /app

# Install dependencies
RUN set -eux; \
    apk add --no-cache \
        bash \
        curl \
        libzip-dev \
        oniguruma-dev \
        mysql-client \
        icu-dev \
        icu-libs \
    && docker-php-ext-install \
        pdo_mysql \
        bcmath \
        zip \
        intl \
        opcache \
    && rm -rf /var/cache/apk/*

# Configure PHP settings (uploads + opcache)
RUN { \
        echo "upload_max_filesize = 10M"; \
        echo "post_max_size = 12M"; \
        echo "max_execution_time = 60"; \
        echo "opcache.enable=1"; \
        echo "opcache.memory_consumption=128"; \
        echo "opcache.interned_strings_buffer=8"; \
        echo "opcache.max_accelerated_files=10000"; \
        echo "opcache.validate_timestamps=0"; \
    } > /usr/local/etc/php/conf.d/custom.ini

# Copy vendor first (cached layer), then source code
COPY --from=composer-base /app/vendor ./vendor
COPY . .

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && rm -rf /app/bootstrap/cache/*.php

EXPOSE 8000

# Cache config/routes for production and start server
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=8000
