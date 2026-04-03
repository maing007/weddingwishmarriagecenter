<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/AdminLeadModel.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/AdminUserModel.php';

class AdminLeadGenerationController
{
    protected $model;
    protected $admin;
    /** @var AdminUserModel */
    protected $adminUserModel;

    public function __construct()
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
        $this->admin = new Admin();
        $this->model = new AdminLeadModel();
        $this->adminUserModel = new AdminUserModel();
    }

    public function displayadminname(): string
    {
        $row = $this->admin->findById($_SESSION['admin_id']);

        return $row ? (string) $row['name'] : 'Admin';
    }

    public function index(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $tab = $_GET['interest_tab'] ?? 'all';
        $allowedTabs = ['all', 'in_process', 'registered', 'closed'];
        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'all';
        }

        $allLeads = $this->model->allLeads();
        $countAll = count($allLeads);
        $countInProcess = $this->model->countByInterest('In-Process-M');
        $countRegistered = $this->model->countByInterest('Registered');
        $countClosed = $this->model->countByInterest('Closed-M');

        $interestMap = [
            'in_process' => 'In-Process-M',
            'registered' => 'Registered',
            'closed' => 'Closed-M',
        ];
        $targetInterest = $interestMap[$tab] ?? null;
        if ($targetInterest === null) {
            $leads = $allLeads;
        } else {
            $leads = array_values(array_filter($allLeads, static function ($row) use ($targetInterest) {
                return (string) ($row['interest_name'] ?? '') === $targetInterest;
            }));
        }

        require __DIR__ . '/../views/admin/lead_generation.php';
    }

    public function bulkInterest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/lead-generation');
            exit;
        }
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['flash_error'] = 'Invalid request token.';
            header('Location: ' . BASE_URL . '/admin/lead-generation');
            exit;
        }
        $interest = $_POST['new_interest'] ?? '';
        $raw = $_POST['lead_ids'] ?? '';
        $ids = array_filter(array_map('intval', explode(',', (string) $raw)));
        if ($this->model->bulkUpdateInterest($ids, $interest)) {
            $_SESSION['flash_success'] = 'Interest updated for selected leads.';
        } else {
            $_SESSION['flash_error'] = 'Could not update interest. Check selection and interest value.';
        }
        $returnTab = $_POST['return_tab'] ?? 'all';
        header('Location: ' . BASE_URL . '/admin/lead-generation?interest_tab=' . urlencode((string) $returnTab));
        exit;
    }

    public function comment(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/lead-generation');
            exit;
        }
        $leadId = (int) ($_POST['lead_id'] ?? 0);
        $comment = trim((string) ($_POST['comment'] ?? ''));
        $type = trim((string) ($_POST['comment_type'] ?? 'general'));
        if ($leadId < 1 || $comment === '') {
            $_SESSION['flash_error'] = 'Lead and comment are required.';
            header('Location: ' . BASE_URL . '/admin/lead-generation');
            exit;
        }
        $lead = $this->model->find($leadId);
        if (!$lead) {
            $_SESSION['flash_error'] = 'Lead not found.';
            header('Location: ' . BASE_URL . '/admin/lead-generation');
            exit;
        }
        $this->model->addComment($leadId, (int) $_SESSION['admin_id'], $comment, $type);
        $_SESSION['flash_success'] = 'Comment saved.';
        header('Location: ' . BASE_URL . '/admin/lead-generation');
        exit;
    }

    public function commentsJson(): void
    {
        $leadId = (int) ($_GET['lead_id'] ?? 0);
        if ($leadId < 1) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'rows' => []]);
            exit;
        }
        $filters = [
            'type' => $_GET['type'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
        ];
        $rows = $this->model->getComments($leadId, $filters);
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'rows' => $rows]);
        exit;
    }

    public function add(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $lead = [];
        $countries = require __DIR__ . '/../config/lead_countries.php';
        $sources = require __DIR__ . '/../config/lead_sources.php';
        $adminUsers = $this->adminUserModel->allAdminUsers();
        require __DIR__ . '/../views/admin/lead_generation_form.php';
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/lead-generation/add');
            exit;
        }
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['flash_error'] = 'Invalid request token.';
            header('Location: ' . BASE_URL . '/admin/lead-generation/add');
            exit;
        }
        $data = $this->collectLeadPost();
        $err = $this->validateLeadForm($data);
        if ($err !== null) {
            $_SESSION['flash_error'] = $err;
            header('Location: ' . BASE_URL . '/admin/lead-generation/add');
            exit;
        }
        $data['created_by'] = $this->displayadminname();
        if (empty($data['reg_date'])) {
            $data['reg_date'] = date('Y-m-d H:i:s');
        }
        $this->model->create($data);
        $_SESSION['flash_success'] = 'Lead created.';
        header('Location: ' . BASE_URL . '/admin/lead-generation');
        exit;
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $lead = $id > 0 ? $this->model->find($id) : null;
        if (!$lead) {
            $_SESSION['flash_error'] = 'Lead not found.';
            header('Location: ' . BASE_URL . '/admin/lead-generation');
            exit;
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $countries = require __DIR__ . '/../config/lead_countries.php';
        $sources = require __DIR__ . '/../config/lead_sources.php';
        $adminUsers = $this->adminUserModel->allAdminUsers();
        require __DIR__ . '/../views/admin/lead_generation_form.php';
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/lead-generation');
            exit;
        }
        $id = (int) ($_POST['id'] ?? 0);
        $lead = $id > 0 ? $this->model->find($id) : null;
        if (!$lead) {
            $_SESSION['flash_error'] = 'Lead not found.';
            header('Location: ' . BASE_URL . '/admin/lead-generation');
            exit;
        }
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['flash_error'] = 'Invalid request token.';
            header('Location: ' . BASE_URL . '/admin/lead-generation/edit?id=' . $id);
            exit;
        }
        $data = $this->collectLeadPost();
        $err = $this->validateLeadForm($data);
        if ($err !== null) {
            $_SESSION['flash_error'] = $err;
            header('Location: ' . BASE_URL . '/admin/lead-generation/edit?id=' . $id);
            exit;
        }
        foreach (['reg_date', 'lead_code', 'reg_matri_id', 'next_followup', 'staff_username'] as $k) {
            if (($data[$k] ?? null) === null || $data[$k] === '') {
                $data[$k] = $lead[$k] ?? null;
            }
        }
        $this->model->update($id, $data);
        $_SESSION['flash_success'] = 'Lead updated.';
        header('Location: ' . BASE_URL . '/admin/lead-generation');
        exit;
    }

    public function task(): void
    {
        $id = (int) ($_GET['lead_id'] ?? $_GET['id'] ?? 0);
        $lead = $id > 0 ? $this->model->find($id) : null;
        require __DIR__ . '/../views/admin/lead_generation_task.php';
    }

    public function report(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $allLeads = $this->model->allLeads();
        usort($allLeads, static function ($a, $b) {
            $ta = strtotime((string) ($a['reg_date'] ?? $a['created_at'] ?? '')) ?: 0;
            $tb = strtotime((string) ($b['reg_date'] ?? $b['created_at'] ?? '')) ?: 0;

            return $tb <=> $ta;
        });
        require __DIR__ . '/../views/admin/lead_generation_report.php';
    }

    public function deleteLead(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/lead-generation/report');
            exit;
        }
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['flash_error'] = 'Invalid request token.';
            header('Location: ' . BASE_URL . '/admin/lead-generation/report');
            exit;
        }
        $id = (int) ($_POST['id'] ?? 0);
        if ($id < 1) {
            header('Location: ' . BASE_URL . '/admin/lead-generation/report');
            exit;
        }
        if ($this->model->find($id)) {
            $this->model->delete($id);
            $_SESSION['flash_success'] = 'Lead removed.';
        }
        header('Location: ' . BASE_URL . '/admin/lead-generation/report');
        exit;
    }

    public function followupReport(): void
    {
        $allLeads = $this->model->allLeads();
        require __DIR__ . '/../views/admin/lead_generation_followup_report.php';
    }

    private function validateLeadForm(array $data): ?string
    {
        if (($data['full_name'] ?? '') === '') {
            return 'Name is required.';
        }
        if (empty($data['country_id']) || empty($data['country'])) {
            return 'Country is required.';
        }
        if (($data['interest_name'] ?? '') === '') {
            return 'Interest is required.';
        }
        if (($data['importance'] ?? '') === '') {
            return 'Importance is required.';
        }

        return null;
    }

    /**
     * @return array<string, int|string|null>
     */
    private function collectLeadPost(): array
    {
        $countries = require __DIR__ . '/../config/lead_countries.php';
        $sources = require __DIR__ . '/../config/lead_sources.php';

        $countryIdRaw = trim((string) ($_POST['country_id'] ?? ''));
        $countryName = ($countryIdRaw !== '' && isset($countries[$countryIdRaw]))
            ? $countries[$countryIdRaw] : '';

        $sourceKey = trim((string) ($_POST['source'] ?? ''));
        $sourceName = null;
        if ($sourceKey !== '' && isset($sources[$sourceKey])) {
            $label = $sources[$sourceKey];
            $sourceName = ($label !== '' && $label !== 'Select Source') ? $label : null;
        }

        $tid = (int) ($_POST['team_assign'] ?? 0);
        $assignName = null;
        if ($tid > 0) {
            foreach ($this->adminUserModel->allAdminUsers() as $u) {
                if ((int) $u['id'] === $tid) {
                    $assignName = $u['name'];
                    break;
                }
            }
        }

        $interest = trim((string) ($_POST['interest'] ?? $_POST['interest_name'] ?? ''));
        $allowedInt = ['In-Process-M', 'Registered', 'Closed-M'];
        if (!in_array($interest, $allowedInt, true)) {
            $interest = '';
        }

        $importance = trim((string) ($_POST['importance'] ?? ''));
        $allowedImp = ['Important', 'Moderate', 'Not Important'];
        if (!in_array($importance, $allowedImp, true)) {
            $importance = '';
        }

        $gender = trim((string) ($_POST['gender'] ?? ''));
        if (!in_array($gender, ['Male', 'Female'], true)) {
            $gender = '';
        }

        return [
            'full_name' => trim((string) ($_POST['username'] ?? $_POST['full_name'] ?? '')),
            'gender' => $gender ?: null,
            'lead_code' => trim((string) ($_POST['lead_code'] ?? '')) ?: null,
            'country' => $countryName ?: null,
            'country_id' => $countryIdRaw !== '' ? (int) $countryIdRaw : null,
            'city' => trim((string) ($_POST['city'] ?? '')) ?: null,
            'state' => trim((string) ($_POST['state_id'] ?? $_POST['state'] ?? '')) ?: null,
            'address' => trim((string) ($_POST['address'] ?? '')) ?: null,
            'phone1' => trim((string) ($_POST['phone_no_1'] ?? $_POST['phone1'] ?? '')) ?: null,
            'phone2' => trim((string) ($_POST['phone_no_2'] ?? '')) ?: null,
            'phone3' => trim((string) ($_POST['phone_no_3'] ?? '')) ?: null,
            'phone4' => trim((string) ($_POST['phone_no_4'] ?? '')) ?: null,
            'email' => trim((string) ($_POST['email'] ?? '')) ?: null,
            'interest_name' => $interest,
            'team_assign' => $assignName,
            'team_assign_id' => $tid > 0 ? $tid : null,
            'importance' => $importance,
            'reg_matri_id' => trim((string) ($_POST['reg_matri_id'] ?? '')) ?: null,
            'reg_date' => $this->normalizeDatetimeLocal($_POST['reg_date'] ?? ''),
            'source_name' => $sourceName,
            'next_followup' => trim((string) ($_POST['next_followup'] ?? '')) ?: null,
            'staff_username' => trim((string) ($_POST['staff_username'] ?? '')) ?: null,
        ];
    }

    private function normalizeDatetimeLocal($raw): ?string
    {
        $s = trim((string) $raw);
        if ($s === '') {
            return null;
        }
        if (strpos($s, 'T') !== false) {
            $s = str_replace('T', ' ', $s);
            if (strlen($s) === 16) {
                $s .= ':00';
            }
        }

        return $s ?: null;
    }
}
