# Finals POS

A Point-of-Sale system for the final laboratory activity. This repository currently contains the Laravel foundation and database structure; product, customer, staff, authentication, sale-entry, and sales-history screens are the next implementation stage.

## Stack

- Laravel 13
- PHP 8.3 or newer (PHP 8.5.12 is installed locally at `C:\php-8.5.12\php.exe`)
- MySQL / MariaDB (local setup uses XAMPP by default)
- Blade views and Laravel's standard session-based web stack

Laravel 13 requires PHP 8.3 or newer. On this machine, `php` currently resolves to XAMPP's PHP 8.2.12 because it appears earlier on `PATH`; use `C:\php-8.5.12\php.exe` for this project. The standalone PHP install includes the MySQL PDO extension DLL, but it may need enabling in `php.ini` before using MySQL. The XAMPP Apache PHP version is separate from this CLI setup.

## Local setup

1. Install PHP 8.3+, Composer, and MySQL. Start MySQL in XAMPP and create an empty database named `finals_pos`.
2. Install PHP dependencies with `composer install`.
3. Copy `.env.example` to `.env` if `.env` does not already exist. Set the database connection values for your machine.
4. Generate the application key: `php artisan key:generate`.
5. To seed an initial staff account, set `POS_ADMIN_USERNAME`, `POS_ADMIN_NAME`, and `POS_ADMIN_PASSWORD` in `.env`. These values are optional and must not be committed.
6. Create the tables and optional initial staff account: `php artisan migrate --seed`.
7. Start the local server with the PHP 8.5 CLI: `C:\php-8.5.12\php.exe artisan serve`.

Uploaded product images and staff avatars will use Laravel's public storage disk. When upload features are implemented, run `php artisan storage:link` so files can be served from the public directory.

## Database foundation

- `users`: staff login details, avatar path, and an `is_active` flag for archiving staff.
- `products`: product details, current stock, image path, and an `is_active` flag for archiving products.
- `customers`: customer contact details.
- `sales`: product, optional customer, staff member, quantity, unit price at time of sale, and total.

Sales restrict deletion of their product and staff records so history remains valid. Deleting a customer sets the historical sale's customer reference to null. The `unit_price` field is an addition to the activity schema so historical totals remain understandable if a product's current price changes. Laravel's `created_at` is non-null and defaults to the database current time; `updated_at` is nullable.

## Current scope

The migrations, Eloquent models, table relationships, and configurable initial staff seeder are in place. No POS pages or login flow have been implemented yet.
