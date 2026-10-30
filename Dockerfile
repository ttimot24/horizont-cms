FROM php:8.4-apache AS builder

WORKDIR /var/www/html

COPY ./ /var/www/html

RUN rm -rf /var/www/html/plugins/*

RUN apt-get update && \
    apt-get install -y git zip cron	npm nodejs libzip-dev vim

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Apache configuration
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions zip pdo_mysql pdo_pgsql pdo_sqlite &&  \
    a2enmod rewrite && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Composer install
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');" && \
    chown -R www-data /var/www/html && \
    composer install

# NPM install
RUN npm install --global cross-env && \ 
    npm install --global webpack && \
    npm ci --force && npm run build && \
    rm -rf /var/www/html/node_modules

RUN chmod -R 775 /var/www/html && \
    chmod -R 775 /var/www/html/storage

## --- Production image ---

FROM php:8.4-apache

WORKDIR /var/www/html

COPY --from=builder /usr/local/bin/ /usr/local/bin/

RUN install-php-extensions zip pdo_mysql pdo_pgsql pdo_sqlite intl gd &&  \
    a2enmod rewrite && \
    adduser --disabled-password --gecos "" appuser && \
    usermod -aG sudo appuser && \
    apt-get update && \
    apt-get install -y zip cron	vim

COPY --from=builder --chown=appuser /var/www/html /var/www/html
COPY --from=builder --chown=appuser /etc/apache2/apache2.conf /etc/apache2/apache2.conf

RUN echo "error_log = /dev/stderr" >> /usr/local/etc/php/php.ini

# Apache's master process runs as root (it has to bind port 80 and own its pid
# and log directories) while its workers run as `appuser`, so files created by
# the application keep `appuser` ownership.
ENV APACHE_RUN_USER=appuser \
    APACHE_RUN_GROUP=appuser

# Laravel's scheduler runs from cron as `appuser`: cron itself needs root to
# read the crontab spool and to drop privileges for the job. The explicit PATH
# is needed because cron otherwise falls back to /usr/bin:/bin, where php is not
# present.
RUN (crontab -u appuser -l 2>/dev/null; \
        echo "PATH=/usr/local/bin:/usr/local/sbin:/usr/sbin:/usr/bin:/sbin:/bin"; \
        echo "* * * * * cd /var/www/html && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1") \
    | crontab -u appuser -

# There is deliberately no USER directive: the container has to start as root,
# because `printenv > /etc/environment` (the cron jobs read that file through
# pam_env, which is how they receive DB_* / APP_*) and the cron daemon both
# require root. Apache drops to `appuser` for its workers via APACHE_RUN_USER.
CMD ["sh", "-c", "printenv > /etc/environment && cron && exec apache2-foreground"]

EXPOSE 80
