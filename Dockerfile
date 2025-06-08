# Usa una imagen oficial de PHP con extensiones necesarias
FROM php:8.1-fpm-slim

# Instala dependencias de sistema
RUN apt-get update && apt-get install -y \
    git zip unzip libpq-dev \
    && apt-get upgrade -y \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensiones de PHP para Postgres y JWT
RUN docker-php-ext-install pdo pdo_pgsql

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crea directorio de la aplicación
WORKDIR /var/www/html

# Copia archivos y instala dependencias de PHP
COPY . .
RUN composer install --no-dev --optimize-autoloader

# Genera key de Laravel y limpia cache (opcional)
RUN php artisan key:generate

# Exponer el puerto que usará PHP-FPM
EXPOSE 9000

# Arranca PHP-FPM
CMD ["php-fpm"]
