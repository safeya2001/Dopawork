#!/bin/bash
set -e

# Clear any cached config that may have baked-in wrong DB settings
php artisan config:clear
php artisan cache:clear

# Run migrations with env vars from Render (no cache = reads live env vars)
php artisan migrate --force

# Create storage symlink (ignore if already exists)
php artisan storage:link || true

# Start Apache
apache2-foreground
