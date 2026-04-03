<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/MemberSaleFeeModel.php';
require_once __DIR__ . '/../models/Admin.php';

class AdminMemberSaleFeesController
{
    protected MemberSaleFeeModel $model;
    protected Admin $admin;

    public function __construct()
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
        $this->admin = new Admin();
        $this->model = new MemberSaleFeeModel();
    }

    public function displayadminname(): string
    {
        $row = $this->admin->findById($_SESSION['admin_id']);

        return $row ? (string) $row['name'] : 'Admin';
    }

    public function registrationFee(): void
    {
        $feeType = MemberSaleFeeModel::TYPE_REGISTRATION;
        $pageTitle = 'Manage Member Sale - ALL';
        $feeColumnLabel = 'Registration Fee';
        $rows = $this->model->allByType($feeType);
        require __DIR__ . '/../views/admin/payment_fee_report.php';
    }

    public function rishtaFee(): void
    {
        $feeType = MemberSaleFeeModel::TYPE_RISHTA;
        $pageTitle = 'Manage Rishta Fee - ALL';
        $feeColumnLabel = 'Rishta Fee';
        $rows = $this->model->allByType($feeType);
        require __DIR__ . '/../views/admin/payment_fee_report.php';
    }
}
