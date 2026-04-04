<?php

/**
 * Matri ID prefixes and display helper — loaded from index-bootstrap.php on every request
 * so views work even if config/config.php on the server is an older copy.
 */
if (!defined('MATRI_ID_PREFIX')) {
    define('MATRI_ID_PREFIX', 'WW');
}
if (!defined('MATRI_ID_PREFIX_LEGACY')) {
    define('MATRI_ID_PREFIX_LEGACY', 'NG');
}

if (!function_exists('matri_id_display')) {
    /**
     * Normalize matri id for display: legacy NG… → WW…; empty + user id → synthetic WW id.
     */
    function matri_id_display(?string $stored, int $fallbackUserId = 0, bool $padFallbackTo5 = false): string
    {
        $s = trim((string) ($stored ?? ''));
        $pfx = MATRI_ID_PREFIX;
        $leg = MATRI_ID_PREFIX_LEGACY;
        if ($s === '') {
            if ($fallbackUserId <= 0) {
                return '';
            }

            return $padFallbackTo5
                ? $pfx . str_pad((string) $fallbackUserId, 5, '0', STR_PAD_LEFT)
                : $pfx . $fallbackUserId;
        }
        if (stripos($s, $pfx) === 0) {
            return $s;
        }
        if (stripos($s, $leg) === 0) {
            return $pfx . preg_replace('/^' . preg_quote($leg, '/') . '/i', '', $s);
        }

        return $s;
    }
}
