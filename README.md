
Project setup and migrations

This project is a Laravel 10 application configured to run with Docker (Laravel Sail) or locally.

Quick start (Docker — Laravel Sail)

Prerequisites:
- Docker Desktop 4.x+ (Docker Engine 20+)

Steps:
1. Copy environment file
```
cp .env.example .env
```
2. Ensure database values in `.env` match Sail defaults (adjust if needed):
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```
3. Start the containers
```
./vendor/bin/sail up -d
```
4. Install PHP dependencies (if not already installed)
```
./vendor/bin/sail composer install
```
5. Generate the application key
```
./vendor/bin/sail artisan key:generate
```
6. Run database migrations
```
./vendor/bin/sail artisan migrate
```
Optional:
- Seed demo data
```
./vendor/bin/sail artisan db:seed
```
- Run the test suite
```
./vendor/bin/sail test
```
- Stop containers
```
./vendor/bin/sail down
```

Local setup (without Docker)

Prerequisites:
- PHP >= 8.2 with extensions: `pdo_mysql`, `mbstring`, `bcmath`, `openssl`
- Composer 2.x
- MySQL 8.x (or MariaDB compatible)
- Redis (optional, if you plan to use queues/cache)

Steps:
1. Install PHP dependencies
```
composer install
```
2. Copy environment file and configure DB credentials for your local MySQL
```
cp .env.example .env
```
3. Generate the application key
```
php artisan key:generate
```
4. Create the database (if it doesn’t exist) and run migrations
```
php artisan migrate
```
5. Start the local dev server
```
php artisan serve
```

How to run the migrations

Migrations live in `database/migrations`.

- Using Docker (Sail): `./vendor/bin/sail artisan migrate`
- Locally: `php artisan migrate`

Common migration commands:
```
# Roll back the last batch
php artisan migrate:rollback

# Re-run all migrations from scratch
php artisan migrate:fresh

# Seed after fresh migrations
php artisan migrate:fresh --seed
```

Prompt strategy (how I ensured the AI follows instructions)

I followed a latest-instructions-first approach, using the repository’s current structure (Laravel + Sail) and the attached README context to produce clear, reproducible steps for both Docker and local environments. I kept commands explicit, minimized assumptions, and grouped tasks by goal (setup, keys, dependencies, migrations, tests). I avoided changing code, focused on documentation, and wrote concise, verifiable commands so the outcome is easy to test and aligns with your request.
