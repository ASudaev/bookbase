#!/bin/bash

#Go to project dir
cd /var/www/bookbase

#Install Composer dependencies
composer install

#Sleep for 180 seconds (waiting for MySQL initialization)
sleep 180

#Apply database migrations
php bin/console doctrine:migrations:migrate -n

#Load sample data for working environment
php bin/console doctrine:fixtures:load --group=dev -n

#Apply database migrations for test database
APP_ENV=test php bin/console doctrine:migrations:migrate -n

#Load sample data for test environment
php bin/console doctrine:fixtures:load --group=test --env=test -n

#Run unit tests
APP_ENV=test symfony php bin/phpunit
