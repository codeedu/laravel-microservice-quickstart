#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
composer install

chown -R www-data:www-data storage

php artisan key:generate
php artisan migrate

php-fpm
