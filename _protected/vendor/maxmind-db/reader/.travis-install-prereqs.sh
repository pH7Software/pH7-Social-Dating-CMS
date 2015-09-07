#!/bin/sh

set -e
set -x

git submodule update --init --recursive
composer self-update
composer install --dev -n --prefer-source
if [ "hhvm" != "$(phpenv version-name)" ]
then
  git clone --recursive git://github.com/maxmind/libmaxminddb
  cd libmaxminddb
  ./bootstrap
  ./configure
  make
  sudo make install
  sudo ldconfig
  pyrus install pear/PHP_CodeSniffer
fi
