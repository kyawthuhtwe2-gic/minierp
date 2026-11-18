# MiniERP Backend

Laravel 13 backend, verified with PHP 8.5.12. The Laravel project requires
PHP `^8.3`, which includes PHP 8.5.

The application uses MySQL. For local PHP development, run a MySQL server and
create the database and user configured in `backend/.env`. The test suite uses
an in-memory SQLite database, so enable both `pdo_mysql` and `pdo_sqlite` in the
loaded `php.ini`. Check enabled extensions with `php -m`.

## Run with Docker

From the repository root, make sure `backend/.env` exists and has an
application key, and `frontend/.env` exists. See the root README for
first-time environment setup, then run:

```powershell
docker compose up --build
```

The backend is available at <http://localhost:8000>. Docker runs MySQL 8.4
with data stored in the persistent `mysql-data` volume. The backend waits for
MySQL to become healthy and runs migrations when it starts.

## Setup on Windows

From the repository root, run:

```powershell
Set-Location .\backend
composer install
if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    php artisan key:generate
}
php artisan migrate
php artisan serve
```

Before running these commands, make sure the MySQL server is running and the
database/user in `.env` exist. Run the test suite with `php artisan test`.
Tests use SQLite in memory. Keep `.env` local; it contains the application key
and is excluded from version control.

## MVC structure

- `app/Http/Controllers/` — HTTP controllers.
- `app/Models/` — Eloquent models.
- `resources/views/` — Blade views.
- `routes/web.php` — web routes.
- `database/migrations/` — database schema.
- `tests/Feature/` and `tests/Unit/` — application and unit tests.
