FROM php:8.4-fpm

RUN apt-get update -y && apt-get install -y --no-install-recommends \
    git curl unzip zip bash \
    libicu-dev libonig-dev libzip-dev \
    default-mysql-client \
    nodejs npm \
 && docker-php-ext-install pdo pdo_mysql mbstring intl zip opcache \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN groupadd -g 1000 www && useradd -u 1000 -g www -m www
USER www

EXPOSE 9000 5173