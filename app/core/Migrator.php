<?php

/**
 * Runs versioned SQL migrations from app/migrations/*.php on each deploy.
 *
 * Each file must return an array of SQL strings (can be empty). Files are run
 * in lexical order; each file runs at most once (recorded in schema_migrations).
 *
 * MySQL note: DDL (ALTER/CREATE TABLE) causes implicit commits. Prefer additive,
 * idempotent statements (new tables/columns/indexes) to avoid data loss.
 */
class Migrator
{
    private const LOCK_NAME = 'wedding_app_migrations';
    private const LOCK_TIMEOUT_SEC = 20;

    /**
     * @param bool $ignoreSkip If true, runs even when SKIP_DB_MIGRATIONS=1 (for CLI).
     * @return bool false if DB unreachable or a migration statement failed
     */
    public static function run(bool $ignoreSkip = false): bool
    {
        if (!$ignoreSkip && (!defined('RUN_DB_MIGRATIONS') || !RUN_DB_MIGRATIONS)) {
            return true;
        }
        if (!defined('APP_ROOT')) {
            return false;
        }

        try {
            $pdo = Database::getInstance()->pdo();
        } catch (Throwable $e) {
            error_log('Migrator: database unavailable — ' . $e->getMessage());

            return false;
        }

        $lockName = self::LOCK_NAME;
        $timeout = (int) self::LOCK_TIMEOUT_SEC;
        $lockStmt = $pdo->query("SELECT GET_LOCK('{$lockName}', {$timeout})");
        $got = $lockStmt ? (int) $lockStmt->fetchColumn() : 0;
        if ($got !== 1) {
            return true;
        }

        $ok = true;
        try {
            self::ensureMigrationsTable($pdo);
            $applied = self::appliedVersions($pdo);
            $dir = APP_ROOT . '/app/migrations';
            if (!is_dir($dir)) {
                return true;
            }

            $files = glob($dir . '/*.php') ?: [];
            sort($files, SORT_STRING);

            foreach ($files as $path) {
                $version = basename($path);
                if (!self::isRunnableMigrationFilename($version)) {
                    continue;
                }
                if (isset($applied[$version])) {
                    continue;
                }

                $sqlList = require $path;
                if (!is_array($sqlList)) {
                    error_log('Migrator: skip ' . $version . ' (must return array of SQL strings)');
                    continue;
                }

                foreach ($sqlList as $sql) {
                    $sql = trim((string) $sql);
                    if ($sql === '') {
                        continue;
                    }
                    try {
                        $pdo->exec($sql);
                    } catch (Throwable $e) {
                        error_log('Migrator: failed ' . $version . ' — ' . $e->getMessage());
                        throw $e;
                    }
                }

                $stmt = $pdo->prepare('INSERT INTO schema_migrations (`version`, `applied_at`) VALUES (?, NOW())');
                $stmt->execute([$version]);
                $applied[$version] = true;
            }
        } catch (Throwable $e) {
            error_log('Migrator: aborted — ' . $e->getMessage());
            $ok = false;
        } finally {
            try {
                $pdo->query("SELECT RELEASE_LOCK('{$lockName}')");
            } catch (Throwable $e) {
                // ignore
            }
        }

        return $ok;
    }

    public static function isRunnableMigrationFilename(string $basename): bool
    {
        if (substr($basename, -4) !== '.php') {
            return false;
        }
        if ($basename === 'run_cli.php') {
            return false;
        }

        return true;
    }

    /**
     * Status for admin UI: auto-run flag, DB connectivity, per-file applied state.
     *
     * @return array{auto_run: bool, db_ok: bool, error: ?string, migrations: list<array{file: string, applied: bool, applied_at: ?string}>}
     */
    public static function getStatus(): array
    {
        $out = [
            'auto_run' => defined('RUN_DB_MIGRATIONS') && RUN_DB_MIGRATIONS,
            'db_ok' => false,
            'error' => null,
            'migrations' => [],
        ];
        if (!defined('APP_ROOT')) {
            $out['error'] = 'APP_ROOT not defined';

            return $out;
        }

        try {
            $pdo = Database::getInstance()->pdo();
            self::ensureMigrationsTable($pdo);
            $out['db_ok'] = true;

            $stmt = $pdo->query('SELECT version, applied_at FROM schema_migrations ORDER BY applied_at ASC, version ASC');
            $appliedRows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            $byVersion = [];
            foreach ($appliedRows as $row) {
                $byVersion[(string) $row['version']] = (string) $row['applied_at'];
            }

            $dir = APP_ROOT . '/app/migrations';
            if (!is_dir($dir)) {
                return $out;
            }

            $files = glob($dir . '/*.php') ?: [];
            sort($files, SORT_STRING);
            foreach ($files as $path) {
                $v = basename($path);
                if (!self::isRunnableMigrationFilename($v)) {
                    continue;
                }
                $out['migrations'][] = [
                    'file' => $v,
                    'applied' => isset($byVersion[$v]),
                    'applied_at' => $byVersion[$v] ?? null,
                ];
            }
        } catch (Throwable $e) {
            $out['error'] = $e->getMessage();
        }

        return $out;
    }

    private static function ensureMigrationsTable(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS schema_migrations (
                version VARCHAR(255) NOT NULL,
                applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (version)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * @return array<string, true>
     */
    private static function appliedVersions(PDO $pdo): array
    {
        $stmt = $pdo->query('SELECT version FROM schema_migrations');
        $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
        $out = [];
        foreach ($rows as $v) {
            $out[(string) $v] = true;
        }

        return $out;
    }
}
