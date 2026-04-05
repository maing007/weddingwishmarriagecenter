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
