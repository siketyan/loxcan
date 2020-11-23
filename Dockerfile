FROM php:7.4-cli-alpine

COPY . /app

RUN php -r "copy('https://getcomposer.org/download/2.0.7/composer.phar', 'composer.phar');" \
 && ./composer.phar install

ENTRYPOINT ["/app/entrypoint.sh"]
