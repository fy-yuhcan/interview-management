#!/bin/bash
# after_install.sh

echo "Running AfterInstall hook..."

cp /var/www/html/interview-management/.env.production /var/www/html/interview-management/.env

chown -R apache:apache /var/www/html/interview-management
chmod -R 755 /var/www/html/interview-management

chmod -R 775 /var/www/html/interview-management/storage
chmod -R 775 /var/www/html/interview-management/bootstrap/cache

set -e
