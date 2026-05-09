<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/AdminInvoiceModel.php';

require_once __DIR__ . '/../../vendor/dompdf/src/Dompdf.php';
require_once __DIR__ . '/../../vendor/dompdf/src/Options.php';

use Dompdf\Dompdf;

class AdminInvoicesController extends Controller
{
    protected $model;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once __DIR__ . '/../models/AdminInvoiceModel.php';
        $this->model = new AdminInvoiceModel();
        

        // Admin auth check (basic)
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
        
        require_once __DIR__.'/../models/Admin.php';
        $this->admin = new Admin();
    }


    public function displayadminname() {
        $admin = $this->admin->findById($_SESSION['admin_id']);
        return $admin ? $admin['name'] : 'Admin';
         require __DIR__.'/../views/admin/partials/header.php';
    }


    // ✅ REQUIRED: /admin/invoices
    public function index()
    {
        $invoices = $this->model->all();
        $title = 'Invoices';

        require __DIR__ . '/../views/admin/invoices.php';
    }

    // ✅ PDF DOWNLOAD
    public function download()
    {
        $id = (int)($_GET['package_id'] ?? 0);
        if ($id <= 0) die('Invalid invoice');

        $invoice = $this->model->find($id);
        if (!$invoice) die('Invoice not found');

        ob_start();
        require __DIR__ . '/../views/admin/invoice_pdf.php';
        $html = ob_get_clean();
require_once __DIR__ . '/../../vendor/dompdf/src/Dompdf.php';
require_once __DIR__ . '/../../vendor/dompdf/src/Options.php';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream(
            $invoice['invoice_no'] . '.pdf',
            ['Attachment' => true]
        );
        exit;
    }
}
