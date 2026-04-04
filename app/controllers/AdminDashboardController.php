<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class AdminDashboardController extends Controller
{

    protected $manager;
    protected $admin;

    // Declare properties explicitly to avoid PHP 8.2+ deprecation
    public $Contact_Messages;
    public $profiles;
    public $inc;

    public function __construct()
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
        require_once __DIR__ . '/../models/Admin.php';
        $this->admin = new Admin();

        require_once __DIR__ . '/../models/AdminUserManager.php';
        $this->manager = new AdminUserManager();

        require_once __DIR__ . '/../models/Contact.php';
        $this->Contact_Messages = new Contact();

        require_once __DIR__ . '/../models/AdminPaidProfileModel.php';
        $this->profiles = new AdminPaidProfileModel();

        require_once __DIR__ . '/../models/AdminInvoiceModel.php';
        $this->inc = new AdminInvoiceModel();
    }


    public function displayadminname()
    {
        $admin = $this->admin->findById($_SESSION['admin_id']);
        return $admin ? $admin['name'] : 'Admin';
        require __DIR__ . '/../views/admin/partials/header.php';
    }
    public function index()
    {
        $profiles = $this->profiles->all();
        $inc = $this->inc->all();
        $users = $this->manager->allUsers();
        require __DIR__ . '/../views/admin/dashboard.php';
    }
    public function membersreport()
    {
        require_once __DIR__ . '/../models/MemberSaleFeeModel.php';

        $model = new MemberSaleFeeModel();

        $search = trim((string) ($_GET['search_filed'] ?? ''));

        $saleScope = strtolower(trim((string) ($_GET['sale_scope'] ?? 'all')));
        $allowedScopes = ['all', MemberSaleFeeModel::TYPE_REGISTRATION, MemberSaleFeeModel::TYPE_RISHTA];
        if (!in_array($saleScope, $allowedScopes, true)) {
            $saleScope = 'all';
        }

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

        $tabCounts = $model->salesReportScopeCounts($search);
        $totalRows = $model->salesReportTotal($search, $saleScope, $payFilter, $filterStaff);
        $totalPages = max(1, (int) ceil($totalRows / $limit));
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $limit;
        $rows = $model->salesReportRows($search, $saleScope, $payFilter, $filterStaff, $limit, $offset);
        $staffFilterOptions = $model->salesReportDistinctStaffNames();

        if ($saleScope === MemberSaleFeeModel::TYPE_REGISTRATION) {
            $pageHead = 'Manage Member Sale — Registration';
        } elseif ($saleScope === MemberSaleFeeModel::TYPE_RISHTA) {
            $pageHead = 'Manage Member Sale — Rishta';
        } else {
            $pageHead = 'Manage Member Sale — ALL';
        }

        $msrSelfUrl = BASE_URL . '/admin/sales-report';
        $msrTabUrlAll = BASE_URL . '/admin/sales-report';
        $msrTabUrlReg = BASE_URL . '/admin/reports/payments/registration-fee';
        $msrTabUrlRishta = BASE_URL . '/admin/reports/payments/rishta-fee';
        $msrLockScope = false;

        require_once __DIR__ . '/../views/admin/memberssalesreport.php';
    }
    public function team()
    {
        // Get search/filter/sort/limit from POST or GET
        $search = $_POST['search_filed'] ?? '';
        $limit  = $_POST['limit_per_page'] ?? 10;
        $sort   = $_POST['sort_order_js'] ?? 'created_at-desc';
        $status = $_GET['status'] ?? '';

        // Fetch admin users
        $admins = $this->admin->get_admin($search, $limit, $sort, $status);

        // Pass to view
        require __DIR__ . '/../views/admin/team-managements.php';
    }
    public function bulkAction()
    {
        require_once __DIR__ . '/../models/Admin.php';
        $adminModel = new Admin();

        $ids = $_POST['ids'] ?? [];
        $action = $_POST['action'] ?? '';

        if (empty($ids)) {
            $_SESSION['error'] = "No users selected";
            header("Location: " . BASE_URL . "/admin/team-management");
            exit;
        }

        foreach ($ids as $id) {

            if ($action == 'approve') {
                $adminModel->updateStatus($id, 'approved');
            }

            if ($action == 'delete') {
                $adminModel->deleteAdmin($id);
            }
        }

        $_SESSION['success'] = "Bulk action completed";
        header("Location: " . BASE_URL . "/admin/team-management");
        exit;
    }
    public function attendanceData()
    {
        require_once __DIR__ . '/../models/Admin.php';

        $admin = new Admin();

        $data = $this->requestData(); // 🔥 use your helper

        $id = $data['id'] ?? 0;

        if (!$id) {
            $this->json(['weekly' => 0]);
        }

        $weekly = $admin->getWeeklyAttendance($id);

        $this->json([
            'weekly' => (int)$weekly
        ]);
    }
    public function markAttendance()
    {
        require_once __DIR__ . '/../models/Admin.php';

        $admin = new Admin();

        $data = $this->requestData();

        $id = $data['id'] ?? 0;

        if (!$id) {
            $this->json(['status' => 'error']);
        }

        $admin->markAttendance($id);

        $this->json(['status' => 'success']);
    }
    public function deleteUser()
    {
        $this->manager->deleteUser((int)$_POST['user_id']);
        header('Location: ' . BASE_URL . '/admin/dashboard');
    }

    public function show_messages()
    {
        $Contact_Messages = $this->Contact_Messages->contact_messages();
        require __DIR__ . '/../views/admin/show_contact_messages.php';
    }
    // DELETE SINGLE
    public function deleteMessage($id)
    {
        $this->Contact_Messages->deleteContactMessage($id);

        header("Location: " . BASE_URL . "/admin/contact-messages?success=Deleted");
        exit;
    }

    // BULK ACTION (DELETE / EXPORT)
    public function bulkMessagesAction()
    {
        $action = $_POST['action'] ?? '';
        $ids    = $_POST['ids'] ?? [];

        if (empty($ids)) {
            header("Location: " . BASE_URL . "/admin/contact-messages?error=No selection");
            exit;
        }

        if ($action === 'delete') {
            $this->Contact_Messages->deleteMultipleMessages($ids);
            header("Location: " . BASE_URL . "/admin/contact-messages?success=Deleted");
            exit;
        }

        if ($action === 'export') {
            $messages = $this->Contact_Messages->getMessagesByIds($ids);
            $this->exportCSV($messages);
        }
    }

    // EXPORT ALL
    public function exportAllMessages()
    {
        $messages = $this->Contact_Messages->getAllMessages();
        $this->exportCSV($messages);
    }

    // CSV FUNCTION
    private function exportCSV($data)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="contacts.csv"');

        $output = fopen('php://output', 'w');

        // Header row
        fputcsv($output, ['ID', 'Name', 'Phone', 'Email', 'Subject', 'Message']);

        foreach ($data as $row) {
            fputcsv($output, [
                $row['id'],
                $row['name'],
                $row['phone'],
                $row['email'],
                $row['subject'],
                $row['description']
            ]);
        }

        fclose($output);
        exit;
    }

    public function markPaid()
    {
        $this->manager->markPaid($_POST['user_id'], $_POST['package_id']);
        header('Location: ' . BASE_URL . '/admin/dashboard');
    }

    public function changePasswordForm()
    {
        $adminId = $_SESSION['admin_id'] ?? null;

        if (!$adminId) {
            die("Unauthorized");
        }

        // get admin data (optional)
        $admin = $this->admin->getById($adminId);

        require __DIR__ . '/../views/admin/changepassword.php';
    }
    public function updatePassword()
    {
        $adminId = $_SESSION['admin_id'] ?? null;

        if (!$adminId) {
            die("Unauthorized");
        }

        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (!$password || !$confirm) {
            $error = "All fields are required";
            require __DIR__ . '/../views/admin/changepassword.php';
            return;
        }

        if (strlen($password) < 6) {
            $error = "Password must be at least 6 characters";
            require __DIR__ . '/../views/admin/changepassword.php';
            return;
        }

        if ($password !== $confirm) {
            $error = "Passwords do not match";
            require __DIR__ . '/../views/admin/changepassword.php';
            return;
        }

        // call model
        $this->admin->updatePassword($adminId, $password);

        header("Location: " . BASE_URL . "/admin/dashboard?success=Password updated");
        exit;
    }
}
