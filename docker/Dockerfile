FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite \
    && sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
