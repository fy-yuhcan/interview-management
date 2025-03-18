#!/bin/bash
# before_install.sh

echo "Running BeforeInstall hook..."
sudo systemctl stop nginx
sudo systemctl stop php-fpm

sudo rm -rf /var/www/html/interview-management/*

set -e
