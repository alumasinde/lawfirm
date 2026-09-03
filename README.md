# Webi Wenani & Associates Advocates

A custom PHP and MySQL website and content management system for a Kenyan law firm.

## Requirements

- PHP 8.2+
- MySQL 8+
- PDO MySQL extension

## Local setup

1. Copy `.env.example` to `.env` and update database values.
2. Create the database configured in `.env`.
3. Run migrations:

   `php database/migrate.php`

4. Point your web server document root to `public/`.
5. Run `php -S localhost:8000 -t public` for local development.
php -S 0.0.0.0:8000 -t public public/index.php

lawfirm.test:8000 & lawfirm.test:8000/admin/loin

## Database migrations

Migration files live in `database/migrations/`. The runner records completed migrations in the `migrations` table and only applies pending files.

## Design system

The active design is controlled by `config/design/active.ini`. It contains semantic brand, text, surface, layout, border and spacing tokens.

`app/View/StyleLinker.php` converts those values into CSS custom properties and loads the active versioned stylesheet manifest from `public/css/<version>/`.

## Structure

- `app/` application code
- `config/` configuration
- `database/migrations/` database migrations
- `public/` web root
- `resources/` views and reusable components
- `storage/` runtime files
