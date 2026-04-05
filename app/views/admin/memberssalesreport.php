<?php
$title = $pageHead ?? 'Members sales report';
/** @var string $pageHead */
/** @var string $search */
/** @var string $saleScope */
/** @var string $payFilter */
/** @var string $filterStaff */
/** @var int $limit */
/** @var int $page */
/** @var int $totalRows */
/** @var int $totalPages */
/** @var array $rows */
/** @var array $tabCounts */
/** @var array $staffFilterOptions */
/** @var string $msrSelfUrl */
/** @var string $msrTabUrlAll */
/** @var string $msrTabUrlReg */
/** @var string $msrTabUrlRishta */
/** @var bool $msrLockScope */

$msrSelfUrl = $msrSelfUrl ?? (BASE_URL . '/admin/sales-report');
$msrTabUrlAll = $msrTabUrlAll ?? (BASE_URL . '/admin/sales-report');
$msrTabUrlReg = $msrTabUrlReg ?? (BASE_URL . '/admin/reports/payments/registration-fee');
$msrTabUrlRishta = $msrTabUrlRishta ?? (BASE_URL . '/admin/reports/payments/rishta-fee');
$msrLockScope = !empty($msrLockScope);

$fmtMoney = static function ($raw): string {
    $n = (float) $raw;
    if (floor($n) == $n) {
        return (string) (int) $n;
    }

    return number_format($n, 2, '.', '');
};

$fmtDate = static function ($raw): string {
    if ($raw === null || $raw === '') {
        return '-';
    }
    $t = strtotime((string) $raw);

    return $t ? date('Y-m-d', $t) : '-';
};

$payStLabel = static function ($raw): string {
    $s = strtoupper(trim((string) ($raw ?? '')));

    return $s !== '' ? $s : '-';
};

$msrCardLayout = $msrCardLayout ?? false;
$msrPayCounts = $msrPayCounts ?? [
    'reg_unpaid' => 0,
    'reg_paid' => 0,
    'rishta_unpaid' => 0,
    'rishta_paid' => 0,
];
$msrProofsByFee = $msrProofsByFee ?? [];

$fmtDateLong = static function ($raw): string {
    if ($raw === null || $raw === '') {
        return 'N/A';
    }
    $t = strtotime((string) $raw);

    return $t ? date('F j, Y', $t) : 'N/A';
};

if ($saleScope === 'registration') {
    $feeColLabel = 'Registration fee';
} elseif ($saleScope === 'rishta') {
    $feeColLabel = 'Rishta fee';
} else {
    $feeColLabel = 'Fee amount';
}

$msrSelfHref = static function (array $extra = []) use ($msrSelfUrl, $msrLockScope, $saleScope, $search, $payFilter, $filterStaff, $limit): string {
    $q = array_merge([
        'search_filed' => $search,
        'pay_filter' => $payFilter,
        'filter_staff' => $filterStaff,
        'limit_per_page' => $limit,
    ], $extra);
    if (!$msrLockScope) {
        if (!array_key_exists('sale_scope', $q)) {
            $q['sale_scope'] = $saleScope;
        }
    } else {
        unset($q['sale_scope']);
    }
    $q = array_filter($q, static function ($v) {
        return $v !== null && $v !== '';
    });

    return $msrSelfUrl . '?' . http_build_query($q);
};

$msrTabHref = static function (string $which) use ($msrTabUrlAll, $msrTabUrlReg, $msrTabUrlRishta, $search, $payFilter, $filterStaff, $limit): string {
    $q = array_filter([
        'search_filed' => $search,
        'pay_filter' => $payFilter,
        'filter_staff' => $filterStaff,
        'limit_per_page' => $limit,
        'page' => 1,
    ], static function ($v) {
        return $v !== null && $v !== '';
    });
    if ($which === 'all') {
        $q['sale_scope'] = 'all';

        return $msrTabUrlAll . '?' . http_build_query($q);
    }
    if ($which === 'registration') {
        return $msrTabUrlReg . '?' . http_build_query($q);
    }
    if ($which === 'rishta') {
        return $msrTabUrlRishta . '?' . http_build_query($q);
    }

    return '#';
};

$msrResetQ = ['limit_per_page' => $limit, 'page' => 1];
if (!$msrLockScope) {
    $msrResetQ['sale_scope'] = $saleScope;
}
$msrResetUrl = $msrSelfUrl . '?' . http_build_query($msrResetQ);

$msrPayTabHref = static function (string $scope, string $payBin) use ($msrSelfUrl, $msrLockScope, $search, $filterStaff, $limit): string {
    $q = [
        'search_filed' => $search,
        'pay_filter' => $payBin,
        'filter_staff' => $filterStaff,
        'limit_per_page' => $limit,
        'page' => 1,
    ];
    if (!$msrLockScope) {
        $q['sale_scope'] = $scope;
    }
    $q = array_filter($q, static function ($v) {
        return $v !== null && $v !== '';
    });

    return $msrSelfUrl . '?' . http_build_query($q);
};

$msrCurrentReturnUrl = $msrSelfHref(['page' => $page]);

$msrPayTabActive = static function (string $scope, string $payBin) use ($saleScope, $payFilter): bool {
    return $saleScope === $scope && $payFilter === $payBin;
};

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-manage-members.css">
<style>
    .msr-topbar {
        justify-content: flex-start;
        padding-left: 12px;
        padding-right: 16px;
    }
    .msr-topbar .msr-page-title {
        font-size: 15px;
        font-weight: 600;
        color: #111;
        margin-left: 8px;
        margin-right: auto;
        letter-spacing: 0.01em;
    }
    .msr-topbar .admin-profile {
        margin-left: auto;
    }
    .btn-msr-teal {
        background: #4da7ba;
        border-color: #3d96a9;
        color: #fff;
        font-weight: 600;
    }
    .btn-msr-teal:hover {
        background: #3d96a9;
        border-color: #358999;
        color: #fff;
    }
    .msr-panel {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 16px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
    }
    .msr-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 14px;
    }
    .msr-tab {
        background: #e8e8e8;
        border: 1px solid #d8d8d8;
        color: #444;
        text-decoration: none;
        border-radius: 4px 4px 0 0;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 700;
    }
    .msr-tab small {
        font-size: 10px;
        color: #666;
        font-weight: 600;
    }
    .msr-tab.active {
        background: #4da7ba;
        color: #fff;
        border-color: #3d96a9;
    }
    .msr-tab.active small {
        color: #fff;
    }
    .msr-filter-panel {
        display: none;
        background: #f8f9fa;
        border: 1px solid #e2e2e2;
        border-radius: 4px;
        padding: 12px 14px;
        margin-bottom: 14px;
    }
    .msr-filter-panel.open {
        display: block;
    }
    .msr-table-wrap {
        overflow-x: auto;
    }
    .msr-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .msr-table thead th {
        background: #555;
        color: #fff;
        font-weight: 600;
        padding: 10px 8px;
        border: 1px solid #444;
        white-space: nowrap;
    }
    .msr-table tbody td {
        padding: 8px;
        border: 1px solid #ddd;
        vertical-align: middle;
    }
    .msr-table tbody tr:nth-child(even) {
        background: #f5f5f5;
    }
    .msr-table tbody tr:nth-child(odd) {
        background: #fff;
    }
    .msr-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
        font-size: 13px;
    }
    .admin-page.msr-sales-page {
        background: #f4f7f6;
    }
    .msr-pay-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0;
        margin: 0 0 18px;
        border-bottom: 1px solid #dde3e1;
        background: transparent;
    }
    .msr-pay-tab {
        display: inline-block;
        padding: 10px 16px 11px;
        font-size: 13px;
        font-weight: 600;
        color: #555;
        text-decoration: none;
        border: none;
        border-bottom: 3px solid transparent;
        margin-bottom: -1px;
        background: transparent;
        border-radius: 0;
    }
    .msr-pay-tab:hover {
        color: #0096c7;
        text-decoration: none;
        background: rgba(0, 150, 199, 0.06);
    }
    .msr-pay-tab.active {
        color: #0096c7;
        background: #fff;
        border-bottom-color: #4da7ba;
        font-weight: 700;
    }
    .msr-pay-tab small {
        font-weight: 600;
        color: #888;
        font-size: 12px;
    }
    .msr-pay-tab.active small {
        color: #0096c7;
    }
    .msr-sales-card {
        background: #fff;
        border: 1px solid #dfe3e6;
        border-radius: 6px;
        padding: 16px 18px 14px;
        margin-bottom: 16px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
    }
    .msr-sales-card-hd {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e8ebee;
    }
    .msr-sales-card-hd .form-check-input {
        margin-top: 5px;
    }
    .msr-sales-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin: 0;
        letter-spacing: 0.01em;
        line-height: 1.35;
    }
    .msr-sales-card-title .msr-matri-paren {
        font-weight: 700;
        color: #1f2937;
    }
    .msr-sales-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 32px;
        font-size: 13px;
        line-height: 1.5;
    }
    @media (max-width: 767px) {
        .msr-sales-grid {
            grid-template-columns: 1fr;
        }
    }
    .msr-sales-row {
        display: flex;
        flex-wrap: nowrap;
        gap: 0 6px;
        align-items: baseline;
    }
    .msr-sales-row .lbl {
        color: #5c6370;
        font-weight: 500;
        min-width: 128px;
        flex-shrink: 0;
    }
    .msr-sales-row .val {
        color: #111827;
        font-weight: 600;
        flex: 1;
        min-width: 0;
    }
    .msr-sales-desc {
        grid-column: 1 / -1;
        margin-top: 8px;
        align-items: flex-start;
    }
    .msr-sales-desc .val {
        font-weight: 500;
        line-height: 1.45;
    }
    .msr-sales-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid #e8ebee;
    }
    .msr-sales-actions .btn {
        font-size: 12px;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 5px;
        border: none;
        color: #fff;
    }
    .msr-btn-paylink {
        background: #48cae4;
    }
    .msr-btn-paylink:hover {
        background: #3dbdd8;
        color: #fff;
    }
    .msr-btn-proof {
        background: #0096c7;
    }
    .msr-btn-proof:hover {
        background: #007fad;
        color: #fff;
    }
    .msr-btn-addproof {
        background: #2ecc71;
    }
    .msr-btn-download {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 4px;
        background: #5a9fd4;
        color: #fff !important;
        border: none;
        text-decoration: none;
        display: inline-block;
        white-space: nowrap;
    }
    .msr-btn-download:hover {
        background: #4a8fc4;
        color: #fff !important;
    }
    .msr-btn-addproof:hover {
        background: #27ae60;
        color: #fff;
    }
    .msr-btn-follow {
        background: #f4b942;
        color: #333 !important;
    }
    .msr-btn-follow:hover {
        background: #e5aa35;
        color: #222 !important;
    }
    .msr-btn-invoice {
        background: #5ab8c9;
    }
    .msr-btn-invoice:hover {
        background: #4aa8b9;
        color: #fff;
    }
    .msr-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1060;
        background: rgba(0, 0, 0, .45);
        align-items: center;
        justify-content: center;
        padding: 20px;
        overflow-y: auto;
    }
    .msr-modal-overlay.show {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
    .msr-modal-dialog {
        background: #fff;
        border-radius: 8px;
        max-width: 520px;
        width: 100%;
        max-height: min(90vh, 640px);
        overflow: auto;
        box-shadow: 0 8px 32px rgba(0, 0, 0, .18);
        margin: auto;
        flex-shrink: 0;
        align-self: center;
    }
    .msr-modal-hd {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        border-bottom: 1px solid #e8e8e8;
    }
    .msr-modal-hd h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #555;
    }
    .msr-modal-close {
        border: none;
        background: none;
        font-size: 22px;
        line-height: 1;
        color: #999;
        cursor: pointer;
        padding: 0 4px;
    }
    .msr-modal-body {
        padding: 18px;
    }
    .msr-modal-body label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #444;
        margin-bottom: 4px;
        margin-top: 10px;
    }
    .msr-modal-body label:first-child {
        margin-top: 0;
    }
    .msr-modal-body .form-control {
        border-radius: 6px;
        font-size: 13px;
    }
    .msr-proof-table {
        width: 100%;
        font-size: 12px;
        border-collapse: collapse;
    }
    .msr-proof-table th,
    .msr-proof-table td {
        border: 1px solid #e5e5e5;
        padding: 8px;
        text-align: left;
    }
    .msr-proof-table th {
        background: #f7f7f7;
    }
</style>

<div class="admin-main">
    <div class="admin-topbar msr-topbar">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu">
            <i class="fa fa-bars"></i>
        </button>
        <div class="msr-page-title"><?= htmlspecialchars($pageHead, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="admin-profile" id="adminProfileTrigger">
            <div class="admin-profile-box">
                <span><?= htmlspecialchars($this->displayadminname(), ENT_QUOTES, 'UTF-8') ?></span>
                <i class="fa fa-user"></i>
            </div>
            <div class="admin-dropdown" id="adminDropdown">
                <a href="<?= BASE_URL ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
                <a href="<?= BASE_URL ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </div>

    <main class="admin-page msr-sales-page">
        <div class="admin-content">
            <div class="container-fluid py-3">
                <div class="msr-panel">
                    <?php if (!empty($_SESSION['flash_error'])): ?>
                        <div class="alert alert-danger mb-3" style="border-radius: 6px;"><?= htmlspecialchars((string) $_SESSION['flash_error'], ENT_QUOTES, 'UTF-8');
                        unset($_SESSION['flash_error']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION['flash_success'])): ?>
                        <div class="alert alert-success mb-3" style="border-radius: 6px;"><?= htmlspecialchars((string) $_SESSION['flash_success'], ENT_QUOTES, 'UTF-8');
                        unset($_SESSION['flash_success']); ?></div>
                    <?php endif; ?>
                    <form method="get" action="<?= htmlspecialchars($msrSelfUrl, ENT_QUOTES, 'UTF-8') ?>" id="msrSearchForm" class="row g-2 align-items-center mb-3">
                        <?php if (!$msrLockScope): ?>
                            <input type="hidden" name="sale_scope" value="<?= htmlspecialchars($saleScope, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                        <input type="hidden" name="pay_filter" value="<?= htmlspecialchars($payFilter, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="filter_staff" value="<?= htmlspecialchars($filterStaff, ENT_QUOTES, 'UTF-8') ?>">
                        <?php if ($msrCardLayout): ?>
                            <div class="col-auto d-flex align-items-center">
                                <label class="mb-0 small" style="cursor:pointer;font-weight:600;color:#444;">
                                    <input type="checkbox" id="msrSelectAllCb" class="form-check-input me-1" style="margin-top:0;vertical-align:middle;">
                                    Select All
                                </label>
                            </div>
                        <?php endif; ?>
                        <div class="col-lg-5 col-md-6">
                            <div class="input-group">
                                <input type="search" name="search_filed" class="form-control" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search here…">
                                <button class="btn btn-msr-teal" type="submit"><i class="fa fa-search"></i> Search</button>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <select name="limit_per_page" class="form-select" onchange="this.form.submit()">
                                <?php foreach ([1, 2, 3, 5, 10, 25, 50, 100] as $n): ?>
                                    <option value="<?= $n ?>" <?= ((int) $limit === $n) ? 'selected' : '' ?>>Show <?= $n ?> entries</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-5 col-md-12 text-md-end">
                            <button type="button" class="btn btn-msr-teal" onclick="document.getElementById('msrFilterPanel').classList.toggle('open')">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                        </div>
                    </form>

                    <div id="msrFilterPanel" class="msr-filter-panel <?= ($payFilter !== 'all' || $filterStaff !== '') ? 'open' : '' ?>">
                        <form method="get" action="<?= htmlspecialchars($msrSelfUrl, ENT_QUOTES, 'UTF-8') ?>" class="row g-2 align-items-end">
                            <input type="hidden" name="search_filed" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (!$msrLockScope): ?>
                                <input type="hidden" name="sale_scope" value="<?= htmlspecialchars($saleScope, ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>
                            <input type="hidden" name="limit_per_page" value="<?= (int) $limit ?>">
                            <div class="col-md-4">
                                <label class="form-label small mb-1">Staff payment status</label>
                                <select name="pay_filter" class="form-select form-select-sm">
                                    <option value="all" <?= $payFilter === 'all' ? 'selected' : '' ?>>All</option>
                                    <option value="unpaid" <?= $payFilter === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                                    <option value="paid" <?= $payFilter === 'paid' ? 'selected' : '' ?>>Paid</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1">Staff name</label>
                                <select name="filter_staff" class="form-select form-select-sm">
                                    <option value="">All staff</option>
                                    <?php foreach ($staffFilterOptions as $sn): ?>
                                        <option value="<?= htmlspecialchars($sn, ENT_QUOTES, 'UTF-8') ?>" <?= $filterStaff === $sn ? 'selected' : '' ?>><?= htmlspecialchars($sn, ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-msr-teal btn-sm">Apply filters</button>
                                <a class="btn btn-outline-secondary btn-sm ms-1" href="<?= htmlspecialchars($msrResetUrl, ENT_QUOTES, 'UTF-8') ?>">Reset</a>
                            </div>
                        </form>
                    </div>

                    <!-- <div class="msr-tabs">
                        <?php if (!$msrLockScope): ?>
                            <a class="msr-tab <?= $saleScope === 'all' ? 'active' : '' ?>" href="<?= htmlspecialchars($msrTabHref('all'), ENT_QUOTES, 'UTF-8') ?>">All <small>(<?= (int) ($tabCounts['all'] ?? 0) ?>)</small></a>
                        <?php endif; ?>
                        <a class="msr-tab <?= $saleScope === 'registration' ? 'active' : '' ?>" href="<?= htmlspecialchars($msrTabHref('registration'), ENT_QUOTES, 'UTF-8') ?>">Registration <small>(<?= (int) ($tabCounts['registration'] ?? 0) ?>)</small></a>
                        <a class="msr-tab <?= $saleScope === 'rishta' ? 'active' : '' ?>" href="<?= htmlspecialchars($msrTabHref('rishta'), ENT_QUOTES, 'UTF-8') ?>">Rishta <small>(<?= (int) ($tabCounts['rishta'] ?? 0) ?>)</small></a>
                    </div> -->

                    <?php if ($msrCardLayout): ?>
                        <div class="msr-pay-tabs">
                            <?php if (!$msrLockScope): ?>
                                <a class="msr-pay-tab <?= $msrPayTabActive('registration', 'unpaid') ? 'active' : '' ?>" href="<?= htmlspecialchars($msrPayTabHref('registration', 'unpaid'), ENT_QUOTES, 'UTF-8') ?>">Registration fee receivable <small>(<?= (int) ($msrPayCounts['reg_unpaid'] ?? 0) ?>)</small></a>
                                <a class="msr-pay-tab <?= $msrPayTabActive('registration', 'paid') ? 'active' : '' ?>" href="<?= htmlspecialchars($msrPayTabHref('registration', 'paid'), ENT_QUOTES, 'UTF-8') ?>">Registration fee received <small>(<?= (int) ($msrPayCounts['reg_paid'] ?? 0) ?>)</small></a>
                                <a class="msr-pay-tab <?= $msrPayTabActive('rishta', 'unpaid') ? 'active' : '' ?>" href="<?= htmlspecialchars($msrPayTabHref('rishta', 'unpaid'), ENT_QUOTES, 'UTF-8') ?>">Rishta fee receivable <small>(<?= (int) ($msrPayCounts['rishta_unpaid'] ?? 0) ?>)</small></a>
                                <a class="msr-pay-tab <?= $msrPayTabActive('rishta', 'paid') ? 'active' : '' ?>" href="<?= htmlspecialchars($msrPayTabHref('rishta', 'paid'), ENT_QUOTES, 'UTF-8') ?>">Rishta fee received <small>(<?= (int) ($msrPayCounts['rishta_paid'] ?? 0) ?>)</small></a>
                            <?php elseif ($saleScope === 'registration'): ?>
                                <a class="msr-pay-tab <?= $msrPayTabActive('registration', 'unpaid') ? 'active' : '' ?>" href="<?= htmlspecialchars($msrPayTabHref('registration', 'unpaid'), ENT_QUOTES, 'UTF-8') ?>">Registration fee receivable <small>(<?= (int) ($msrPayCounts['reg_unpaid'] ?? 0) ?>)</small></a>
                                <a class="msr-pay-tab <?= $msrPayTabActive('registration', 'paid') ? 'active' : '' ?>" href="<?= htmlspecialchars($msrPayTabHref('registration', 'paid'), ENT_QUOTES, 'UTF-8') ?>">Registration fee received <small>(<?= (int) ($msrPayCounts['reg_paid'] ?? 0) ?>)</small></a>
                            <?php else: ?>
                                <a class="msr-pay-tab <?= $msrPayTabActive('rishta', 'unpaid') ? 'active' : '' ?>" href="<?= htmlspecialchars($msrPayTabHref('rishta', 'unpaid'), ENT_QUOTES, 'UTF-8') ?>">Rishta fee receivable <small>(<?= (int) ($msrPayCounts['rishta_unpaid'] ?? 0) ?>)</small></a>
                                <a class="msr-pay-tab <?= $msrPayTabActive('rishta', 'paid') ? 'active' : '' ?>" href="<?= htmlspecialchars($msrPayTabHref('rishta', 'paid'), ENT_QUOTES, 'UTF-8') ?>">Rishta fee received <small>(<?= (int) ($msrPayCounts['rishta_paid'] ?? 0) ?>)</small></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($rows)): ?>
                        <div class="alert alert-warning mb-0">No record found.</div>
                    <?php elseif ($msrCardLayout): ?>
                        <?php foreach ($rows as $r): ?>
                            <?php
                            $fid = (int) ($r['id'] ?? 0);
                            $proofs = $msrProofsByFee[$fid] ?? [];
                            $feeType = (string) ($r['fee_type'] ?? '');
                            $isRegFee = ($feeType === 'registration');
                            $matriDisp = matri_id_display((string) ($r['matri_id'] ?? ''), (int) ($r['card_user_id'] ?? 0));
                            $email = trim((string) ($r['card_email'] ?? ''));
                            $pkg = trim((string) ($r['package'] ?? ''));
                            $pkgDisp = $pkg !== '' ? $pkg : 'N/A';
                            $durRaw = $r['card_plan_duration_days'] ?? null;
                            $durStr = ($durRaw !== null && $durRaw !== '') ? ((int) $durRaw) . ' Days' : 'N/A';
                            $expTs = strtotime((string) ($r['card_expires_at'] ?? ''));
                            $currentPlan = ($expTs && $expTs >= strtotime('today')) ? 'Yes' : ((trim((string) ($r['card_expires_at'] ?? '')) !== '') ? 'No' : 'N/A');
                            $txId = isset($proofs[0]['transaction_id']) ? trim((string) $proofs[0]['transaction_id']) : '';
                            $txDisp = $txId !== '' ? $txId : 'N/A';
                            $finalFee = (float) ($r['card_final_fee'] ?? 0);
                            $feeUponRishta = $finalFee > 0 ? ('PKR ' . $fmtMoney($finalFee)) : 'N/A';
                            $payMode = trim((string) ($r['payment_mode'] ?? ''));
                            $payModeDisp = $payMode !== '' ? $payMode : 'N/A';
                            $desc = trim((string) ($r['payment_note'] ?? ''));
                            $clientName = trim((string) ($r['client_name'] ?? ''));
                            $invoiceUrl = rtrim((string) BASE_URL, '/') . '/admin/accounts/invoice/registration?id=' . $fid;
                            ?>
                            <div class="msr-sales-card" data-fee-id="<?= $fid ?>">
                                <div class="msr-sales-card-hd">
                                    <input type="checkbox" class="form-check-input msr-row-cb" name="fee_ids[]" value="<?= $fid ?>" form="msrBulkForm" aria-label="Select row">
                                    <h3 class="msr-sales-card-title"><?= htmlspecialchars($clientName !== '' ? $clientName : 'Member', ENT_QUOTES, 'UTF-8') ?><?php if ($matriDisp !== ''): ?> <span class="msr-matri-paren">( <?= htmlspecialchars($matriDisp, ENT_QUOTES, 'UTF-8') ?> )</span><?php endif; ?></h3>
                                </div>
                                <div class="msr-sales-grid">
                                    <div class="msr-sales-row"><span class="lbl">Plan Name :</span><span class="val"><?= htmlspecialchars($pkgDisp, ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <div class="msr-sales-row"><span class="lbl">Email :</span><span class="val"><?= htmlspecialchars($email !== '' ? $email : 'N/A', ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <div class="msr-sales-row"><span class="lbl">Category Type :</span><span class="val"><?= htmlspecialchars($pkgDisp, ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <div class="msr-sales-row"><span class="lbl">Payment Mode :</span><span class="val"><?= htmlspecialchars($payModeDisp, ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <div class="msr-sales-row"><span class="lbl">Plan Activated :</span><span class="val"><?= htmlspecialchars($fmtDateLong($r['activation_date'] ?? null), ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <div class="msr-sales-row"><span class="lbl">Plan Expired :</span><span class="val"><?= htmlspecialchars($fmtDateLong($r['card_expires_at'] ?? null), ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <div class="msr-sales-row"><span class="lbl">Plan Duration :</span><span class="val"><?= htmlspecialchars($durStr, ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <div class="msr-sales-row"><span class="lbl">Plan Amount :</span><span class="val">PKR <?= htmlspecialchars($fmtMoney($r['fee_amount'] ?? 0), ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <div class="msr-sales-row"><span class="lbl">Grand Total :</span><span class="val">PKR <?= htmlspecialchars($fmtMoney($r['fee_amount'] ?? 0), ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <div class="msr-sales-row"><span class="lbl">Current Plan :</span><span class="val"><?= htmlspecialchars($currentPlan, ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <div class="msr-sales-row"><span class="lbl">Transaction Id :</span><span class="val"><?= htmlspecialchars($txDisp, ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <div class="msr-sales-row"><span class="lbl">Fee Upon Rishta :</span><span class="val"><?= htmlspecialchars($feeUponRishta, ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <div class="msr-sales-row msr-sales-desc"><span class="lbl">Description :</span><span class="val"><?= htmlspecialchars($desc !== '' ? $desc : 'N/A', ENT_QUOTES, 'UTF-8') ?></span></div>
                                </div>
                                <div class="msr-sales-actions">
                                    <button type="button" class="btn msr-btn-paylink" data-invoice-url="<?= htmlspecialchars($invoiceUrl, ENT_QUOTES, 'UTF-8') ?>" data-is-reg="<?= $isRegFee ? '1' : '0' ?>" onclick="msrCopyPaymentLink(this)">Payment Link</button>
                                    <button type="button" class="btn msr-btn-proof" onclick="msrOpenProofModal(<?= (int) $fid ?>)">Payment Proof</button>
                                    <button type="button" class="btn msr-btn-addproof" onclick="msrOpenAddProofModal(<?= (int) $fid ?>, '<?= $isRegFee ? 'register' : 'rishta' ?>')">Add Payment Proof</button>
                                    <button type="button" class="btn msr-btn-follow" onclick="msrFollowUp(<?= json_encode($email) ?>, <?= json_encode($clientName) ?>)">Follow Up</button>
                                    <?php if ($isRegFee): ?>
                                        <button type="button" class="btn msr-btn-invoice" onclick="window.open('<?= htmlspecialchars($invoiceUrl, ENT_QUOTES, 'UTF-8') ?>','_blank')">View Invoice</button>
                                    <?php else: ?>
                                        <button type="button" class="btn msr-btn-invoice" disabled title="Registration invoice only">View Invoice</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div id="msr-proof-html-<?= (int) $fid ?>" style="display:none!important" aria-hidden="true">
                                <?php if ($proofs === []): ?>
                                    <p class="text-muted" style="margin:0;font-size:13px;">No payment proofs recorded.</p>
                                <?php else: ?>
                                    <table class="msr-proof-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Txn ID</th>
                                                <th>Bank</th>
                                                <th>Proof file</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($proofs as $pr): ?>
                                                <?php
                                                $receiptPath = trim((string) ($pr['receipt_path'] ?? ''));
                                                $receiptUrl = $receiptPath !== '' ? public_url_for_path($receiptPath) : '';
                                                $receiptFn = $receiptPath !== '' ? basename(str_replace('\\', '/', $receiptPath)) : '';
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars((string) ($pr['paid_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= htmlspecialchars($fmtMoney($pr['paid_amount'] ?? 0), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= htmlspecialchars((string) ($pr['transaction_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= htmlspecialchars((string) ($pr['bank_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td>
                                                        <?php if ($receiptUrl !== ''): ?>
                                                            <a class="btn msr-btn-download" href="<?= htmlspecialchars($receiptUrl, ENT_QUOTES, 'UTF-8') ?>" download="<?= htmlspecialchars($receiptFn !== '' ? $receiptFn : 'payment-proof', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Download</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">—</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <form id="msrBulkForm" method="post" action="#" class="d-none" aria-hidden="true"></form>
                    <?php else: ?>
                        <div class="msr-table-wrap">
                            <table class="msr-table">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Activation date</th>
                                        <th>Staff name</th>
                                        <th>TI name</th>
                                        <th>Matri Id</th>
                                        <th>Client name</th>
                                        <th><?= htmlspecialchars($feeColLabel, ENT_QUOTES, 'UTF-8') ?></th>
                                        <th>Package</th>
                                        <th>Payment mode</th>
                                        <th>Staff payment status</th>
                                        <th>Staff payment mode</th>
                                        <th>Staff paid on</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $r): ?>
                                        <tr>
                                            <td><input type="checkbox" class="form-check-input msr-row-cb" name="fee_ids[]" value="<?= (int) ($r['id'] ?? 0) ?>" form="msrBulkForm" aria-label="Select row"></td>
                                            <td><?= htmlspecialchars($fmtDate($r['activation_date'] ?? null), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($r['staff_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($r['ti_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?php $md = matri_id_display((string) ($r['matri_id'] ?? '')); ?><?= htmlspecialchars($md !== '' ? $md : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($r['client_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($fmtMoney($r['fee_amount'] ?? 0), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars(trim((string) ($r['package'] ?? '')) !== '' ? (string) $r['package'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars(trim((string) ($r['payment_mode'] ?? '')) !== '' ? (string) $r['payment_mode'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($payStLabel($r['staff_payment_status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars(trim((string) ($r['staff_payment_mode'] ?? '')) !== '' ? (string) $r['staff_payment_mode'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($fmtDate($r['staff_paid_on'] ?? null), ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <form id="msrBulkForm" method="post" action="#" class="d-none" aria-hidden="true"></form>
                    <?php endif; ?>

                    <div class="msr-pagination">
                        <div>Page <?= (int) $page ?> of <?= (int) $totalPages ?> | Total <?= (int) $totalRows ?> record(s)</div>
                        <div class="d-flex gap-2">
                            <?php
                            $prev = max(1, (int) $page - 1);
                            $next = min((int) $totalPages, (int) $page + 1);
                            ?>
                            <a class="btn btn-sm btn-outline-secondary <?= (int) $page <= 1 ? 'disabled' : '' ?>" href="<?= (int) $page <= 1 ? '#' : htmlspecialchars($msrSelfHref(['page' => $prev]), ENT_QUOTES, 'UTF-8') ?>">Prev</a>
                            <a class="btn btn-sm btn-outline-secondary <?= (int) $page >= (int) $totalPages ? 'disabled' : '' ?>" href="<?= (int) $page >= (int) $totalPages ? '#' : htmlspecialchars($msrSelfHref(['page' => $next]), ENT_QUOTES, 'UTF-8') ?>">Next</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php if (!empty($msrCardLayout)): ?>
<div id="msrProofOverlay" class="msr-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="msrProofTitle">
    <div class="msr-modal-dialog" onclick="event.stopPropagation()">
        <div class="msr-modal-hd">
            <h4 id="msrProofTitle">Payment Proof</h4>
            <button type="button" class="msr-modal-close" onclick="msrCloseProofModal()" aria-label="Close">&times;</button>
        </div>
        <div class="msr-modal-body" id="msrProofModalBody"></div>
    </div>
</div>

<div id="msrAddProofOverlay" class="msr-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="msrAddProofTitle">
    <div class="msr-modal-dialog" onclick="event.stopPropagation()">
        <div class="msr-modal-hd">
            <h4 id="msrAddProofTitle">Add Payment Proof</h4>
            <button type="button" class="msr-modal-close" onclick="msrCloseAddProofModal()" aria-label="Close">&times;</button>
        </div>
        <div class="msr-modal-body">
            <form id="msrPaymentProofForm" method="post" enctype="multipart/form-data" action="<?= htmlspecialchars(rtrim(BASE_URL, '/') . '/admin/accounts/income/payment-proof', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="payment_id" id="msr_pp_fee_id" value="">
                <input type="hidden" name="return_url" value="<?= htmlspecialchars($msrCurrentReturnUrl, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="type" id="msr_pp_type" value="register">
                <label for="msr_bank_name">Bank Name :</label>
                <input type="text" class="form-control" name="bank_name" id="msr_bank_name" required>
                <label for="msr_account_title">Account Title :</label>
                <input type="text" class="form-control" name="account_title" id="msr_account_title" required>
                <label for="msr_transaction_id">Transaction ID :</label>
                <input type="text" class="form-control" name="transaction_id" id="msr_transaction_id" required>
                <label for="msr_paid_amount">Amount :</label>
                <input type="text" class="form-control" name="paid_amount" id="msr_paid_amount" required>
                <label for="msr_paid_date">Date :</label>
                <input type="date" class="form-control" name="date" id="msr_paid_date" required>
                <label for="msr_receipt">Payment Proof :</label>
                <input type="file" class="form-control" name="receipt" id="msr_receipt" accept=".jpg,.jpeg,.png,.pdf,.webp">
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" style="background:#5ab8c9;border-color:#4aa8b9;">Add</button>
                    <button type="button" class="btn btn-default" onclick="msrCloseAddProofModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    function msrOverlayClick(e) {
        if (e.target.classList && e.target.classList.contains('msr-modal-overlay')) {
            e.target.classList.remove('show');
        }
    }
    var po = document.getElementById('msrProofOverlay');
    var ao = document.getElementById('msrAddProofOverlay');
    if (po) po.addEventListener('click', msrOverlayClick);
    if (ao) ao.addEventListener('click', msrOverlayClick);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (po) po.classList.remove('show');
            if (ao) ao.classList.remove('show');
        }
    });
    var sacb = document.getElementById('msrSelectAllCb');
    if (sacb) {
        sacb.addEventListener('change', function () {
            document.querySelectorAll('input.msr-row-cb').forEach(function (cb) {
                cb.checked = sacb.checked;
            });
        });
    }
})();

function msrOpenProofModal(fid) {
    var src = document.getElementById('msr-proof-html-' + fid);
    var body = document.getElementById('msrProofModalBody');
    var ov = document.getElementById('msrProofOverlay');
    if (body) body.innerHTML = src ? src.innerHTML : '<p class="text-muted" style="margin:0;">No data.</p>';
    if (ov) ov.classList.add('show');
}

function msrCloseProofModal() {
    var ov = document.getElementById('msrProofOverlay');
    if (ov) ov.classList.remove('show');
}

function msrOpenAddProofModal(fid, typ) {
    var el = document.getElementById('msr_pp_fee_id');
    var t = document.getElementById('msr_pp_type');
    if (el) el.value = String(fid);
    if (t) t.value = typ || 'register';
    var ov = document.getElementById('msrAddProofOverlay');
    if (ov) ov.classList.add('show');
}

function msrCloseAddProofModal() {
    var ov = document.getElementById('msrAddProofOverlay');
    if (ov) ov.classList.remove('show');
}

function msrCopyPaymentLink(btn) {
    var url = btn.getAttribute('data-invoice-url') || '';
    var isReg = btn.getAttribute('data-is-reg') === '1';
    if (!isReg) {
        window.alert('Payment link (invoice URL) is available for registration fees only.');
        return;
    }
    if (!url) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function () {
            window.alert('Invoice link copied to clipboard.');
        }).catch(function () {
            window.prompt('Copy this link:', url);
        });
    } else {
        window.prompt('Copy this link:', url);
    }
}

function msrFollowUp(email, name) {
    var em = (email || '').trim();
    if (!em) {
        window.alert('No email on file for this member.');
        return;
    }
    var subj = 'Follow up — ' + (name || 'Member');
    window.location.href = 'mailto:' + encodeURIComponent(em) + '?subject=' + encodeURIComponent(subj);
}
</script>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
