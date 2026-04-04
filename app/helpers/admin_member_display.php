<?php

/**
 * Shared formatting for admin member cards (users list, profile view upload summary).
 */
if (!function_exists('admin_member_na')) {
    function admin_member_na($v): string
    {
        $s = trim((string) ($v ?? ''));

        return ($s === '' || strcasecmp($s, 'N/A') === 0) ? 'N/A' : $s;
    }
}

if (!function_exists('admin_member_mobile_display')) {
    function admin_member_mobile_display(array $u): string
    {
        $m = trim((string) ($u['mobile_number'] ?? ''));
        if ($m !== '') {
            return $m;
        }

        return admin_member_na($u['phone'] ?? '');
    }
}

if (!function_exists('admin_member_birth_display')) {
    function admin_member_birth_display($dob): string
    {
        if ($dob === null || $dob === '') {
            return 'N/A';
        }
        $s = trim((string) $dob);
        if ($s === '' || $s === '0000-00-00' || strpos($s, '0000-00-00') === 0) {
            return 'N/A';
        }
        try {
            $dt = new DateTime($s);
            $age = (new DateTime('today'))->diff($dt)->y;

            return $dt->format('F j, Y') . ' (' . $age . ' Years)';
        } catch (Throwable $e) {
            return admin_member_na($s);
        }
    }
}

if (!function_exists('admin_member_datetime_display')) {
    function admin_member_datetime_display($raw): string
    {
        if ($raw === null) {
            return 'N/A';
        }
        $s = trim((string) $raw);
        if ($s === '' || strpos($s, '0000-00-00') === 0) {
            return 'N/A';
        }
        $t = strtotime($s);

        return $t ? date('F j, Y h:i A', $t) : 'N/A';
    }
}

if (!function_exists('admin_member_date_display')) {
    function admin_member_date_display($raw): string
    {
        if ($raw === null) {
            return 'N/A';
        }
        $s = trim((string) $raw);
        if ($s === '' || strpos($s, '0000-00-00') === 0) {
            return 'N/A';
        }
        $t = strtotime($s);

        return $t ? date('F j, Y', $t) : 'N/A';
    }
}

if (!function_exists('admin_member_partner_pdf_display')) {
    function admin_member_partner_pdf_display(array $u): string
    {
        $cv = trim((string) ($u['cv_file'] ?? ''));

        return $cv !== '' ? 'Yes' : 'No';
    }
}

if (!function_exists('admin_member_final_fee_display')) {
    function admin_member_final_fee_display($raw): string
    {
        if ($raw === null || $raw === '') {
            return '0';
        }
        if (is_numeric($raw)) {
            return (string) (0 + (float) $raw);
        }

        return admin_member_na($raw);
    }
}

if (!function_exists('admin_member_uuid_display')) {
    function admin_member_uuid_display(int $userId): string
    {
        return 'UU' . $userId;
    }
}

if (!function_exists('admin_member_added_by_display')) {
    function admin_member_added_by_display(array $u): string
    {
        $name = trim((string) ($u['added_by_name'] ?? ''));
        if ($name !== '') {
            return $name;
        }

        return 'N/A';
    }
}
