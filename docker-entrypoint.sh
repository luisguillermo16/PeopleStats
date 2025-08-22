#!/bin/bash
set -e

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

exec apache2-foreground