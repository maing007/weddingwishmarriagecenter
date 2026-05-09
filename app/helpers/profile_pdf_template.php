<?php

declare(strict_types=1);

/**
 * Load image from disk (uploads/ or public/assets/) and return a data: URI for PDF/html2canvas.
 * Avoids broken public /uploads/ URLs on production and CORS/taint issues with canvas.
 */
function profile_pdf_image_data_uri_from_relative_path(string $relative): ?string
{
    if (!function_exists('admin_member_photo_public_absolute_path')) {
        require_once dirname(__DIR__) . '/helpers/public_url.php';
    }
    $rel = trim(str_replace('\\', '/', $relative));
    if ($rel === '' || preg_match('#^https?://#i', $rel)) {
        return null;
    }
    $abs = admin_member_photo_public_absolute_path($rel);
    if ($abs === null || !is_readable($abs)) {
        return null;
    }
    $raw = @file_get_contents($abs);
    if ($raw === false || $raw === '') {
        return null;
    }
    if (strlen($raw) > 6 * 1024 * 1024) {
        return null;
    }
    $mime = 'image/jpeg';
    if (function_exists('finfo_open')) {
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        if ($fi !== false) {
            $det = finfo_file($fi, $abs);
            finfo_close($fi);
            if (is_string($det) && strncmp($det, 'image/', 6) === 0) {
                $mime = $det;
            }
        }
    }

    return 'data:' . $mime . ';base64,' . base64_encode($raw);
}

/**
 * Parse DOB from user_details (date/datetime string, or DateTimeInterface). Rejects invalid MySQL zero-dates.
 */
function profile_pdf_template_parse_dob_immutable($dobRaw): ?DateTimeImmutable
{
    if ($dobRaw === null) {
        return null;
    }
    if ($dobRaw instanceof DateTimeInterface) {
        return DateTimeImmutable::createFromInterface($dobRaw);
    }

    $s = trim((string) $dobRaw);
    if ($s === '' || preg_match('/^0000-00-00/i', $s)) {
        return null;
    }

    if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $s, $m)) {
        $s = $m[1];
    }

    try {
        $d = new DateTimeImmutable($s);
        $y = (int) $d->format('Y');
        if ($y < 1900 || $y > (int) date('Y')) {
            return null;
        }

        return $d;
    } catch (Throwable $e) {
        // try common manual formats
    }

    $try = strlen($s) > 10 ? substr($s, 0, 10) : $s;
    foreach (['d/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d', 'Y-m-d'] as $fmt) {
        $d = DateTimeImmutable::createFromFormat('!' . $fmt, $try);
        if ($d !== false) {
            $y = (int) $d->format('Y');
            if ($y >= 1900 && $y <= (int) date('Y')) {
                return $d;
            }
        }
    }

    return null;
}

function profile_pdf_template_age_from_dob($dobRaw): string
{
    $dob = profile_pdf_template_parse_dob_immutable($dobRaw);
    if ($dob === null) {
        return '—';
    }
    $today = new DateTimeImmutable('today');
    if ($dob > $today) {
        return '—';
    }

    return (string) $dob->diff($today)->y;
}

/**
 * no_of_married_brother / no_of_married_sister are TEXT fields: either legacy integers or phrases
 * ("One married brother", …) from the admin dropdown — do not cast with (int).
 */
function profile_pdf_template_married_siblings_line(array $user): string
{
    $mbRaw = trim((string) ($user['no_of_married_brother'] ?? ''));
    $msRaw = trim((string) ($user['no_of_married_sister'] ?? ''));

    if ($mbRaw === '' && $msRaw === '') {
        return '—';
    }

    $brotherIsInt = $mbRaw !== '' && preg_match('/^-?\d+$/', $mbRaw) === 1;
    $sisterIsInt = $msRaw !== '' && preg_match('/^-?\d+$/', $msRaw) === 1;

    if ($brotherIsInt && $sisterIsInt) {
        $mb = (int) $mbRaw;
        $ms = (int) $msRaw;
        if ($mb === 0 && $ms === 0) {
            return 'No married brother, No married sister';
        }

        return $mb . ' married brother' . ($mb === 1 ? '' : 's') . ', ' . $ms . ' married sister' . ($ms === 1 ? '' : 's');
    }

    $parts = [];
    if ($mbRaw !== '') {
        $parts[] = $mbRaw;
    }
    if ($msRaw !== '') {
        $parts[] = $msRaw;
    }

    return $parts !== [] ? implode('; ', $parts) : '—';
}

/**
 * Shared variables for the Wedding Wish profile PDF / card UI (admin PDF preview + member feed).
 *
 * @param bool $preferAdminMemberPhotoProxy When true (admin PDF preview), use /admin/users/member-photo for local files (requires admin session). When false (public profile, member feed), use /upload/… or data: URIs only — guests cannot load the admin photo endpoint.
 *
 * @return array{
 *   fullName: string,
 *   matriDisplay: string,
 *   pdfFileTitle: string,
 *   profileImageUrl: string,
 *   ageStr: string,
 *   heightStr: string,
 *   houseLocation: string,
 *   houseSizeStr: string,
 *   ownership: string,
 *   siblingsLine: string,
 *   marriedSiblingsLine: string,
 *   siteUrl: string,
 *   copyrightYear: int,
 *   educationDisplay: string,
 *   workDetailDisplay: string
 * }
 */
function profile_pdf_template_compute_vars(array $user, bool $preferAdminMemberPhotoProxy = false): array
{
    if (!function_exists('matri_id_display')) {
        require_once __DIR__ . '/matri.php';
    }

    $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['second_name'] ?? $user['last_name'] ?? ''));
    if ($fullName === '') {
        $fullName = 'Member';
    }

    $matriRaw = matri_id_display((string) ($user['matri_id'] ?? ''), (int) ($user['id'] ?? 0), true);
    $matriDisplay = '(' . $matriRaw . ')';

    $pdfFileTitle = $matriDisplay . ' ' . $fullName;

    $ageStr = profile_pdf_template_age_from_dob($user['dob'] ?? null);

    if (!function_exists('admin_member_is_placeholder_photo_path')) {
        require_once dirname(__DIR__) . '/helpers/public_url.php';
    }

    $resolveMediaUrl = static function (string $path): string {
        return public_url_for_path($path);
    };

    /**
     * Admin PDF preview only: same as member list (streams file with admin session).
     * Public /profile/{id} and member feed must not use this URL — it redirects guests to login.
     */
    $profileImageUrl = '';
    $uid = (int) ($user['id'] ?? 0);
    if (!function_exists('admin_member_first_upload_relative_path')) {
        require_once dirname(__DIR__) . '/helpers/public_url.php';
    }
    $firstRel = admin_member_first_upload_relative_path($user);
    if ($preferAdminMemberPhotoProxy && $uid > 0 && $firstRel !== '') {
        $profileImageUrl = rtrim((string) (defined('BASE_URL') ? BASE_URL : ''), '/') . '/admin/users/member-photo?id=' . $uid;
    }

    $photoRaw = '';
    if ($profileImageUrl === '') {
        foreach (['photo2_url', 'photo1_status', 'photo3_url', 'photo4_url', 'photo5_url', 'photo6_url', 'avatar'] as $pk) {
            $p = trim((string) ($user[$pk] ?? ''));
            if ($p === '') {
                continue;
            }
            if (preg_match('#^https?://#i', $p)) {
                $photoRaw = $p;
                break;
            }
            if (admin_member_is_placeholder_photo_path($p)) {
                continue;
            }
            if (admin_member_is_photo_status_token($p)) {
                continue;
            }
            $photoRaw = $p;
            break;
        }
    }

    if ($profileImageUrl === '' && $photoRaw !== '' && preg_match('#^https?://#i', $photoRaw)) {
        $profileImageUrl = $photoRaw;
    } elseif ($profileImageUrl === '' && $photoRaw !== '') {
        $embedded = profile_pdf_image_data_uri_from_relative_path($photoRaw);
        $profileImageUrl = $embedded ?? $resolveMediaUrl($photoRaw);
    }
    if ($profileImageUrl === '') {
        $g = strtolower(trim((string) ($user['gender'] ?? '')));
        $defRel = ($g === 'female' || strncmp($g, 'female', 6) === 0)
            ? 'assets/images/female.png'
            : 'assets/images/male.png';
        $defData = profile_pdf_image_data_uri_from_relative_path($defRel);
        $profileImageUrl = $defData ?? $resolveMediaUrl($defRel);
    }

    $heightStr = trim((string) ($user['height'] ?? ''));
    if ($heightStr === '') {
        $heightStr = '—';
    }

    $houseLocParts = array_filter([
        trim((string) ($user['country'] ?? '')),
        trim((string) ($user['state'] ?? '')),
        trim((string) ($user['city'] ?? '')),
        trim((string) ($user['area'] ?? '')),
    ]);
    $houseLocation = $houseLocParts !== [] ? implode(', ', $houseLocParts) : '—';

    $marla = trim((string) ($user['house_size_marla'] ?? ''));
    $houseSizeStr = $marla !== '' ? $marla . ' (Marla)' : trim((string) ($user['house_size'] ?? ''));
    if ($houseSizeStr === '') {
        $houseSizeStr = '—';
    }

    $ownership = trim((string) ($user['residence'] ?? ''));
    if ($ownership === '') {
        $ownership = trim((string) ($user['house_type'] ?? ''));
    }
    if ($ownership === '') {
        $ownership = '—';
    }

    $b = (int) ($user['no_of_brothers'] ?? 0);
    $s = (int) ($user['no_of_sisters'] ?? 0);
    $siblingsLine = $b . ' brother' . ($b === 1 ? '' : 's') . ', ' . $s . ' sister' . ($s === 1 ? '' : 's');

    $marriedSiblingsLine = profile_pdf_template_married_siblings_line($user);

    $siteUrl = rtrim((string) (defined('BASE_URL') ? BASE_URL : ''), '/');
    $copyrightYear = (int) date('Y');

    $educationDisplay = profile_pdf_template_format_scalar_or_json_list($user['education'] ?? null);
    if ($educationDisplay === '') {
        $educationDisplay = '—';
    }

    $workParts = array_values(array_filter([
        trim((string) ($user['occupation'] ?? '')),
        trim((string) ($user['designation'] ?? '')),
        trim((string) ($user['employed_in'] ?? '')),
    ], static function ($v) {
        return $v !== '';
    }));
    $workDetail = trim((string) ($user['work_detail'] ?? ''));
    if ($workDetail !== '') {
        $workDetailDisplay = ($workParts !== [] ? implode(' — ', $workParts) . ' — ' : '') . $workDetail;
    } else {
        $workDetailDisplay = $workParts !== [] ? implode(' — ', $workParts) : '—';
    }

    return [
        'fullName' => $fullName,
        'matriDisplay' => $matriDisplay,
        'pdfFileTitle' => $pdfFileTitle,
        'profileImageUrl' => $profileImageUrl,
        'ageStr' => $ageStr,
        'heightStr' => $heightStr,
        'houseLocation' => $houseLocation,
        'houseSizeStr' => $houseSizeStr,
        'ownership' => $ownership,
        'siblingsLine' => $siblingsLine,
        'marriedSiblingsLine' => $marriedSiblingsLine,
        'siteUrl' => $siteUrl,
        'copyrightYear' => $copyrightYear,
        'educationDisplay' => $educationDisplay,
        'workDetailDisplay' => $workDetailDisplay,
    ];
}

/**
 * Match admin profile view: plain string, or JSON array / CSV list → comma-separated text.
 */
function profile_pdf_template_format_scalar_or_json_list($raw): string
{
    if ($raw === null) {
        return '';
    }
    if (is_array($raw)) {
        $flat = array_values(array_filter(array_map('trim', array_map('strval', $raw)), static function ($v) {
            return $v !== '';
        }));

        return $flat !== [] ? implode(', ', $flat) : '';
    }
    $s = trim((string) $raw);
    if ($s === '') {
        return '';
    }
    if ($s[0] === '[' || $s[0] === '{') {
        $decoded = json_decode($s, true);
        if (is_array($decoded)) {
            $flat = array_values(array_filter(array_map(static function ($item) {
                return trim((string) $item);
            }, $decoded), static function ($v) {
                return $v !== '';
            }));

            return $flat !== [] ? implode(', ', $flat) : '';
        }
    }

    return $s;
}
