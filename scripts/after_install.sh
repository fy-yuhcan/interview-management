#!/bin/bash
# after_install.sh

echo "Running AfterInstall hook..."

sudo chown -R apache:apache /var/www/html/interview-management
sudo chmod -R 755 /var/www/html/interview-management

set -e
