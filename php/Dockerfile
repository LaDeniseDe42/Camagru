FROM php:8.2-fpm

# Installer sendmail et d'autres dépendances
RUN apt-get update && apt-get install -y sendmail msmtp

# Créer le dossier gallery si nécessaire et donner les permissions adéquates
RUN mkdir -p /var/www/html/gallery && \
    chown -R www-data:www-data /var/www/html/gallery && \
    chmod -R 775 /var/www/html/gallery

RUN chmod 1777 /tmp

# Créer le dossier gallery si nécessaire et donner les permissions adéquates
RUN mkdir -p /var/www/html/gallery && \
    chown -R www-data:www-data /var/www/html/gallery && \
    chmod -R 775 /var/www/html/gallery

# Assurez-vous que le dossier /tmp ait les bonnes permissions
RUN chmod 1777 /tmp


# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Configuration de sendmail (ou msmtp) pour envoyer des e-mails via un serveur SMTP
RUN echo "sendmail_path = '/usr/bin/msmtp -C /etc/msmtprc -t -i'" >> /usr/local/etc/php/conf.d/sendmail.ini
RUN echo "mail.log = /var/log/mail.log" >> /usr/local/etc/php/conf.d/sendmail.ini
# remplacer sendmail_path dans le fichier php.ini

# Configuration de msmtp pour utiliser le serveur SMTP d'Outlook
RUN echo "defaults" > /etc/msmtprc && \
    echo "auth on" >> /etc/msmtprc && \
    echo "tls on" >> /etc/msmtprc && \
    echo "tls_starttls on" >> /etc/msmtprc && \
    echo "tls_trust_file /etc/ssl/certs/ca-certificates.crt" >> /etc/msmtprc && \
    echo "logfile /var/log/msmtp.log" >> /etc/msmtprc &&\
    echo "account gmail" >> /etc/msmtprc && \
    echo "auth plain" >> /etc/msmtprc && \
    echo "host smtp.gmail.com" >> /etc/msmtprc && \
    echo "port 587" >> /etc/msmtprc && \
    echo "from camamailgru@gmail.com" >> /etc/msmtprc && \
    echo "user camamailgru" >> /etc/msmtprc && \
    echo "password cihn ntjz emhy tbzx" >> /etc/msmtprc && \
    echo "account default : gmail" >> /etc/msmtprc


RUN touch /var/log/mail.log && \
    chmod 666 /var/log/mail.log && \
    chown www-data:www-data /var/log/mail.log

# Assurez-vous que le fichier msmtprc a les bonnes permissions
RUN chmod 600 /etc/msmtprc
RUN chown www-data:www-data /etc/msmtprc

# Définir le répertoire de travail
WORKDIR /var/www/html
