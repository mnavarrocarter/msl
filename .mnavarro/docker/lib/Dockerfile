FROM alpine:edge as base

ARG UID=1000
ARG USER=mnavarro

# Add User
RUN adduser -h /$USER -D -u $UID $USER

FROM base as runtime

# Install PHP and extensions
RUN apk add --no-cache php81 \
    php81-curl \
    php81-phar \
    php81-json \
    php81-zip \
    php81-xml \
    php81-xmlreader \
    php81-xmlwriter \
    php81-sockets \
    php81-session \
    php81-bcmath \
    php81-fileinfo \
    php81-simplexml \
    php81-tokenizer \
    php81-iconv \
    php81-sodium \
    php81-mbstring \
    php81-openssl \
    php81-pcntl \
    php81-posix

# Link PHP
RUN ln -s /usr/bin/php81 /usr/bin/php

FROM runtime as composer

ARG COMPOSER_VERSION=2.1.9

# We need to install composer
RUN apk add --no-cache wget && wget -O /usr/bin/composer https://github.com/composer/composer/releases/download/$COMPOSER_VERSION/composer.phar && \
    chmod +x /usr/bin/composer

FROM composer as dev

# We need to install and configure XDebug for development
RUN apk add --no-cache php81-pecl-xdebug
COPY ./lib/xdebug.ini /etc/php81/conf.d/99_xdebug.ini