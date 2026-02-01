FROM php:8.2-fpm-alpine AS php-base

WORKDIR /app

# Install system dependencies
RUN apk add --no-cache \
    bash \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    postgresql-client \
    postgresql-dev \
    libzip-dev \
    oniguruma-dev

# Install PHP core extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    pdo \
    zip

# Install PHP GD extension with specific config
RUN docker-php-ext-configure gd \
        --with-freetype=/usr/include \
        --with-jpeg=/usr/include \
    && docker-php-ext-install gd

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js
RUN apk add --no-cache nodejs npm

# Copy composer files first
COPY --chown=www-data:www-data composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-scripts --no-interaction --prefer-dist

# Copy package files for npm
COPY --chown=www-data:www-data package*.json ./

# Install npm dependencies
RUN npm install --no-fund --legacy-peer-deps

# Copy Laravel source files with chown to normalize permissions
COPY --chown=www-data:www-data app ./app
COPY --chown=www-data:www-data bootstrap ./bootstrap
COPY --chown=www-data:www-data config ./config
COPY --chown=www-data:www-data database ./database
COPY --chown=www-data:www-data public ./public
COPY --chown=www-data:www-data resources ./resources
COPY --chown=www-data:www-data routes ./routes
COPY --chown=www-data:www-data artisan ./
COPY --chown=www-data:www-data .env.example ./

# Copy docker setup script
COPY --chown=www-data:www-data docker/setup.sh /app/docker/setup.sh
RUN chmod +x /app/docker/setup.sh

# Create storage directories
RUN mkdir -p /app/storage/logs /app/storage/app/private /app/storage/app/public \
    && mkdir -p /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views

# Build frontend assets
RUN npm run build

# Run composer scripts
RUN composer run-script post-autoload-dump

# Fix permissions for all app directories
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage \
    && chmod -R 755 /app/bootstrap/cache

FROM php-base AS app

EXPOSE 8000
ENTRYPOINT ["/bin/bash"]
CMD ["/app/docker/setup.sh"]

FROM php-base AS supervisor

RUN apk add --no-cache supervisor && mkdir -p /var/log/supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/supervisor/conf.d/*.conf /etc/supervisor/conf.d/
USER root
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

FROM node:16-alpine AS vite

WORKDIR /app
COPY package*.json ./
COPY postcss.config.js tailwind.config.js vite.config.js ./
COPY resources ./resources
RUN npm install --no-fund --legacy-peer-deps
EXPOSE 5173
ENV VITE_HOST=0.0.0.0
ENV VITE_PORT=5173
CMD ["npm", "run", "dev"]
