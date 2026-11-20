#!/bin/sh
set -eu

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY must be set in the Render environment."
    exit 1
fi

if [ -z "${POS_ADMIN_USERNAME:-}" ] || [ -z "${POS_ADMIN_PASSWORD:-}" ]; then
    echo "POS_ADMIN_USERNAME and POS_ADMIN_PASSWORD must be set for the initial staff account."
    exit 1
fi

if [ "${IMAGE_STORAGE_DRIVER:-imagekit}" = "imagekit" ] && [ -z "${IMAGEKIT_PRIVATE_KEY:-}" ]; then
    echo "IMAGEKIT_PRIVATE_KEY must be set when IMAGE_STORAGE_DRIVER=imagekit."
    exit 1
fi

port="${PORT:-10000}"
sed -i "s/Listen 80/Listen ${port}/" /etc/apache2/ports.conf
sed -i "s/\*:10000/*:${port}/g" /etc/apache2/sites-available/000-default.conf

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

php artisan config:cache
php artisan migrate --force
php artisan db:seed --force

exec apache2-foreground
