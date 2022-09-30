<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Deploying on local machine using xampp

Requirements
- [Xampp](https://www.apachefriends.org/download.html)
- [Composer](https://getcomposer.org/download/)

## Installing dependencies int the project

Run this command in main project directory
```
composer install
```

## Setting up .env file

To create .env file simply copy and rename .env.example file that is stored in main directory to .env open it in code and change environmental variables that you are intrested in.

## Important environmental variables

- APP_KEY - to generate app key run ```php artisan key:generate``` in main directory
- DB_DATABASE - name of database that will be used
- SANCTUM_EXPIRATION - time untill access token will expire (seconds)

## Migrating database

Before running this command you have to create database with name same as ```DB_DATABASE``` in your ```.env``` file after this run command:<br>
```php artisan migrate```
to fill database with tables.

## Running server

```
php artisan serve
```

## Debug commands

Creating password request without email where <br> email = email of account which password has to be reseted <br>
recoverytoken = set value of this token if there won't be anything it will be set to ```foo```
```
php artisan password:reset email recoverytoken
```

