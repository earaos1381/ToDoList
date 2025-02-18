# 1. Imagen base con PHP y Apache
FROM php:8.2-apache

# 2. Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libonig-dev libxml2-dev nodejs npm \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql mbstring exif pcntl bcmath gd

# 3. Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# 4. Configurar el sitio web
COPY docker-apache.conf /etc/apache2/sites-available/000-default.conf

# 5. Configurar directorio de trabajo
RUN git config --global --add safe.directory /var/www/html

# 6. Copiar los archivos de Laravel al contenedor
COPY . /var/www/html

# 7. Instalar Composer y dependencias de Laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 8. Configurar el usuario de Apache
ENV COMPOSER_ALLOW_SUPERUSER=1

# 9. Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# 10. Posicionar directorio de trabajo
WORKDIR /var/www/html

# 11. Instalar dependencias de Vue y compilar assets
RUN npm install && npm run build

# 12. Dar permisos a storage y bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# 13. Limpiar y optimizar la aplicación de Laravel
RUN php artisan optimize:clear
RUN php artisan view:cache
RUN php artisan config:cache

# 14. Copiar archivos de base de datos
#COPY ./bd.sql /docker-entrypoint-initdb.d/

# 15. Ejecutar el script SQL para poblar la base de datos
#RUN /usr/bin/mysql -u root -p${root} < /docker-entrypoint-initdb.d/bd.sql

# 16. Exponer el puerto 80
EXPOSE 80

# 17. Comando de inicio
CMD ["apache2-foreground"]
