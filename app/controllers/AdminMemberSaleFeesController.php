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

    /** Reports → Payments: same table as Members Sales Report, scoped to registration fees. */
    public function registrationFeeReport(): void
    {
        $this->renderMemberSalesReport(MemberSaleFeeModel::TYPE_REGISTRATION);
    }

    /** Reports → Payments: same table as Members Sales Report, scoped to rishta fees. */
    public function rishtaFeeReport(): void
    {
        $this->renderMemberSalesReport(MemberSaleFeeModel::TYPE_RISHTA);
    }

    private function renderMemberSalesReport(string $fixedScope): void
    {
        $search = trim((string) ($_GET['search_filed'] ?? ''));

        $payFilter = strtolower(trim((string) ($_GET['pay_filter'] ?? 'all')));
        if (!in_array($payFilter, ['all', 'paid', 'unpaid'], true)) {
            $payFilter = 'all';
        }

        $filterStaff = trim((string) ($_GET['filter_staff'] ?? ''));

        $limit = (int) ($_GET['limit_per_page'] ?? 10);
        if (!in_array($limit, [1, 2, 3, 5, 10, 25, 50, 100], true)) {
            $limit = 10;
        }

        $page = (int) ($_GET['page'] ?? 1);
        if ($page < 1) {
            $page = 1;
        }

        $saleScope = $fixedScope;

        $tabCounts = $this->model->salesReportScopeCounts($search);
        $totalRows = $this->model->salesReportTotal($search, $saleScope, $payFilter, $filterStaff);
        $totalPages = max(1, (int) ceil($totalRows / $limit));
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $limit;
        $rows = $this->model->salesReportRows($search, $saleScope, $payFilter, $filterStaff, $limit, $offset);
        $staffFilterOptions = $this->model->salesReportDistinctStaffNames();

        $pageHead = $fixedScope === MemberSaleFeeModel::TYPE_REGISTRATION
            ? 'Manage Member Sale — Registration'
            : 'Manage Member Sale — Rishta';

        $msrSelfUrl = BASE_URL . '/admin/reports/payments/'
            . ($fixedScope === MemberSaleFeeModel::TYPE_REGISTRATION ? 'registration-fee' : 'rishta-fee');
        $msrTabUrlAll = BASE_URL . '/admin/sales-report';
        $msrTabUrlReg = BASE_URL . '/admin/reports/payments/registration-fee';
        $msrTabUrlRishta = BASE_URL . '/admin/reports/payments/rishta-fee';
        $msrLockScope = true;

        require __DIR__ . '/../views/admin/memberssalesreport.php';
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
