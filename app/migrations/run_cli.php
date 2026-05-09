<?php

/**
 * Manual migration run (same logic as web bootstrap).
 *
 * Usage from project root:
 *   php app/migrations/run_cli.php
 */
declare(strict_types=1);

$appRoot = dirname(__DIR__, 2);
if (!is_file($appRoot . '/config/config.php')) {
    fwrite(STDERR, "config/config.php not found. Run from project root.\n");
    exit(1);
}

define('APP_ROOT', $appRoot);

require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/core/Database.php';
require_once APP_ROOT . '/app/core/Migrator.php';

$ok = Migrator::run(true);
if (!$ok) {
    fwrite(STDERR, "Migrations failed or database unreachable. See error_log.\n");
    exit(1);
}
echo "Migrations OK.\n";
exit(0);
