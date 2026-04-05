<?php
// Basic configuration for XAMPP / MySQL

define('DB_HOST', 'localhost');
define('DB_NAME', 'usmanv'); // Create this DB in phpMyAdmin
define('DB_USER', 'root');            // Default XAMPP user
define('DB_PASS', '');                // Default XAMPP password (empty)

// Public site URL (no trailing slash). Override with APP_BASE_URL in the environment.
// If unset, it is derived from the current request so both document-root setups work:
//   - http://localhost/              (with DocumentRoot = public/)
//   - http://localhost/wedding/public/
$appBase = getenv('APP_BASE_URL');
if ($appBase !== false && $appBase !== '') {
    define('BASE_URL', rtrim($appBase, '/'));
} elseif (php_sapi_name() !== 'cli' && !empty($_SERVER['HTTP_HOST'])) {
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
        && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
        $https = true;
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && (string) $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
        $https = true;
    }
    $scheme = $https ? 'https' : 'http';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $pathPrefix = ($dir === '' || $dir === '/') ? '' : $dir;
    define('BASE_URL', $scheme . '://' . $_SERVER['HTTP_HOST'] . $pathPrefix);
} else {
    define('BASE_URL', 'http://localhost/wedding/public');
}

define('VIEW_PATH', dirname(__DIR__) . '/app/views/');
define('ASSETS_URL', dirname(__DIR__) . '/public/assets/');

/**
 * Auto-run app/migrations/*.php on boot. Set env SKIP_DB_MIGRATIONS=1 to disable (emergency only).
 */
if (!defined('RUN_DB_MIGRATIONS')) {
    define('RUN_DB_MIGRATIONS', getenv('SKIP_DB_MIGRATIONS') !== '1');
}

// Matri helpers (also loaded from index-bootstrap.php after this file for older production configs).
require_once dirname(__DIR__) . '/app/helpers/matri.php';
// Upload / asset URL builder (uses BASE_URL).
require_once dirname(__DIR__) . '/app/helpers/public_url.php';
// All form uploads → public/uploads/ (see app/helpers/upload_storage.php).
require_once dirname(__DIR__) . '/app/helpers/upload_storage.php';
