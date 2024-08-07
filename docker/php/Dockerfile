FROM php:8.3.9-cli

WORKDIR /app

RUN apt-get update
RUN apt-get install -y git zip unzip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
