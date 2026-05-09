<?php
/**
 * Cloudflare-aware security: headers, real client IP, optional Turnstile verification,
 * optional origin lock (traffic must come through Cloudflare — set CLOUDFLARE_ENFORCE=1 in production).
 */

/**
 * Send baseline security headers (safe behind Cloudflare or direct).
 */
function app_send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
        && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
        $https = true;
    }
    if ($https) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

/**
 * Best-effort client IP when behind Cloudflare or other proxies.
 */
function app_client_ip(): string
{
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return trim((string) $_SERVER['HTTP_CF_CONNECTING_IP']);
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', (string) $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($parts[0]);
    }
    return trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));
}

/**
 * If CLOUDFLARE_ENFORCE=1, block requests that do not appear to come through Cloudflare (no CF-Ray).
 * Enable only when the site is exclusively served via Cloudflare (not for local XAMPP).
 */
function app_cloudflare_enforce_origin(): void
{
    if (getenv('CLOUDFLARE_ENFORCE') !== '1' || php_sapi_name() === 'cli') {
        return;
    }
    if (!empty($_SERVER['HTTP_CF_RAY'])) {
        return;
    }
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return;
    }
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Forbidden';
    exit;
}

/**
 * Verify Cloudflare Turnstile token (POST field cf-turnstile-response).
 */
function app_cloudflare_turnstile_verify(?string $token): bool
{
    $secret = defined('CLOUDFLARE_TURNSTILE_SECRET_KEY') ? CLOUDFLARE_TURNSTILE_SECRET_KEY : '';
    if ($secret === '') {
        return true;
    }
    $token = trim((string) $token);
    if ($token === '') {
        return false;
    }
    $payload = http_build_query([
        'secret'   => $secret,
        'response' => $token,
        'remoteip' => app_client_ip(),
    ], '', '&');
    $ctx = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $payload,
            'timeout' => 10,
        ],
    ]);
    $raw = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $ctx);
    if ($raw === false) {
        return false;
    }
    $json = json_decode($raw, true);
    return is_array($json) && !empty($json['success']);
}
