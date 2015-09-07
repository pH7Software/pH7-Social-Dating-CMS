#!/bin/sh

set -e
set -x

if [ "hhvm" != "$(phpenv version-name)" ]
then
  cd ext
  phpize
  ./configure --with-maxminddb --enable-maxminddb-debug
  make clean
  make
  NO_INTERACTION=1 make test
  cd ..
fi
