<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AdminUserDetailsController
{
    private $model;
    protected $admin_details;
    protected $admin;
    public function __construct()
    {
        require_once __DIR__ . '/../models/UserDetails.php';
        require_once __DIR__ . '/../models/Admin.php';
        $this->admin = new Admin();
        $this->model = new UserDetails();
    }
    public function displayadminname()
    {
        $admin = $this->admin->findById($_SESSION['admin_id']);
        return $admin ? $admin['name'] : 'Admin';
        require __DIR__ . '/../views/admin/partials/header.php';
    }

    /**
     * Validate basic-details payload. Returns null if OK, or a human-readable error string.
     */
    private function validateBasicPayload(array $data): ?string
    {
        $lead = trim((string) ($data['lead'] ?? ''));
        if ($lead === '') {
            return 'Please select a lead.';
        }
        $gender = trim((string) ($data['gender'] ?? ''));
        if ($gender === '') {
            return 'Please select gender.';
        }
        $first = trim((string) ($data['first_name'] ?? ''));
        if ($first === '') {
            return 'Please enter first name.';
        }
        $last = trim((string) ($data['second_name'] ?? ''));
        if ($last === '') {
            return 'Please enter last name.';
        }
        $email = trim((string) ($data['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Please enter a valid email address.';
        }
        $password = (string) ($data['password'] ?? '');
        if (trim($password) === '') {
            return 'Please enter a password.';
        }
        if (strlen($password) < 6) {
            return 'Password must be at least 6 characters.';
        }
        $mobile = trim((string) ($data['mobile_number'] ?? ''));
        if ($mobile === '') {
            return 'Please enter mobile number.';
        }

        return null;
    }

    /** True when basic step was saved and still passes validation (session may be stale). */
    private function isBasicStepComplete(): bool
    {
        $basic = $_SESSION['user_form']['basic'] ?? null;
        if (!is_array($basic) || $basic === []) {
            return false;
        }

        return $this->validateBasicPayload($basic) === null;
    }

    /** Block later wizard steps until basic details are completed. */
    private function requireBasicComplete(): void
    {
        if ($this->isBasicStepComplete()) {
            return;
        }
        $_SESSION['flash_error'] = 'Please complete the Basic Details form first.';
        header('Location: ' . BASE_URL . '/admin/add_user/user/basic');
        exit;
    }

    /* ===============================
       1. BASIC DETAILS SUBMIT
    =============================== */
    public function saveBasicDetails()
    {
        $error = $this->validateBasicPayload($_POST);
        if ($error !== null) {
            $_SESSION['flash_error'] = $error;
            header('Location: ' . BASE_URL . '/admin/add_user/user/basic');
            exit;
        }

        $_SESSION['user_form']['basic'] = $_POST;

        header('Location: ' . BASE_URL . '/admin/add_user/user/residence');
        exit;
    }

    /* ===============================
       2. RESIDENCE
    =============================== */
    public function saveResidence()
    {
        $this->requireBasicComplete();
        $_SESSION['user_form']['residence'] = $_POST;


        header('Location: ' . BASE_URL . '/admin/add_user/user/physical');
        exit;
    }

    /* ===============================
       3. PHYSICAL INFO
    =============================== */
    public function savePhysical()
    {
        $this->requireBasicComplete();
        $_SESSION['user_form']['physical'] = $_POST;

        header('Location: ' . BASE_URL . '/admin/add_user/user/other');
        exit;
    }

    /* ===============================
       4. OTHER INFO
    =============================== */
    public function saveOtherInfo()
    {
        $this->requireBasicComplete();
        $_SESSION['user_form']['other'] = $_POST;

        header('Location: ' . BASE_URL . '/admin/add_user/user/partner');
        exit;
    }

    /* ===============================
       5. PARTNER PREFERENCE
    =============================== */
    public function savePartner()
    {
        $this->requireBasicComplete();
        $_SESSION['user_form']['partner'] = $_POST;

        header('Location: ' . BASE_URL . '/admin/add_user/user/upload');
        exit;
    }

    /* ===============================
       FILE UPLOAD → public/uploads/… (app_save_upload)
    =============================== */
    private function uploadFile($file, $folder = 'uploads/')
    {
        $folder = trim(str_replace('\\', '/', $folder), '/');
        if ($folder === 'uploads' || $folder === '') {
            $sub = '';
        } elseif (str_starts_with($folder, 'uploads/')) {
            $sub = substr($folder, strlen('uploads/'));
        } else {
            $sub = $folder;
        }

        return app_save_upload($file, $sub);
    }

    /* ===============================
       6. FILE UPLOAD + FINAL SUBMIT
    =============================== */
    public function submitAll()
    {
        $this->requireBasicComplete();

        $model = new UserDetails();

        $data = array_merge(
            $_SESSION['user_form']['basic'] ?? [],
            $_SESSION['user_form']['residence'] ?? [],
            $_SESSION['user_form']['physical'] ?? [],
            $_SESSION['user_form']['other'] ?? [],
            $_SESSION['user_form']['partner'] ?? []
        );
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        /* ===============================
           HANDLE FILES
        =============================== */
        $data['photo1_status'] = $this->uploadFile($_FILES['photo1_status']);
        $data['photo2_url'] = $this->uploadFile($_FILES['photo2_url']);
        $data['photo3_url'] = $this->uploadFile($_FILES['photo3_url']);
        $data['photo4_url'] = $this->uploadFile($_FILES['photo4_url']);
        $data['photo5_url'] = $this->uploadFile($_FILES['photo5_url']);
        $data['photo6_url'] = $this->uploadFile($_FILES['photo6_url']);
        $data['id_proof_file'] = $this->uploadFile($_FILES['id_proof_file']);
        $data['cv_file'] = $this->uploadFile($_FILES['cv_file']);

        /* ===============================
           SAVE TO DATABASE
        =============================== */
        $newId = $model->create($data);
        if ($newId) {
            require_once __DIR__ . '/../models/MemberSaleFeeModel.php';
            $feeModel = new MemberSaleFeeModel();
            $feeModel->syncRegistrationSaleRowFromUserDetails((int) $newId);
            $feeModel->syncRishtaSaleRowFromUserDetails((int) $newId);
        }

        /* ===============================
           CLEAR SESSION
        =============================== */
        unset($_SESSION['user_form']);

        header('Location: ' . BASE_URL . '/admin/dashboard');
        exit;
    }
    /* ===============================
   LOAD FORMS (GET METHODS)
=============================== */

    // 1. Basic
    public function basicForm()
    {
        $admin_details = $this->admin->get_admin_details();
        require __DIR__ . '/../views/admin/user_details_form/basic_details_form.php';
    }

    // 2. Residence
    public function residenceForm()
    {
        $this->requireBasicComplete();
        require __DIR__ . '/../views/admin/user_details_form/residence_form.php';
    }

    // 3. Physical
    public function physicalForm()
    {
        $this->requireBasicComplete();
        require __DIR__ . '/../views/admin/user_details_form/physical_form.php';
    }

    // 4. Other
    public function otherForm()
    {
        $this->requireBasicComplete();
        require __DIR__ . '/../views/admin/user_details_form/other_info.php';
    }

    // 5. Partner
    public function partnerForm()
    {
        $this->requireBasicComplete();
        require __DIR__ . '/../views/admin/user_details_form/partner_form.php';
    }

    // 6. Upload
    public function uploadForm()
    {
        $this->requireBasicComplete();
        require __DIR__ . '/../views/admin/user_details_form/upload_form.php';
    }
}
