#!/bin/sh

set -e
# Start
echo "Deploying application ..."

# Enter maintenance mode
(php artisan down --render=errors/503) || true

# Update codebase
git fetch origin deploy
git reset --hard origin/deploy

# Install dependencies based on lock file
composer install --no-interaction --prefer-dist --optimize-autoloader

# Migrate database
php artisan migrate --force

# Reload PHP to update opcache
# echo "" | sudo -S service php8.2-fpm reload

# Exit maintenance mode
php artisan optimize:clear
php artisan up

echo "Application deployed!"
# Complete!
