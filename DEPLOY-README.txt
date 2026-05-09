Deploy notes
============

Database migrations
-------------------
On each HTTP request the app runs pending SQL from app/migrations/*.php (see app/migrations/MIGRATIONS_README.txt).

After uploading new code, the first hit to the site applies any new migration files; no separate deploy script is required.

To disable auto-migrations (emergency): set environment variable SKIP_DB_MIGRATIONS=1

To run migrations from SSH:
  php app/migrations/run_cli.php

Admin UI (logged in):
  /admin/system/database-migrations
