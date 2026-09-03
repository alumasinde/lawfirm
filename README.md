# Webi Wenani & Associates Advocates

A custom PHP and MySQL website and content management system for a Kenyan law firm.

## Requirements

- PHP 8.2+
- MySQL 8+
- PDO MySQL extension

## Local setup

1. Copy `.env.example` to `.env` and update database values.
2. Create the database.
3. Import `database/schema.sql`.
4. Point your web server document root to `public/`.
5. Run `php -S localhost:8000 -t public` for local development.

## Structure

- `app/` application code
- `config/` configuration
- `database/` schema
- `public/` web root
- `resources/` views and assets
- `storage/` runtime files
