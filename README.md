# Currency Converter PHP Symfony Framework

## Overview

This project is a Currency Converter application built with PHP using the Symfony framework and MySQL. The application allows users to convert an amount from one currency to various other currencies. It includes features such as IP-based access control, user authentication, and CRUD operations for managing users and IPs.

The assignment is designed to test PHP and MVC skills, focusing on backend functionalities and system architecture. The application also supports optional features like password reset and an admin dashboard for user

## Features

- **Currency Conversion**: Conversion of a specified amount from one currency to multiple other currencies.
- **Exchange Rate Import**: Fetch exchange rates from the backend and load them into the form.
- **User Authentication**: Secure login with username/password and IP-based restrictions.
- **Remember Me**: Option for users to stay logged in across sessions.
- **Role-Based View Management**: Different views and features based on user roles (Admin, User).
- **Forgot Password**: Users can reset their passwords.
- **Admin Dashboard**: Admins can manage users.
- **Exchange Rate Import**: Fetch exchange rates from the backend and load them into the form.
- **Logging**: Log activities and errors using Symfony's Monolog for debugging and monitoring.

## Technologies Used

- **Symfony 5.4**: PHP framework for building robust web applications.
- **MySQL**: Relational database management system.
- **Bootstrap**: Frontend framework for basic styling.
- **PHP**: Server-side scripting language.
- **Git**: Version control system.

## Getting Started

### Prerequisites

- **PHP 7.4+**
- **MySQL 5.7+**
- **Composer** (Dependency manager for PHP)
- **XAMPP** (Optional: for local development)

### Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/SLoharkar/Currency-Converter-PHP-Symfony-Framework.git
   cd Currency-Converter-PHP-Symfony-Framework
    ```

2. **Install Dependencies**

   Ensure you have Composer installed and run:

   ```bash
   composer install
   ```
   
3. **Configure Environment Variables**

   Copy the `.env.test` file to set up your environment variables:

   ```bash
   cp .env.test .env
   ```

   Copy the `.env` file and set up your database connection and application secret:

   ```env
   DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
   APP_SECRET="your_random_app_secret_key"
   ```

4. **Update Composer Dependencies**

   After setting up the environment variables, you can update the Composer dependencies to ensure you have the latest versions:

   ```bash
   composer update
   ```

5. **Import the Database Schema and Initial Data**

   **Import the `.sql` File**

   Use MySQL Workbench, phpMyAdmin, or XAMPP to run the `xampp.sql` file:

   - Open MySQL Workbench or phpMyAdmin.
   - Create a new database if you haven't already.
   - Import the `xampp.sql` file into the new database.

6. **Run the Application**

   You can run the Symfony application using the built-in PHP server. Use the following command:

   ```bash
   php -S localhost:3000 -t public
   ```

   Replace `localhost` with your IP address if you want to access the application from another device on your network.

7. **Access the Application**

   Open your web browser and navigate to [http://localhost:3000](http://localhost:3000) (or replace `localhost` with your IP address if applicable).


## Usage

### User Authentication

- **Login**: Visit `/login` to access the login page.
- **Register**: Visit `/register` to create a new user account.
- **Forgot Password**: If enabled, visit `/forgot_password` to reset your password.

### Currency Conversion

- The application automatically fetches live currency rates from [FloatRates](http://www.floatrates.com/json-feeds.html).
- Select a "From" currency from the dropdown.
- Enter the amount to be converted.
- Click the "Convert" button to see the conversion table with live rates.


### Role-Based Views

- **Admin**:
  - **Dashboard**: `/admin/dashboard` - Manage users operations like update and delete users.

- **User**:
  - **Homepage**: `/` - Main page of the application.
  - **Currency Converter**: `/user/currency-converter` - Perform currency conversions.

## Logging with Monolog

The application uses [Monolog](https://symfony.com/doc/current/logging.html) for logging activities and errors. Logs are stored in the `var/log/` directory.

### Monolog Configuration

Monolog is configured in `config/packages/monolog.yaml`:

- **Log File Location**: Logs are written to `var/log/`.
- **Log Levels**: The application logs messages of various levels:

  - `**debug**`: Detailed debug information.
  - `**info**`: General application information.
  - `**warning**`: Exceptional occurrences that are not errors.
  - `**error**`: Runtime errors that do not require immediate action.
  - `**critical**`: Critical conditions, such as a component failing.
  - For detailed logs, you can adjust the `level` setting in `monolog.yaml` to `debug`, `info`, `warning`, `error`, or `critical`.


## Website Trailer

Check out our trailer video to get a quick overview of the **Currency Converter** application:

https://github.com/user-attachments/assets/d8dec905-eee0-43b8-890c-a208a8c0f8ae

