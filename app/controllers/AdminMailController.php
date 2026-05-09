<?php

require_once __DIR__ . '/../services/MailService.php';

class AdminMailController extends Controller
{
    protected $admin;

    public function __construct()
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
        require_once __DIR__ . '/../models/Admin.php';
        $this->admin = new Admin();
    }

    public function displayadminname(): string
    {
        $admin = $this->admin->findById($_SESSION['admin_id']);

        return $admin ? (string) $admin['name'] : 'Admin';
    }

    public function inbox(): void
    {
        require __DIR__ . '/../views/admin/mail/unified_inbox.php';
    }

    public function compose(): void
    {
        require __DIR__ . '/../views/admin/mail/compose.php';
    }

    public function send(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/mail/compose');
            exit;
        }

        require_once dirname(__DIR__) . '/services/AdminSmtpMailer.php';

        try {
            $mailer = new AdminSmtpMailer();
            $mailer->send(
                (string) ($_POST['to'] ?? ''),
                (string) ($_POST['subject'] ?? ''),
                (string) ($_POST['message'] ?? '')
            );
            $_SESSION['success'] = 'Email sent successfully';
            header('Location: ' . BASE_URL . '/admin/mail/inbox');
        } catch (Exception $e) {
            $_SESSION['error'] = 'Mail failed: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '/admin/mail/compose');
        }
        exit;
    }
}
