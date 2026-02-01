FROM php:8.2-cli-bookworm

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        bash \
        ca-certificates \
        curl \
        git \
        unzip \
        zip \
        libpq-dev \
        libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN set -e; \
    apt-get update; \
    apt-get install -y --no-install-recommends $PHPIZE_DEPS; \
    pecl install redis; \
    docker-php-ext-enable redis; \
    docker-php-ext-install pdo_pgsql zip pcntl; \
    apt-get purge -y --auto-remove $PHPIZE_DEPS; \
    rm -rf /var/lib/apt/lists/* /tmp/pear

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY setup.sh /usr/local/bin/laravel-setup
RUN chmod +x /usr/local/bin/laravel-setup

EXPOSE 8000
CMD ["/usr/local/bin/laravel-setup"]
