FROM php:8.2-apache

# Installation des extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Installation des dépendances pour GD (images)
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Copier le code source
COPY . /var/www/html/

# Définir les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Créer le fichier .env dans le container
RUN mkdir -p /home/gachacv && touch /home/gachacv/.env \
    && chown www-data:www-data /home/gachacv/.env

# Exposer le port 80
EXPOSE 80
