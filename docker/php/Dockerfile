FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    && pecl install redis \
    && docker-php-ext-enable redis

WORKDIR /var/www
ENTRYPOINT []
CMD ["bash"]
