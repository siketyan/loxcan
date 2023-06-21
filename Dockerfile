FROM php:8.2-cli

COPY . /app
WORKDIR /app

RUN apt update && apt install -y git zip unzip

COPY --from=composer:2.5.5 /usr/bin/composer /usr/bin/composer
RUN composer install -n --no-dev

ENTRYPOINT ["/app/entrypoint.sh"]
