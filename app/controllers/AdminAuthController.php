<?php

    if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AdminAuthController extends Controller {
  
    private function ensureSessionStarted() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function loginForm() {
        require __DIR__.'/../views/admin/login.php';
    }

    public function login() {
        $this->ensureSessionStarted();
        require_once __DIR__.'/../models/Admin.php';
        $admin = new Admin();
        $row = $admin->findByEmail($_POST['email']);

        if ($row && password_verify($_POST['password'],$row['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $row['id'];
            header('Location: '.BASE_URL.'/admin/dashboard');
            exit;
        }
        die('Invalid login');
    }

    public function registerForm() {
        require __DIR__.'/../views/admin/register.php';
    }

    public function register() {
        require_once __DIR__.'/../models/Admin.php';
        (new Admin())->create($_POST['name'],$_POST['email'],$_POST['password']);
        header('Location: '.BASE_URL.'/admin/login');
    }

    public function logout() {
        $this->ensureSessionStarted();
        $_SESSION = [];
        session_destroy();
        header('Location: '.BASE_URL.'/admin/login');
    }
}
