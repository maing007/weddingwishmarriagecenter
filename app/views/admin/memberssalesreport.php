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

    <main class="admin-page">
        <div class="admin-content">
            <div class="container-fluid py-3">
                <div class="msr-panel">
                    <form method="get" action="<?= htmlspecialchars($msrSelfUrl, ENT_QUOTES, 'UTF-8') ?>" id="msrSearchForm" class="row g-2 align-items-center mb-3">
                        <?php if (!$msrLockScope): ?>
                            <input type="hidden" name="sale_scope" value="<?= htmlspecialchars($saleScope, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                        <input type="hidden" name="pay_filter" value="<?= htmlspecialchars($payFilter, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="filter_staff" value="<?= htmlspecialchars($filterStaff, ENT_QUOTES, 'UTF-8') ?>">
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

                    <div class="msr-tabs">
                        <a class="msr-tab <?= $saleScope === 'all' ? 'active' : '' ?>" href="<?= htmlspecialchars($msrTabHref('all'), ENT_QUOTES, 'UTF-8') ?>">All <small>(<?= (int) ($tabCounts['all'] ?? 0) ?>)</small></a>
                        <a class="msr-tab <?= $saleScope === 'registration' ? 'active' : '' ?>" href="<?= htmlspecialchars($msrTabHref('registration'), ENT_QUOTES, 'UTF-8') ?>">Registration <small>(<?= (int) ($tabCounts['registration'] ?? 0) ?>)</small></a>
                        <a class="msr-tab <?= $saleScope === 'rishta' ? 'active' : '' ?>" href="<?= htmlspecialchars($msrTabHref('rishta'), ENT_QUOTES, 'UTF-8') ?>">Rishta <small>(<?= (int) ($tabCounts['rishta'] ?? 0) ?>)</small></a>
                    </div>

                    <?php if (empty($rows)): ?>
                        <div class="alert alert-warning mb-0">No record found.</div>
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
                                            <td><?= htmlspecialchars(trim((string) ($r['matri_id'] ?? '')) !== '' ? (string) $r['matri_id'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
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
    </main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
