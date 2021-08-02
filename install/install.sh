#!/bin/bash

#Go to project dir
cd /var/www/bookbase

#Install Composer dependencies
composer install

#Sleep for 120 seconds (waiting for MySQL initialization)
sleep 120

#Apply database migrations
php bin/console doctrine:migrations:migrate
