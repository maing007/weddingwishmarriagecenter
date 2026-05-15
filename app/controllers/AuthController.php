<?php
// app/controllers/AuthController.php

class AuthController
{
    protected $userModel;

    public function __construct()
    {
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Load User model
        require_once __DIR__ . '/../models/User.php';
        $this->userModel = new User();

        // Simple CSRF token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /* ================= REGISTRATION ================= */

    // POST /register-user
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        // Required fields from your home page form
        $required = [
            'firstname',
            'lastname',
            'email',
            'password',
            'birth_date',
            'birth_month',
            'birth_year',
            'religion',
            'gender'
        ];

        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = 'Please fill all required fields.';
                header('Location: ' . BASE_URL . '/');
                exit;
            }
        }

        // Terms checkbox
        if (empty($_POST['terms']) || $_POST['terms'] !== 'Yes') {
            $_SESSION['error'] = 'You must agree to Terms and Conditions.';
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        // DOB
        $dob = sprintf(
            '%04d-%02d-%02d',
            (int)$_POST['birth_year'],
            (int)$_POST['birth_month'],
            (int)$_POST['birth_date']
        );

        // Phone & country code (intl-tel-input fills mobile_number + country_code on submit)
        $phone = trim(
            (string)(
                $_POST['mobile_number']
                ?? $_POST['phone']
                ?? $_POST['phone_input']
                ?? ''
            )
        );
        $country_code = trim((string)($_POST['country_code'] ?? ''));

        $religionRaw = (string)($_POST['religion'] ?? '');
        $religionMap = [
            '53' => 'Islam',
            '54' => 'Sikh',
            '52' => 'Hindu',
            '51' => 'Christian',
            '47' => 'Qadiyani',
        ];
        $religion = $religionMap[$religionRaw] ?? $religionRaw;

        if ($phone === '' || strlen(preg_replace('/\D/', '', $phone)) < 7) {
            $_SESSION['error'] = 'Please enter a valid phone number.';
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        // Duplicate email check
        if ($this->userModel->findByEmail(trim($_POST['email']))) {
            $_SESSION['error'] = 'Email is already registered.';
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        // Data for DB
        $data = [
            'gender'        => $_POST['gender'],
            'first_name'    => trim($_POST['firstname']),
            'second_name'   => trim($_POST['lastname']),
            'phone'         => trim($phone),
            'country_code'  => trim($country_code),
            'email'         => trim($_POST['email']),
            'password'      => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'dob'           => $dob,
            'religion'      => $religion,
        ];

        if ($this->userModel->modelone($data)) {
            $_SESSION['flash_success'] = 'Registration successful! You can now log in.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        } else {
            $_SESSION['error'] = 'Something went wrong. Please try again.';
            header('Location: ' . BASE_URL . '/');
            exit;
        }
    }

    /* ================= LOGIN FORM ================= */

    // GET /login
    public function showLoginForm()
    {
        $title = 'Login';

        // Flash messages
        $error   = $_SESSION['flash_error']   ?? '';
        $success = $_SESSION['flash_success'] ?? '';
        $old     = $_SESSION['flash_old']     ?? [];

        // Clear flash
        unset($_SESSION['flash_error'], $_SESSION['flash_success'], $_SESSION['flash_old']);

        // Pass to view if needed
        $csrfToken = $_SESSION['csrf_token'];

        // Load header + view + footer
        require __DIR__ . '/../views/partials/header.php';
        require __DIR__ . '/../views/home/frontend/login.php';
        require __DIR__ . '/../views/partials/footer.php';
    }

    /* ================= LOGIN SUBMIT ================= */

    // POST /login
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // CSRF check
        if (
            empty($_POST['csrf_token']) ||
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            $_SESSION['flash_error'] = 'Invalid form token. Please try again.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        require_once __DIR__ . '/../helpers/cloudflare_security.php';
        if (CLOUDFLARE_TURNSTILE_SECRET_KEY !== ''
            && !app_cloudflare_turnstile_verify($_POST['cf-turnstile-response'] ?? null)) {
            $_SESSION['flash_error'] = 'Security check failed. Please try again.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $_SESSION['flash_error'] = 'Please fill all required fields.';
            $_SESSION['flash_old']   = ['username' => $username];
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Email OR matri id
        $user = $this->userModel->findByEmailOrMatriId($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash_error'] = 'Invalid username or password.';
            $_SESSION['flash_old']   = ['username' => $username];
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Success – set session & go to dashboard
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['first_name'];
        $_SESSION['logged_in'] = true;

        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    /* ================= LOGOUT ================= */

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();

        header('Location: ' . BASE_URL . '/');
        exit;
    }
}
