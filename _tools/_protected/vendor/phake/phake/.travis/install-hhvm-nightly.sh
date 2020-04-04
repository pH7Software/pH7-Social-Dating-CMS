#!/usr/bin/env bash
set -e
set -o pipefail

if [ "$TRAVIS_PHP_VERSION" = "hhvm" ]; then

    # Install the nightly build of HHVM ...
    curl http://dl.hhvm.com/conf/hhvm.gpg.key | sudo apt-key add -
    echo deb http://dl.hhvm.com/ubuntu precise main | sudo tee /etc/apt/sources.list.d/hhvm.list
    sudo apt-get update
    sudo apt-get -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" install hhvm-nightly
    hhvm --version

    # Ensure the PHP symlink points to the nightly HHVM build ...
    PHP_PATH=$(which php)
    HHVM_PATH=$(which hhvm)
    sudo rm "$PHP_PATH"
    sudo ln -s "$HHVM_PATH" "$PHP_PATH"

    echo
    php --version
fi
