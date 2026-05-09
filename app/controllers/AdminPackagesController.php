<?php

class AdminPackagesController extends Controller
{
    private $model;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
         require_once __DIR__.'/../models/Admin.php';
        $this->admin = new Admin();

        require_once __DIR__ . '/../models/AdminPackageModel.php';
        $this->model = new AdminPackageModel();

        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
    }


public function displayadminname() {
        $admin = $this->admin->findById($_SESSION['admin_id']);
        return $admin ? $admin['name'] : 'Admin';
         require __DIR__.'/../views/admin/partials/header.php';
    }
    // Show form
    public function assignForm()
    {
        $users    = $this->model->users();
        $packages = $this->model->packages();

        require __DIR__ . '/../views/admin/assign_package.php';
    }

    // Handle submit
    public function assign()
    {
        $userId    = (int)$_POST['user_id'];
        $packageId = (int)$_POST['package_id'];

        if (!$userId || !$packageId) {
            header('Location: ' . BASE_URL . '/admin/assign-package');
            exit;
        }

        $this->model->assignPackage($userId, $packageId);

        header('Location: ' . BASE_URL . '/admin/paid-profiles');
        exit;
    }
    public function delete()
{
    $id = (int)$_GET['id'];

    if ($id > 0) {
        $this->model->deleteUserPackage($id);
    }

    header('Location: ' . BASE_URL . '/admin/paid-profiles');
    exit;
}
public function edit()
{
    $id = (int)$_GET['id'];

    $package = $this->model->findUserPackageById($id);
    if (!$package) {
        header('Location: ' . BASE_URL . '/admin/paid-profiles');
        exit;
    }

    require __DIR__ . '/../views/admin/edit_package.php';
}
public function update()
{
    $id = (int)$_POST['id'];

    $this->model->updateUserPackage($id, [
        'package_name' => $_POST['package_name'],
        'price'        => $_POST['price'],
        'status'       => $_POST['status'],
        'expires_at'   => $_POST['expires_at']
    ]);

    header('Location: ' . BASE_URL . '/admin/paid-profiles');
    exit;
}

}
