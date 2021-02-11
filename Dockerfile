FROM php:7.2-fpm

RUN addgroup --gid 1000 --system app
RUN adduser --uid 1000 --system --disabled-login --disabled-password --gid 1000 app

COPY ./configs/www.conf /usr/local/etc/php-fpm.d/www.conf
RUN rm /usr/local/etc/php-fpm.d/zz-docker.conf

COPY ./configs/php.ini /usr/local/etc/php

RUN apt-get update && apt-get install -y \
    #build-essential \
    #libpng-dev \
    #libjpeg62-turbo-dev \
    libfreetype6-dev \
    #locales \
    zip \
    #jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    nano

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql \
    zip \
    exif \
    pcntl

#RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
#RUN docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR  /var/www/track/public

COPY . .

RUN cd /var/www/track/public/app && composer update && composer install