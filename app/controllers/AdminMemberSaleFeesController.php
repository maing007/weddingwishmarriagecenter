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
        $pageTitle = 'Manage Registration Fee — ALL';
        $pageHead = 'Manage Registration Fee — ALL';
        $feeColumnLabel = 'Registration Fee';
        $rows = $this->model->allByTypeForIncomeUi($feeType);
        require __DIR__ . '/../views/admin/income_fee_members.php';
    }

    public function rishtaFee(): void
    {
        $feeType = MemberSaleFeeModel::TYPE_RISHTA;
        $pageTitle = 'Manage Rishta Fee — ALL';
        $pageHead = 'Manage Rishta Fee — ALL';
        $feeColumnLabel = 'Rishta Fee';
        $rows = $this->model->allByTypeForIncomeUi($feeType);
        require __DIR__ . '/../views/admin/income_fee_members.php';
    }

    public function feePaidApproved(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/accounts/income/registration-fee');
            exit;
        }
        $feeId = (int) ($_POST['fee_id'] ?? 0);
        $row = $feeId > 0 ? $this->model->findById($feeId) : null;
        if (!$row) {
            $_SESSION['flash_error'] = 'Fee record not found.';
            header('Location: ' . BASE_URL . '/admin/accounts/income/registration-fee');
            exit;
        }
        $ok = $this->model->markFeePaidAndApproveMember($feeId);
        $_SESSION['flash_' . ($ok ? 'success' : 'error')] = $ok
            ? 'Marked as paid and member approved (when profile is linked).'
            : 'Could not update fee / member. Try again.';
        $dest = (($row['fee_type'] ?? '') === MemberSaleFeeModel::TYPE_RISHTA)
            ? '/admin/accounts/income/rishta-fee'
            : '/admin/accounts/income/registration-fee';
        header('Location: ' . BASE_URL . $dest);
        exit;
    }
}
