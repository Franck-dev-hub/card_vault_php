#!/bin/sh
set -e

echo "Waiting for PostgreSQL..."
while ! pg_isready -h postgres -p 5432 > /dev/null 2>&1; do
  sleep 1
done

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true

echo "Starting PHP server..."
php -S 0.0.0.0:8000 -t public
