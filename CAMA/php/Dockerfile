FROM php:8.2-fpm

# Installer sendmail, msmtp, etc.
RUN apt-get update && apt-get install -y sendmail msmtp

# Installer extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Créer le dossier /gallery avec permissions correctes
RUN mkdir -p /gallery && \
    chown -R www-data:www-data /gallery && \
    chmod -R 775 /gallery

# Configuration PHP personnalisée (php.ini)
COPY php.ini /usr/local/etc/php/conf.d/custom.ini


# Script poyr msmtprc
COPY --chown=www-data:www-data entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Log mail
RUN touch /var/log/mail.log && \
    chmod 666 /var/log/mail.log && \
    chown www-data:www-data /var/log/mail.log

# Utiliser le script d’entrée
ENTRYPOINT ["/entrypoint.sh"]

# Définir le répertoire de travail
WORKDIR /var/www/html
