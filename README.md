# KapePOS

A coffeehouse point-of-sale system for managing products, customers, staff accounts, inventory, and sales. Staff can record a sale against available stock and review the transaction history.

## Project team

- Vince Gio Acedillo
- Joeffrey Allado
- John Michael Jao

## Course project

- **Course:** IT0049 — Web System Technologies
- **Project:** Complete Point-of-Sale System
- **Framework:** Laravel 13

> The supplied activity document names CodeIgniter for the POS implementation. This repository is implemented in Laravel 13, following the team's framework decision. Confirm with the instructor that Laravel is accepted for submission.

## Technology

- PHP 8.3 or newer and Laravel 13
- Blade templates and Tailwind CSS, compiled with Vite
- MySQL or MariaDB for local development
- TiDB Cloud Starter for the hosted MySQL-compatible database
- Render for application hosting
- ImageKit for persistent hosted product images and staff avatars

## Features

- Staff username/password sign-in, sign-out, session renewal, login throttling, and protected management routes
- Product listing, search, create, edit, archive, and restore, with validated product images
- Customer listing, search, create, view, edit, and delete
- Staff listing, search, create, view, edit, activate/deactivate, and safe deletion, with hashed passwords and optional avatars
- Sale recording for one product per transaction, with an optional customer
- Server-calculated totals, saved sale-time unit prices, and transactional stock updates
- Clear rejection of sales that exceed available stock
- Paginated sales history with product, customer when present, staff, quantity, total, and date
- Dashboard summary of today's sales, transactions, inventory, recent sales, and low-stock items

## Database

Laravel migrations create the database schema and foreign-key relationships for products, customers, users, and sales. The schema follows the supplied activity, with additional fields for product/staff active status and the unit price at the time of sale.

Products and staff referenced by sales are preserved through archiving or deactivation. Deleting a customer keeps the sale and clears its optional customer reference. The activity's sales schema represents one product per sale; a multi-item cart is outside this project scope.

The repository includes migrations and seeders. The optional demo seeder adds fictional coffeehouse products and customers and, when the sales table is empty, sample sales. Those sample sales reduce stock. Catalog rows are not duplicated on service restarts.

## Run locally

### Requirements

Install PHP 8.3 or newer with the PDO driver for your database, Composer, Node.js with npm, and MySQL or MariaDB. Check the active PHP version with `php -v` before installing dependencies.

### Setup

1. Create an empty database, for example `finals_pos`.
2. Copy `.env.example` to `.env` and set the database connection values:

   ```dotenv
   APP_ENV=local
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=finals_pos
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. Install PHP and JavaScript dependencies:

   ```bash
   composer install
   npm ci
   ```

4. Generate the application key and configure the first staff account in `.env`:

   ```bash
   php artisan key:generate
   ```

   Set `POS_ADMIN_USERNAME`, `POS_ADMIN_NAME`, and `POS_ADMIN_PASSWORD`. Keep `.env` private; never commit secrets to Git.

5. Run migrations and seed the database:

   ```bash
   php artisan migrate --seed
   ```

6. For local image uploads, create the public storage link and build the frontend:

   ```bash
   php artisan storage:link
   npm run build
   ```

7. Start the Laravel development server:

   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```

   Open [http://127.0.0.1:8000](http://127.0.0.1:8000). To use Vite's live frontend development mode, run `npm run dev` in a second terminal.

If your `php` command points to a PHP version older than 8.3, use the path to your PHP 8.3+ executable for the Composer and Artisan commands.

### Local image storage

Local development uses Laravel's `public` storage disk by default. Run `php artisan storage:link` once so uploaded images can be displayed. Render's filesystem is temporary, so the hosted deployment uses ImageKit for persistent uploads.

## Demo access and sample data

The local database seeder creates the initial staff account only when all required `POS_ADMIN_*` credentials are configured. Demo catalog data is enabled by default in local development; set `POS_DEMO_DATA_ENABLED=false` if you want an empty local catalog.

The Render Blueprint enables `POS_DEMO_ACCESS_ENABLED` and `POS_DEMO_DATA_ENABLED`. When demo access is enabled and the staff credentials are configured, the login page displays those credentials to visitors. Anyone who can open the page can then sign in and make changes to the demo data. Use a dedicated demo account and never put personal or production credentials there. Set `POS_DEMO_ACCESS_ENABLED=false` to hide the public credential panel.

## Configuration reference

| Variable | Purpose |
|---|---|
| `APP_KEY` | Laravel encryption key; generate locally and keep stable across deployments |
| `APP_URL` | Base URL of the app |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Database connection |
| `MYSQL_ATTR_SSL_CA` | CA bundle path for TLS database connections when required |
| `POS_ADMIN_USERNAME`, `POS_ADMIN_NAME`, `POS_ADMIN_PASSWORD` | Initial staff account created by the seeder |
| `POS_DEMO_ACCESS_ENABLED` | Whether the configured demo credentials appear on the login page |
| `POS_DEMO_DATA_ENABLED` | Whether the fictional demo catalog and sample sales are seeded |
| `POS_REPORTING_TIMEZONE` | Timezone used for dashboard reporting; defaults to `Asia/Manila` |
| `POS_LOW_STOCK_THRESHOLD` | Quantity at or below which active products appear in the low-stock list; defaults to `5` |
| `IMAGE_STORAGE_DRIVER` | `local` for local public-disk storage or `imagekit` for ImageKit uploads |
| `IMAGEKIT_PRIVATE_KEY` | Server-side ImageKit private key; keep it secret |

## Render deployment

The repository includes a root `Dockerfile`, a Render Blueprint (`render.yaml`), and a startup script. The container builds the Vite assets, applies database migrations, runs the configured seeders, and starts Apache on Render's assigned port.

The current hosted setup uses Render for the Laravel service, TiDB Cloud for persistent MySQL-compatible records, and ImageKit for persistent uploaded images. Configure the following in Render's environment settings:

- `APP_URL`: the service's HTTPS URL
- `APP_KEY`: a valid Laravel key; keep the same value between deploys
- `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, and `DB_PORT`: values from the TiDB connection details; the Blueprint uses port `4000`
- `MYSQL_ATTR_SSL_CA`: the container CA bundle path configured in the Blueprint
- `IMAGEKIT_PRIVATE_KEY`: the ImageKit private API key
- `POS_ADMIN_USERNAME`, `POS_ADMIN_NAME`, and `POS_ADMIN_PASSWORD`: the initial staff account
- `POS_DEMO_ACCESS_ENABLED` and `POS_DEMO_DATA_ENABLED`: whether public demo credentials and sample records are enabled

Do not commit `.env`, database passwords, the Laravel `APP_KEY`, or private storage keys. The service uses database-backed sessions, cache, and queues so those records are not kept on Render's temporary filesystem.

Render free services can sleep when idle, and external database and image providers have their own usage limits and availability conditions. Check current provider terms and quotas before relying on the demo for a presentation.

## Tests and build

Run the automated tests and build the production frontend assets with:

```bash
php artisan test
npm run build
```

The feature tests cover authentication and route protection, management validation, image uploads, password hashing, archive/deletion behavior, sale calculations, stock rejection, and sales history. Tests use an in-memory SQLite database; they do not replace a deployment check against the configured MySQL-compatible database.

## Links

- [GitHub repository](https://github.com/binsacedillo/finalsIT0049)
- [Hosted application — staff login](https://finals-pos.onrender.com/login)
