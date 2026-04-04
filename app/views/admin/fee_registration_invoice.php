<?php
declare(strict_types=1);
/** @var array<string, mixed> $row */
/** @var string $adminName */

$ud = $row['_user'] ?? [];
$client = trim((string) ($row['client_name'] ?? 'Member'));
$custId = trim((string) ($ud['matri_id'] ?? ''));
if ($custId === '') {
    $custId = trim((string) ($row['matri_id'] ?? ''));
}
if ($custId === '') {
    $custId = matri_id_display('', (int) ($ud['id'] ?? 0), true);
}
$inv = trim((string) ($row['invoice_ref'] ?? ''));
if ($inv === '') {
    $inv = 'INV-01-' . str_pad((string) ($row['id'] ?? '0'), 6, '0', STR_PAD_LEFT);
}
$pkg = trim((string) ($row['package'] ?? ''));
$amt = (float) ($row['fee_amount'] ?? 0);
$disc = (float) ($row['discount_amount'] ?? 0);
$lineSubtotal = max(0, $amt + $disc);
$grand = max(0, $amt);
$act = !empty($row['activation_date']) ? date('Y-m-d', strtotime((string) $row['activation_date'])) : date('Y-m-d');
$exp = $act;
if (!empty($ud['id'])) {
    try {
        $db = Database::getInstance()->pdo();
        $st = $db->prepare('SELECT expires_at FROM user_packages WHERE user_id = :u ORDER BY id DESC LIMIT 1');
        $st->execute([':u' => (int) $ud['id']]);
        $ex = $st->fetchColumn();
        if ($ex) {
            $exp = date('Y-m-d', strtotime((string) $ex));
        }
    } catch (Throwable $e) {
    }
}
$durDays = (int) round((strtotime($exp) - strtotime($act)) / 86400);
if ($durDays < 1) {
    $durDays = 1;
}
$mobile = trim((string) ($ud['mobile_number'] ?? '')) !== '' ? (string) $ud['mobile_number'] : (string) ($ud['phone'] ?? '');
$emailTo = trim((string) ($ud['email'] ?? ''));
$payStatusLabel = stripos((string) ($row['staff_payment_status'] ?? ''), 'paid') !== false ? 'Registration Fee Received' : 'Registration Fee Receivable';
$payStatusClass = stripos((string) ($row['staff_payment_status'] ?? ''), 'paid') !== false ? 'text-success' : 'text-danger';
$payMode = trim((string) ($row['payment_mode'] ?? ''));
$payModeDisp = $payMode !== '' ? $payMode : 'N/A';
$staffName = trim((string) ($row['staff_name'] ?? ''));
$staffNameDisp = $staffName !== '' ? $staffName : 'N/A';
$finalRishta = (float) ($ud['final_fee'] ?? 0);
$finalRishtaNote = $finalRishta > 0 ? number_format($finalRishta, 0, '.', ',') : '0';

$addrParts = array_filter([
    trim((string) ($ud['address'] ?? '')),
    trim((string) ($ud['area'] ?? '')),
    trim((string) ($ud['city'] ?? '')),
    trim((string) ($ud['state'] ?? '')),
    trim((string) ($ud['country'] ?? '')),
], static fn (string $s) => $s !== '');
$addressDisp = $addrParts !== [] ? implode(', ', $addrParts) : '';

$fmtPkr = static function (float $n): string {
    return 'PKR ' . number_format($n, 2, '.', ',');
};

$productLine = $pkg !== '' ? ($pkg . ' Membership for ' . $durDays . ' Days') : ('Membership for ' . $durDays . ' Days');

/** Office branding (adjust URLs/text per deployment). */
$invoiceLogoUrl = ''.BASE_URL.'/assets/images/logo.png';
$invoiceCompany = 'Wedding Wish Marriage Center';
$invoiceAddress = 'Model Town Link Road Zainab Tower Office No. M75 Near Amana Mall';
$invoicePhones = [
    '+92 322-6817540',
    '+92 309-7688394',
    '+92 309-5996132',
];
$invoiceEmail = 'info@weddingwishmarriagecentre.com';
$invoiceStaffPhone = 'N/A';

$invoiceBanks = [
    [
        'logo' => 'https://bankislami.com.pk/wp-content/uploads/2021/12/BIPL-Logo-640x199.png',
        'bank_label' => 'Bank Islami',
        'account' => '206600326970001',
        'title' => 'WEDDING WISH MARRIAGE CENTER',
        'iban' => 'PK09BKIP0206600326970001',
    ],
];

$title = $inv . ' ' . $custId . ' ' . $client;
$topbarTitle = $inv . ' ' . $custId . ' ' . $client;

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>
<style>
    .invoice-admin-page {
        background: #f4f7f6;
    }
    .invoice-wrap-outer {
        max-width: 1100px;
        margin: 0 auto;
    }
    .invoice-print-hint {
        background: #e3f2fd;
        border: 1px solid #90caf9;
        color: #1565c0;
        font-size: 13px;
        padding: 10px 14px;
        border-radius: 4px;
        margin-bottom: 16px;
    }
    .invoice-topbar {
        justify-content: flex-start;
        padding-left: 12px;
        padding-right: 16px;
    }
    .invoice-topbar .invoice-topbar-title {
        font-size: 15px;
        font-weight: 600;
        color: #111;
        margin-left: 8px;
        margin-right: auto;
        letter-spacing: 0.01em;
        line-height: 1.35;
    }
    .invoice-topbar .admin-profile {
        margin-left: auto;
    }
    section.invoice {
        background: #fff;
        padding: 22px 28px 32px;
        border: 1px solid #e8e8e8;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
        color: #222;
    }
    section.invoice .page-header {
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #eee;
    }
    section.invoice .page-header img#logo {
        max-height: 42px;
        width: auto;
        vertical-align: middle;
    }
    section.invoice .invoice-info {
        margin-bottom: 8px;
    }
    section.invoice .invoice-col {
        font-size: 13px;
        line-height: 1.55;
        color: #333;
    }
    section.invoice .invoice-col address {
        margin-top: 6px;
        font-style: normal;
    }
    section.invoice .table {
        margin-bottom: 12px;
        font-size: 13px;
    }
    section.invoice .table > thead > tr > th {
        border-bottom: 2px solid #e0e0e0;
        font-weight: 600;
        color: #333;
        padding: 10px 8px;
    }
    section.invoice .table > tbody > tr > td {
        padding: 10px 8px;
        border-top: 1px solid #eee;
        vertical-align: middle;
    }
    section.invoice hr {
        margin: 18px 0;
        border: 0;
        border-top: 1px solid #e0e0e0;
    }
    section.invoice h4 {
        font-size: 15px;
        font-weight: 700;
        margin: 14px 0 8px;
        color: #222;
    }
    section.invoice h3.pl-50 {
        font-size: 17px;
        font-weight: 700;
        margin: 16px 0 10px;
    }
    section.invoice .pl-50 {
        padding-left: 50px;
        margin-bottom: 0;
        font-size: 13px;
        line-height: 1.65;
        color: #333;
    }
    section.invoice .inv_bank_logo img {
        max-height: 36px;
        max-width: 120px;
        width: auto;
        height: auto;
        object-fit: contain;
    }
    section.invoice .text-center.end-note {
        margin-top: 20px;
        font-size: 12px;
        color: #888;
    }
    .invoice-print-row img[onclick] {
        max-width: 48px;
        cursor: pointer;
    }
    @media print {
        .dashboard-sidebar,
        #sidebarOverlay,
        .invoice-topbar .mobile-menu-btn,
        .admin-topbar .admin-profile,
        .no-print,
        .invoice-print-hint {
            display: none !important;
        }
        .admin-main {
            margin-left: 0 !important;
        }
        .invoice-admin-page {
            background: #fff !important;
            padding: 0 !important;
        }
        .invoice-wrap-outer {
            max-width: 100% !important;
        }
        section.invoice {
            border: none;
            box-shadow: none;
            padding: 12px 16px;
        }
    }
</style>

<div class="admin-main">
    <div class="admin-topbar invoice-topbar">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu">
            <i class="fa fa-bars"></i>
        </button>
        <div class="invoice-topbar-title"><?= htmlspecialchars($topbarTitle, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="admin-profile" id="adminProfileTrigger">
            <div class="admin-profile-box">
                <span><?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></span>
                <i class="fa fa-user"></i>
            </div>
            <div class="admin-dropdown" id="adminDropdown">
                <a href="<?= BASE_URL ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
                <a href="<?= BASE_URL ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </div>

    <main class="admin-page invoice-admin-page">
        <div class="container-fluid py-3">
            <div class="invoice-wrap-outer">
                <div class="invoice-print-hint no-print">
                    Note: This page has been enhanced for printing. Click the print control at the bottom of the invoice to print.
                </div>

                <section class="invoice" id="section-to-print">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="page-header">
                                <?php if ($invoiceLogoUrl !== ''): ?>
                                    <img id="logo" src="<?= htmlspecialchars($invoiceLogoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Logo">&nbsp;
                                <?php endif; ?>
                                <strong><?= htmlspecialchars($invoiceCompany, ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="row invoice-info">
                        <div class="col-md-4 col-xs-4 invoice-col">
                            From
                            <address>
                                <strong><?= htmlspecialchars($invoiceCompany, ENT_QUOTES, 'UTF-8') ?></strong><br>
                                <div class="row">
                                    <div class="col-xs-12"><?= htmlspecialchars($invoiceAddress, ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="col-xs-12" style="margin-top:6px;"><strong>Call us:</strong></div>
                                    <?php foreach ($invoicePhones as $ph): ?>
                                        <div class="col-xs-12"><?= htmlspecialchars($ph, ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php endforeach; ?>
                                    <div class="col-xs-12" style="margin-top:6px;">Email: <?= htmlspecialchars($invoiceEmail, ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="col-xs-12" style="margin-top:6px;">Payment Status : <b class="<?= htmlspecialchars($payStatusClass, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($payStatusLabel, ENT_QUOTES, 'UTF-8') ?></b></div>
                                </div>
                            </address>
                        </div>
                        <div class="col-md-4 col-xs-4 invoice-col">
                            &nbsp;&nbsp;&nbsp; To
                            <address>
                                <div class="col-xs-12"><strong><?= htmlspecialchars($client, ENT_QUOTES, 'UTF-8') ?></strong></div>
                                <div class="col-xs-12">Mobile : <?= htmlspecialchars($mobile !== '' ? $mobile : '', ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="col-xs-12">Email : <?= htmlspecialchars($emailTo, ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="col-xs-12">Address : <?= htmlspecialchars($addressDisp, ENT_QUOTES, 'UTF-8') ?></div>
                            </address>
                        </div>
                        <div class="col-md-4 col-xs-4 invoice-col">
                            <div class="col-xs-12"><strong>Invoice : </strong><?= htmlspecialchars($inv, ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="col-xs-12"><strong>Customer Id : </strong><?= htmlspecialchars($custId, ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="col-xs-12"><strong>Payment Mode : </strong><?= htmlspecialchars($payModeDisp, ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="col-xs-12"><strong>Staff : </strong><?= htmlspecialchars($staffNameDisp, ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="col-xs-12"><strong>Staff Phone : </strong><?= htmlspecialchars($invoiceStaffPhone, ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <br>
                        <div class="col-xs-12 table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Qty</th>
                                        <th>Product</th>
                                        <th>Activated On</th>
                                        <th>Expired On</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><?= htmlspecialchars($productLine, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($act, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($exp, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($fmtPkr($lineSubtotal), ENT_QUOTES, 'UTF-8') ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><b>&nbsp;</b></td>
                                        <td><b>Discount</b></td>
                                        <td><?= htmlspecialchars($fmtPkr($disc), ENT_QUOTES, 'UTF-8') ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><b>&nbsp;</b></td>
                                        <td><b>Grand Total</b></td>
                                        <td><b><?= htmlspecialchars($fmtPkr($grand), ENT_QUOTES, 'UTF-8') ?></b></td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <h4>Note : </h4>
                            <p class="pl-50">1. This fee is non-refundable <br>
                                2. Final agreed fee is Rs. <?= htmlspecialchars($finalRishtaNote, ENT_QUOTES, 'UTF-8') ?>, which must be payable immediately upon the completion of rishta and not on Nikah or any official ceremony.
                            </p>
                            <p class="pl-50">3. Please deposit amount in any of the following bank details:<br>
                            </p>
                            <div>
                                <h3 class="pl-50">Bank Details</h3>
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>Bank</th>
                                            <th>Account No</th>
                                            <th>Account Title</th>
                                            <th>IBAN</th>
                                        </tr>
                                        <?php foreach ($invoiceBanks as $b): ?>
                                            <tr>
                                                <td class="inv_bank_logo">
                                                    <?php if (!empty($b['logo'])): ?>
                                                        <img src="<?= htmlspecialchars((string) $b['logo'], ENT_QUOTES, 'UTF-8') ?>" alt="bank logo">
                                                    <?php else: ?>
                                                        <strong><?= htmlspecialchars((string) ($b['bank_label'] ?? 'Bank'), ENT_QUOTES, 'UTF-8') ?></strong>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars((string) $b['account'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars((string) $b['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars((string) $b['iban'], ENT_QUOTES, 'UTF-8') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            $pn = trim((string) ($row['payment_note'] ?? ''));
                            if ($pn !== ''): ?>
                                <p class="pl-50" style="margin-top:12px"><strong>Description :</strong><br><?= nl2br(htmlspecialchars($pn, ENT_QUOTES, 'UTF-8')) ?></p>
                            <?php endif; ?>
                            <p class="text-center end-note">This is a computer generated invoice</p>
                        </div>
                    </div>
                    <div class="row no-print invoice-print-row">
                        <div class="col-xs-12">
                            <div align="left">
                                <img src="https://nikahglobal.pk/assets/back_end/images/print.png" alt="Print" onclick="window.print()" style="text-align:center; cursor:pointer;"><br>
                                <span><strong>Print Invoice</strong></span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

<?php require __DIR__ . '/partials/footer.php'; ?>
