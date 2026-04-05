<?php

declare(strict_types=1);

/**
 * Central upload storage: everything from forms goes under public/uploads/
 * so URLs /uploads/… match .htaccess → public/uploads/…
 */
if (!function_exists('app_public_uploads_dir')) {
    function app_public_uploads_dir(): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads';
    }
}

if (!function_exists('app_public_uploads_subdir')) {
    /**
     * Absolute path to public/uploads/{sub}, e.g. "blogs", "avatars", "tasks/images".
     */
    function app_public_uploads_subdir(string $sub): string
    {
        $sub = trim(str_replace('\\', '/', $sub), '/');
        $sub = preg_replace('#\.{2,}|//+#', '', $sub) ?? '';
        $sub = trim((string) $sub, '/');
        $base = app_public_uploads_dir();
        if ($sub === '') {
            return $base;
        }

        return $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $sub);
    }
}

if (!function_exists('app_save_upload')) {
    /**
     * Save an HTTP uploaded file under public/uploads/{subDir}.
     *
     * @param array{name?:string,tmp_name?:string,error?:int} $file Single $_FILES element
     * @param string $subDir Subpath under uploads (empty = files directly in uploads/)
     * @return string|null DB-friendly path: uploads/... (no leading slash)
     */
    function app_save_upload(array $file, string $subDir = ''): ?string
    {
        if (empty($file['tmp_name']) || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            return null;
        }

        $subDir = trim(str_replace('\\', '/', $subDir), '/');
        $subDir = preg_replace('#\.{2,}|//+#', '', $subDir) ?? '';
        $subDir = trim((string) $subDir, '/');

        $targetDir = app_public_uploads_subdir($subDir);
        if (!is_dir($targetDir) && !@mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
            return null;
        }

        $orig = (string) ($file['name'] ?? 'file');
        $base = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($orig));
        $base = $base !== '' ? $base : 'file.bin';
        $safeName = time() . '_' . $base;

        $dest = $targetDir . DIRECTORY_SEPARATOR . $safeName;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return null;
        }

        if ($subDir === '') {
            return 'uploads/' . $safeName;
        }

        return 'uploads/' . $subDir . '/' . $safeName;
    }
}
