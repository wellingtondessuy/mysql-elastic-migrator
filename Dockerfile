FROM php:8.3-fpm-bullseye

# Install system dependencies
RUN apt-get update && apt-get install -y \
    cron \
    nano \
    git \
    curl \
    zip \
    unzip \
    libonig-dev \
    iputils-ping \
    libsodium-dev \
    libicu-dev \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring bcmath opcache sodium intl zip

COPY php.ini /usr/local/etc/php/php.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install

