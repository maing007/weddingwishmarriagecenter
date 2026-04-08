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

    private function suggestedAboutUsFromWizard(array $basic, array $residence, array $physical): string
    {
        $first = trim((string) ($basic['first_name'] ?? ''));
        $last = trim((string) ($basic['second_name'] ?? ''));
        $g = trim((string) ($basic['gender'] ?? ''));
        $dob = trim((string) ($basic['dob'] ?? ''));
        $rel = trim((string) ($basic['religion'] ?? ''));
        $caste = trim((string) ($basic['caste'] ?? ''));
        $edu = trim((string) ($basic['education'] ?? ''));
        $job = trim((string) ($basic['occupation'] ?? ''));
        $city = trim((string) ($residence['city'] ?? ''));
        $country = trim((string) ($residence['country'] ?? ''));
        $ht = trim((string) ($physical['height'] ?? ''));
        $wt = trim((string) ($physical['weight'] ?? ''));

        $lines = [];
        $name = trim($first . ' ' . $last);
        if ($name !== '') {
            $lines[] = 'I am ' . $name . ($g !== '' ? ' (' . $g . ')' : '') . '.';
        }
        if ($dob !== '') {
            $lines[] = 'Date of birth: ' . $dob . '.';
        }
        if ($rel !== '' || $caste !== '') {
            $lines[] = 'Religion/caste: ' . trim($rel . ($rel !== '' && $caste !== '' ? ', ' : '') . $caste) . '.';
        }
        if ($edu !== '' || $job !== '') {
            $lines[] = 'Education/work: ' . trim($edu . ($edu !== '' && $job !== '' ? ' — ' : '') . $job) . '.';
        }
        if ($city !== '' || $country !== '') {
            $lines[] = 'Based in ' . trim($city . ($city !== '' && $country !== '' ? ', ' : '') . $country) . '.';
        }
        if ($ht !== '' || $wt !== '') {
            $lines[] = 'Height/weight: ' . trim($ht . ($ht !== '' && $wt !== '' ? ' / ' : '') . $wt) . '.';
        }
        $lines[] = 'Please complete this section with your own words.';

        return implode("\n", array_filter($lines, static function ($l) {
            return trim((string) $l) !== '';
        }));
    }

    private function normalizeWizardGender(array &$data): void
    {
        $g = strtolower(trim((string) ($data['gender'] ?? '')));
        if ($g === 'male') {
            $data['gender'] = 'Male';
        } elseif ($g === 'female') {
            $data['gender'] = 'Female';
        } elseif ($g !== '') {
            $data['gender'] = 'Other';
        }
    }

    /** @return string|null Error message, or null if OK */
    private function sanitizeDateAndTimeForUserDetails(array &$data): ?string
    {
        $dob = trim((string) ($data['dob'] ?? ''));
        if ($dob === '') {
            $data['dob'] = null;
        } else {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
                return 'Please enter a valid date of birth (use the date picker).';
            }
            $parts = explode('-', $dob);
            if (count($parts) !== 3 || !checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
                return 'Please enter a valid date of birth.';
            }
        }

        $bt = trim((string) ($data['birth_time'] ?? ''));
        if ($bt === '') {
            $data['birth_time'] = null;
        } elseif (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $bt)) {
            return 'Please enter birth time as HH:MM (or use the time picker).';
        }

        return null;
    }

    public function checkEmailJson(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $email = trim((string) ($_GET['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['ok' => true, 'available' => false, 'message' => 'Enter a valid email address.']);
            exit;
        }
        $exists = $this->model->emailExists($email);
        echo json_encode([
            'ok' => true,
            'available' => !$exists,
            'message' => $exists ? 'This email is already registered.' : 'Email is available.',
        ]);
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

        $email = trim((string) ($_POST['email'] ?? ''));
        if ($this->model->emailExists($email)) {
            $_SESSION['flash_error'] = 'This email is already registered. Please use a different email.';
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
    /**
     * @param array<string, mixed>|null $file Single $_FILES element
     */
    private function uploadFile(?array $file, string $folder = 'uploads/'): ?string
    {
        if ($file === null || !is_array($file)) {
            return null;
        }
        if (!function_exists('app_save_upload')) {
            return null;
        }
        $folder = trim(str_replace('\\', '/', $folder), '/');
        if ($folder === 'uploads' || $folder === '') {
            $sub = '';
        } elseif (strpos($folder, 'uploads/') === 0) {
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

        $data = array_merge(
            $_SESSION['user_form']['basic'] ?? [],
            $_SESSION['user_form']['residence'] ?? [],
            $_SESSION['user_form']['physical'] ?? [],
            $_SESSION['user_form']['other'] ?? [],
            $_SESSION['user_form']['partner'] ?? [],
            $_POST
        );

        $this->normalizeWizardGender($data);
        $dateErr = $this->sanitizeDateAndTimeForUserDetails($data);
        if ($dateErr !== null) {
            $_SESSION['flash_error'] = $dateErr;
            header('Location: ' . BASE_URL . '/admin/add_user/user/upload');
            exit;
        }

        if (!empty($data['password']) && strpos((string) $data['password'], '$2y$') !== 0) {
            $data['password'] = password_hash((string) $data['password'], PASSWORD_BCRYPT);
        }

        if (trim((string) ($data['user_status'] ?? '')) === '') {
            $data['user_status'] = 'unapproved';
        }

        $files = $_FILES ?? [];
        $data['photo1_status'] = $this->uploadFile($files['photo1_status'] ?? null);
        $data['photo2_url'] = $this->uploadFile($files['photo2_url'] ?? null);
        $data['photo3_url'] = $this->uploadFile($files['photo3_url'] ?? null);
        $data['photo4_url'] = $this->uploadFile($files['photo4_url'] ?? null);
        $data['photo5_url'] = $this->uploadFile($files['photo5_url'] ?? null);
        $data['photo6_url'] = $this->uploadFile($files['photo6_url'] ?? null);
        $data['id_proof_file'] = $this->uploadFile($files['id_proof_file'] ?? null);
        $data['cv_file'] = $this->uploadFile($files['cv_file'] ?? null);

        $result = $this->model->insertSafe($data);
        if (!$result['ok']) {
            $_SESSION['flash_error'] = $result['error'] !== '' ? $result['error'] : 'Could not save member. Please check all steps and try again.';
            header('Location: ' . BASE_URL . '/admin/add_user/user/upload');
            exit;
        }

        $newId = (int) ($result['id'] ?? 0);
        if ($newId > 0) {
            try {
                require_once __DIR__ . '/../models/MemberSaleFeeModel.php';
                $feeModel = new MemberSaleFeeModel();
                $feeModel->syncRegistrationSaleRowFromUserDetails($newId);
                $feeModel->syncRishtaSaleRowFromUserDetails($newId);
            } catch (Throwable $e) {
                // Member is saved; fee sync can be fixed from accounts
            }
        }

        unset($_SESSION['user_form']);
        $_SESSION['flash_success'] = 'Member created successfully.';
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
        $basic = $_SESSION['user_form']['basic'] ?? [];
        $residence = $_SESSION['user_form']['residence'] ?? [];
        $physical = $_SESSION['user_form']['physical'] ?? [];
        $other = $_SESSION['user_form']['other'] ?? [];
        $draftAbout = trim((string) ($other['about_us'] ?? ''));
        $suggested_about_us = $draftAbout !== '' ? $draftAbout : $this->suggestedAboutUsFromWizard(
            is_array($basic) ? $basic : [],
            is_array($residence) ? $residence : [],
            is_array($physical) ? $physical : []
        );
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
