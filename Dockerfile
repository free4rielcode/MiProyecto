FROM php:8.2-cli
WORKDIR /app
COPY . .
RUN apt-get update && apt-get install -y git unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader
CMD php -S 0.0.0.0:${PORT} -t public
