#!/bin/bash
# before_install.sh

echo "Running BeforeInstall hook..."
sudo systemctl stop nginx
sudo systemctl stop php-fpm
sudo rm -rf /var/www/html/interview-management/docker-compose.yml
sudo rm -rf /var/www/html/interview-management/composer.lock
sudo rm -rf /var/www/html/interview-management/artisan
sudo rm -rf /var/www/html/interview-management/tailwind.config.js
sudo rm -rf /var/www/html/interview-management/package.json
sudo rm -rf /var/www/html/interview-management/package-lock.json
sudo rm -rf /var/www/html/interview-management/.editorconfig
sudo rm -rf /var/www/html/interview-management/README.md

set -e
