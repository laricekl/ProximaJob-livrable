#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

restore_application() {
  php artisan up >/dev/null 2>&1 || true
}
trap restore_application EXIT

php artisan down --retry=60

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci --audit=false
npm run build

php artisan migrate --force
php artisan storage:link --force
php artisan optimize
php artisan queue:restart
php artisan production:check --strict

trap - EXIT
php artisan up

echo "Déploiement ProximaJob terminé avec succès."
