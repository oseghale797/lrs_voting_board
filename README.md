
# ivote Application Setup Guide

This guide will help you set up the ivote application on your local machine using Laravel artisan and Laragon. You will also learn how to set up a database using HeidiSQL (optional) or any other database management tool and how to migrate your database using Sail.
Since the Crypto Subtle Web API used for hashing messages require being loaded over https, this application will force all request to be over https.
```bash
   https://developer.mozilla.org/en-US/docs/Web/API/Crypto/subtle
``` 
If you're running the application with sail, we've provided you with sail https package that gracefully does sets up your nginx with https. 
```bash
   https://laravel-news.com/package/ryoluo-sail-ssl
``` 
## Prerequisites

Make sure you have the following installed on your Windows PC:

- Laragon
- Laravel Artisan (Sail is included in Laravel 8 and above)
- HeidiSQL (optional) or any other database management tool for database access

## 1. Clone the Repository

First, clone the ivote repository to your local machine:

```bash
git clone https://github.com/oseghale797/lrs_voting_board
cd lrs_voting_board
```
If you prefer using the zipped file ivote.zip, skip step 2 and 3 but
make sure your database env configuration matches your database settings.

## 2. Install Dependencies

To install all required dependencies using Laravel Sail, run the following commands:

```bash
# Download Laragon (full edition)
https://laragon.org/download/

# Install PHP Composer on Laragon Terminal
composer install

# Install Node.js dependencies on Laragon Terminal
npm install

# You may need to download the latest PHP version and add it to Laragon
https://www.kreaweb.be/laragon-update-php/

# Copy the lrs_voting_board
cp lrs_voting_board folder into C:\laragon\www

```


## 3. Set Up Environment Variables

Copy the `.env.example` file to create a `.env` configuration file:

```bash
cp .env.example .env 
```

Then, edit the `.env` file to configure your environment settings, particularly the database details:

```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=ivote_db
DB_USERNAME=root
DB_PASSWORD=

# Change Session_driver from database
SESSION_DRIVE=file

#Change the App admin email and password
APP_ADMIN_EMAIL="username@example.com"
APP_ADMIN_PASSWORD="Password"
```

Make sure the database credentials matches your MySQL instance.

## 4. Start Artisan server

Use Laravel Artisan to start the server that will run the application via Laragon Terminal:

```bash
php artisan serve

# Open another Terminal:

npm run dev

```

To get the APP_KEY in your .env file use in laragon terminal:

```bash
php artisan key:generate

```
On Laragon app go to Settings > enable SSL for Nginix and Apache and then start the servers

## 5. Set Up the Database

You can manage the MySQL database using any preferred tool, but HeidiSQL is suggested. You can download HeidiSQL here: [HeidiSQL Download Link](https://www.heidisql.com/download.php)

### Using HeidiSQL (Optional)

1. Install and launch HeidiSQL.
2. Create a new session with the following settings:
   - **Network Type**: `MySQL (TCP/IP)`
   - **Hostname/IP**: `127.0.0.1`
   - **User**: `root`
   - **Password**: 
   - **Port**: `3306`
   - **Database**: `ivote_db`

If you prefer another database management tool, just ensure the connection details match the user, password, and database name set in the `.env` file.

## 6. Running Database Migrations

With the database set up, run the migrations to set up the necessary tables on Laragon Terminal:

```bash
Php artisan migrate
```

## 7. Accessing the Application

Once everything is up and running, you can access the application in your browser at:

```bash
https://lrs_voting_board.test/login

#If you rename the folder of lrs_voting_board to ivote at C:\laragon\www location use:

https://ivote.test/login

```

## Conclusion

You now have the ivote application running using Laragon. Additionally, you’ve set up your MySQL database using HeidiSQL or another database management tool. Follow the instructions to manage the app, run migrations, and start development.
