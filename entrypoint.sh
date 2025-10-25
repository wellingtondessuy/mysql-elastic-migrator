#!/bin/sh
set -e

echo "Waiting database connection $DB_HOST:$DB_PORT..."

until php -r "try { new PDO(\"mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_DATABASE\", \"$DB_USERNAME\", \"$DB_PASSWORD\"); exit(0); } catch (Exception \$e) { exit(1); }"
do
  echo "Connection failed. Retry after 3 seconds..."
  sleep 3
done

composer install

exec php-fpm
