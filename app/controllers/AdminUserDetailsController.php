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

    /* ===============================
       1. BASIC DETAILS SUBMIT
    =============================== */
    public function saveBasicDetails()
    {
        $_SESSION['user_form']['basic'] = $_POST;

        header('Location: ' . BASE_URL . '/admin/add_user/user/residence');
        exit;
    }

    /* ===============================
       2. RESIDENCE
    =============================== */
    public function saveResidence()
    {
        $_SESSION['user_form']['residence'] = $_POST;


        header('Location: ' . BASE_URL . '/admin/add_user/user/physical');
        exit;
    }

    /* ===============================
       3. PHYSICAL INFO
    =============================== */
    public function savePhysical()
    {

        $_SESSION['user_form']['physical'] = $_POST;

        header('Location: ' . BASE_URL . '/admin/add_user/user/other');
        exit;
    }

    /* ===============================
       4. OTHER INFO
    =============================== */
    public function saveOtherInfo()

    {


        $_SESSION['user_form']['other'] = $_POST;

        header('Location: ' . BASE_URL . '/admin/add_user/user/partner');
        exit;
    }

    /* ===============================
       5. PARTNER PREFERENCE
    =============================== */
    public function savePartner()
    {

        $_SESSION['user_form']['partner'] = $_POST;

        header('Location: ' . BASE_URL . '/admin/add_user/user/upload');
        exit;
    }

    /* ===============================
       FILE UPLOAD FUNCTION
    =============================== */
    private function uploadFile($file, $folder = 'uploads/')
    {
        if (!empty($file['name'])) {
            $targetDir = __DIR__ . '/../../public/' . $folder;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = time() . '_' . $file['name'];
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                return $folder . $fileName;
            }
        }
        return null;
    }

    /* ===============================
       6. FILE UPLOAD + FINAL SUBMIT
    =============================== */
    public function submitAll()
    {


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
        $model->create($data);

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
        require __DIR__ . '/../views/admin/user_details_form/residence_form.php';
    }

    // 3. Physical
    public function physicalForm()
    {
        require __DIR__ . '/../views/admin/user_details_form/physical_form.php';
    }

    // 4. Other
    public function otherForm()
    {
        require __DIR__ . '/../views/admin/user_details_form/other_info.php';
    }

    // 5. Partner
    public function partnerForm()
    {
        require __DIR__ . '/../views/admin/user_details_form/partner_form.php';
    }

    // 6. Upload
    public function uploadForm()
    {
        require __DIR__ . '/../views/admin/user_details_form/upload_form.php';
    }
}
