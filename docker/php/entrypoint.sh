#!/bin/sh
set -e

APP_DIR="${APP_DIR:-/var/www/html}"
VAR_DIR="$APP_DIR/var"
UPLOAD_DIR="$APP_DIR/public/uploads"

if [ -d "$VAR_DIR" ]; then
  mkdir -p "$VAR_DIR/cache" "$VAR_DIR/log"
  chown -R www-data:www-data "$VAR_DIR/cache" "$VAR_DIR/log"
  chmod -R ug+rwX "$VAR_DIR/cache" "$VAR_DIR/log"
fi

mkdir -p "$UPLOAD_DIR/media" "$UPLOAD_DIR/profils" "$UPLOAD_DIR/chants"
chown -R www-data:www-data "$UPLOAD_DIR"
chmod -R ug+rwX "$UPLOAD_DIR"

exec docker-php-entrypoint "$@"
