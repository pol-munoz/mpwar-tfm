FROM php:8.0.9-fpm-alpine3.14

COPY ./ops/php-fpm/uploads.ini /usr/local/etc/php/conf.d

RUN apk update && docker-php-ext-install bcmath pdo_mysql

RUN apk add --no-cache curl git unzip \
    && curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/bin --filename=composer
