FROM php:8.3-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . .

RUN mkdir -p data/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 data

EXPOSE 80

CMD ["apache2-foreground"]
