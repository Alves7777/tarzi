#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

MODE="${1:-native}"

if [[ "$MODE" == "docker" ]]; then
  echo ">> Subindo MySQL via Docker (porta 3307)..."
  docker compose up -d mysql

  echo ">> Aguardando MySQL..."
  for _ in {1..30}; do
    if docker compose exec -T mysql mysqladmin ping -h 127.0.0.1 -uroot -proot --silent 2>/dev/null; then
      break
    fi
    sleep 2
  done

  export DB_PORT=3307
fi

if [[ "$MODE" == "native" ]]; then
  echo ">> Criando banco e usuário no MySQL local (requer sudo)..."
  sudo mysql < database/mysql-init.sql
fi

php artisan config:clear

echo ">> Gerando permissões Filament Shield..."
php artisan shield:generate --all --panel=admin --option=policies_and_permissions --no-interaction

echo ">> Rodando migrations + seed..."
php artisan migrate:fresh --seed --force

echo ">> Sincronizando permissões do super_admin..."
php artisan db:seed --class=BaseAdminRolesSeeder --force
php artisan permission:cache-reset

echo ""
echo "Pronto!"
echo "Admin:      /admin/login       → admin@zeivoll.com.br / password"
echo "Anunciante: /anunciante/login  → anunciante@tarzi.com.br / password"
