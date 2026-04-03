<?php
// app/controllers/DashboardController.php

class DashboardController extends Controller
{
    protected $userModel;
    protected $paidModel;
      protected $assignmentModel;

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
        $model = new MemberAssignmentModel();
        $assignments = $model->getUserAssignments((int)$_SESSION['user_id']);

        $error = $_SESSION['flash_error'] ?? '';
        $success = $_SESSION['flash_success'] ?? '';
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

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

        /* IMAGE UPLOAD */
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {

            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            if (is_writable($uploadDir)) {
                $ext      = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                $safeName = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                $dest     = $uploadDir . $safeName;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                    $avatarPath = '/uploads/avatars/' . $safeName;
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
        $profileImgUrl = !empty($img)
            ? BASE_URL . '/' . ltrim((string)$img, '/')
            : BASE_URL . '/assets/images/default-avatar.png';

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
        }

        header('Location: ' . $target);
        exit;
    }

/* =========================
   VIEW FULL PROFILE OF ANOTHER USER
========================= */
public function viewUserProfile($id)
{
    $userId = (int)$id;

    $member = $this->userModel->findMemberForDisplay($userId);
    if (!$member) {
        $_SESSION['flash_error'] = 'User not found.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    require_once __DIR__ . '/../models/UserProfile.php';
    $profileDetails = (new UserProfile())->getByUserId($userId) ?: null;

    $age = null;
    if (!empty($member['dob']) && $member['dob'] !== '0000-00-00') {
        $dob = new DateTime($member['dob']);
        $age = (new DateTime())->diff($dob)->y;
    }

    $img = $member['photo2_url'] ?? $member['avatar'] ?? ($member['photo1_status'] ?? '');
    $profileImgUrl = !empty($img)
        ? BASE_URL . '/' . ltrim((string)$img, '/')
        : BASE_URL . '/assets/images/default-avatar.png';

    $title = trim(($member['first_name'] ?? '') . ' ' . ($member['second_name'] ?? $member['last_name'] ?? ''));
    $error = $_SESSION['flash_error'] ?? '';
    $success = $_SESSION['flash_success'] ?? '';
    unset($_SESSION['flash_error'], $_SESSION['flash_success']);

    $assignment = null;
    $memberId = $userId;

    require __DIR__ . '/../views/dashboard/feed-view.php';
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

    $age = null;
    if (!empty($member['dob']) && $member['dob'] !== '0000-00-00') {
        $dob = new DateTime($member['dob']);
        $age = (new DateTime())->diff($dob)->y;
    }

    $img = $member['photo2_url'] ?? $member['avatar'] ?? ($member['photo1_status'] ?? '');
    $profileImgUrl = !empty($img)
        ? BASE_URL . '/' . ltrim((string)$img, '/')
        : BASE_URL . '/assets/images/default-avatar.png';

    $assignment = (object)$assignmentRow;
    $memberId = $assignedProfileId;
    $title = trim(($member['first_name'] ?? '') . ' ' . ($member['second_name'] ?? ''));
    $error = $_SESSION['flash_error'] ?? '';
    $success = $_SESSION['flash_success'] ?? '';
    unset($_SESSION['flash_error'], $_SESSION['flash_success']);

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
