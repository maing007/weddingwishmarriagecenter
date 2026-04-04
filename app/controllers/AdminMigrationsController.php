<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Admin.php';

class AdminMigrationsController
{
    public const INDEX_URL = '/admin/system/database-migrations';

    protected Admin $admin;

    public function __construct()
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
        $this->admin = new Admin();
    }

    public function displayadminname(): string
    {
        $row = $this->admin->findById($_SESSION['admin_id']);

        return $row ? (string) $row['name'] : 'Admin';
    }

    public function index(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $status = Migrator::getStatus();
        require __DIR__ . '/../views/admin/database_migrations.php';
    }

    public function runNow(): void
    {
        $redir = BASE_URL . self::INDEX_URL;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $redir);
            exit;
        }
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['flash_error'] = 'Invalid request token.';
            header('Location: ' . $redir);
            exit;
        }

        $ok = Migrator::run(true);
        if ($ok) {
            $_SESSION['flash_success'] = 'Migrations ran successfully (any pending steps were applied).';
        } else {
            $_SESSION['flash_error'] = 'Migration run failed or database unreachable. Check server error_log.';
        }
        header('Location: ' . $redir);
        exit;
    }
}
