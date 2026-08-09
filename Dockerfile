# syntax=docker/dockerfile:1

ARG PHP_VERSION=8.2

FROM composer:2 AS composer

FROM php:${PHP_VERSION}-fpm-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libxml2-dev \
        libonig-dev \
        libpng-dev \
        libwebp-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" curl dom exif gd mbstring pdo_mysql simplexml xml xmlwriter zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/ph7builder-uploads.ini

WORKDIR /var/www

COPY --chown=www-data:www-data . /var/www

RUN composer install --no-interaction --no-progress --prefer-dist \
    && php -r 'foreach (["curl", "dom", "exif", "fileinfo", "gd", "iconv", "mbstring", "openssl", "pdo_mysql", "simplexml", "xml", "xmlwriter", "zip", "zlib"] as $extension) { if (!extension_loaded($extension)) { fwrite(STDERR, "Missing PHP extension: {$extension}\n"); exit(1); } }' \
    && php -r '$gd = gd_info(); if (empty($gd["FreeType Support"]) || empty($gd["WebP Support"])) { fwrite(STDERR, "GD requires FreeType and WebP support.\n"); exit(1); }' \
    && find /var/www -type d -exec chmod 0755 {} + \
    && find /var/www -type f -exec chmod 0644 {} + \
    && chmod 0775 /var/www \
    && find /var/www/_install -type d -exec chmod 0775 {} + \
    && find /var/www/_install/data/caches /var/www/_install/data/logs /var/www/_protected/app/configs /var/www/_protected/data/backup /var/www/_protected/data/cache /var/www/_protected/data/log /var/www/_protected/data/tmp /var/www/data /var/www/_repository/module -type d -exec chmod 0775 {} + \
    && find /var/www/_install/data/caches /var/www/_install/data/logs /var/www/_protected/app/configs /var/www/_protected/data/backup /var/www/_protected/data/cache /var/www/_protected/data/log /var/www/_protected/data/tmp /var/www/data /var/www/_repository/module -type f -exec chmod 0664 {} + \
    && chmod 0664 /var/www/_protected/app/system/modules/affiliate/config/config.ini /var/www/_protected/app/system/modules/payment/config/config.ini /var/www/_protected/app/system/modules/sms-verification/config/config.ini /var/www/_protected/app/system/modules/video/config/config.ini \
    && chown -R www-data:www-data /var/www \
    && chmod 2775 /var/www/_install/data/caches
