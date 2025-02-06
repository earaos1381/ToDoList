#!/bin/bash

# Aplicar permisos
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Esperar a que la base de datos esté lista
echo "Esperando a que la base de datos esté disponible..."
until nc -z -v -w30 db 3306; do
  echo "Esperando la base de datos..."
  sleep 5
done

# Ejecutar migraciones y seeding
php artisan migrate --force
php artisan db:seed --force

# Iniciar Apache
exec apache2-foreground
