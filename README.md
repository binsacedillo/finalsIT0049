# Finals POS

A staff-only Point-of-Sale application for the IT0049 final project. It includes product, customer, and staff management, secure staff sign-in, image uploads, stock-aware sale recording, and sales history.

## Stack

- Laravel 13 and PHP 8.3 or newer
- Blade and Tailwind CSS, built with Vite
- MySQL / MariaDB for local development; TiDB Cloud Starter is the recommended free cloud database for the Render demo

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
- **Dashboard overview:** daily sales and transaction totals, active products, out-of-stock count, latest five sales, and active products with stock at or below the configurable threshold. Reporting dates use `POS_REPORTING_TIMEZONE` (default `Asia/Manila`); `POS_LOW_STOCK_THRESHOLD` defaults to five units.
- **Products:** list, search, create, edit, archive, and restore products. Product images accept JPG, PNG, and WebP files up to 2 MB and are stored on Laravel's public disk.
- **Customers:** list, search, view, create, edit, and delete customer records. Deleting a customer keeps sales and clears their optional customer reference.
- **Staff:** list, search, view, create, edit, activate, deactivate, and delete staff. Passwords are hashed. Avatars accept JPG, PNG, and WebP files up to 2 MB. Staff referenced by sales cannot be deleted; deactivate those accounts instead.
- **Sales:** one product per transaction, optional customer, positive quantity, server-calculated total, saved unit price, and stock decrement within a database transaction. Insufficient stock is rejected with a message and does not create a sale.
- **Sales history:** paginated transactions with product, customer when available, staff, quantity, saved unit price, total, and date.
- **Demo records:** an optional idempotent seeder adds eleven fictional coffeehouse products, five sample customers, and up to eight sample sales. Sample sales reduce stock; the seeder does not add them when any sales already exist.

## Database and project notes

The schema is built by Laravel migrations and the initial staff account is configured by the database seeder. The sales table retains foreign-key relationships: product and staff records referenced by sales are preserved, while deleting a customer sets the historical customer reference to null. The `unit_price` column records the price at sale time so history remains accurate after product prices change. Product and staff archive states use `is_active`.

The supplied sales schema represents one product per sale; a multi-item shopping cart is not part of this implementation.

## Render deployment (free demo setup)

The repository includes a Dockerfile and `render.yaml` for deploying the Laravel app as a Render Web Service. The suggested no-cost demo stack is:

- **Render Free Web Service** for the PHP app. It sleeps after 15 minutes without traffic and may take about a minute to wake.
- **TiDB Cloud Starter** for persistent MySQL-compatible POS records. Public TiDB connections require TLS.
- **ImageKit Free** for product images and staff avatars. The free tier currently includes 3 GB media storage and 20 GB monthly bandwidth; uploads or delivery can stop after those quotas are reached.

The app runs migrations on startup and seeds the initial staff account from environment variables only when that username does not already exist. `POS_DEMO_DATA_ENABLED=true` enables the fictional catalog and sample sales; the Render Blueprint enables it for the review site. The seeder avoids duplicate catalog records on restarts and skips sample sales if the database already contains any transactions. Set it to `false` for a blank database. The app uses database-backed sessions, cache, and queues so the Render service does not depend on its temporary filesystem for these records. ImageKit keeps uploaded photos outside the Render filesystem.

### Before creating the Render service

1. Create a TiDB Cloud Starter MySQL-compatible instance. In its Connect dialog, copy the public host, port, database name, and username exactly as supplied. Public connections require TLS; the Render setup points PDO at the container's CA bundle. Add Render's outbound IP ranges to the TiDB IP access list.
2. Create an ImageKit account and copy its **private API key**. Keep it secret; the application sends image uploads from the server.
3. Push this repository to GitHub.

### Deploy

1. In Render, create a Blueprint from this repository and select the `render.yaml` file, or create a Web Service using the Docker runtime and repository-root `Dockerfile`.
2. Choose the **Free** plan for a demo. Set the prompted environment values:
   - `APP_URL`: the service's HTTPS `onrender.com` URL.
   - `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`: values from TiDB's Connect dialog. `DB_PORT` is set to `4000` by the blueprint.
   - `IMAGEKIT_PRIVATE_KEY`: ImageKit's server-side private key.
   - `POS_ADMIN_USERNAME`, `POS_ADMIN_NAME`, and `POS_ADMIN_PASSWORD`: the first staff login. Use a strong password and keep these values in Render, not Git.
   - `APP_KEY` is generated by the Blueprint. Keep it unchanged across redeployments so existing encrypted sessions remain valid.
3. Create the service. Its startup command caches production config, applies migrations, seeds the initial staff account if needed, then starts Apache on Render's assigned port.
4. Open the Render URL, sign in with the initial staff credentials, and verify product upload, customer/staff management, sale recording, and history.

### Free-tier behavior

This stack can keep POS records and uploaded images between Render restarts because those live in TiDB and ImageKit. Render's free app can sleep when idle. TiDB Starter has monthly storage/request quotas and blocks new connections if its quota is exhausted; ImageKit limits image storage and delivery. Check each provider's current limits before the demo. This is appropriate for a class demonstration, not a production POS with availability guarantees.

For local use, leave `IMAGE_STORAGE_DRIVER=local`; image uploads continue to use Laravel's public disk. On Render, `IMAGE_STORAGE_DRIVER=imagekit` sends images to ImageKit and saves each remote URL and provider file ID in the database.

## Verification

Run the test suite using PHP 8.5:

```powershell
& 'C:\php-8.5.12\php.exe' artisan test
npm.cmd run build
```

Feature tests cover authentication, access restrictions, CRUD validation, upload storage, password hashing, archive/deletion behavior, sale calculations, stock rejection, and sales history. Tests use an in-memory SQLite database; run migrations against the configured MySQL database before local use.
