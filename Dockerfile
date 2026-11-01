FROM php:8.2-apache

# Installation des dépendances et de l'extension pdo_pgsql pour Supabase
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Render attend que le service écoute sur le port 10000 par défaut
RUN sed -i 's/80/10000/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Configuration Apache pour autoriser les directives et configurer DocumentRoot
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copie du code applicatif
COPY . /var/www/html/

# Droits d'écriture pour les dossiers uploads et data
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html

EXPOSE 10000

CMD ["apache2-foreground"]
