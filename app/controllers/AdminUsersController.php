<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/MemberAssignmentModel.php';
require_once __DIR__ . '/../models/AdminPaidProfileModel.php';
require_once __DIR__ . '/../models/AdminUserModel.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../services/MailService.php';

class AdminUsersController
{
    protected $userModel;
    protected $paidModel;
    protected $model;
    protected $admin;

    public function __construct()
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }

        $this->admin     = new Admin();
        $this->userModel = new User();
        $this->paidModel = new AdminPaidProfileModel();
        $this->model     = new AdminUserModel();
    }

    // ✅ FIXED
    public function displayadminname()
    {
        $admin = $this->admin->findById($_SESSION['admin_id']);
        return $admin ? $admin['name'] : 'Admin';
    }

    public function index()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require_once __DIR__ . '/../models/MemberSaleFeeModel.php';
        new MemberSaleFeeModel();

        $filterLabels = [
            'today' => 'Today Member(s)',
            'last_week' => 'Last Week Member(s)',
            'last_month' => 'Last Month Member(s)',
            'total' => 'Total Member(s)',
            'male' => 'Male Member(s)',
            'female' => 'Female Member(s)',
            'active' => 'Active Member',
            'paid' => 'Paid Member',
        ];

        $requestedFilter = strtolower(trim((string)($_GET['dashboard_filter'] ?? '')));
        $dashboardFilter = array_key_exists($requestedFilter, $filterLabels) ? $requestedFilter : '';

        $users = $this->model->allUsers($dashboardFilter === '' ? null : $dashboardFilter);
        $activeFilterLabel = $dashboardFilter !== '' ? $filterLabels[$dashboardFilter] : 'All Members';
        $more  = $this->model->more_details();

        require __DIR__ . '/../views/admin/users.php';
    }

    public function bulkStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['flash_error'] = 'Invalid request token.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $status = strtolower(trim((string)($_POST['bulk_status'] ?? '')));
        $allowed = ['approved', 'unapproved', 'suspended'];
        if (!in_array($status, $allowed, true)) {
            $_SESSION['flash_error'] = 'Invalid status selected.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $ids = $_POST['selected_users'] ?? [];
        if (!is_array($ids) || empty($ids)) {
            $_SESSION['flash_error'] = 'Please select at least one user.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $ids = array_values(array_filter(array_map('intval', $ids), function ($id) {
            return $id > 0;
        }));

        if (empty($ids)) {
            $_SESSION['flash_error'] = 'No valid users selected.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        if ($status === 'approved') {
            require_once __DIR__ . '/../models/MemberSaleFeeModel.php';
            new MemberSaleFeeModel();
            $ok = $this->model->queueSelectedForRegistrationFee($ids);
            $_SESSION['flash_' . ($ok ? 'success' : 'error')] = $ok
                ? 'Selected members were sent to Registration Fee (assign plan there to approve).'
                : 'Could not queue members for registration fee.';
        } else {
            $updated = $this->model->bulkUpdateUserStatus($ids, $status);
            if ($updated) {
                $_SESSION['flash_success'] = 'Selected users updated to ' . ucfirst($status) . '.';
            } else {
                $_SESSION['flash_error'] = 'No user status was updated.';
            }
        }

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function paidToSpotlight()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $filter = strtolower(trim((string)($_GET['featured_filter'] ?? 'all')));
        if (!in_array($filter, ['all', 'featured', 'non_featured'], true)) {
            $filter = 'all';
        }
        $users = $this->model->spotlightUsers($filter === 'all' ? null : $filter);
        $featuredCount = 0;
        $nonFeaturedCount = 0;
        foreach ($users as $u) {
            if (strtolower((string)($u['featured_status'] ?? 'non_featured')) === 'featured') {
                $featuredCount++;
            } else {
                $nonFeaturedCount++;
            }
        }
        require __DIR__ . '/../views/admin/paid_to_spotlight.php';
    }

    public function bulkFeaturedSpotlight()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/paid-to-spotlight');
            exit;
        }
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['flash_error'] = 'Invalid request token.';
            header('Location: ' . BASE_URL . '/admin/paid-to-spotlight');
            exit;
        }
        $status = strtolower(trim((string)($_POST['featured_status'] ?? '')));
        if (!in_array($status, ['featured', 'non_featured'], true)) {
            $_SESSION['flash_error'] = 'Invalid featured status.';
            header('Location: ' . BASE_URL . '/admin/paid-to-spotlight');
            exit;
        }
        $ids = $_POST['selected_users'] ?? [];
        $ids = array_values(array_filter(array_map('intval', (array)$ids), static function ($id) {
            return $id > 0;
        }));
        if (empty($ids)) {
            $_SESSION['flash_error'] = 'Please select at least one profile.';
            header('Location: ' . BASE_URL . '/admin/paid-to-spotlight');
            exit;
        }
        $ok = $this->model->bulkUpdateFeaturedStatus($ids, $status);
        $_SESSION['flash_' . ($ok ? 'success' : 'error')] = $ok ? 'Featured status updated.' : 'No profile updated.';
        header('Location: ' . BASE_URL . '/admin/paid-to-spotlight');
        exit;
    }

    public function changeMembershipPlan()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $filter = strtolower(trim((string)($_GET['status_filter'] ?? 'all')));
        $allowed = ['all', 'approved', 'unapproved'];
        if (!in_array($filter, $allowed, true)) {
            $filter = 'all';
        }

        $users = $this->model->spotlightUsers(null);
        if ($filter !== 'all') {
            $users = array_values(array_filter($users, static function ($u) use ($filter) {
                return strtolower((string)($u['status'] ?? '')) === $filter;
            }));
        }

        $approvedCount = 0;
        $unapprovedCount = 0;
        foreach ($users as $u) {
            $st = strtolower((string)($u['status'] ?? ''));
            if ($st === 'approved') {
                $approvedCount++;
            } elseif ($st === 'unapproved') {
                $unapprovedCount++;
            }
        }

        require __DIR__ . '/../views/admin/change_membership_plan.php';
    }

    public function expiredMembers()
    {
        $filter = strtolower(trim((string)($_GET['status_filter'] ?? 'all')));
        if (!in_array($filter, ['all', 'approved', 'unapproved'], true)) {
            $filter = 'all';
        }
        $users = $this->model->expiredMembershipUsers($filter === 'all' ? null : $filter);
        $approvedCount = 0;
        $unapprovedCount = 0;
        foreach ($users as $u) {
            $st = strtolower((string)($u['status'] ?? ''));
            if ($st === 'approved') {
                $approvedCount++;
            } elseif ($st === 'unapproved') {
                $unapprovedCount++;
            }
        }
        require __DIR__ . '/../views/admin/expired_members.php';
    }

    public function memberFollowupReport()
    {
        $filter = strtolower(trim((string)($_GET['followup_filter'] ?? 'all')));
        if (!in_array($filter, ['all', 'today', 'previous', 'next'], true)) {
            $filter = 'all';
        }
        $users = $this->model->followupReportUsers($filter);
        $counts = $this->model->followupReportCounts();
        require __DIR__ . '/../views/admin/member_followup_report.php';
    }

    public function advancedSearch()
    {
        $filters = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
        $options = $this->model->advancedSearchOptions();
        $rows = $this->model->advancedSearchUsers($filters);
        require __DIR__ . '/../views/admin/advanced_search.php';
    }

    public function matchMaking()
    {
        $filter = strtolower(trim((string)($_GET['status_filter'] ?? 'all')));
        if (!in_array($filter, ['all', 'approved', 'unapproved', 'suspended'], true)) {
            $filter = 'all';
        }

        $allUsers = $this->model->allUsers(null);
        $approvedCount = 0;
        $unapprovedCount = 0;
        $suspendedCount = 0;
        foreach ($allUsers as $u) {
            $st = strtolower((string)($u['status'] ?? ''));
            if ($st === 'approved') {
                $approvedCount++;
            } elseif ($st === 'unapproved') {
                $unapprovedCount++;
            } elseif ($st === 'suspended') {
                $suspendedCount++;
            }
        }

        $users = $allUsers;
        if ($filter !== 'all') {
            $users = array_values(array_filter($allUsers, static function ($u) use ($filter) {
                return strtolower((string)($u['status'] ?? '')) === $filter;
            }));
        }

        require __DIR__ . '/../views/admin/match_making.php';
    }

    public function memberEvaluationForm()
    {
        $userId = (int)($_GET['id'] ?? 0);
        if ($userId <= 0) {
            $_SESSION['flash_error'] = 'Invalid user.';
            header('Location: ' . BASE_URL . '/admin/match-making');
            exit;
        }
        $user = $this->model->getUserDetailsById($userId);
        if (!$user) {
            $_SESSION['flash_error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/admin/match-making');
            exit;
        }
        $answers = $this->model->getMemberEvaluation($userId);
        require __DIR__ . '/../views/admin/member_evaluation_form.php';
    }

    public function saveMemberEvaluationForm()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/match-making');
            exit;
        }
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            $_SESSION['flash_error'] = 'Invalid user.';
            header('Location: ' . BASE_URL . '/admin/match-making');
            exit;
        }
        $payload = [
            'q1' => $_POST['q1'] ?? '',
            'q2' => $_POST['q2'] ?? '',
            'q3' => $_POST['q3'] ?? '',
            'q4' => $_POST['q4'] ?? '',
            'q5' => $_POST['q5'] ?? '',
            'q6' => $_POST['q6'] ?? '',
        ];
        $ok = $this->model->saveMemberEvaluation($userId, (int)$_SESSION['admin_id'], $payload);
        $_SESSION['flash_' . ($ok ? 'success' : 'error')] = $ok ? 'Member evaluation saved.' : 'Unable to save evaluation.';
        header('Location: ' . BASE_URL . '/admin/member-evaluation?id=' . $userId);
        exit;
    }

    public function acceptedMatches()
    {
        $rows = $this->model->acceptedMatches();
        require __DIR__ . '/../views/admin/accepted_matches.php';
    }

    public function interactionReport()
    {
        $userId = (int)($_GET['id'] ?? 0);
        $action = strtolower(trim((string)($_GET['action'] ?? 'opened')));
        $allowed = ['opened', 'deferred', 'declined', 'meeting', 'accepted'];

        if ($userId <= 0 || !in_array($action, $allowed, true)) {
            $_SESSION['flash_error'] = 'Invalid interaction request.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $interaction_member = $this->model->getMemberInteractionCounts($userId);
        if (!$interaction_member) {
            $_SESSION['flash_error'] = 'Member not found.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $titleMap = [
            'opened' => 'Opened',
            'deferred' => 'Deferred',
            'declined' => 'Declined',
            'meeting' => 'Meeting',
            'accepted' => 'Accepted',
        ];

        $rows = $this->model->getInteractionDetails($userId, $action);
        $reportTitle = $titleMap[$action];
        $interaction_action = $action;
        $interaction_user_id = $userId;

        require __DIR__ . '/../views/admin/user_interactions.php';
    }

    public function createuser()
    {
        // CSRF token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // require __DIR__ . '/../views/admin/add_user.php';
        header('Location: ' . BASE_URL . '/admin/add_user/user/basic');
        exit;
    }

    public function storeuser()
    {
        // ✅ CSRF CHECK
        if (
            empty($_POST['csrf_token']) ||
            $_POST['csrf_token'] !== $_SESSION['csrf_token']
        ) {
            $_SESSION['flash_error'] = "Invalid request!";
            header("Location: " . BASE_URL . "/admin/create-user");
            exit;
        }

        unset($_SESSION['csrf_token']);

        // ✅ VALIDATION
        $required = ['first_name', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['flash_error'] = "All required fields must be filled!";
                header("Location: " . BASE_URL . "/admin/create-user");
                exit;
            }
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = "Invalid email format!";
            header("Location: " . BASE_URL . "/admin/create-user");
            exit;
        }

        // ✅ CHECK DUPLICATE EMAIL (Make sure method exists)
        if ($this->userModel->emailExists($_POST['email'])) {
            $_SESSION['flash_error'] = "Email already exists!";
            header("Location: " . BASE_URL . "/admin/create-user");
            exit;
        }

        // ✅ PREPARE DATA
        $userData = [
            'first_name'    => trim($_POST['first_name']),
            'last_name'     => trim($_POST['last_name'] ?? ''),
            'gender'        => $_POST['gender'] ?? null,
            'dob'           => $_POST['dob'] ?? '2000-01-01',
            'email'         => trim($_POST['email']),
            'password_hash' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'status'        => $_POST['status'] ?? 'approved',
            'admin_comment' => $_POST['admin_comment'] ?? null,
            'avatar'        => $this->uploadAvatar($_FILES['avatar'] ?? null)
        ];

        // ✅ TRANSACTION (IMPORTANT)
        try {
            $this->userModel->beginTransaction();

            $userId = $this->userModel->modelone($userData);

            if (!$userId) {
                throw new Exception("User creation failed");
            }

            $profileData = [
                'user_id'        => $userId,
                'education'      => $_POST['education'] ?? null,
                'occupation'     => $_POST['occupation'] ?? null,
                'annual_income'  => $_POST['annual_income'] ?? null,
                'marital_status' => $_POST['marital_status'] ?? 'Single',
                'languages'      => $_POST['languages'] ?? null,
                'religion'       => $_POST['religion'] ?? null,
                'body_type'      => $_POST['body_type'] ?? null,
                'complexion'     => $_POST['complexion'] ?? null,
                'bio'            => $_POST['bio'] ?? null,
                'created_at'     => date('Y-m-d H:i:s')
            ];

            $this->userModel->modeltwo($profileData);

            $this->userModel->commit();

            $_SESSION['flash_success'] = "User created successfully!";
        } catch (Exception $e) {
            $this->userModel->rollBack();

            $_SESSION['flash_error'] = "Error: " . $e->getMessage();
            header("Location: " . BASE_URL . "/admin/create-user");
            exit;
        }

        header("Location: " . BASE_URL . "/admin/users");
        exit;
    }

    // ✅ SECURE FILE UPLOAD (public/uploads/avatars via app_save_upload)
    private function uploadAvatar($file)
    {
        if (!$file || empty($file['tmp_name'])) {
            return '/uploads/avatars/default.png';
        }
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true) || ($file['size'] ?? 0) > 2 * 1024 * 1024) {
            return '/uploads/avatars/default.png';
        }
        $rel = app_save_upload($file, 'avatars');

        return $rel !== null ? '/' . $rel : '/uploads/avatars/default.png';
    }

    public function viewUserProfile()
    {
        if (empty($_GET['id'])) {
            $_SESSION['flash_error'] = 'User ID missing.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $userId = (int) $_GET['id'];
        $user   = $this->userModel->findById($userId);

        if (!$user) {
            $_SESSION['flash_error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $age = null;
        if (!empty($user['dob']) && $user['dob'] !== '0000-00-00') {
            $dob = new DateTime($user['dob']);
            $age = (new DateTime())->diff($dob)->y;
        }

        $title = $user['first_name'] . ' ' . $user['last_name'];

        require __DIR__ . '/../views/admin/view_user_profile.php';
    }

    public function editProfileForm()
    {
        if (empty($_GET['id'])) {
            $_SESSION['flash_error'] = 'User ID missing.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $userId = (int) $_GET['id'];
        $user   = $this->userModel->findById($userId);

        if (!$user) {
            $_SESSION['flash_error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $_SESSION['csrf_token_user_' . $userId] = bin2hex(random_bytes(32));

        $title = 'Edit Profile';

        require __DIR__ . '/../views/admin/edit_user_profile.php';
    }

    public function updateProfile()
    {
        if (empty($_POST['id']) || empty($_POST['csrf_token'])) {
            $_SESSION['flash_error'] = 'Invalid request.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $userId = (int) $_POST['id'];

        if (
            !isset($_SESSION['csrf_token_user_' . $userId]) ||
            $_POST['csrf_token'] !== $_SESSION['csrf_token_user_' . $userId]
        ) {
            $_SESSION['flash_error'] = 'Invalid CSRF token.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        unset($_SESSION['csrf_token_user_' . $userId]);

        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'email'      => trim($_POST['email'] ?? ''),
        ];

        $updated = $this->userModel->updateUser($userId, $data);

        $_SESSION['flash_success'] = $updated
            ? 'Profile updated successfully.'
            : 'Failed to update profile.';

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function assignMember()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (empty($_POST['assigned_to']) || empty($_POST['assigned_member'])) {
                $_SESSION['flash_error'] = "Invalid data!";
                header('Location: ' . BASE_URL . '/admin/dashboard');
                exit;
            }

            $model = new MemberAssignmentModel();

            $data = [
                'assigned_to'     => $_POST['assigned_to'],
                'assigned_member' => $_POST['assigned_member'],
                'assigned_by'     => $_SESSION['admin_id'],
                'admin_comment'   => $_POST['admin_comment'] ?? null
            ];

            if ($model->assignMember($data)) {
                $_SESSION['flash_success'] = "Member Assigned Successfully!";
            } else {
                $_SESSION['flash_error'] = "Assignment failed!";
            }

            header('Location: ' . BASE_URL . '/admin/dashboard');
            exit;
        }
    }

    public function delete()
    {
        if (
            !empty($_POST['id']) &&
            !empty($_POST['csrf_token']) &&
            $_POST['csrf_token'] === $_SESSION['csrf_token']
        ) {
            $this->model->deleteUser((int)$_POST['id']);
            $_SESSION['flash_success'] = "User deleted!";
        } else {
            $_SESSION['flash_error'] = "Invalid request!";
        }

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function addComment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $comment = trim((string)($_POST['comment'] ?? ''));
        $type = trim((string)($_POST['comment_type'] ?? 'general'));

        if ($userId <= 0 || $comment === '') {
            $_SESSION['flash_error'] = 'Comment is required.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $this->model->addProfileComment($userId, (int)$_SESSION['admin_id'], $comment, $type ?: 'general');
        $_SESSION['flash_success'] = 'Comment added successfully.';
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function commentsJson()
    {
        $userId = (int)($_GET['user_id'] ?? 0);
        if ($userId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Invalid user.']);
            exit;
        }

        $filters = [
            'type' => trim((string)($_GET['type'] ?? '')),
            'date_from' => trim((string)($_GET['date_from'] ?? '')),
            'date_to' => trim((string)($_GET['date_to'] ?? '')),
        ];
        $rows = $this->model->getProfileComments($userId, $filters);
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'rows' => $rows]);
        exit;
    }

    public function memberDynamicTeamJson()
    {
        $userId = (int) ($_GET['user_id'] ?? 0);
        if ($userId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Invalid user.']);
            exit;
        }

        $data = $this->model->getMemberDynamicAssignTeam($userId);
        header('Content-Type: application/json');
        echo json_encode(['ok' => true] + $data);
        exit;
    }

    public function adminProfileView()
    {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->model->getUserDetailsById($id);
        if (!$user) {
            $_SESSION['flash_error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }
        $user = array_merge($user, $this->model->getUserListSupplement($id));
        require __DIR__ . '/../views/admin/admin_profile_view_clean.php';
    }

    public function profilePdfTemplate()
    {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->model->getUserDetailsById($id);
        if (!$user) {
            $_SESSION['flash_error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }
        require __DIR__ . '/../views/admin/profile_pdf_template.php';
    }

    public function sendEmailConfirmation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }
        $id = (int)($_POST['user_id'] ?? 0);
        $user = $this->model->getUserDetailsById($id);
        if (!$user || empty($user['email'])) {
            $_SESSION['flash_error'] = 'User email not found.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $name = trim(($user['first_name'] ?? '') . ' ' . ($user['second_name'] ?? ''));
        $logoUrl = BASE_URL . '/assets/images/logo.png';
        $body = '
            <div style="font-family:Arial,sans-serif;max-width:620px;margin:0 auto;border:1px solid #e5e5e5;padding:20px">
                <div style="text-align:center;margin-bottom:14px">
                    <img src="' . htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') . '" alt="Logo" style="max-height:60px">
                </div>
                <h2 style="margin:0 0 10px;color:#144f7c">Email Confirmation</h2>
                <p>Dear ' . htmlspecialchars($name !== '' ? $name : 'Member', ENT_QUOTES, 'UTF-8') . ',</p>
                <p>Your profile email is now verified by the admin team.</p>
                <p>If this was not requested by you, please contact us immediately.</p>
                <hr style="border:none;border-top:1px solid #eee;margin:18px 0">
                <p style="font-size:13px;color:#555;margin:0">
                    Wedding Wish Marriage Centre<br>
                    Email: support@weddingwishmarriagecentre.com<br>
                    Phone: +92-349-6186700
                </p>
            </div>
        ';
        $mailer = new MailService();
        $sent = $mailer->send($user['email'], 'Email Verification Confirmation', $body);

        $_SESSION['flash_' . ($sent === true ? 'success' : 'error')] =
            $sent === true ? 'Verification email sent.' : ('Email send failed: ' . $sent);
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    /**
     * Basic-details completeness for an existing member row (edit wizard).
     * Same required fields as add-member basic step, except password only must exist in DB.
     */
    private function editStepsBasicIncompleteMessage(array $user): ?string
    {
        if (trim((string) ($user['lead'] ?? '')) === '') {
            return 'Please complete the Basic Details form first (lead is required).';
        }
        if (trim((string) ($user['gender'] ?? '')) === '') {
            return 'Please complete the Basic Details form first (gender is required).';
        }
        if (trim((string) ($user['first_name'] ?? '')) === '') {
            return 'Please complete the Basic Details form first (first name is required).';
        }
        $last = trim((string) ($user['second_name'] ?? ''));
        if ($last === '') {
            $last = trim((string) ($user['last_name'] ?? ''));
        }
        if ($last === '') {
            return 'Please complete the Basic Details form first (last name is required).';
        }
        $email = trim((string) ($user['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Please complete the Basic Details form first (valid email is required).';
        }
        if (trim((string) ($user['password'] ?? '')) === '') {
            return 'Please complete the Basic Details form first (account must have a password).';
        }
        if (trim((string) ($user['mobile_number'] ?? '')) === '') {
            return 'Please complete the Basic Details form first (mobile number is required).';
        }

        return null;
    }

    /** Validate basic fields submitted from edit-steps (password optional if left blank). */
    private function validateEditStepsSubmitBasic(array $post): ?string
    {
        if (trim((string) ($post['lead'] ?? '')) === '') {
            return 'Please select a lead in Basic Details.';
        }
        if (trim((string) ($post['gender'] ?? '')) === '') {
            return 'Please select gender in Basic Details.';
        }
        if (trim((string) ($post['first_name'] ?? '')) === '') {
            return 'Please enter first name in Basic Details.';
        }
        $last = trim((string) ($post['second_name'] ?? ''));
        if ($last === '') {
            $last = trim((string) ($post['last_name'] ?? ''));
        }
        if ($last === '') {
            return 'Please enter last name in Basic Details.';
        }
        $email = trim((string) ($post['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Please enter a valid email in Basic Details.';
        }
        $pwd = (string) ($post['password'] ?? '');
        if (trim($pwd) !== '' && strlen($pwd) < 6) {
            return 'Password must be at least 6 characters.';
        }
        if (trim((string) ($post['mobile_number'] ?? '')) === '') {
            return 'Please enter mobile number in Basic Details.';
        }

        return null;
    }

    public function editProfileSteps()
    {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->model->getUserDetailsById($id);
        if (!$user) {
            $_SESSION['flash_error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }
        $columns = $this->model->getEditableColumns();
        $admin_details = $this->admin->get_admin_details();
        $edit_basic_incomplete_message = $this->editStepsBasicIncompleteMessage($user);
        $edit_basic_locked = ($edit_basic_incomplete_message !== null);
        require __DIR__ . '/../views/admin/edit_user_profile_steps.php';
    }

    public function updateProfileSteps()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid user.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }
        $basicErr = $this->validateEditStepsSubmitBasic($_POST);
        if ($basicErr !== null) {
            $_SESSION['flash_error'] = $basicErr;
            header('Location: ' . BASE_URL . '/admin/users/edit-steps?id=' . $id);
            exit;
        }
        $post = $_POST;
        $newPwd = trim((string)($post['password'] ?? ''));
        if ($newPwd === '') {
            unset($post['password']);
        } else {
            $post['password'] = password_hash($newPwd, PASSWORD_BCRYPT);
        }
        $this->mergeMemberMediaUploadsIntoPost($post);
        $updated = $this->model->updateAllUserDetails($id, $post);
        $_SESSION['flash_' . ($updated ? 'success' : 'error')] =
            $updated ? 'Profile updated successfully.' : 'No fields updated.';
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    private function saveUploadedFile(array $file, string $folder): ?string
    {
        $folder = trim(str_replace('\\', '/', $folder), '/');
        if ($folder === 'uploads') {
            $sub = '';
        } elseif (str_starts_with($folder, 'uploads/')) {
            $sub = substr($folder, strlen('uploads/'));
        } else {
            $sub = $folder;
        }
        $rel = app_save_upload($file, $sub);

        return $rel === null ? null : '/' . $rel;
    }

    /**
     * Persist new photos / ID proof / CV from edit-steps (multipart). Paths are web-visible under /public/uploads/.
     */
    private function mergeMemberMediaUploadsIntoPost(array &$post): void
    {
        $maxBytes = 8 * 1024 * 1024;
        $photoCols = ['photo1_status', 'photo2_url', 'photo3_url', 'photo4_url', 'photo5_url', 'photo6_url'];
        $photoExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $docCols = ['id_proof_file', 'cv_file'];
        $docExt = array_merge($photoExt, ['pdf']);

        foreach ($photoCols as $col) {
            if (empty($_FILES[$col]) || (int)($_FILES[$col]['error'] ?? 1) !== UPLOAD_ERR_OK) {
                continue;
            }
            $f = $_FILES[$col];
            if (($f['size'] ?? 0) > $maxBytes) {
                continue;
            }
            $ext = strtolower(pathinfo((string) $f['name'], PATHINFO_EXTENSION));
            if ($ext === '' || !in_array($ext, $photoExt, true)) {
                continue;
            }
            $rel = $this->saveUploadedFile($f, 'uploads');
            if ($rel !== null) {
                $post[$col] = ltrim($rel, '/');
            }
        }

        foreach ($docCols as $col) {
            if (empty($_FILES[$col]) || (int)($_FILES[$col]['error'] ?? 1) !== UPLOAD_ERR_OK) {
                continue;
            }
            $f = $_FILES[$col];
            if (($f['size'] ?? 0) > $maxBytes) {
                continue;
            }
            $ext = strtolower(pathinfo((string) $f['name'], PATHINFO_EXTENSION));
            if ($ext === '' || !in_array($ext, $docExt, true)) {
                continue;
            }
            $rel = $this->saveUploadedFile($f, 'uploads');
            if ($rel !== null) {
                $post[$col] = ltrim($rel, '/');
            }
        }
    }

    public function openTaskForm()
    {
        $userId = (int)($_GET['id'] ?? 0);
        $user = $this->model->getUserDetailsById($userId);
        if (!$user) {
            $_SESSION['flash_error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }
        $admins = $this->model->allAdminUsers();
        require __DIR__ . '/../views/admin/admin_task_form.php';
    }

    public function storeTask()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }
        $data = [
            'user_id' => (int)($_POST['user_id'] ?? 0),
            'task_name' => trim((string)($_POST['task_name'] ?? '')),
            'assigned_admin_id' => (int)($_POST['assigned_admin_id'] ?? 0),
            'status' => trim((string)($_POST['status'] ?? 'open')),
            'activity' => trim((string)($_POST['activity'] ?? '')),
            'main_topic' => trim((string)($_POST['main_topic'] ?? '')),
            'task_meeting' => trim((string)($_POST['task_meeting'] ?? '')),
            'date_from' => trim((string)($_POST['date_from'] ?? '')),
            'date_to' => trim((string)($_POST['date_to'] ?? '')),
            'priority' => trim((string)($_POST['priority'] ?? '')),
            'details' => trim((string)($_POST['details'] ?? '')),
            'image_path' => $this->saveUploadedFile($_FILES['image'] ?? [], 'uploads/tasks/images'),
            'attachment_path' => $this->saveUploadedFile($_FILES['attachment'] ?? [], 'uploads/tasks/attachments'),
            'admin_comment' => trim((string)($_POST['admin_comment'] ?? '')),
            'visible_to' => is_array($_POST['visible_to'] ?? null) ? implode(',', $_POST['visible_to']) : trim((string)($_POST['visible_to'] ?? '')),
            'created_by' => (int)$_SESSION['admin_id'],
        ];

        if ($data['user_id'] <= 0 || $data['task_name'] === '') {
            $_SESSION['flash_error'] = 'Task name is required.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $ok = $this->model->createTask($data);
        $_SESSION['flash_' . ($ok ? 'success' : 'error')] = $ok ? 'Task created.' : 'Task create failed.';
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }
}
