FROM php:7.4-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN apt-get update \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -i 's#AllowOverride None#AllowOverride All#' /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && apt-get install -y librabbitmq-dev \
    && printf "\n" | pecl install amqp \
    && docker-php-ext-configure pdo_mysql \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && docker-php-ext-enable amqp