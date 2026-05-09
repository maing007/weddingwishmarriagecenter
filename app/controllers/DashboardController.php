<?php
// app/controllers/DashboardController.php

class DashboardController extends Controller
{
    protected $userModel;
    protected $paidModel;
      protected $assignmentModel;

    /**
     * Full user_details row for PDF-style profile card; fallback merge for legacy members.
     *
     * @param array|null $profileDetails user_profile_details row
     */
    protected function resolveProfilePdfUser(int $targetId, array $member, ?array $profileDetails): array
    {
        require_once __DIR__ . '/../models/AdminUserModel.php';
        $row = (new AdminUserModel())->getUserDetailsById($targetId);
        if ($row) {
            return $row;
        }
        $merged = $member;
        if (is_array($profileDetails)) {
            foreach ($profileDetails as $k => $v) {
                if (!array_key_exists($k, $merged) || $merged[$k] === '' || $merged[$k] === null) {
                    $merged[$k] = $v;
                }
            }
        }
        if (empty($merged['work_detail']) && !empty($merged['occupation'])) {
            $merged['work_detail'] = (string) $merged['occupation'];
        }

        return $merged;
    }

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require_once __DIR__ . '/../models/MemberAssignmentModel.php';
          $model = new MemberAssignmentModel();

        require_once __DIR__ . '/../models/User.php';
        $this->userModel = new User();
        require_once __DIR__ . '/../models/AdminPaidProfileModel.php';
    $this->paidModel = new AdminPaidProfileModel(); // ✅ correct
        // 🔒 AUTH CHECK (GLOBAL FOR DASHBOARD)
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /* =========================
       DASHBOARD HOME (MEMBERS FEED)
    ========================== */
    public function index()
    {
        $title = 'Dashboard';
        $uid = (int) $_SESSION['user_id'];
        $model = new MemberAssignmentModel();
        $assignments = $model->getUserAssignments($uid);

        $viewer = $this->userModel->findById($uid);
        $discoverMembers = [];
        try {
            require_once __DIR__ . '/../models/MemberFeedModel.php';
            $feedModel = new MemberFeedModel();
            $discoverMembers = $feedModel->getDiscoverMembers(
                $uid,
                (string) ($viewer['gender'] ?? ''),
                48
            );
        } catch (Throwable $e) {
            error_log('Dashboard discover feed: ' . $e->getMessage());
            $discoverMembers = [];
        }

        $error = $_SESSION['flash_error'] ?? '';
        $success = $_SESSION['flash_success'] ?? '';
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        $feedApprovePopup = !empty($_SESSION['feed_approve_popup']);
        unset($_SESSION['feed_approve_popup']);

        require __DIR__ . '/../views/dashboard/home.php';
    }

    /* =========================
       EDIT PROFILE FORM
    ========================== */
    public function editProfileForm()
    {
        $userId = $_SESSION['user_id'];
        $user   = $this->userModel->findById($userId);

        if (!$user) {
            $_SESSION['flash_error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $title   = 'Edit Profile';
        $error   = $_SESSION['flash_error'] ?? '';
        $success = $_SESSION['flash_success'] ?? '';

        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        require __DIR__ . '/../views/dashboard/profile_edit.php';
    }

    /* =========================
       UPDATE BASIC PROFILE
    ========================== */
    public function updateProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name'] ?? '');
        $phone     = trim($_POST['phone'] ?? $_POST['mobile_number'] ?? '');
        $religion  = trim($_POST['religion'] ?? '');
        $bio       = trim($_POST['bio'] ?? '');
        $countryCode = trim($_POST['country_code'] ?? '');

        $birthDate  = $_POST['birth_date'] ?? '';
        $birthMonth = $_POST['birth_month'] ?? '';
        $birthYear  = $_POST['birth_year'] ?? '';

        if ($firstName === '' || $lastName === '') {
            $_SESSION['flash_error'] = 'First name and last name are required.';
            header('Location: ' . BASE_URL . '/dashboard/profile');
            exit;
        }

        $user = $this->userModel->findById($_SESSION['user_id']);
        if (!$user) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $dob = sprintf(
            '%04d-%02d-%02d',
            (int)($birthYear ?: date('Y', strtotime($user['dob']))),
            (int)($birthMonth ?: date('m', strtotime($user['dob']))),
            (int)($birthDate ?: date('d', strtotime($user['dob'])))
        );

        $avatarPath = $user['photo2_url'] ?? $user['avatar'] ?? '';

        /* IMAGE UPLOAD → public/uploads/avatars */
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo((string) $_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed, true) && ($_FILES['avatar']['size'] ?? 0) <= 2 * 1024 * 1024) {
                $rel = app_save_upload($_FILES['avatar'], 'avatars');
                if ($rel !== null) {
                    $avatarPath = '/' . $rel;
                }
            }
        }

        $payload = [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'phone'      => $phone,
            'religion'   => $religion,
            'dob'        => $dob,
            'bio'        => $bio,
            'country_code' => $countryCode,
        ];
        if ($avatarPath !== '') {
            $payload['avatar'] = $avatarPath;
        }

        $this->userModel->updateProfile($_SESSION['user_id'], $payload);

        $_SESSION['flash_success'] = 'Profile updated successfully.';
        header('Location: ' . BASE_URL . '/dashboard/profile');
        exit;
    }



    /* =========================
       VIEW SAVED PROFILES
    ========================== */
    public function savedProfiles()
    {
        $userId = $_SESSION['user_id'];
        $rows = $this->userModel->getSavedProfiles($userId);
        $seen = [];
        $savedProfiles = [];
        foreach ($rows as $row) {
            $sid = (int)($row['id'] ?? 0);
            if ($sid <= 0 || isset($seen[$sid])) {
                continue;
            }
            $seen[$sid] = true;
            $savedProfiles[] = $row;
        }

        $title = 'Saved Profiles';
        $error = $_SESSION['flash_error'] ?? '';
        $success = $_SESSION['flash_success'] ?? '';
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        require __DIR__ . '/../views/dashboard/saved_profiles.php';
    }

    /* =========================
       VIEW OWN PROFILE
    ========================== */
    public function viewProfile()
    {
        $userId = (int)$_SESSION['user_id'];
        $user   = $this->userModel->findById($userId);

        if (!$user) {
            $_SESSION['flash_error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        require_once __DIR__ . '/../models/UserProfile.php';
        $profileModel = new UserProfile();
        $profileDetails = $profileModel->getByUserId($userId);

        $myPackage = $this->paidModel->findLatestPackageForUser($userId);

        $age = null;
        if (!empty($user['dob']) && $user['dob'] !== '0000-00-00') {
            $dob = new DateTime($user['dob']);
            $age = (new DateTime())->diff($dob)->y;
        }

        $img = $user['photo2_url'] ?? $user['avatar'] ?? ($user['photo1_status'] ?? '');
        if (!empty($img)) {
            $profileImgUrl = public_url_for_path((string) $img);
        } else {
            $gDash = strtolower(trim((string) ($user['gender'] ?? '')));
            $profileImgUrl = ($gDash === 'female' || strncmp($gDash, 'female', 6) === 0)
                ? public_url_for_path('assets/images/female.png')
                : public_url_for_path('assets/images/male.png');
        }

        $title   = 'My Profile';
        $error   = $_SESSION['flash_error'] ?? '';
        $success = $_SESSION['flash_success'] ?? '';

        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        require __DIR__ . '/../views/dashboard/profile_view.php';
    }

    /* =========================
       SAVE / BOOKMARK PROFILE
    ========================== */
    public function saveProfile()
    {
        $userId = $_SESSION['user_id'];
        if (
            empty($_POST['csrf_token']) ||
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], (string)$_POST['csrf_token'])
        ) {
            $_SESSION['flash_error'] = 'Invalid form submission.';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $savedId = (int)($_POST['user_id'] ?? 0);
        if ($savedId > 0 && $savedId !== (int)$userId) {
            $this->userModel->saveProfile($userId, $savedId);
            $_SESSION['flash_success'] = 'Profile saved to your list.';
        }

        $target = BASE_URL . '/dashboard';
        $ret = (string)($_POST['return'] ?? 'dashboard');
        if ($ret === 'saved') {
            $target = BASE_URL . '/dashboard/saved-profiles';
        } elseif ($ret === 'assignment') {
            $aid = (int)($_POST['assignment_id'] ?? 0);
            if ($aid > 0) {
                $target = BASE_URL . '/dashboard/openAssignment?id=' . $aid;
            }
        } elseif ($ret === 'discover-profile') {
            $pid = (int) ($_POST['user_id'] ?? 0);
            if ($pid > 0) {
                $target = BASE_URL . '/dashboard/user/' . $pid . '?context=discover';
            }
        }

        header('Location: ' . $target);
        exit;
    }

/* =========================
   VIEW FULL PROFILE OF ANOTHER USER
========================= */
public function viewUserProfile($id)
{
    $targetId = (int) $id;
    $viewerId = (int) $_SESSION['user_id'];

    $member = $this->userModel->findMemberForDisplay($targetId);
    if (!$member) {
        $_SESSION['flash_error'] = 'User not found.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    require_once __DIR__ . '/../models/MemberAssignmentModel.php';
    $assignmentModel = new MemberAssignmentModel();
    $assignmentRow = $assignmentModel->findByPair($viewerId, $targetId);
    $assignment = $assignmentRow ? (object) $assignmentRow : null;

    $viewer = $this->userModel->findById($viewerId);
    $context = strtolower(trim((string) ($_GET['context'] ?? '')));
    $isDiscoverContext = ($context === 'discover');

    require_once __DIR__ . '/../models/MemberFeedModel.php';
    $feedModel = new MemberFeedModel();
    $feedInteraction = null;
    try {
        $feedInteraction = $feedModel->getInteraction($viewerId, $targetId);
    } catch (Throwable $e) {
        $feedInteraction = null;
    }

    $canDiscover = $feedModel->canDiscoverInteract(
        $viewerId,
        $targetId,
        (string) ($viewer['gender'] ?? ''),
        (string) ($member['gender'] ?? '')
    );
    $savedOk = $this->userModel->isProfileSaved($viewerId, $targetId);
    $allowed = $assignment !== null
        || $savedOk
        || MemberFeedModel::isOppositeGender($viewer['gender'] ?? '', $member['gender'] ?? '');

    if (!$allowed) {
        $_SESSION['flash_error'] = 'You cannot view this profile.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    if ($isDiscoverContext && (!$canDiscover || $assignment !== null)) {
        $_SESSION['flash_error'] = 'This profile is not available in discovery.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    if ($isDiscoverContext && $canDiscover && $assignment === null) {
        try {
            $feedModel->markViewed($viewerId, $targetId);
            $feedInteraction = $feedModel->getInteraction($viewerId, $targetId);
        } catch (Throwable $e) {
            // ignore
        }
    }

    require_once __DIR__ . '/../models/UserProfile.php';
    $profileDetails = (new UserProfile())->getByUserId($targetId) ?: null;

    $profilePdfUser = $this->resolveProfilePdfUser($targetId, $member, $profileDetails);

    require_once __DIR__ . '/../helpers/profile_pdf_template.php';
    $title = profile_pdf_template_compute_vars($profilePdfUser, false)['pdfFileTitle'];
    $error = $_SESSION['flash_error'] ?? '';
    $success = $_SESSION['flash_success'] ?? '';
    unset($_SESSION['flash_error'], $_SESSION['flash_success']);

    $memberId = $targetId;
    $showContactDetails = $assignment !== null;

    require __DIR__ . '/../views/dashboard/feed-view.php';
}

/**
 * Discovery feed: approve (remove from feed; contact admin for next steps) or defer (admin deferred matches).
 */
public function feedAction()
{
    $viewerId = (int) $_SESSION['user_id'];
    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], (string) $_POST['csrf_token'])
    ) {
        $_SESSION['flash_error'] = 'Invalid form submission.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    $action = strtolower(trim((string) ($_POST['action'] ?? '')));
    $targetId = (int) ($_POST['target_user_id'] ?? 0);
    if ($targetId <= 0 || $targetId === $viewerId) {
        $_SESSION['flash_error'] = 'Invalid profile.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    require_once __DIR__ . '/../models/MemberFeedModel.php';
    $feedModel = new MemberFeedModel();
    $viewer = $this->userModel->findById($viewerId);
    $target = $this->userModel->findById($targetId);
    if (!$viewer || !$target) {
        $_SESSION['flash_error'] = 'Profile not found.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    if (!$feedModel->canDiscoverInteract(
        $viewerId,
        $targetId,
        (string) ($viewer['gender'] ?? ''),
        (string) ($target['gender'] ?? '')
    )) {
        $_SESSION['flash_error'] = 'This action is not allowed for this profile.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    $existing = $feedModel->getInteraction($viewerId, $targetId);
    if ($action === 'approve') {
        if (!empty($existing['approved_at'])) {
            $_SESSION['flash_success'] = 'You have already approved this profile.';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        $feedModel->approve($viewerId, $targetId);
        $_SESSION['flash_success'] = 'Thank you. Please contact admin or support for the next steps.';
        $_SESSION['feed_approve_popup'] = true;
    } elseif ($action === 'deferred') {
        if (!empty($existing['deferred_at'])) {
            $_SESSION['flash_success'] = 'This profile was already deferred.';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        $feedModel->defer($viewerId, $targetId);
        require_once __DIR__ . '/../models/DeferredMatchModel.php';
        try {
            (new DeferredMatchModel())->insertFromDashboardFeed($viewerId, $targetId);
        } catch (Throwable $e) {
            error_log('DeferredMatch insertFromDashboardFeed: ' . $e->getMessage());
        }
        $_SESSION['flash_success'] = 'Profile deferred. Our team has been notified.';
    } else {
        $_SESSION['flash_error'] = 'Unknown action.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    header('Location: ' . BASE_URL . '/dashboard');
    exit;
}

public function openAssignment()
{
    $userId = (int)$_SESSION['user_id'];
    $assignmentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (!$assignmentId) {
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    require_once __DIR__ . '/../models/MemberAssignmentModel.php';
    $assignmentModel = new MemberAssignmentModel();

    $assignmentRow = $assignmentModel->findOwnedBy($assignmentId, $userId);
    if (!$assignmentRow) {
        $_SESSION['flash_error'] = 'Assignment not found.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    $assignmentModel->markOpened($assignmentId);
    $assignmentModel->addHistory($assignmentId, $userId, 'opened');

    $assignedProfileId = (int)$assignmentRow['assigned_member'];
    $member = $this->userModel->findMemberForDisplay($assignedProfileId);

    if (!$member) {
        $_SESSION['flash_error'] = 'Profile not found.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    require_once __DIR__ . '/../models/UserProfile.php';
    $profileDetails = (new UserProfile())->getByUserId($assignedProfileId) ?: null;

    $profilePdfUser = $this->resolveProfilePdfUser($assignedProfileId, $member, $profileDetails);

    require_once __DIR__ . '/../helpers/profile_pdf_template.php';
    $title = profile_pdf_template_compute_vars($profilePdfUser, false)['pdfFileTitle'];

    $assignment = (object)$assignmentRow;
    $memberId = $assignedProfileId;
    $error = $_SESSION['flash_error'] ?? '';
    $success = $_SESSION['flash_success'] ?? '';
    unset($_SESSION['flash_error'], $_SESSION['flash_success']);

    $isDiscoverContext = false;
    $canDiscover = false;
    $feedInteraction = null;
    $showContactDetails = true;

    require __DIR__ . '/../views/dashboard/feed-view.php';
}

public function acceptAssignment()
{
    require_once __DIR__ . '/../models/MemberAssignmentModel.php';
    $assignmentModel = new MemberAssignmentModel();

    $assignmentId = (int)($_GET['id'] ?? 0);
    $userId = (int)$_SESSION['user_id'];

    if (!$assignmentId || !$assignmentModel->findOwnedBy($assignmentId, $userId)) {
        $_SESSION['flash_error'] = 'Assignment not found.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    $assignmentModel->updateStatus($assignmentId, 'accepted');
    $assignmentModel->addHistory($assignmentId, $userId, 'accepted');

    $_SESSION['flash_success'] = 'Assignment accepted.';
    header('Location: ' . BASE_URL . '/dashboard');
    exit;
}

public function declineAssignment()
{
    require_once __DIR__ . '/../models/MemberAssignmentModel.php';
    $assignmentModel = new MemberAssignmentModel();

    $assignmentId = (int)($_GET['id'] ?? 0);
    $userId = (int)$_SESSION['user_id'];

    if (!$assignmentId || !$assignmentModel->findOwnedBy($assignmentId, $userId)) {
        $_SESSION['flash_error'] = 'Assignment not found.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    $assignmentModel->updateStatus($assignmentId, 'declined');
    $assignmentModel->addHistory($assignmentId, $userId, 'declined');

    $_SESSION['flash_success'] = 'Assignment declined.';
    header('Location: ' . BASE_URL . '/dashboard');
    exit;
}


}
