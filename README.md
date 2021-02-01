# Projet 3 - Stats Aviron

This desktop application built on web technologies allows the user (rowing club manager) to follow-up with his club’s data and analyze them through seasons.
Main features :
- Securing the access of this non-public website, it can only be accessed by the administrator for now.
- Importing the French Rowing Federation’s csv files containing subscribers data per season
- Displaying a table summing up subscribers’ data per category, licence type or status
- Filtering the tables according to different options the user can choose between
- Displaying with tables and charts subscribers’ statistics, for the current ones and those who stopped their subscription
- Displaying data of the subscription renewal with a pyramid, only for competition licences

## Getting Started

### Prerequisites

* Check composer is installed
* Check yarn is installed
* Check Symfony version is 5.* and PHP 7.4
* The Bootstrap version used is 4.5

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
   `MAILER_FROM_ADDRESS` is the sender's email address
5. Create the database with `php bin/console doctrine:database:create`
6. Execute migrations and create tables with `php bin/console doctrine:migrations:migrate`
7. Load the fixtures with `php bin/console doctrine:fixtures:load`
8. Run `yarn encore dev` to build assets

### Working

1. Run `symfony server:start` to launch your local php web server


## Authors

  * [Julie Rebeyrolle](https://github.com/JulieRebeyrolle)
  * [Barbara Gonthier](https://github.com/BarbaraGonthier)
  * [Aurélie Frarin](https://github.com/Aurelie-F)
  * [Matthieu Bardet](https://github.com/MatthieuBardet)


