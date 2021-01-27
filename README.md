# Projet 3 - Stats Aviron

Interface for the President of the Angers rowing club, which allows to integrate
FFA's data and to get statistics

## Getting Started

### Prerequisites

* Check composer is installed
* Check yarn is installed
* Check Symfony version is 5.*

### Install

1. Clone this project
2. Run `composer install`
3. Run `yarn install`
4. Create .env.local file from the .env file and modify 
   
    _- db_user : your username_

    _- db_password : your password_
   
    in :
   ```
   DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/aviron
   ```
   Configure the DSN to deliver emails over SMTP (the user, pass and port parameters are optional)
   ```
   ###> symfony/mailer ###
   # MAILER_DSN=smtp://user:pass@smtp.example.com:port
   ###< symfony/mailer ###
   ```
5. Create the database with `php bin/console doctrine:database:create`
6. Execute migrations and create tables with `php bin/console doctrine:migrations:migrate`
7. Load the fixtures with `php bin/console doctrine:fixtures:load`
8. Run `yarn encore dev` to build assets

### Working

1. Run `symfony server:start` to launch your local php web server

## Built With
* Symfony 5
* PHP 7.4  
* Bootstrap 4
* Symfony UX Chart JS 1.1

## Authors

  * [Julie Rebeyrolle](https://github.com/JulieRebeyrolle)
  * [Barbara Gonthier](https://github.com/BarbaraGonthier)
  * [Aurélie Frarin](https://github.com/Aurelie-F)
  * [Matthieu Bardet](https://github.com/MatthieuBardet)


