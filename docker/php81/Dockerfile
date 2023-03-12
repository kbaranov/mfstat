FROM php:8.1-fpm

RUN apt-get update && apt-get install -y git curl zip unzip vim
RUN apt-get update

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/app

EXPOSE 9000
CMD ["php-fpm"]