#!/bin/bash
# start.sh

echo "Running ApplicationStart hook..."

sudo systemctl start nginx
sudo systemctl start php-fpm

set -e
