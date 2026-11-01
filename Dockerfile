FROM php:8.3-apache

RUN docker-php-ext-install pdo_pgsql

WORKDIR /var/www/html
COPY . .

RUN mkdir -p data/uploads \
    && chown -R www-data:www-data data

EXPOSE 80
