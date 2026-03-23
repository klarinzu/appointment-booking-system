#!/usr/bin/env bash

set -euo pipefail

SOURCE_DIR="${1:-$(pwd)}"
TARGET_INPUT="${2:-~/projects/appointment-booking-system}"
shift $(( $# > 1 ? 2 : $# ))

SKIP_INSTALL=0
SKIP_MIGRATE=0
INSTALL_NODE=0

for arg in "$@"; do
    case "$arg" in
        --skip-install)
            SKIP_INSTALL=1
            ;;
        --skip-migrate)
            SKIP_MIGRATE=1
            ;;
        --install-node)
            INSTALL_NODE=1
            ;;
        *)
            echo "Unknown option: $arg" >&2
            exit 1
            ;;
    esac
done

expand_path() {
    case "$1" in
        "~")
            echo "$HOME"
            ;;
        "~/"*)
            echo "$HOME/${1#~/}"
            ;;
        *)
            echo "$1"
            ;;
    esac
}

TARGET_DIR="$(expand_path "$TARGET_INPUT")"

mkdir -p "$TARGET_DIR"

echo "Syncing project into WSL:"
echo "  from: $SOURCE_DIR"
echo "  to:   $TARGET_DIR"

rsync -a --delete \
    --exclude '.git/' \
    --exclude '.expo/' \
    --exclude '.idea/' \
    --exclude '.vscode/' \
    --exclude 'node_modules/' \
    --exclude 'vendor/' \
    --exclude 'storage/logs/' \
    --exclude 'storage/framework/cache/data/' \
    --exclude 'bootstrap/cache/*.php' \
    "$SOURCE_DIR"/ "$TARGET_DIR"/

if [[ -f "$SOURCE_DIR/.env" ]]; then
    cp "$SOURCE_DIR/.env" "$TARGET_DIR/.env"
fi

cd "$TARGET_DIR"

if [[ "$SKIP_INSTALL" -eq 0 ]]; then
    if [[ ! -f vendor/autoload.php ]]; then
        echo "Installing Composer dependencies inside WSL..."
        composer install --no-interaction --prefer-dist --no-scripts
    fi

    if [[ "$INSTALL_NODE" -eq 1 && ! -d node_modules ]]; then
        echo "Installing npm dependencies inside WSL..."
        npm install
    fi
fi

echo "Starting Docker services from WSL..."
docker compose up -d student-dashboard db pma

echo "Repairing Laravel writable permissions inside Docker..."
docker compose exec -T student-dashboard sh /var/www/html/scripts/docker-fix-permissions.sh

echo "Refreshing Laravel package discovery inside Docker..."
docker compose exec -T --user www-data student-dashboard php artisan package:discover --no-ansi

if [[ "$SKIP_MIGRATE" -eq 0 ]]; then
    echo "Running database migrations..."
    docker compose exec -T --user www-data student-dashboard php artisan migrate --force
fi

cat <<EOF

WSL copy is ready.
Project path: $TARGET_DIR
Student dashboard: http://127.0.0.1:8000
phpMyAdmin: http://127.0.0.1:8080

For faster next runs, open Ubuntu WSL and use:
  cd "$TARGET_DIR"
  docker compose up -d student-dashboard db pma

If you need frontend dev dependencies in WSL later, rerun with:
  --install-node
EOF
