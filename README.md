# Student marks taking and processing

These are what is it all about globally:
   -  app anaovana relevé de notes
   -  moyennes
   -  délibérations
   -  classement


What we know:
   - 12 centres d'examen
   - 4 classes
   - 6 matières


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


