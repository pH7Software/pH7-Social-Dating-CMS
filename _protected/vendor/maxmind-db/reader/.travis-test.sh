#!/bin/sh

set -e
set -x

mkdir -p build/logs
phpunit -c .coveralls-phpunit.xml.dist
if [ "hhvm" != "$(phpenv version-name)" ]
then
    phpcs --standard=PSR2 src/
    echo "mbstring.internal_encoding=utf-8" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
    echo "mbstring.func_overload = 7" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
    phpunit
    echo "extension = ext/modules/maxminddb.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
    phpunit
fi
