<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/DeferredMatchModel.php';
require_once __DIR__ . '/../models/Admin.php';

class AdminDeferredMatchesController
{
    protected $model;
    protected $admin;

    public function __construct()
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
        $this->admin = new Admin();
        $this->model = new DeferredMatchModel();
    }

    public function displayadminname(): string
    {
        $row = $this->admin->findById($_SESSION['admin_id']);

        return $row ? (string) $row['name'] : 'Admin';
    }

    public function index(): void
    {
        $deferredRows = $this->model->allRows();
        require __DIR__ . '/../views/admin/deferred_matches.php';
    }
}
