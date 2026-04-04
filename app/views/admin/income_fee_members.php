<?php
/** @var string $pageTitle */
/** @var string $pageHead */
/** @var string $feeColumnLabel */
/** @var array $rows */

$title = $pageTitle;
$isRishta = stripos($feeColumnLabel, 'rishta') !== false;

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

$allCount = is_array($rows ?? null) ? count($rows) : 0;
$approvedCount = $unapprovedCount = $suspendedCount = $unlinkedCount = 0;
foreach (($rows ?? []) as $tmp) {
    $uid = (int) ($tmp['linked_user_id'] ?? 0);
    if ($uid <= 0) {
        $unlinkedCount++;

        continue;
    }
    $st = strtolower((string) ($tmp['user_status'] ?? 'unapproved'));
    if ($st === 'approved') {
        $approvedCount++;
    } elseif ($st === 'unapproved') {
        $unapprovedCount++;
    } elseif ($st === 'suspended') {
        $suspendedCount++;
    } else {
        $unapprovedCount++;
    }
}

$staffOpts = $tiOpts = $payModeOpts = $statusOpts = [];
foreach (($rows ?? []) as $tmp) {
    $s = trim((string) ($tmp['staff_name'] ?? ''));
    if ($s !== '' && !in_array($s, $staffOpts, true)) {
        $staffOpts[] = $s;
    }
    $s = trim((string) ($tmp['ti_name'] ?? ''));
    if ($s !== '' && !in_array($s, $tiOpts, true)) {
        $tiOpts[] = $s;
    }
    $s = trim((string) ($tmp['payment_mode'] ?? ''));
    if ($s !== '' && !in_array($s, $payModeOpts, true)) {
        $payModeOpts[] = $s;
    }
    $s = trim((string) ($tmp['staff_payment_status'] ?? ''));
    if ($s !== '' && !in_array($s, $statusOpts, true)) {
        $statusOpts[] = $s;
    }
}
sort($staffOpts);
sort($tiOpts);
sort($payModeOpts);
sort($statusOpts);

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-manage-members.css">
<style>
    .income-fee-page-title {
        font-size: 15px;
        font-weight: 600;
        color: #111;
        margin-left: 8px;
        margin-right: auto;
        letter-spacing: 0.01em;
    }
    .admin-topbar.income-fee-topbar {
        justify-content: flex-start;
        padding-left: 12px;
        padding-right: 16px;
    }
    .income-fee-topbar .admin-profile {
        margin-left: auto;
    }
    .income-fee-header-actions {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    .btn-copy-matri {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 4px;
        border: 1px solid #2ecc71;
        background: #eafaf1;
        color: #27ae60;
        font-weight: 800;
        font-size: 12px;
        cursor: pointer;
        line-height: 1;
        flex-shrink: 0;
    }
    .btn-copy-matri:hover {
        background: #d4f5e3;
    }
    .btn-action.btn-action-muted {
        opacity: 0.45;
        pointer-events: none;
        cursor: not-allowed;
    }
</style>

<div class="admin-main">
<div class="admin-topbar income-fee-topbar">
    <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu">
        <i class="fa fa-bars"></i>
    </button>
    <div class="income-fee-page-title"><?= htmlspecialchars($pageHead, ENT_QUOTES, 'UTF-8') ?></div>
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
    <div class="container-fluid">
        <div class="page-head"><?= htmlspecialchars($pageHead, ENT_QUOTES, 'UTF-8') ?></div>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger mb-3" style="border-radius: 6px;"><?= htmlspecialchars((string) $_SESSION['flash_error'], ENT_QUOTES, 'UTF-8');
            unset($_SESSION['flash_error']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success mb-3" style="border-radius: 6px;"><?= htmlspecialchars((string) $_SESSION['flash_success'], ENT_QUOTES, 'UTF-8');
            unset($_SESSION['flash_success']); ?></div>
        <?php endif; ?>

        <div class="top-controls">
            <div class="applied-filter-row">
                <span class="applied-filter-chip">Showing: <?= htmlspecialchars($feeColumnLabel, ENT_QUOTES, 'UTF-8') ?> records</span>
            </div>
            <div class="controls-row controls-row-top users-top-row">
                <div class="users-search-wrap">
                    <div class="input-group">
                        <input type="text" id="feeMemberSearch" class="form-control" placeholder="Search here...">
                        <button class="btn btn-light border search-clear-btn" type="button" id="feeClearSearchBtn" aria-label="Clear search">
                            <i class="fa fa-times"></i>
                        </button>
                        <button type="button" class="btn btn-primary" id="feeSearchBtn">
                            <i class="bi bi-search"></i>
                            Search
                        </button>
                    </div>
                </div>
                <div class="users-actions-wrap text-end">
                    <a href="<?= BASE_URL ?>/admin/add-user" class="btn btn-danger me-2">
                        <i class="bi bi-person-plus"></i> Add New
                    </a>
                    <button type="button" class="btn btn-info text-white me-2" onclick="openFeeFilterPopup()">
                        <i class="bi bi-funnel"></i> Filter1
                    </button>
                    <button type="button" class="btn btn-info text-white" onclick="openFeeFilterPopup()">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </div>

            <div class="controls-row controls-row-mid users-mid-row mt-2">
                <div class="users-select-wrap">
                    <div class="form-check d-inline-flex align-items-center gap-2">
                        <input class="form-check-input" type="checkbox" id="selectAllFeeMembers">
                        <label class="form-check-label" for="selectAllFeeMembers">Select All</label>
                    </div>
                </div>
                <div class="users-status-wrap text-end">
                    <div class="status-pill-row">
                        <button type="button" class="status-pill sp-approved" onclick="submitFeeBulkStatus('approved')">Approved</button>
                        <button type="button" class="status-pill sp-unapproved" onclick="submitFeeBulkStatus('unapproved')">Unapproved</button>
                        <button type="button" class="status-pill sp-suspended" onclick="submitFeeBulkStatus('suspended')"><i class="fa fa-user-times"></i> Suspended</button>
                    </div>
                </div>
            </div>

            <div class="controls-row controls-row-bottom users-bottom-row mt-3">
                <div class="users-show-wrap">
                    <div class="show-entry-wrap">
                        <label class="me-2 mb-0">Show</label>
                        <select id="showFeeEntries" class="form-select d-inline-block w-auto">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="9999">All</option>
                        </select>
                        <label class="ms-2 mb-0">Entries</label>
                    </div>
                </div>
                <div class="users-sort-wrap text-end">
                    <div class="sort-wrap">
                        <label class="me-2 mb-0">Sort</label>
                        <select id="sortFeeMembers" class="form-select d-inline-block w-auto">
                            <option value="latest_desc">Latest Descending</option>
                            <option value="latest_asc">Latest Ascending</option>
                            <option value="name_asc">Name A-Z</option>
                            <option value="name_desc">Name Z-A</option>
                        </select>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs custom-tabs mt-4">
                <li class="nav-item">
                    <a class="nav-link active tab-filter-fee" data-tab="all" href="#">All <small>(<?= (int) $allCount ?>)</small></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-filter-fee" data-tab="approved" href="#">Approved List <small>(<?= (int) $approvedCount ?>)</small></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-filter-fee" data-tab="unapproved" href="#">Unapproved List <small>(<?= (int) $unapprovedCount ?>)</small></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-filter-fee" data-tab="suspended" href="#">Suspended List <small>(<?= (int) $suspendedCount ?>)</small></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-filter-fee" data-tab="unlinked" href="#">No profile link <small>(<?= (int) $unlinkedCount ?>)</small></a>
                </li>
            </ul>
        </div>

        <div id="feeMemberList" class="mt-4">
            <?php foreach ($rows as $r):
                $uid = (int) ($r['linked_user_id'] ?? 0);
                $feeId = (int) ($r['id'] ?? 0);
                $displayName = trim(($r['ud_first_name'] ?? '') . ' ' . ($r['ud_second_name'] ?? ''));
                if ($displayName === '') {
                    $displayName = trim((string) ($r['client_name'] ?? '')) ?: 'Client';
                }
                $matriShow = matri_id_display((string) ($r['matri_id'] ?? ''), $uid);
                $tabStatus = $uid > 0 ? strtolower((string) ($r['user_status'] ?? 'unapproved')) : 'unlinked';
                $actTs = strtotime((string) ($r['activation_date'] ?? '')) ?: 0;
                $paySt = strtolower(trim((string) ($r['staff_payment_status'] ?? '')));
                $badgeClass = ($paySt === 'paid') ? 'status-approved' : 'status-unapproved';
                $badgeIcon = ($paySt === 'paid') ? 'fa-thumbs-up' : 'fa-clock-o';
                $badgeText = strtoupper((string) ($r['staff_payment_status'] ?? 'UNPAID'));

                $photo = '';
                if ($uid > 0) {
                    $photo = trim((string) ($r['photo2_url'] ?? ''));
                    if ($photo === '') {
                        $photo = trim((string) ($r['photo1_status'] ?? ''));
                    }
                }
                $imgSrc = $photo !== '' && preg_match('#^https?://#i', $photo)
                    ? $photo
                    : ($photo !== '' ? BASE_URL . '/' . ltrim($photo, '/') : BASE_URL . '/assets/images/default-avatar.png');

                $planName = trim((string) ($r['active_plan_name'] ?? ''));
                if ($planName === '') {
                    $planName = trim((string) ($r['package'] ?? ''));
                }
                $planNameDisp = $planName !== '' ? $planName : '-';
                $planExp = $fmtDate($r['plan_expires_at'] ?? null);
                $feeAmountDisp = $fmtMoney($r['fee_amount'] ?? 0);
                $profileFinalFee = $isRishta ? $fmtMoney($r['ud_final_fee'] ?? 0) : $fmtMoney($r['ud_registration_fee'] ?? 0);
                ?>
            <div class="user-card searchable-card fee-income-card"
                 data-tab-status="<?= htmlspecialchars($tabStatus, ENT_QUOTES, 'UTF-8') ?>"
                 data-date="<?= (int) $actTs ?>"
                 data-name="<?= htmlspecialchars(strtolower($displayName), ENT_QUOTES, 'UTF-8') ?>"
                 data-staff="<?= htmlspecialchars(strtolower(trim((string) ($r['staff_name'] ?? ''))), ENT_QUOTES, 'UTF-8') ?>"
                 data-ti="<?= htmlspecialchars(strtolower(trim((string) ($r['ti_name'] ?? ''))), ENT_QUOTES, 'UTF-8') ?>"
                 data-paymode="<?= htmlspecialchars(strtolower(trim((string) ($r['payment_mode'] ?? ''))), ENT_QUOTES, 'UTF-8') ?>"
                 data-paystatus="<?= htmlspecialchars(strtolower(trim((string) ($r['staff_payment_status'] ?? ''))), ENT_QUOTES, 'UTF-8') ?>">

                <div class="user-card-header">
                    <div class="user-left-title">
                        <input type="checkbox"
                               class="user-checkbox fee-user-cb"
                               value="<?= $uid > 0 ? (int) $uid : '' ?>"
                               data-fee-id="<?= (int) $feeId ?>"
                            <?= $uid > 0 ? '' : ' disabled title="No linked member for bulk status"' ?>>
                        <h5><?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?> ( <?= htmlspecialchars($matriShow, ENT_QUOTES, 'UTF-8') ?> )</h5>
                    </div>
                    <div class="income-fee-header-actions">
                        <div class="approved-badge <?= htmlspecialchars($badgeClass, ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fa <?= htmlspecialchars($badgeIcon, ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i>
                            <?= htmlspecialchars($badgeText, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <button type="button" class="btn-copy-matri" title="Copy Matri ID" aria-label="Copy Matri ID" data-copy="<?= htmlspecialchars($matriShow, ENT_QUOTES, 'UTF-8') ?>">C</button>
                    </div>
                </div>

                <div class="counter-row">
                    <?php if ($uid > 0): ?>
                        <a class="counter-box blue text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int) $uid ?>&action=opened">Opened (<?= (int) ($r['opened_count'] ?? 0) ?>)</a>
                        <a class="counter-box yellow text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int) $uid ?>&action=deferred">Deferred (<?= (int) ($r['deferred_count'] ?? 0) ?>)</a>
                        <a class="counter-box red text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int) $uid ?>&action=declined">Declined (<?= (int) ($r['declined_count'] ?? 0) ?>)</a>
                        <a class="counter-box cyan text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int) $uid ?>&action=meeting">Meeting (<?= (int) ($r['meeting_count'] ?? 0) ?>)</a>
                        <a class="counter-box green text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int) $uid ?>&action=accepted">Accepted (<?= (int) ($r['accepted_count'] ?? 0) ?>)</a>
                    <?php else: ?>
                        <span class="counter-box blue">Opened (0)</span>
                        <span class="counter-box yellow">Deferred (0)</span>
                        <span class="counter-box red">Declined (0)</span>
                        <span class="counter-box cyan">Meeting (0)</span>
                        <span class="counter-box green">Accepted (0)</span>
                    <?php endif; ?>
                </div>

                <div class="user-main-content">
                    <div class="profile-image-box">
                        <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="">
                    </div>
                    <div class="details-column details-grid">
                        <p><strong>Gender</strong><span>:</span> <?= htmlspecialchars((string) ($r['gender'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Mobile</strong><span>:</span> <?= htmlspecialchars(trim((string) ($r['mobile_number'] ?? '')) !== '' ? (string) $r['mobile_number'] : (string) ($r['phone'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Religion</strong><span>:</span> <?= htmlspecialchars((string) ($r['religion'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Caste</strong><span>:</span> <?= htmlspecialchars((string) ($r['caste'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Mother Tongue</strong><span>:</span> <?= htmlspecialchars((string) ($r['mother_tongue'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Marital Status</strong><span>:</span> <?= htmlspecialchars((string) ($r['marital_status'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Plan Name</strong><span>:</span> <?= htmlspecialchars($planNameDisp, ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Plan Expired On</strong><span>:</span> <?= htmlspecialchars($planExp, ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Added By</strong><span>:</span> <?= htmlspecialchars(trim((string) ($r['ti_name'] ?? '')) !== '' ? (string) $r['ti_name'] : (string) ($r['staff_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Matri Id</strong><span>:</span> <?= htmlspecialchars($matriShow !== '' ? $matriShow : '-', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="details-column details-grid">
                        <p><strong>Email</strong><span>:</span> <?= htmlspecialchars((string) ($r['email'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Country</strong><span>:</span> <?= htmlspecialchars((string) ($r['country'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>State</strong><span>:</span> <?= htmlspecialchars((string) ($r['state'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>City</strong><span>:</span> <?= htmlspecialchars((string) ($r['city'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Birthdate</strong><span>:</span> <?= htmlspecialchars($fmtDate($r['dob'] ?? null), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Registered On</strong><span>:</span> <?= htmlspecialchars($uid > 0 ? $fmtDate($r['ud_created_at'] ?? null) : '-', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Last Login</strong><span>:</span> -</p>
                        <p><strong>Partner Contact Pdf</strong><span>:</span> -</p>
                        <p><strong><?= htmlspecialchars($feeColumnLabel, ENT_QUOTES, 'UTF-8') ?></strong><span>:</span> <?= htmlspecialchars($feeAmountDisp, ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Profile <?= $isRishta ? 'Final Rishta' : 'Registration' ?> (DB)</strong><span>:</span> <?= htmlspecialchars($profileFinalFee, ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Activation</strong><span>:</span> <?= htmlspecialchars($fmtDate($r['activation_date'] ?? null), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Payment Mode</strong><span>:</span> <?= htmlspecialchars((string) ($r['payment_mode'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>

                <div class="action-row">
                    <?php if ($uid > 0):
                        $jsName = htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');
                        ?>
                        <a class="btn-action btn-action-teal" href="<?= BASE_URL ?>/admin/users/open-task?id=<?= (int) $uid ?>">Open Task</a>
                        <button type="button" class="btn-action btn-action-cyan" onclick="openDynamicTeamPopup(<?= (int) $uid ?>, '<?= $jsName ?>')">View Team</button>
                        <button type="button" class="btn-action btn-action-teal" onclick="openCommentPopup(<?= (int) $uid ?>, '<?= $jsName ?>')">Add Comment</button>
                        <a class="btn-action btn-action-teal" href="<?= BASE_URL ?>/admin/users/profile-view?id=<?= (int) $uid ?>">View Profile</a>
                        <button type="button" class="btn-action btn-action-amber" onclick="openViewCommentsPopup(<?= (int) $uid ?>, '<?= $jsName ?>')">View Comments</button>
                        <a class="btn-action btn-action-cyan" href="<?= BASE_URL ?>/admin/users/edit-steps?id=<?= (int) $uid ?>">Edit Profile</a>
                        <a class="btn-action btn-action-cyan" target="_blank" href="<?= BASE_URL ?>/profile/<?= (int) $uid ?>">Profile Link</a>
                        <a class="btn-action btn-action-green" target="_blank" href="<?= BASE_URL ?>/admin/users/profile-pdf-template?id=<?= (int) $uid ?>">Profile PDF</a>
                        <form method="post" action="<?= BASE_URL ?>/admin/users/send-email-confirmation" class="btn-action-form">
                            <input type="hidden" name="user_id" value="<?= (int) $uid ?>">
                            <button class="btn-action btn-action-teal" type="submit">Email Confirmation</button>
                        </form>
                        <?php if ($paySt === 'paid'): ?>
                            <span class="btn-action btn-action-muted" title="This fee is already marked paid">Marked paid</span>
                        <?php else: ?>
                            <form method="post" action="<?= BASE_URL ?>/admin/accounts/income/fee-paid-approved" class="btn-action-form" onsubmit="return confirm('Mark this fee as Paid and set linked member to Approved?');">
                                <input type="hidden" name="fee_id" value="<?= (int) $feeId ?>">
                                <button class="btn-action btn-action-green" type="submit">Paid as Approved</button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <a class="btn-action btn-action-teal" href="<?= BASE_URL ?>/admin/users/open-task?id=<?= (int)$u['id'] ?>">Open Task</a>
                    <button type="button" class="btn-action btn-action-cyan" onclick="openDynamicTeamPopup(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['first_name'].' '.$u['last_name'], ENT_QUOTES) ?>')">
                        View Team</button>
                    <button type="button" class="btn-action btn-action-teal" onclick="openCommentPopup(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['first_name'].' '.$u['last_name'], ENT_QUOTES) ?>')">Add Comment</button>
                    <a class="btn-action btn-action-teal" href="<?= BASE_URL ?>/admin/users/profile-view?id=<?= (int)$u['id'] ?>">View Profile</a>
                    <button type="button" class="btn-action btn-action-amber" onclick="openViewCommentsPopup(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['first_name'].' '.$u['last_name'], ENT_QUOTES) ?>')">View Comments</button>
                    <a class="btn-action btn-action-cyan" href="<?= BASE_URL ?>/admin/users/edit-steps?id=<?= (int)$u['id'] ?>">Edit Profile</a>
                    <a class="btn-action btn-action-cyan" target="_blank" href="<?= BASE_URL ?>/profile/<?= (int)$u['id'] ?>">Profile Link</a>
                    <a class="btn-action btn-action-green" target="_blank" href="<?= BASE_URL ?>/admin/users/profile-pdf-template?id=<?= (int)$u['id'] ?>">Profile PDF</a>
                 
                        <?php if ($paySt === 'paid'): ?>
                            <span class="btn-action btn-action-muted" title="This fee is already marked paid">Marked paid</span>
                        <?php else: ?>
                            <form method="post" action="<?= BASE_URL ?>/admin/accounts/income/fee-paid-approved" class="btn-action-form" onsubmit="return confirm('Mark this fee as Paid only (no linked member)?');">
                                <input type="hidden" name="fee_id" value="<?= (int) $feeId ?>">
                                <button class="btn-action btn-action-green" type="submit">Paid as Approved</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div id="feeFilterPopup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup">
        <div class="popup-header">
            <h3>Filter Data</h3>
            <span class="close-popup" onclick="closeFeeFilterPopup()">&times;</span>
        </div>
        <form id="feeAdvancedFilterForm" onsubmit="applyFeeAdvancedFilters(event)">
            <div class="popup-body">
                <div class="form-group">
                    <label>Staff Name</label>
                    <select class="form-control" id="feeFilterStaff">
                        <option value="">All</option>
                        <?php foreach ($staffOpts as $opt): ?>
                            <option value="<?= htmlspecialchars(strtolower($opt), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>TI Name</label>
                    <select class="form-control" id="feeFilterTi">
                        <option value="">All</option>
                        <?php foreach ($tiOpts as $opt): ?>
                            <option value="<?= htmlspecialchars(strtolower($opt), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Mode</label>
                    <select class="form-control" id="feeFilterPayMode">
                        <option value="">All</option>
                        <?php foreach ($payModeOpts as $opt): ?>
                            <option value="<?= htmlspecialchars(strtolower($opt), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Staff Payment Status</label>
                    <select class="form-control" id="feeFilterPayStatus">
                        <option value="">All</option>
                        <?php foreach ($statusOpts as $opt): ?>
                            <option value="<?= htmlspecialchars(strtolower($opt), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="popup-footer">
                <button type="submit" class="btn-submit">Submit</button>
                <button type="button" class="btn-cancel" onclick="clearFeeAdvancedFilters()">Reset</button>
                <button type="button" class="btn-cancel" onclick="closeFeeFilterPopup()">Close</button>
            </div>
        </form>
    </div>
</div>

<!-- ADD COMMENT POPUP -->
<div id="commentPopup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup custom-popup-lg">
        <div class="popup-header">
            <h3 id="commentPopupTitle">Add Comment</h3>
            <span class="close-popup" onclick="closeCommentPopup()">&times;</span>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/admin/users/comment">
            <div class="popup-body">
                <input type="hidden" name="user_id" id="comment_user_id">
                <div class="form-group">
                    <label>Type</label>
                    <select class="form-control" name="comment_type">
                        <option value="general">General</option>
                        <option value="follow_up">Follow Up</option>
                        <option value="warning">Warning</option>
                        <option value="approval_note">Approval Note</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Comment</label>
                    <textarea class="form-control" name="comment" rows="6" placeholder="Write comment..." required></textarea>
                </div>
            </div>
            <div class="popup-footer">
                <button type="submit" class="btn-submit">Save Comment</button>
                <button type="button" class="btn-cancel" onclick="closeCommentPopup()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="viewCommentsPopup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup custom-popup-xl">
        <div class="popup-header">
            <h3 id="viewCommentPopupTitle">View Comments</h3>
            <span class="close-popup" onclick="closeViewCommentsPopup()">&times;</span>
        </div>
        <div class="popup-body">
            <div class="row g-2 mb-3">
                <input type="hidden" id="view_comment_user_id">
                <div class="col-md-4">
                    <label>Type</label>
                    <select id="filter_comment_type" class="form-control">
                        <option value="">All</option>
                        <option value="general">General</option>
                        <option value="follow_up">Follow Up</option>
                        <option value="warning">Warning</option>
                        <option value="approval_note">Approval Note</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>From</label>
                    <input type="date" id="filter_comment_from" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>To</label>
                    <input type="date" id="filter_comment_to" class="form-control">
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm mb-3" onclick="loadProfileComments()">Apply Filter</button>
            <div id="commentsResults" style="max-height:380px;overflow:auto;"></div>
        </div>
        <div class="popup-footer">
            <button type="button" class="btn-cancel" onclick="closeViewCommentsPopup()">Close</button>
        </div>
    </div>
</div>

<div id="dynamicTeamPopup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup custom-popup-lg">
        <div class="popup-header">
            <h3 id="dynamicTeamPopupTitle">Dynamic assign team</h3>
            <span class="close-popup" onclick="closeDynamicTeamPopup()">&times;</span>
        </div>
        <div class="popup-body">
            <div id="dynamicTeamMeta" class="mb-3 small text-muted"></div>
            <div id="dynamicTeamResults"></div>
        </div>
        <div class="popup-footer">
            <button type="button" class="btn-cancel" onclick="closeDynamicTeamPopup()">Close</button>
        </div>
    </div>
</div>

<script>
function openCommentPopup(userId, userName){
    document.getElementById('comment_user_id').value = userId;
    document.getElementById('commentPopupTitle').innerText = 'Add Comment - ' + userName;
    document.getElementById('commentPopup').style.display = 'flex';
}
function closeCommentPopup(){
    document.getElementById('commentPopup').style.display = 'none';
}
function openViewCommentsPopup(userId, userName){
    document.getElementById('view_comment_user_id').value = userId;
    document.getElementById('viewCommentPopupTitle').innerText = 'View Comments - ' + userName;
    document.getElementById('viewCommentsPopup').style.display = 'flex';
    loadProfileComments();
}
function closeViewCommentsPopup(){
    document.getElementById('viewCommentsPopup').style.display = 'none';
}
function openDynamicTeamPopup(userId, userName){
    document.getElementById('dynamicTeamPopupTitle').innerText = 'Dynamic assign team — ' + userName;
    document.getElementById('dynamicTeamMeta').innerHTML = '';
    document.getElementById('dynamicTeamResults').innerHTML = '<div class="text-muted">Loading…</div>';
    document.getElementById('dynamicTeamPopup').style.display = 'flex';
    const url = `<?= BASE_URL ?>/admin/users/member-dynamic-team-json?user_id=${encodeURIComponent(userId)}`;
    fetch(url)
        .then(r => r.json())
        .then(data => {
            const meta = document.getElementById('dynamicTeamMeta');
            const wrap = document.getElementById('dynamicTeamResults');
            if (!data.ok) {
                wrap.innerHTML = '<div class="alert alert-warning mb-0">Unable to load team.</div>';
                return;
            }
            let metaParts = [];
            if (data.team_name) metaParts.push('<strong>Team</strong>: ' + escapeHtmlFee(data.team_name));
            if (data.primary_lead_name) metaParts.push('<strong>Assigned lead</strong>: ' + escapeHtmlFee(data.primary_lead_name));
            meta.innerHTML = metaParts.length ? metaParts.join(' &nbsp;|&nbsp; ') : '<span>Staff linked to tasks, matches, or team grouping from this profile.</span>';
            if (!data.rows || data.rows.length === 0) {
                wrap.innerHTML = '<div class="alert alert-warning mb-0">No assigned team staff found.</div>';
                return;
            }
            const rowsHtml = data.rows.map(function(row){
                const dept = escapeHtmlFee(row.department || '—');
                let desig = escapeHtmlFee(row.designation || 'Staff');
                if (row.is_primary) desig += ' <span class="badge bg-success ms-1">Primary lead</span>';
                const name = escapeHtmlFee(row.name || '');
                const contact = row.contact ? '(' + escapeHtmlFee(row.contact) + ')' : '';
                const off = row.official ? '<span class="badge bg-success ms-1">Official</span>' : '';
                return '<tr><td>' + dept + '</td><td>' + desig + '</td><td>' + name + ' ' + contact + ' ' + off + '</td></tr>';
            }).join('');
            wrap.innerHTML = '<div class="table-responsive"><table class="table table-sm table-striped align-middle mb-0" style="background:#fff;border:1px solid #e9ecef;border-radius:6px;"><thead><tr><th>Department</th><th>Designation</th><th>Name</th></tr></thead><tbody>' + rowsHtml + '</tbody></table></div>';
        })
        .catch(function(){
            document.getElementById('dynamicTeamResults').innerHTML = '<div class="alert alert-danger mb-0">Unable to load team.</div>';
        });
}
function closeDynamicTeamPopup(){
    document.getElementById('dynamicTeamPopup').style.display = 'none';
}
function escapeHtmlFee(str){
    return (str || '').replace(/[&<>"']/g, function(m){
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]);
    });
}
function loadProfileComments(){
    const userId = document.getElementById('view_comment_user_id').value;
    const type = document.getElementById('filter_comment_type').value;
    const from = document.getElementById('filter_comment_from').value;
    const to = document.getElementById('filter_comment_to').value;
    const url = `<?= BASE_URL ?>/admin/users/comments-json?user_id=${encodeURIComponent(userId)}&type=${encodeURIComponent(type)}&date_from=${encodeURIComponent(from)}&date_to=${encodeURIComponent(to)}`;
    fetch(url)
        .then(r => r.json())
        .then(data => {
            const wrap = document.getElementById('commentsResults');
            if (!data.ok || !data.rows || data.rows.length === 0) {
                wrap.innerHTML = '<div class="alert alert-warning mb-0">No comments found.</div>';
                return;
            }
            wrap.innerHTML = data.rows.map(function(row){
                return '<div class="comment-item"><div class="comment-meta"><strong>' + escapeHtmlFee(row.admin_name || 'Admin') + '</strong> | ' + escapeHtmlFee(row.comment_type || 'general') + ' | ' + escapeHtmlFee(row.created_at || '') + '</div><div>' + escapeHtmlFee(row.comment || '') + '</div></div>';
            }).join('');
        })
        .catch(function(){
            document.getElementById('commentsResults').innerHTML = '<div class="alert alert-danger mb-0">Unable to load comments.</div>';
        });
}

(function () {
    document.querySelectorAll('.btn-copy-matri').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var t = this.getAttribute('data-copy') || '';
            if (!t) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(t).catch(function () {});
            }
        });
    });
})();

const feeSearchInput = document.getElementById('feeMemberSearch');
const feeSortSelect = document.getElementById('sortFeeMembers');
const feeShowEntries = document.getElementById('showFeeEntries');
let feeActiveTab = 'all';
let feeCards = Array.from(document.querySelectorAll('.fee-income-card'));
const feeAdvancedFilters = { staff: '', ti: '', paymode: '', paystatus: '' };

document.getElementById('selectAllFeeMembers').addEventListener('change', function () {
    const on = this.checked;
    document.querySelectorAll('.fee-user-cb:not(:disabled)').forEach(function (cb) {
        cb.checked = on;
    });
});

function updateFeeList() {
    const q = (feeSearchInput && feeSearchInput.value ? feeSearchInput.value : '').toLowerCase().trim();
    const limit = parseInt(feeShowEntries.value, 10) || 10;
    let filtered = feeCards.filter(function (card) {
        const text = card.innerText.toLowerCase();
        const tabSt = card.dataset.tabStatus || '';
        if (!text.includes(q)) return false;
        if (feeActiveTab === 'all') {
            /* ok */
        } else if (feeActiveTab === 'unlinked') {
            if (tabSt !== 'unlinked') return false;
        } else if (tabSt !== feeActiveTab) {
            return false;
        }
        if (feeAdvancedFilters.staff && card.dataset.staff !== feeAdvancedFilters.staff) return false;
        if (feeAdvancedFilters.ti && card.dataset.ti !== feeAdvancedFilters.ti) return false;
        if (feeAdvancedFilters.paymode && card.dataset.paymode !== feeAdvancedFilters.paymode) return false;
        if (feeAdvancedFilters.paystatus && card.dataset.paystatus !== feeAdvancedFilters.paystatus) return false;
        return true;
    });
    const sort = feeSortSelect.value;
    filtered.sort(function (a, b) {
        if (sort === 'latest_desc') return parseInt(b.dataset.date, 10) - parseInt(a.dataset.date, 10);
        if (sort === 'latest_asc') return parseInt(a.dataset.date, 10) - parseInt(b.dataset.date, 10);
        if (sort === 'name_asc') return (a.dataset.name || '').localeCompare(b.dataset.name || '');
        if (sort === 'name_desc') return (b.dataset.name || '').localeCompare(a.dataset.name || '');
        return 0;
    });
    feeCards.forEach(function (c) { c.style.display = 'none'; });
    filtered.slice(0, limit).forEach(function (c) { c.style.display = 'block'; });
}

feeSearchInput.addEventListener('keyup', updateFeeList);
feeSortSelect.addEventListener('change', updateFeeList);
feeShowEntries.addEventListener('change', updateFeeList);
document.getElementById('feeSearchBtn').addEventListener('click', updateFeeList);
document.getElementById('feeClearSearchBtn').addEventListener('click', function () {
    feeSearchInput.value = '';
    updateFeeList();
});

document.querySelectorAll('.tab-filter-fee').forEach(function (tab) {
    tab.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelectorAll('.tab-filter-fee').forEach(function (t) { t.classList.remove('active'); });
        this.classList.add('active');
        feeActiveTab = this.dataset.tab || 'all';
        updateFeeList();
    });
});

function openFeeFilterPopup() {
    document.getElementById('feeFilterPopup').style.display = 'flex';
}
function closeFeeFilterPopup() {
    document.getElementById('feeFilterPopup').style.display = 'none';
}
function applyFeeAdvancedFilters(e) {
    if (e) e.preventDefault();
    feeAdvancedFilters.staff = (document.getElementById('feeFilterStaff').value || '').toLowerCase();
    feeAdvancedFilters.ti = (document.getElementById('feeFilterTi').value || '').toLowerCase();
    feeAdvancedFilters.paymode = (document.getElementById('feeFilterPayMode').value || '').toLowerCase();
    feeAdvancedFilters.paystatus = (document.getElementById('feeFilterPayStatus').value || '').toLowerCase();
    closeFeeFilterPopup();
    updateFeeList();
}
function clearFeeAdvancedFilters() {
    document.getElementById('feeAdvancedFilterForm').reset();
    feeAdvancedFilters.staff = '';
    feeAdvancedFilters.ti = '';
    feeAdvancedFilters.paymode = '';
    feeAdvancedFilters.paystatus = '';
    updateFeeList();
}

function submitFeeBulkStatus(statusValue) {
    const selected = Array.from(document.querySelectorAll('.fee-user-cb:checked'))
        .map(function (cb) { return cb.value; })
        .filter(function (v) { return v !== '' && v !== '0'; });
    if (selected.length === 0) {
        alert('Select at least one row with a linked member profile.');
        return;
    }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= BASE_URL ?>/admin/users/bulk-status';
    const bulk = document.createElement('input');
    bulk.type = 'hidden';
    bulk.name = 'bulk_status';
    bulk.value = statusValue;
    form.appendChild(bulk);
    selected.forEach(function (id) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_users[]';
        input.value = id;
        form.appendChild(input);
    });
    document.body.appendChild(form);
    form.submit();
}

updateFeeList();

window.addEventListener('click', function (e) {
    const pop = document.getElementById('feeFilterPopup');
    if (pop && e.target === pop) {
        pop.style.display = 'none';
    }
    const cp = document.getElementById('commentPopup');
    if (cp && e.target === cp) cp.style.display = 'none';
    const vc = document.getElementById('viewCommentsPopup');
    if (vc && e.target === vc) vc.style.display = 'none';
    const teamPop = document.getElementById('dynamicTeamPopup');
    if (teamPop && e.target === teamPop) teamPop.style.display = 'none';
});
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
