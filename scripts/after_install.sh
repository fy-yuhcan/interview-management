#!/bin/bash
# after_install.sh

echo "Running AfterInstall hook..."

#環境変数ファイルの準備
ENV_PROD="/var/www/html/interview-management/.env.production"
ENV="/var/www/html/interview-management/.env"

if [ -f "$ENV_PROD" ]; then
  cp "$ENV_PROD" "$ENV"
  echo ".env.production copied successfully."
else
  echo "ERROR: .env.production does not exist!" >&2
  exit 1
fi

#Composer依存関係のインストール
cd /var/www/html/interview-management
composer install --no-interaction --prefer-dist --optimize-autoloader

#設定の自動化
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

#パーミッション調整
chown -R apache:apache /var/www/html/interview-management
chmod -R 755 /var/www/html/interview-management
chmod -R 775 /var/www/html/interview-management/storage
chmod -R 775 /var/www/html/interview-management/bootstrap/cache

set -e

