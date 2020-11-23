FROM php:7.4-cli-alpine

COPY . /app
WORKDIR /app

RUN apk add --no-cache git

RUN php -r "copy('https://getcomposer.org/download/2.0.7/composer.phar', 'composer.phar');" \
 && chmod +x ./composer.phar \
 && ./composer.phar install

ENTRYPOINT ["/app/entrypoint.sh"]
