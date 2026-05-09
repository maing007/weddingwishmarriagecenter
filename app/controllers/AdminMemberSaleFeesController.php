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
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $feeType = MemberSaleFeeModel::TYPE_REGISTRATION;
        $pageTitle = 'Manage Registration Fee — ALL';
        $pageHead = 'Manage Registration Fee — ALL';
        $feeColumnLabel = 'Registration Fee';
        $rows = $this->model->allByTypeForIncomeUi($feeType);
        $planPackages = $this->model->allPackages();
        $planStaff = $this->model->allAdminStaffForPlan();
        require __DIR__ . '/../views/admin/income_fee_members.php';
    }

    public function rishtaFee(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $feeType = MemberSaleFeeModel::TYPE_RISHTA;
        $pageTitle = 'Manage Rishta Fee — ALL';
        $pageHead = 'Manage Rishta Fee — ALL';
        $feeColumnLabel = 'Rishta Fee';
        $rows = $this->model->allByTypeForIncomeUi($feeType);
        $planPackages = [];
        $planStaff = [];
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
        $rows = $this->model->salesReportRowsForCards($search, $saleScope, $payFilter, $filterStaff, $limit, $offset);
        $staffFilterOptions = $this->model->salesReportDistinctStaffNames();

        $feeIds = [];
        foreach ($rows as $rr) {
            $feeIds[] = (int) ($rr['id'] ?? 0);
        }
        $msrProofsByFee = $this->model->listPaymentProofsForFeeIds($feeIds);

        $msrCardLayout = true;
        $msrPayCounts = [
            'reg_unpaid' => $this->model->salesReportTotal($search, MemberSaleFeeModel::TYPE_REGISTRATION, 'unpaid', $filterStaff),
            'reg_paid' => $this->model->salesReportTotal($search, MemberSaleFeeModel::TYPE_REGISTRATION, 'paid', $filterStaff),
            'rishta_unpaid' => $this->model->salesReportTotal($search, MemberSaleFeeModel::TYPE_RISHTA, 'unpaid', $filterStaff),
            'rishta_paid' => $this->model->salesReportTotal($search, MemberSaleFeeModel::TYPE_RISHTA, 'paid', $filterStaff),
        ];

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
        $feeType = (($row['fee_type'] ?? '') === MemberSaleFeeModel::TYPE_RISHTA)
            ? MemberSaleFeeModel::TYPE_RISHTA
            : MemberSaleFeeModel::TYPE_REGISTRATION;
        header('Location: ' . $this->accountsIncomeFeeListUrl($feeType));
        exit;
    }

    /** Accounts → Income → Registration fee / Rishta fee list (sidebar). */
    private function accountsIncomeFeeListUrl(string $feeType): string
    {
        $path = ($feeType === MemberSaleFeeModel::TYPE_RISHTA)
            ? '/admin/accounts/income/rishta-fee'
            : '/admin/accounts/income/registration-fee';

        return rtrim(BASE_URL, '/') . $path;
    }

    public function assignPlanSubmit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/accounts/income/registration-fee');
            exit;
        }
        $token = (string) ($_POST['csrf_token'] ?? '');
        if ($token === '' || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['flash_error'] = 'Invalid request token.';
            header('Location: ' . BASE_URL . '/admin/accounts/income/registration-fee');
            exit;
        }

        $res = $this->model->assignRegistrationPlanAndApprove([
            'fee_id' => (int) ($_POST['fee_id'] ?? 0),
            'user_id' => (int) ($_POST['user_id'] ?? 0),
            'package_id' => (int) ($_POST['plan_id'] ?? 0),
            'staff_id' => (int) ($_POST['staff_id'] ?? 0),
            'team_label' => (string) ($_POST['team_label'] ?? $_POST['team_id'] ?? ''),
            'rishta_fee' => $_POST['rishta_fee'] ?? 0,
            'bonus_days' => $_POST['bonus_days'] ?? 0,
            'discount' => $_POST['discount'] ?? 0,
            'payment_note' => $_POST['payment_note'] ?? '',
        ]);

        $_SESSION['flash_' . ($res['ok'] ? 'success' : 'error')] = $res['message'];
        if ($res['ok']) {
            header('Location: ' . $this->accountsIncomeFeeListUrl(MemberSaleFeeModel::TYPE_REGISTRATION));
            exit;
        }
        header('Location: ' . BASE_URL . '/admin/accounts/income/registration-fee');
        exit;
    }

    public function deleteMemberSaleFee(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/accounts/income/registration-fee');
            exit;
        }
        $token = (string) ($_POST['csrf_token'] ?? '');
        if ($token === '' || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['flash_error'] = 'Invalid request token.';
            header('Location: ' . BASE_URL . '/admin/accounts/income/registration-fee');
            exit;
        }
        $feeId = (int) ($_POST['fee_id'] ?? 0);
        $ok = $feeId > 0 && $this->model->deleteMemberSaleFeeById($feeId);
        $_SESSION['flash_' . ($ok ? 'success' : 'error')] = $ok ? 'Fee / sales row deleted.' : 'Could not delete fee row.';
        $loc = $this->sanitizeAdminRedirect($_POST['redirect'] ?? '');
        header('Location: ' . $loc);
        exit;
    }

    /** @param mixed $path */
    private function sanitizeAdminRedirect($path): string
    {
        $p = is_string($path) ? trim($path) : '';
        if ($p === '' || $p[0] !== '/') {
            return BASE_URL . '/admin/accounts/income/registration-fee';
        }
        if (strpos($p, '//') !== false) {
            return BASE_URL . '/admin/accounts/income/registration-fee';
        }

        return rtrim(BASE_URL, '/') . $p;
    }

    public function paymentProofSubmit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/sales-report');
            exit;
        }
        $feeId = (int) ($_POST['payment_id'] ?? $_POST['fee_id'] ?? 0);
        $res = $this->model->savePaymentProof($feeId, $_POST, $_FILES['receipt'] ?? null);
        $_SESSION['flash_' . ($res['ok'] ? 'success' : 'error')] = $res['message'];
        $back = (string) ($_POST['return_url'] ?? '');
        if ($back !== '' && strpos($back, (string) BASE_URL) === 0) {
            header('Location: ' . $back);
            exit;
        }
        header('Location: ' . BASE_URL . '/admin/sales-report');
        exit;
    }

    /**
     * Stream payment proof with correct MIME (static /uploads/ often lacks types; nosniff then blocks or flags downloads).
     */
    public function paymentProofDownload(): void
    {
        $proofId = (int) ($_GET['proof_id'] ?? 0);
        $resolved = $this->model->paymentProofFileForAdminDownload($proofId);
        if ($resolved === null) {
            http_response_code(404);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Payment proof not found.';
            exit;
        }
        $abs = $resolved['absolute'];
        $fn = $resolved['filename'];

        $mime = 'application/octet-stream';
        if (function_exists('finfo_open')) {
            $fi = finfo_open(FILEINFO_MIME_TYPE);
            if ($fi !== false) {
                $det = finfo_file($fi, $abs);
                finfo_close($fi);
                if (is_string($det) && $det !== '') {
                    $mime = $det;
                }
            }
        }
        $ext = strtolower((string) pathinfo($abs, PATHINFO_EXTENSION));
        $byExt = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
        ];
        if ($mime === 'application/octet-stream' && isset($byExt[$ext])) {
            $mime = $byExt[$ext];
        }

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $fn . '"');
        header('Content-Length: ' . (string) filesize($abs));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, max-age=3600');
        readfile($abs);
        exit;
    }

    public function registrationInvoice(): void
    {
        $feeId = (int) ($_GET['id'] ?? 0);
        $row = $feeId > 0 ? $this->model->findFeeWithUserContext($feeId) : null;
        if (!$row || ($row['fee_type'] ?? '') !== MemberSaleFeeModel::TYPE_REGISTRATION) {
            http_response_code(404);
            echo 'Invoice not found.';
            exit;
        }
        $adminRow = $this->admin->findById((int) ($_SESSION['admin_id'] ?? 0));
        $adminName = $adminRow ? (string) $adminRow['name'] : 'Admin';
        require __DIR__ . '/../views/admin/fee_registration_invoice.php';
    }
}
