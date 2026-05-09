<?php
$title = "Admin Dashboard";
require __DIR__.'/partials/header.php';
require __DIR__.'/partials/sidebar.php';
?>
<style>
    .admin-page {
        padding-top: 12px;
        padding-bottom: 18px;
    }

    .admin-dashboard-topbar {
        justify-content: flex-start;
        padding: 0 18px;
    }

    .admin-dashboard-topbar .mobile-menu-btn {
        margin-right: 14px;
        flex-shrink: 0;
    }

    .admin-dashboard-topbar .admin-topbar-title {
        font-size: 13px;
        font-weight: 600;
        color: #565656;
        line-height: 1.5;
    }

    .admin-dashboard-topbar .admin-profile {
        margin-left: auto;
    }

    .admin-dashboard-topbar .admin-profile-box {
        font-size: 13px;
        line-height: 1.5;
    }

    .stats-row { --bs-gutter-x: 12px; --bs-gutter-y: 12px; }

    .stat-card {
        background: #fff;
        border: 1px solid #ececec;
        padding: 10px 12px;
        min-height: 70px;
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .stat-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .stat-card-link:hover .stat-card {
        border-color: #d6e7f7;
        box-shadow: 0 3px 10px rgba(0, 0, 0, .04);
    }

    .stat-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }
    .blue { background: #2f8edb; }
    .red { background: #df745f; }
    .green { background: #39bf72; }
    .sky { background: #2f96db; }

    .stat-text h2 {
        margin: 0;
        font-size: 23px;
        line-height: 1;
        color: #3a3a3a;
        font-weight: 400;
    }
    .stat-text p {
        margin: 4px 0 0;
        color: #868686;
        font-size: 11px;
        line-height: 1.2;
    }

    .member-table-card {
        background: #fff;
        border: 1px solid #ececec;
        margin-top: 14px;
        padding: 12px;
    }
    .member-table-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .member-table-title {
        margin: 0;
        font-size: 24px;
        font-weight: 300;
        color: #5b5b5b;
        text-transform: uppercase;
        letter-spacing: .3px;
    }
    .view-link {
        font-size: 11px;
        color: #7f7f7f;
        text-decoration: none;
    }
    .view-link:hover { color: #4a4a4a; }

    .dashboard-table {
        margin-bottom: 0;
        min-width: 780px;
    }
    .dashboard-table thead th {
        font-weight: 600;
        color: #5e5e5e;
        border-bottom: 1px solid #e2e2e2;
        font-size: 12px;
        text-transform: none;
        white-space: nowrap;
        padding: 8px 10px;
    }
    .dashboard-table td {
        color: #444;
        font-size: 12px;
        line-height: 1.5;
        border-top: 1px solid #efefef;
        vertical-align: top;
        padding: 8px 10px;
    }
    .dashboard-table tbody tr:nth-child(even) {
        background: #fafafa;
    }
    .cell-sub { display: block; color: #9d9d9d; font-size: 11px; line-height: 1.25; }
    .text-nowrap { white-space: nowrap; }

    @media (max-width: 991.98px) {
        .member-table-title { font-size: 19px; }
    }
</style>

<?php
    $totalUsers = is_array($users ?? null) ? count($users) : 0;
    $paidMembers = is_array($profiles ?? null) ? count($profiles) : 0;
    $todayMembers = 0;
    $lastWeekMembers = 0;
    $lastMonthMembers = 0;
    $maleMembers = 0;
    $femaleMembers = 0;
    $activeMembers = $totalUsers;

    $todayStart = strtotime('today');
    $weekStart = strtotime('-7 days');
    $monthStart = strtotime('-30 days');

    if (!empty($users) && is_array($users)) {
        foreach ($users as $user) {
            $createdTs = !empty($user['created_at']) ? strtotime($user['created_at']) : null;
            if ($createdTs) {
                if ($createdTs >= $todayStart) {
                    $todayMembers++;
                }
                if ($createdTs >= $weekStart) {
                    $lastWeekMembers++;
                }
                if ($createdTs >= $monthStart) {
                    $lastMonthMembers++;
                }
            }

            $gender = strtolower(trim((string)($user['gender'] ?? '')));
            if ($gender === 'male') {
                $maleMembers++;
            } elseif ($gender === 'female') {
                $femaleMembers++;
            }
        }
    }
?>

<div class="admin-main">
    <div class="admin-topbar admin-dashboard-topbar">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu">
            <i class="fa fa-bars"></i>
        </button>

        <span class="admin-topbar-title">Dashboard</span>

        <div class="admin-profile" id="adminProfileTrigger">
            <div class="admin-profile-box">
                <span><?= htmlspecialchars($this->displayadminname()) ?></span>
                <i class="fa fa-user"></i>
            </div>

            <div class="admin-dropdown" id="adminDropdown">
                <a href="<?= BASE_URL ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
                <a href="<?= BASE_URL ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </div>

    <main class="admin-page">

    <div class="row stats-row">
        <div class="col-md-3 col-sm-6">
            <a class="stat-card-link" href="<?= BASE_URL ?>/admin/users?dashboard_filter=today">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-text">
                        <h2><?= number_format($todayMembers) ?></h2>
                        <p>Today Member(s)</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a class="stat-card-link" href="<?= BASE_URL ?>/admin/users?dashboard_filter=last_week">
                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                    <div class="stat-text">
                        <h2><?= number_format($lastWeekMembers) ?></h2>
                        <p>Last Week Member(s)</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a class="stat-card-link" href="<?= BASE_URL ?>/admin/users?dashboard_filter=last_month">
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                    <div class="stat-text">
                        <h2><?= number_format($lastMonthMembers) ?></h2>
                        <p>Last Month Member(s)</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a class="stat-card-link" href="<?= BASE_URL ?>/admin/users?dashboard_filter=total">
                <div class="stat-card">
                    <div class="stat-icon sky">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-text">
                        <h2><?= number_format($totalUsers) ?></h2>
                        <p>Total Member(s)</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a class="stat-card-link" href="<?= BASE_URL ?>/admin/users?dashboard_filter=male">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="bi bi-gender-male"></i>
                    </div>
                    <div class="stat-text">
                        <h2><?= number_format($maleMembers) ?></h2>
                        <p>Male Member(s)</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a class="stat-card-link" href="<?= BASE_URL ?>/admin/users?dashboard_filter=female">
                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="bi bi-gender-female"></i>
                    </div>
                    <div class="stat-text">
                        <h2><?= number_format($femaleMembers) ?></h2>
                        <p>Female Member(s)</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a class="stat-card-link" href="<?= BASE_URL ?>/admin/users?dashboard_filter=active">
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div class="stat-text">
                        <h2><?= number_format($activeMembers) ?></h2>
                        <p>Active Member</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a class="stat-card-link" href="<?= BASE_URL ?>/admin/users?dashboard_filter=paid">
                <div class="stat-card">
                    <div class="stat-icon sky">
                        <i class="bi bi-person-fill-check"></i>
                    </div>
                    <div class="stat-text">
                        <h2><?= number_format($paidMembers) ?></h2>
                        <p>Paid Member</p>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <div class="member-table-card">
        <div class="member-table-head">
            <h2 class="member-table-title">Latest Registered Member</h2>
            <a href="<?= BASE_URL ?>/admin/users" class="view-link">View All Member</a>
        </div>

        <?php if (!empty($users)) : ?>
            <div class="table-responsive">
                <table class="table table-hover dashboard-table">
                    <thead>
                        <tr>
                            <th>Matri Id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Marital Status</th>
                            <th>Location</th>
                            <th>Registered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td><?= htmlspecialchars(matri_id_display((string) ($user['matri_id'] ?? ''), (int) ($user['id'] ?? 0), true)) ?></td>
                                <td><?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['gender'] ?? 'Male') ?></td>
                                <td><?= htmlspecialchars($user['marital_status'] ?? 'Unmarried') ?></td>
                                <td>
                                    <?= htmlspecialchars($user['city'] ?? 'Lahore') ?>
                                    <span class="cell-sub"><?= htmlspecialchars($user['country'] ?? 'Pakistan') ?></span>
                                </td>
                                <td class="text-nowrap">
                                    <?php if (!empty($user['created_at'])) : ?>
                                        <?= date('F d, Y h:i A', strtotime($user['created_at'])) ?>
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p class="text-muted">No recent users found.</p>
        <?php endif; ?>

    </div>

<?php require __DIR__.'/partials/footer.php'; ?>