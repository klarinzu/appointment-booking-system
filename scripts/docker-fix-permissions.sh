#!/bin/sh

set -eu

APP_ROOT="/var/www/html"
WRITABLE_DIRS="
$APP_ROOT/storage/logs
$APP_ROOT/storage/framework
$APP_ROOT/bootstrap/cache
"

mkdir -p \
    "$APP_ROOT/storage/logs" \
    "$APP_ROOT/storage/framework/cache/data" \
    "$APP_ROOT/storage/framework/sessions" \
    "$APP_ROOT/storage/framework/views" \
    "$APP_ROOT/bootstrap/cache"

touch "$APP_ROOT/storage/logs/laravel.log"

for path in $WRITABLE_DIRS; do
    if [ -d "$path" ]; then
        find "$path" -type d -exec chmod 0777 {} +
        find "$path" -type f -exec chmod 0666 {} +
    fi
done
