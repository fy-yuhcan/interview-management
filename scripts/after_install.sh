#!/bin/bash
# after_install.sh

echo "Running AfterInstall hook..."

#環境変数ファイルの準備
# ENV_PROD="/home/ec2-user/env.production"

DB_HOST=$(aws ssm get-parameter --name "/interview-management/DB_HOST" --query "Parameter.Value" --output text)
DB_USER=$(aws ssm get-parameter --name "/interview-management/DB_USER_NAME" --query "Parameter.Value" --output text)
DB_PASS=$(aws ssm get-parameter --name "/interview-management/DB_PASS" --query "Parameter.Value" --output text)

ENV="/var/www/html/interview-management/.env"

# if [ -f "$ENV_PROD" ]; then
#   cp "$ENV_PROD" "$ENV"
#   echo ".env.production copied successfully."
# else
#   echo "ERROR: .env.production does not exist!" >&2
#   exit 1
# fi

echo "updateing .env file..."

cat <<EOF > ${ENV}
DB_HOST=${DB_HOST}
DB_USER=${DB_USER}
DB_PASS=${DB_PASS}
EOF

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

