#!/bin/bash

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
until php -r "try { new PDO('mysql:host=mysql;dbname=phone_store', 'phone_store', 'phone_store_password'); echo 'Connected to MySQL successfully!'; } catch (PDOException \$e) { echo \$e->getMessage(); exit(1); }"
do
  echo "MySQL is unavailable - sleeping"
  sleep 1
done

echo "MySQL is up - executing migrations"

# Run database migrations
cd /var/www/html
php migrations/run.php

# Start PHP-FPM
php-fpm 