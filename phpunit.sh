#!/usr/bin/env bash

set -eu

PHP_VERSION=7.2

if ! type docker > /dev/null; then
    echo "Docker is required to run tests."
    exit 1
fi

if [ ! -d "vendor" ]; then
    echo "# No vendor dir detected, installing dependencies first then"
    docker run \
        -it \
        --rm \
        -w /mnt/tmp \
        -v `pwd`:/mnt/tmp \
        -e DEBIAN_FRONTEND=noninteractive \
        -e COMPOSER_MEMORY_LIMIT=-1 \
        -e COMPOSER_INSTALLER=https://getcomposer.org/installer \
        php:${PHP_VERSION} sh -c '\
            apt-get update && \
            apt-get install -yq \
                git \
                unzip \
            && \
            curl -sS ${COMPOSER_INSTALLER} | php -- --quiet --install-dir=/usr/local/bin --filename=composer && \
            composer install \
        '
fi

echo ""

docker run \
    -it \
    --rm \
    -v `pwd`:/mnt/tmp \
    -w /mnt/tmp \
    php:${PHP_VERSION} sh -c '\
        vendor/bin/phpunit \
    '

exit_code=$?

exit $exit_code
