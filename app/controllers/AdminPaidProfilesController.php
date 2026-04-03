<?php
require_once __DIR__ . '/../models/AdminPaidProfileModel.php';

class AdminPaidProfilesController
{
    private $model;
    protected $admin;

    public function __construct()
    {
        require_once __DIR__ . '/../models/Admin.php';
        $this->admin = new Admin();
        $this->model = new AdminPaidProfileModel();
    }


    public function displayadminname()
    {
        $admin = $this->admin->findById($_SESSION['admin_id']);
        return $admin ? $admin['name'] : 'Admin';
        require __DIR__ . '/../views/admin/partials/header.php';
    }




    // LIST
    public function index()
    {
        $profiles = $this->model->all();
        require __DIR__ . '/../views/admin/paid-profiles.php';
    }

    // EDIT FORM
    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) die('Package ID missing');

        $package = $this->model->find($id);
        require __DIR__ . '/../views/admin/edit_paid_profile.php';
    }

    // UPDATE
    public function update()
    {
        if (empty($_POST['id'])) {
            die('ID missing');
        }

        $this->model->update($_POST['id'], $_POST);

        header("Location: " . BASE_URL . "/admin/paid-profiles");
        exit;
    }

    // DELETE
    public function delete()
    {
        if (empty($_POST['id'])) {
            die('ID missing');
        }

        $this->model->delete($_POST['id']);

        header("Location: " . BASE_URL . "/admin/paid-profiles");
        exit;
    }
}
