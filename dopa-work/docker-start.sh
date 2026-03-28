#!/bin/bash
set -e

# Clear ALL caches so no stale compiled views or config from old Docker layers
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run migrations + seed on first deploy
php artisan migrate --force
php artisan db:seed --force

# Create storage symlink (ignore if already exists)
php artisan storage:link || true

# Start Apache
apache2-foreground
