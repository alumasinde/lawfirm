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
5. Run the built-in PHP server for local development:

   `php -S 0.0.0.0:8000 -t public public/index.php`

   Open the site at `http://lawfirm.test:8000` and the administration area at `http://lawfirm.test:8000/admin`.

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


## Administration

The administration area is intentionally excluded from public indexing and is available under `/admin`.

After running migrations, the admin foundation provides a database-driven content management workspace for the existing website resources. Resource definitions are stored in `admin_resources`, so the administration navigation and content registry are driven by database configuration rather than duplicated public-site content in templates.

Run pending migrations after pulling updates:

`php database/migrate.php`
