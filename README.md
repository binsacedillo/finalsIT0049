# Finals POS

A Point-of-Sale system for the final laboratory activity. This repository currently contains the Laravel foundation and database structure; product, customer, staff, authentication, sale-entry, and sales-history screens are the next implementation stage.

## Stack

- Laravel 13
- PHP 8.3 or newer (PHP 8.5.12 is installed locally at `C:\php-8.5.12\php.exe`)
- MySQL / MariaDB (local setup uses XAMPP by default)
- Blade views and Laravel's standard session-based web stack

Laravel 13 requires PHP 8.3 or newer. On this machine, `php` currently resolves to XAMPP's PHP 8.2.12 because it appears earlier on `PATH`; use `C:\php-8.5.12\php.exe` for this project. The standalone PHP install includes ZIP and MySQL PDO extension DLLs, but they are not enabled globally. The commands below enable them for the relevant process. The XAMPP Apache PHP version is separate from this CLI setup.

## Local setup

1. Install PHP 8.3+, Composer, and MySQL. Start MySQL in XAMPP and create an empty database named `finals_pos`.
2. Install PHP dependencies with `C:\php-8.5.12\php.exe -d extension=zip C:\ProgramData\ComposerSetup\bin\composer.phar install` (adjust the Composer path if it differs on your machine).
3. Install frontend dependencies and build assets with `npm.cmd install` and `npm.cmd run build`.
4. Copy `.env.example` to `.env` if `.env` does not already exist. Set the database connection values for your machine.
5. Generate the application key: `C:\php-8.5.12\php.exe artisan key:generate`.
6. Set `POS_ADMIN_USERNAME`, `POS_ADMIN_NAME`, and `POS_ADMIN_PASSWORD` in `.env` for the initial staff login. These values are optional and must not be committed.
7. Create the tables and optional initial staff account: `C:\php-8.5.12\php.exe -d extension=pdo_mysql artisan migrate --seed`.
8. Link the public upload directory: `C:\php-8.5.12\php.exe artisan storage:link`.
9. Start the local server with the PHP 8.5 CLI: `C:\php-8.5.12\php.exe -d extension=pdo_mysql artisan serve`.

Uploaded product images and staff avatars use Laravel's public storage disk. Run `storage:link` so files can be served from the public directory.

## Database foundation

- `users`: staff login details, avatar path, and an `is_active` flag for archiving staff.
- `products`: product details, current stock, image path, and an `is_active` flag for archiving products.
- `customers`: customer contact details.
- `sales`: product, optional customer, staff member, quantity, unit price at time of sale, and total.

Sales restrict deletion of their product and staff records so history remains valid. Deleting a customer sets the historical sale's customer reference to null. The `unit_price` field is an addition to the activity schema so historical totals remain understandable if a product's current price changes. Laravel's `created_at` is non-null and defaults to the database current time; `updated_at` is nullable.

## Current scope

The migrations, Eloquent models, table relationships, configurable initial staff seeder, staff login/logout, protected dashboard, and product CRUD with image upload and archive/restore are in place. Customer and staff management, sale entry, and sales history are being implemented in separate phases.
