#!/bin/sh
set -e

echo "Waiting database connection $DB_HOST:$DB_PORT..."

# O loop 'until' continuará rodando o comando 'php -r ...'
# A cada 5 segundos, até que o comando saia com sucesso (exit code 0).
until php -r "try { new PDO(\"mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_DATABASE\", \"$DB_USERNAME\", \"$DB_PASSWORD\"); exit(0); } catch (Exception \$e) { exit(1); }"
do
  echo "Connection failed. Retry after 5 seconds..."
  sleep 5
done

composer install

exec php-fpm
