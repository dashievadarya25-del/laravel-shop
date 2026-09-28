FROM php:8.4-fpm

# Принимаем переменные UID/GID из docker-compose для настройки прав
ARG USER_ID=1000
ARG GROUP_ID=1000

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    zlib1g-dev \
    libicu-dev \
    libpq-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        intl \
        zip \
        pdo \
        pdo_pgsql \
        pgsql \
        xml \
        bcmath \
        mbstring \
        sockets \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Динамически меняем ID пользователя www-data на те, что переданы из вашей системы (.env)
RUN groupmod -o -g ${GROUP_ID} www-data \
    && usermod -o -u ${USER_ID} -g www-data www-data

WORKDIR /var/www/html

USER www-data
