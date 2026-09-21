FROM composer:2 AS composer

FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring mysqli pdo_mysql xml zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY . /var/www/html
COPY docker/entrypoint.sh /usr/local/bin/zyacbt-entrypoint

RUN composer install \
    --working-dir=application \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress

RUN mkdir -p uploads application/cache application/logs public/uploads \
    && chown -R www-data:www-data uploads application/cache application/logs public/uploads \
    && chmod +x /usr/local/bin/zyacbt-entrypoint

ENTRYPOINT ["/usr/local/bin/zyacbt-entrypoint"]
CMD ["apache2-foreground"]
