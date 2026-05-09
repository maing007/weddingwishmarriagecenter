Database migrations (app/migrations)
====================================

When the app boots (index-bootstrap.php), pending *.php files here run automatically
in alphabetical order. Each file runs only once; completed names are stored in the
table `schema_migrations`.

Admin (after login): /admin/system/database-migrations
  — lists applied vs pending and can run pending migrations on demand.

File format
-----------
Each migration file must return an array of SQL strings:

  <?php
  return [
      "ALTER TABLE user_details ADD COLUMN example_note VARCHAR(100) NULL DEFAULT NULL",
  ];

Use an empty array if the file only documents a release:

  <?php
  return [];

Naming
------
Use a leading timestamp so order is clear, e.g.:
  20250515103000_add_user_details_note.php

Safety / data loss
------------------
- Prefer additive changes: new tables/columns, new indexes.
- Avoid DROP TABLE, DROP COLUMN, or destructive UPDATE without a verified backup.
- MySQL commits implicitly on many DDL statements; multi-step changes may need
  separate files or careful idempotent SQL (IF NOT EXISTS patterns where supported).
- Test on a copy of production data before deploying.

Disable auto-run (emergency)
----------------------------
Set environment variable: SKIP_DB_MIGRATIONS=1

Manual run
----------
  php app/migrations/run_cli.php

Concurrency
-----------
MySQL GET_LOCK is used so parallel web requests do not run migrations twice.
