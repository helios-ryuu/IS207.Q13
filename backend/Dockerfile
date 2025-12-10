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
        intl

COPY --from=composer-base /app/vendor ./vendor
COPY . .

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
