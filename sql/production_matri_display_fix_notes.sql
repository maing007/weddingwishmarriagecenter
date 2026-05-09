-- =============================================================================
-- Production fix: matri_id_display() undefined (PHP)
-- =============================================================================
-- No database schema or data changes are required for this fix.
--
-- Deploy these files to the server:
--   - app/helpers/matri.php          (new)
--   - index-bootstrap.php            (must include require matri.php)
--   - config/config.php              (optional but recommended: includes matri.php)
--
-- The error happened when views called matri_id_display() but an older
-- config/config.php on the server did not define that function. Loading
-- app/helpers/matri.php from index-bootstrap.php fixes it regardless.
-- =============================================================================

-- Optional: only if you use the in-app migration runner and the table is missing
-- (normally it is created automatically on first request).

CREATE TABLE IF NOT EXISTS schema_migrations (
    version VARCHAR(255) NOT NULL,
    applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
