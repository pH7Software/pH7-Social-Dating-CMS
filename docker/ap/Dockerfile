FROM php:7.2-fpm
RUN apt-get update && apt-get install -y exiftool \
	libfcgi-dev libfcgi0ldbl libjpeg62-turbo-dev libmcrypt-dev \
	libssl-dev libc-client2007e libc-client2007e-dev libxml2-dev \
	libbz2-dev libcurl4-openssl-dev libjpeg-dev libpng-dev \
	libfreetype6-dev libkrb5-dev libpq-dev libxml2-dev libxslt1-dev locales

RUN docker-php-ext-install bz2 && \
    docker-php-ext-configure gd \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install gd && \
    docker-php-ext-install iconv && \
    docker-php-ext-install opcache && \
    docker-php-ext-install pdo && \
		docker-php-ext-install exif && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install zip
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN mkdir -p _protected/vendor
ADD . _protected/vendor
RUN php -r "readfile('https://getcomposer.org/installer');" | php
RUN php composer.phar --working-dir=_protected/vendor install
