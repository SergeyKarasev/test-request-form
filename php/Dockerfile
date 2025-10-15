FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
  && docker-php-ext-install mysqli \
  && docker-php-ext-install pdo pdo_mysql \
