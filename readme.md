# Setup a Laravel Project With Sqlite

## Install Composer Dependencies

following command:

    composer install

## Create .env file and copy .env.example file into .env file

following command:

    cp .env.example .env

## Setup Database 

Change confi database in **config/database.php** file

        'default' => env('DB_CONNECTION', 'mysql'),
    to

        'default' => env('DB_CONNECTION', 'sqlite'),

Create a new sqlite using a command:

    touch database/database.sqlite

Check path database using a command:

    php artisan tinker
    >>> database_path('database.sqlite')
    => "absolute/path/to/database.sqlite"       //copy

Open **.env** file setup database

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=homestead
    DB_USERNAME=homestead
    DB_PASSWORD=secret
    
to

    DB_CONNECTION=sqlite
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=absolute/path/to/database.sqlite
    DB_USERNAME=homestead
    DB_PASSWORD=secret

## Migrate the database

following command:

    php artisan migrate

## Start server

following command:

    php artisan serve
