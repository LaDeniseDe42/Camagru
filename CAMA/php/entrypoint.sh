#!/bin/bash

cat > /etc/msmtprc <<EOF
defaults
auth on
tls on
tls_starttls on
tls_trust_file /etc/ssl/certs/ca-certificates.crt
logfile /var/log/msmtp.log

account gmail
host smtp.gmail.com
port 587
from ${SMTP_EMAIL}
user ${SMTP_USER}
password ${SMTP_PASSWORD}

account default : gmail
EOF

chmod 600 /etc/msmtprc
chown www-data:www-data /etc/msmtprc

# Lancer PHP-FPM
exec docker-php-entrypoint php-fpm
