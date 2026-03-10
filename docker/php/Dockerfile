FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/docker-symfony-entrypoint
RUN chmod +x /usr/local/bin/docker-symfony-entrypoint

ENTRYPOINT ["docker-symfony-entrypoint"]
CMD ["php-fpm"]
