#!/bin/sh
set -e

echo "Clearing cache..."
php bin/console cache:clear --env=prod || true
php bin/console cache:warmup --env=prod || true

echo "Building npm assets with Encore..."
npm run build || true

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true

echo "Starting PHP server on port ${PORT:-8000}..."
php -S 0.0.0.0:${PORT:-8000} -t public
