# Webi Wenani & Associates Advocates

A custom PHP and MySQL website and content management system for a Kenyan law firm.

## Requirements

- PHP 8.2+
- MySQL 8+
- PDO MySQL extension
- PHP GD with WebP support recommended for automatic image optimization

## Local setup

1. Copy .env.example to .env and update database values.
2. Create the database configured in .env.
3. Run migrations:

   php database/migrate.php

4. Point your web server document root to public_html/.
5. Run the built-in PHP server:

   php -S 127.0.0.1:8000 -t public_html public_html/index.php

Open the site at http://lawfirm.test:8000 and the administration area at http://lawfirm.test:8000/admin.

## Database migrations

Migration files live only in database/migrations/. The runner records completed migrations in the migrations table and only applies pending files.

After pulling updates, run:

php database/migrate.php

## Media performance

New JPG, PNG and WebP uploads are automatically optimized to WebP when PHP GD supports WebP. GIF files are preserved so animated GIFs are not broken.

To optimize existing media already stored in the database, run once:

php database/optimize_media.php

The media checksum migration merges duplicate database media records, rewires existing foreign-key references to the canonical media item, and removes duplicate files after a successful database transaction.

The PHP process must be able to write to:

public_html/uploads/media/

Supported uploads are JPG, PNG, WEBP and GIF. The application limits individual source uploads to 10 MB.

## Performance cache

Public homepage and shared site layout data are cached in storage/cache/ for ten minutes. Public cache entries are cleared automatically after CMS, homepage, practice-area-detail or media changes.

## Design system

The active design is controlled by config/design/active.ini. It contains semantic brand, text, surface, layout, border and spacing tokens.

app/View/StyleLinker.php converts those values into CSS custom properties and loads the active versioned stylesheet manifest from public_html/css/<version>/.

## Structure

- app/ application code
- config/ configuration
- database/migrations/ database migrations
- public_html/ web root
- resources/ views and reusable components
- storage/ runtime files

## Administration

The administration area is intentionally excluded from public indexing and is available under /admin.

The Media Library is available at /admin/media. Images are validated server-side, stored under public_html/uploads/media/ and recorded in the media table.

Media deletion is protected by database foreign-key usage checks, so images still referenced by advocates, articles or homepage slides cannot be removed.

## Homepage Builder

The Homepage Builder is available from Website management and the dashboard.

It manages:

- Homepage section copy and visibility
- Hero slide content
- Desktop and mobile slide images from the Media Library
- Calls to action
- Overlay opacity
- Slide visibility and display order

## Practice Area Detail Content

Each practice area has database-driven detail content.

From Website management → Practice Areas → Details, administrators can manage:

- Overview copy through the Practice Area rich-text editor
- Key Contacts linked to Advocate records, including their managed profile photos
- Individual Experience matters in display order
- Recent Insights linked to Article records
- Related Services linked to other Practice Areas

The public practice area page resolves these relationships from the database rather than duplicating advocate, article or practice-area data in page content.
