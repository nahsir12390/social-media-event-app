#!/bin/sh
set -eu
: "${APP_KEY:?Set a permanent APP_KEY before starting the service}"
: "${DB_DATABASE:=/var/data/database.sqlite}"
export DB_DATABASE
export APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-http://localhost:10000}}"
mkdir -p "$(dirname "$DB_DATABASE")" /var/data/public storage/app storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
[ -f "$DB_DATABASE" ] || touch "$DB_DATABASE"
# Uploads and SQLite share Render's persistent disk.
rm -rf storage/app/public
ln -s /var/data/public storage/app/public
chown -R www-data:www-data /var/data storage bootstrap/cache
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link --force
php artisan config:cache
php artisan view:cache
sed -i "s/Listen 80/Listen ${PORT:-10000}/" /etc/apache2/ports.conf
sed -i "s/\*:10000/*:${PORT:-10000}/" /etc/apache2/sites-available/000-default.conf
exec apache2-foreground
