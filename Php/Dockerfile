# Image PHP-FPM pour l'application Laravel caudexbid
FROM php:8.4-fpm-alpine

RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql bcmath intl zip opcache pcntl \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY php.ini /usr/local/etc/php/conf.d/caudexbid.ini

WORKDIR /var/www/html
