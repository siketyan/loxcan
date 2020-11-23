FROM alpine:7.4-cli-alpine

COPY . /app

RUN php -r "copy('https://getcomposer.org/download/2.0.7/download.phar', 'composer.phar');" \
 && ./composer.phar install

ENTRYPOINT ["/app/entrypoint.sh"]
