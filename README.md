
# ivote Application Setup Guide

This guide will help you set up the ivote application on your local machine using Laravel Sail and Docker. You will also learn how to set up a database using HeidiSQL (optional) or any other database management tool and how to migrate your database using Sail.
Since the Crypto Subtle Web API used for hashing messages require being loaded over https, this application will force all request to be over https.
```bash
   https://developer.mozilla.org/en-US/docs/Web/API/Crypto/subtle
``` 
If you're running the application with sail, we've provided you with sail https package that gracefully does sets up your nginx with https. 
```bash
   https://laravel-news.com/package/ryoluo-sail-ssl
``` 
However, without sail you can start with symfony
```bash
   symfony server:start
``` 

## Prerequisites

Make sure you have the following installed on your system:

- Docker
- Laravel Sail (Sail is included in Laravel 8 and above)
- HeidiSQL (optional) or any other database management tool for database access

## 1. Clone the Repository

First, clone the ivote repository to your local machine:

```bash
git clone https://github.com/oseghale797/lrs_voting_board
cd lrs_voting_board
```
If you prefer using the zipped file ivote.zip, skip step 2 and 3 but
make sure your database env configuration matches your databse settings.

## 2. Install Dependencies

To install all required dependencies using Laravel Sail, run the following commands:

```bash
# Start Docker container
./vendor/bin/sail up

# Install PHP dependencies inside Docker container
./vendor/bin/sail composer install

# Install Node.js dependencies inside Docker container
./vendor/bin/sail npm install
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
DB_USERNAME=sail
DB_PASSWORD=password
```

Make sure the database credentials match your MySQL instance.

## 4. Start Sail

Use Laravel Sail to start the Docker containers that will run the application:

```bash
./vendor/bin/sail up
```

If you want to build the containers without cache (optional), use:

```bash
./vendor/bin/sail build --no-cache
```

## 5. Set Up the Database

You can manage the MySQL database using any preferred tool, but HeidiSQL is suggested. You can download HeidiSQL here: [HeidiSQL Download Link](https://www.heidisql.com/download.php)

### Using HeidiSQL (Optional)

1. Install and launch HeidiSQL.
2. Create a new session with the following settings:
   - **Network Type**: `MySQL (TCP/IP)`
   - **Hostname/IP**: `127.0.0.1`
   - **User**: `sail`
   - **Password**: `password`
   - **Port**: `3306`
   - **Database**: `ivote_db`

If you prefer another database management tool, just ensure the connection details match the user, password, and database name set in the `.env` file.

## 6. Running Database Migrations

With the database set up, run the migrations to set up the necessary tables:

```bash
./vendor/bin/sail artisan migrate
```

## 7. Accessing the Application

Once everything is up and running, you can access the application in your browser at:

```bash
http://localhost
```

If you encounter any permission issues, run the following commands:

```bash
sudo chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
```

## 8. Stopping Sail

To stop the running Sail application, execute:

```bash
./vendor/bin/sail down
```

## Conclusion

You now have the ivote application running using Docker and Laravel Sail. Additionally, you’ve set up your MySQL database using HeidiSQL or another database management tool. Follow the instructions to manage the app, run migrations, and start development.
