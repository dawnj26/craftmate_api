FROM php:8.3-fpm-alpine

WORKDIR /var/www/app

RUN apk update && apk add \
    curl \
    libpng-dev \
    libxml2-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    git

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && apk --no-cache add nodejs npm

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY ./.php/php.ini /usr/local/etc/php/conf.d/99-nginx.ini

USER root

RUN chmod 777 -R /var/www/app
