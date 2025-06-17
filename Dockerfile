FROM php:8.3-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip zip curl git libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/

RUN ln -snf /usr/share/zoneinfo/Asia/Jerusalem /etc/localtime && echo "Asia/Jerusalem" > /etc/timezone

RUN touch /var/log/php_errors.log && chmod 666 /var/log/php_errors.log

WORKDIR /var/www/html