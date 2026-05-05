#!/usr/bin/env bash
set -e

echo "Building MechFinder..."

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Generate app key
php artisan key:generate --force

# Install Node dependencies
npm install

# Build assets
npm run build

# Clear caches
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize

echo "Build complete!"
