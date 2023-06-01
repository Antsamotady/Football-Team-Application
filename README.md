# Football-Team-Application in Symfony 6.0.2, PHP 8.0.13

We have football teams. Each team has a name, country, money balance and players. Each player has name and surname. Teams can sell/buy players. 


## Installation

- Install Dependencies: Navigate to the project directory and install the required dependencies using Composer:

   ```
   cd <project-directory>
   composer install
   ```

- Set Up Environment Variable **DATABASE_URL** in .env file
    ```
    DATABASE_URL=driver://username:password@host:port/database_name
    ```

- Create your database

- Run Database Migrations: If your Symfony project uses migrations, run the database migrations to set up the database schema:
   ```
   php bin/console doctrine:migrations:migrate
   ```

- Load fixture to get random data (optional)
   ```
   php bin/console doctrine:fixtures:load
   ```
OR
- Import this ready to use tables:
(Also useful if running into trouble with the migrations)
File **sf6_db.sql**
(located at the root of the project in the repo)


- Start the Development Server: Launch the Symfony development server to run the application locally:
   ```
   php bin/console server:start
   ```


## Login Instruction
Here is the login you should use because 

**test@mail.com / lalala**


## Note

To get the project done in time, I used a previous project I had in the past.
The purpose was just to avoid time consumming on the installation of fresh one.

Actually the main controllers involve in this particular project are:
HomeController.php, PlayerController.php and TeamController.php

The rest are not relevent which the latest commits confirmed.



