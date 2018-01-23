FROM php:7.1-fpm

MAINTAINER Vasile Sculea <sculea.vasile@gmail.com>

RUN apt-get update && apt-get install -y \
        wget \
        zlib1g-dev \
        libmcrypt-dev \
    && docker-php-ext-install -j$(nproc) iconv mcrypt mbstring pdo_mysql \
    && docker-php-ext-install zip

RUN apt-get autoremove -y

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

COPY .docker/php/php.ini /usr/local/etc/php/php.ini

# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/UTC /etc/localtime
RUN "date"
