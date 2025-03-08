FROM php:8.1-fpm

WORKDIR /var/www/html/vending-machine/api

RUN apt-get update && apt-get install -y \
    zip \
    vim \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo_mysql

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

RUN usermod -u 1001 www-data

RUN chown -R www-data:www-data /var/www

RUN chown www-data:www-data /var/www/html/

USER www-data