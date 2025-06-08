# 1. Usa la imagen oficial de PHP-FPM
FROM php:8.1-fpm

# 2. Instala dependencias de sistema y extensiones para Postgres
RUN apt-get update \
 && apt-get install -y git zip unzip libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql

# 3. Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Define el directorio de trabajo
WORKDIR /var/www/html

# 5. Copia los archivos y instala dependencias de PHP
COPY . .
RUN composer install --no-dev --optimize-autoloader

# 6. Genera APP_KEY (opcional aquí, pero puedes hacerlo en tu CI/CD)
# RUN php artisan key:generate

# 7. Expón el puerto 9000 para PHP-FPM
EXPOSE 9000

# 8. Comando por defecto al iniciar el contenedor
CMD ["php-fpm"]
