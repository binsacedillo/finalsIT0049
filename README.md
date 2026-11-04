# Finals POS

A staff-only Point-of-Sale application for the IT0049 final project. It includes product, customer, and staff management, secure staff sign-in, image uploads, stock-aware sale recording, and sales history.

## Stack

- Laravel 13 and PHP 8.3 or newer
- Blade and Tailwind CSS, built with Vite
- MySQL / MariaDB for the local application database

On this Windows machine, `php` still resolves to XAMPP's PHP 8.2.12. Laravel 13 requires PHP 8.3+, so use the installed PHP 8.5 CLI directly at `C:\php-8.5.12\php.exe`. Its ZIP and MySQL PDO extensions are available as DLLs but are not enabled globally; the commands below enable them for that command only. Adjust the paths if PHP or Composer is installed elsewhere.

## Local setup

1. Install PHP 8.3+, Composer, Node.js, and MySQL. In XAMPP, start MySQL and create an empty database named `finals_pos`.
2. Install PHP dependencies:

   ```powershell
   & 'C:\php-8.5.12\php.exe' -d extension=zip 'C:\ProgramData\ComposerSetup\bin\composer.phar' install
   ```

   On a machine where Composer already uses PHP 8.3+, `composer install` is sufficient.

3. Install and build the frontend:

   ```powershell
   npm.cmd install
   npm.cmd run build
   ```

4. Copy `.env.example` to `.env` if needed. Set `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE=finals_pos`, `DB_USERNAME`, and `DB_PASSWORD` for the local MySQL server.
5. Generate the application key:

   ```powershell
   & 'C:\php-8.5.12\php.exe' artisan key:generate
   ```

6. Set `POS_ADMIN_USERNAME`, `POS_ADMIN_NAME`, and `POS_ADMIN_PASSWORD` in `.env` to create the first staff account. The seeder skips the account if credentials are not set. Keep `.env` private and never commit it.
7. Run the migrations and seeder, then create the public upload link:

   ```powershell
   & 'C:\php-8.5.12\php.exe' -d extension=pdo_mysql artisan migrate --seed
   & 'C:\php-8.5.12\php.exe' artisan storage:link
   ```

8. Start the local server:

   ```powershell
   & 'C:\php-8.5.12\php.exe' -d extension=pdo_mysql artisan serve
   ```

Open `http://127.0.0.1:8000` and sign in with the initial staff credentials. New staff accounts can then be created from the Staff page; public registration is not available.

## Features

- **Dashboard and authentication:** username/password login, session renewal, rate-limited login attempts, logout, and protection for all management routes. Inactive staff cannot log in or continue using an existing session.
- **Products:** list, search, create, edit, archive, and restore products. Product images accept JPG, PNG, and WebP files up to 2 MB and are stored on Laravel's public disk.
- **Customers:** list, search, view, create, edit, and delete customer records. Deleting a customer keeps sales and clears their optional customer reference.
- **Staff:** list, search, view, create, edit, activate, deactivate, and delete staff. Passwords are hashed. Avatars accept JPG, PNG, and WebP files up to 2 MB. Staff referenced by sales cannot be deleted; deactivate those accounts instead.
- **Sales:** one product per transaction, optional customer, positive quantity, server-calculated total, saved unit price, and stock decrement within a database transaction. Insufficient stock is rejected with a message and does not create a sale.
- **Sales history:** paginated transactions with product, customer when available, staff, quantity, saved unit price, total, and date.

## Database and project notes

The schema is built by Laravel migrations and the initial staff account is configured by the database seeder. The sales table retains foreign-key relationships: product and staff records referenced by sales are preserved, while deleting a customer sets the historical customer reference to null. The `unit_price` column records the price at sale time so history remains accurate after product prices change. Product and staff archive states use `is_active`.

The supplied sales schema represents one product per sale; a multi-item shopping cart is not part of this implementation. Hosting is intentionally deferred.

## Verification

Run the test suite using PHP 8.5:

```powershell
& 'C:\php-8.5.12\php.exe' artisan test
npm.cmd run build
```

Feature tests cover authentication, access restrictions, CRUD validation, upload storage, password hashing, archive/deletion behavior, sale calculations, stock rejection, and sales history. Tests use an in-memory SQLite database; run migrations against the configured MySQL database before local use.
