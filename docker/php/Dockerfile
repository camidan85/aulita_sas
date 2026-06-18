# AULITA — imagen PHP 8.4 (FPM) para producción
FROM php:8.4-fpm

# Dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip zip supervisor libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Config de PHP para producción
COPY docker/php/php.ini /usr/local/etc/php/conf.d/aulita.ini

# Código (en CI/CD; en local se monta por volumen)
COPY . /var/www

RUN mkdir -p /var/log/supervisor \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
