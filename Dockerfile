# 1) Imagen oficial de PHP 8.2 con FPM
FROM php:8.2-fpm

# 2) Instala librerías del SO y extensión PDO para PostgreSQL
RUN apt-get update \
 && apt-get install -y git zip unzip libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql

# 3) Copia Composer desde su imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4) Define el directorio de trabajo
WORKDIR /var/www/html

# 5) Copia todo el proyecto y ejecuta Composer
COPY . .
RUN composer install --no-dev --optimize-autoloader

# 6) Genera la APP_KEY
RUN php artisan key:generate --ansi

# Expón el puerto que Render te asigna (lleva en la variable $PORT)
EXPOSE 8000

# Al arrancar, usa artisan serve en el puerto que Render exporta
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000