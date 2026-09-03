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

## Phase 7 content management

Phase 7 adds a complete database-driven content layer for practice areas, advocates and legal insights, including publishing fields, SEO metadata and managed media relationships.

The Media Library is available at `/admin/media`. Images are validated server-side, stored under `public/uploads/media/` and recorded in the `media` table. The CMS detects media fields dynamically by the `*_media_id` convention and exposes the library as a reusable selector.

Media deletion is protected by database foreign-key usage checks, so images still referenced by advocates, articles or homepage slides cannot be removed.

### Upload permissions

The PHP process must be able to write to:

`public/uploads/media/`

Supported image formats are JPG, PNG, WEBP and GIF. The application currently limits individual uploads to 10 MB.

## Homepage Builder

The Homepage Builder is available from **Website management** and the dashboard. It keeps homepage administration separate from the main sidebar to avoid clutter.

It manages:

- Homepage section copy and visibility
- Hero slide content
- Desktop and mobile slide images from the Media Library
- Calls to action
- Overlay opacity
- Slide visibility and display order


## Practice Area Detail Content

Each practice area now has its own database-driven detail content.

From **Website management → Practice Areas → Details**, administrators can manage:

- Overview copy through the existing Practice Area editor
- Key Contacts linked to Advocate records
- Individual Experience matters in display order
- Recent Insights linked to Article records
- Related Services linked to other Practice Areas

The public practice area page resolves all of these relationships from the database. Advocate names, roles, emails, article titles and practice area URLs are not duplicated in the page content.

After pulling this update, run:

`php database/migrate.php`
