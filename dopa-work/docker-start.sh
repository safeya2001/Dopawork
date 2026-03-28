#!/bin/bash
set -e

# Cache config and routes for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Create storage symlink (ignore if already exists)
php artisan storage:link || true

# Start Apache
apache2-foreground
