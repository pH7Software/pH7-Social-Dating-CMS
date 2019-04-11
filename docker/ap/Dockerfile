# Set the wanted PHP versions
ARG PHP_VERSION=7.3

FROM php:${PHP_VERSION}-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    apt-utils \
    bzip2 \
    curl \
    exiftool \
    git \
    less \
    libbz2-dev \
    libc-client2007e \
    libc-client2007e-dev \
    libcurl4-openssl-dev \
    libfcgi-dev \
    libfcgi0ldbl \
    libfontconfig1 \
    libfontconfig1-dev \
    libfreetype6-dev \
    libjpeg-dev \
    libjpeg62-turbo-dev \
    libkrb5-dev \
    libmcrypt-dev \
    libpng-dev \
    libpq-dev \
    libssl-dev \
    libxml2-dev \
    libxslt1-dev \
    libzip-dev \
    locales \
    mysql-client \
    sudo \
    unzip \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

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

# Install PHP extensions!
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN echo "LC_ALL=en_US.UTF-8" >> /etc/environment
RUN echo "en_US.UTF-8 UTF-8" >> /etc/locale.gen
RUN echo "LANG=en_US.UTF-8" > /etc/locale.conf
RUN locale-gen en_US.UTF-8

RUN useradd -m -s /bin/bash ap
# When need login as root, exec "docker exec -u 0 -it {container} bash"
USER ap
