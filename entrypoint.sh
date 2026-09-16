#!/bin/sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate --force
fi

php artisan storage:link || true
php artisan migrate --force || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
