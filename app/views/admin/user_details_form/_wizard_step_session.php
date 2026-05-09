<?php
/**
 * Repopulate wizard fields from $_SESSION['user_form'][$step].
 * Include after $currentStep is set (e.g. from _step_header.php).
 */
if (!function_exists('wz')) {
    function wz(string $step, string $key, $default = '')
    {
        $v = $_SESSION['user_form'][$step][$key] ?? null;
        if ($v === null) {
            return $default;
        }
        if (is_array($v)) {
            return $v;
        }

        return (string) $v;
    }
}

if (!function_exists('wz_sel')) {
    /** Echo selected="selected" when session value matches option. */
    function wz_sel(string $step, string $key, string $optionValue): string
    {
        $cur = wz($step, $key, '');
        if ($cur === '' && $optionValue === '') {
            return '';
        }

        return strcasecmp(trim((string) $cur), trim($optionValue)) === 0 ? ' selected' : '';
    }
}

if (!function_exists('wz_radio')) {
    function wz_radio(string $step, string $key, string $value): string
    {
        $cur = wz($step, $key, '');

        return strcasecmp(trim((string) $cur), trim($value)) === 0 ? ' checked' : '';
    }
}

if (!function_exists('wz_multi_contains')) {
    /** @param mixed $needle string or value compared loosely */
    function wz_multi_contains(string $step, string $key, $needle): bool
    {
        $raw = $_SESSION['user_form'][$step][$key] ?? [];
        if (!is_array($raw)) {
            $raw = $raw === '' || $raw === null ? [] : [(string) $raw];
        }
        foreach ($raw as $item) {
            if ((string) $item === (string) $needle) {
                return true;
            }
        }

        return false;
    }
}
