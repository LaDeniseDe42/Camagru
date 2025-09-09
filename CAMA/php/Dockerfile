FROM php:8.2-fpm

# Installer dépendances nécessaires (gettext pour envsubst, unzip, git, curl)
RUN apt-get update && apt-get install -y unzip git curl

# Installer les extensions PHP pour MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Créer le dossier gallery et donner les permissions
RUN mkdir -p /gallery && \
    chown -R www-data:www-data /gallery && \
    chmod -R 775 /gallery

# Permissions pour /tmp
RUN chmod 1777 /tmp

# Configuration des limites de taille d'upload PHP
RUN echo "upload_max_filesize=70M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=70M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=128M" >> /usr/local/etc/php/conf.d/uploads.ini

# Installer Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && php -r "unlink('composer-setup.php');"

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier l'application
COPY app /var/www/html

# Installer PHPMailer via Composer
RUN composer require phpmailer/phpmailer

# Lancer PHP-FPM
CMD ["php-fpm"]
