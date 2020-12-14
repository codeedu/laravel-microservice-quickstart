#!/bin/bash


##FORNT-END
cd /var/www/frontend && npm install && cd ..

###BACK-END
#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
cd backend
chown -R www-data:www-data .
if [ ! -f ".env" ]; then
    cp .env.example .env
fi
if [ ! -f ".env.testing" ]; then
    cp .env.testing.example .env.testing
fi
composer install
php artisan key:generate
php artisan migrate

php-fpm
