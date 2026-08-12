#!/usr/bin/env bash
# Ativa MySQL no .env e roda migrations.
# Pré-requisito (rode no terminal com sua senha sudo):
#   sudo mysql < database/mysql-init.sql

set -euo pipefail
cd "$(dirname "$0")/.."

if ! mysql -u tarzi -p'Tarzi_Signage_2026!' -h 127.0.0.1 -P 3306 tarzi_signage -e "SELECT 1" >/dev/null 2>&1; then
  echo "Erro: usuário tarzi ainda não existe no MySQL."
  echo "Execute primeiro: sudo mysql < database/mysql-init.sql"
  exit 1
fi

sed -i 's/^DB_CONNECTION=sqlite/# DB_CONNECTION=sqlite/' .env
sed -i 's/^# DB_CONNECTION=mysql/DB_CONNECTION=mysql/' .env
sed -i 's/^# DB_HOST=/DB_HOST=/' .env
sed -i 's/^# DB_PORT=/DB_PORT=/' .env
sed -i 's/^# DB_DATABASE=/DB_DATABASE=/' .env
sed -i 's/^# DB_USERNAME=/DB_USERNAME=/' .env
sed -i 's/^# DB_PASSWORD=/DB_PASSWORD=/' .env

php artisan config:clear
php artisan migrate:fresh --seed --force

echo ""
echo "MySQL ativo!"
echo "Admin:      /admin/login       → admin@zeivoll.com.br / password"
echo "Anunciante: /anunciante/login  → anunciante@tarzi.com.br / password"
