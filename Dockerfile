FROM php:8.0-cli-alpine

COPY . /app
WORKDIR /app

RUN apk add --no-cache git

RUN php -r "copy('https://getcomposer.org/download/2.1.12/composer.phar', 'composer.phar');" \
 && chmod +x ./composer.phar \
 && ./composer.phar install

ENTRYPOINT ["/app/entrypoint.sh"]
