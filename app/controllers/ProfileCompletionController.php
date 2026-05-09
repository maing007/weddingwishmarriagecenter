<?php

class ProfileCompletionController extends Controller
{
    protected $profileModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 🔒 AUTH GUARD (same style as DashboardController)
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        require_once __DIR__ . '/../models/UserProfile.php';
        $this->profileModel = new UserProfile();
    }

    /* ==========================
       SHOW PROFILE COMPLETION FORM
    ========================== */
    public function create()
    {
        $userId = (int)$_SESSION['user_id'];

        // If already completed → dashboard
        $existing = $this->profileModel->getByUserId($userId);
        if ($existing) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $title = 'Complete Your Profile';
        $error = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_error']);

        require __DIR__ . '/../views/dashboard/profile_complete.php';
    }

    /* ==========================
       SAVE PROFILE COMPLETION
    ========================== */
    public function store()
    {
        $userId = (int)$_SESSION['user_id'];

        // Basic sanitization
        $data = [
            'user_id'            => $userId,
            'education'          => trim($_POST['education'] ?? ''),
            'occupation'         => trim($_POST['occupation'] ?? ''),
            'annual_income'      => trim($_POST['annual_income'] ?? ''),
            'eating_habits'       => trim($_POST['eating_habits'] ?? ''),
            'drinking'            => trim($_POST['drinking'] ?? ''),
            'smoking'             => trim($_POST['smoking'] ?? ''),
            'appearance'          => trim($_POST['appearance'] ?? ''),
            'complexion'          => trim($_POST['complexion'] ?? ''),
            'body_type'           => trim($_POST['body_type'] ?? ''),
            'horoscope_details'   => trim($_POST['horoscope_details'] ?? ''),
            'cast'                => trim($_POST['cast'] ?? ''),
            'height'              => trim($_POST['height'] ?? ''),
            'mother_tongue'       => trim($_POST['mother_tongue'] ?? ''),
        ];

        // OPTIONAL: minimal validation
        if ($data['education'] === '' || $data['occupation'] === '') {
            $_SESSION['flash_error'] = 'Education and Occupation are required.';
            header('Location: ' . BASE_URL . '/dashboard/profile-complete');
            exit;
        }

        $this->profileModel->saveOrUpdate($data);

        $_SESSION['flash_success'] = 'Profile completed successfully.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }
}
