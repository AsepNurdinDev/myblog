# syntax=docker/dockerfile:1

FROM composer:2 AS vendor

WORKDIR /app

COPY backend/composer.json backend/composer.lock ./

# The lockfile is authoritative. Scripts are deliberately deferred until the
# application and its production PHP extensions are present in the final image.
# composer:2 lacks intl while the final image installs it, so only that builder
# platform check is deferred; the runtime extension remains mandatory.
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-req=ext-intl

FROM php:8.5-fpm-alpine AS production

WORKDIR /var/www/html

# Required by Laravel/Filament and the installed production dependencies:
# pdo_mysql for MySQL, mbstring and intl for framework/string handling, zip for
# Filament's production export dependency, and opcache for PHP-FPM performance.
# fileinfo, ctype, filter, openssl and PDO are provided by the PHP base image.
RUN apk add --no-cache \
        icu-libs \
        libzip \
        oniguruma \
        su-exec \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        zip \
    && apk del .build-deps

COPY --from=vendor /app/vendor ./vendor
COPY backend/ ./

# Recreate the public storage link in the image. Its target is supplied by the
# persistent storage volume at runtime; no uploaded data is baked into images.
RUN rm -f public/storage \
    && ln -s /var/www/html/storage/app/public public/storage \
    && chown -R www-data:www-data bootstrap/cache storage

COPY infra/docker/backend/entrypoint.sh /usr/local/bin/myblog-backend-entrypoint
RUN chmod 0755 /usr/local/bin/myblog-backend-entrypoint

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=warning \
    PHP_OPCACHE_ENABLE=1

ENTRYPOINT ["myblog-backend-entrypoint"]
CMD ["php-fpm", "-F"]
