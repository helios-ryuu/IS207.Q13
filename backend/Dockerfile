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

# Install dependencies with APK cache mount
RUN --mount=type=cache,target=/var/cache/apk \
    set -eux; \
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
        opcache

# Configure PHP upload limits
RUN echo "upload_max_filesize = 10M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 12M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time = 60" >> /usr/local/etc/php/conf.d/uploads.ini

# Copy vendor first (cached layer), then source code
COPY --from=composer-base /app/vendor ./vendor
COPY --chown=www-data:www-data . .

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache \
    && rm -rf /app/bootstrap/cache/*.php

EXPOSE 8000

# Cache config/routes for production and start server
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=8000
