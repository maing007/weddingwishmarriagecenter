<?php

/**
 * Build an absolute public URL for a stored file path (uploads, assets, etc.).
 * Handles production issues: missing slash between BASE_URL and path, absolute URLs,
 * protocol-relative URLs, backslashes, and accidental "public/" prefixes in DB values.
 */
if (!function_exists('public_url_for_path')) {
    function public_url_for_path(?string $path): string
    {
        if ($path === null) {
            return '';
        }
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        $norm = str_replace('\\', '/', $path);

        if (preg_match('#^https?://#i', $norm)) {
            return $norm;
        }

        if (strncmp($norm, '//', 2) === 0) {
            $https = false;
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                $https = true;
            }
            if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
                && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
                $https = true;
            }
            $scheme = $https ? 'https' : 'http';

            return $scheme . ':' . $norm;
        }

        if (stripos($norm, 'public/') === 0) {
            $norm = substr($norm, 7);
        } elseif (stripos($norm, '/public/') === 0) {
            $norm = substr($norm, 8);
        }

        $norm = ltrim($norm, '/');
        if (!defined('BASE_URL')) {
            return '/' . $norm;
        }
        $base = rtrim((string) BASE_URL, '/');

        return $base === '' ? '/' . $norm : $base . '/' . $norm;
    }
}

/**
 * Gender default avatar URL (for img onerror fallback when a stored photo URL fails).
 */
if (!function_exists('admin_user_default_avatar_url')) {
    function admin_user_default_avatar_url(array $u): string
    {
        $g = strtolower(trim((string) ($u['gender'] ?? '')));
        if ($g === 'female' || strncmp($g, 'female', 6) === 0) {
            return public_url_for_path('assets/images/female.png');
        }

        return public_url_for_path('assets/images/male.png');
    }
}

/**
 * Absolute photo URL for admin member cards (avatar column + gender defaults).
 */
if (!function_exists('admin_user_card_photo_url')) {
    function admin_user_card_photo_url(array $u): string
    {
        $p = trim((string) ($u['avatar'] ?? ''));
        $usable = $p !== '';
        if ($usable) {
            $norm = strtolower(str_replace('\\', '/', $p));
            if (strpos($norm, 'uploads/avatars/default') !== false
                || strpos($norm, 'default-avatar') !== false
                || strpos($norm, 'avatar-placeholder') !== false) {
                $usable = false;
            }
        }
        if ($usable) {
            return public_url_for_path($p);
        }

        return admin_user_default_avatar_url($u);
    }
}

/** @internal */
if (!function_exists('admin_member_is_placeholder_photo_path')) {
    function admin_member_is_placeholder_photo_path(string $path): bool
    {
        if ($path === '') {
            return true;
        }
        $norm = strtolower(str_replace('\\', '/', $path));

        return strpos($norm, 'uploads/avatars/default') !== false
            || strpos($norm, 'default-avatar') !== false
            || strpos($norm, 'avatar-placeholder') !== false
            || strpos($norm, 'assets/images/male') !== false
            || strpos($norm, 'assets/images/female') !== false;
    }
}

/**
 * Plain-text status values sometimes stored in photo1_status (not a file path).
 */
if (!function_exists('admin_member_is_photo_status_token')) {
    function admin_member_is_photo_status_token(string $path): bool
    {
        $t = strtolower(trim($path));

        return in_array($t, ['approved', 'pending', 'rejected', 'declined', 'none', 'no', 'yes'], true);
    }
}

/**
 * First member-upload path for downloads, same field order as list cards (photo1 … photo6), then avatar.
 */
if (!function_exists('admin_member_first_upload_relative_path')) {
    function admin_member_first_upload_relative_path(array $u): string
    {
        foreach (['photo1_status', 'photo2_url', 'photo3_url', 'photo4_url', 'photo5_url', 'photo6_url'] as $k) {
            if (!array_key_exists($k, $u)) {
                continue;
            }
            $p = trim((string) ($u[$k] ?? ''));
            if ($p === '' || preg_match('#^https?://#i', $p) || admin_member_is_placeholder_photo_path($p)) {
                continue;
            }
            if (admin_member_is_photo_status_token($p)) {
                continue;
            }
            return $p;
        }

        $av = trim((string) ($u['avatar'] ?? ''));
        if ($av !== '' && !preg_match('#^https?://#i', $av) && !admin_member_is_placeholder_photo_path($av)) {
            return $av;
        }

        return '';
    }
}

/**
 * Resolve a DB-stored path to a file under /public (blocks traversal and remote URLs).
 */
if (!function_exists('admin_member_photo_public_absolute_path')) {
    /**
     * Resolve a DB path like uploads/… to a real file.
     * Tries APP_ROOT/public/… (repo layout) then APP_ROOT/… (document root = project folder on shared hosting).
     */
    function admin_member_photo_public_absolute_path(string $relative): ?string
    {
        if (!defined('APP_ROOT')) {
            return null;
        }
        $norm = str_replace('\\', '/', trim($relative));
        if ($norm === '' || preg_match('#^https?://#i', $norm)) {
            return null;
        }
        if (strpos($norm, '..') !== false) {
            return null;
        }
        if (stripos($norm, 'public/') === 0) {
            $norm = substr($norm, 7);
        }
        $norm = ltrim($norm, '/');

        $root = rtrim(str_replace('\\', '/', (string) APP_ROOT), '/');
        $rootReal = realpath($root);
        if ($rootReal === false) {
            return null;
        }

        $candidates = [
            $root . '/public/' . $norm,
            $root . '/' . $norm,
        ];

        foreach ($candidates as $try) {
            $full = realpath($try);
            if ($full === false || !is_file($full)) {
                continue;
            }
            if (strpos($full, $rootReal) !== 0) {
                continue;
            }
            $fullNorm = str_replace('\\', '/', $full);
            if (!preg_match('#(^|/)(uploads|assets)(/)#', $fullNorm)) {
                continue;
            }

            return $full;
        }

        return null;
    }
}
