## Cloud Agent base image for the Jev Laravel test app.
## PHP 8.5 is not in Ubuntu 24.04's default repositories, so it is installed
## from the well-known ondrej/php PPA. Node 22 comes from NodeSource (Ubuntu's
## default nodejs is too old for Vite 8), and Composer is installed globally.
FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        gnupg \
        unzip \
        software-properties-common \
    && add-apt-repository -y ppa:ondrej/php \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        php8.5-cli \
        php8.5-curl \
        php8.5-mbstring \
        php8.5-xml \
        php8.5-zip \
        php8.5-sqlite3 \
        php8.5-bcmath \
        php8.5-intl \
        nodejs \
    && curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

USER ubuntu
