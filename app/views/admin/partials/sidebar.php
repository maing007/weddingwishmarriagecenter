<?php
if (empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/login');
    exit;
}

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = parse_url((string) BASE_URL, PHP_URL_PATH) ?: '';
if ($basePath !== '' && strpos($requestPath, $basePath) === 0) {
    $requestPath = substr($requestPath, strlen($basePath));
}
$requestPath = '/' . ltrim($requestPath, '/');
$requestPath = rtrim($requestPath, '/');
if ($requestPath === '') {
    $requestPath = '/';
}

$isActivePath = static function (string $path, bool $exact = true) use ($requestPath): bool {
    $path = '/' . ltrim($path, '/');
    $path = rtrim($path, '/');
    if ($path === '') {
        $path = '/';
    }
    if ($exact) {
        return $requestPath === $path;
    }
    return $requestPath === $path || strpos($requestPath, $path . '/') === 0;
};
?>

<style>
    .dashboard-sidebar {
        --sb-bg: #495469;
        --sb-bg-open: #3e4759;
        --sb-text: rgba(255, 255, 255, 0.94);
        --sb-text-soft: #e2e6ec;
        width: var(--sidebar-width, 268px);
        min-height: 100vh;
        background: var(--sb-bg);
        padding: 0 8px 10px;
        position: fixed !important;
        left: 0;
        top: 0;
        z-index: 1040;
        overflow-x: hidden;
        overflow-y: auto;
        transition: width .28s ease, transform .25s ease;
        font-family: "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, "Helvetica Neue", Arial, sans-serif;
        font-size: 13px;
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
    }

    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 10px 10px 8px;
        margin: 0 0 2px;
        border-bottom: 1px solid rgba(255, 255, 255, .09);
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        flex: 1;
        min-width: 0;
    }

    .sidebar-logo img {
        max-width: 56px;
        width: 100%;
        height: auto;
        display: block;
    }

    .sidebar-hamburger {
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        border: none;
        background: transparent;
        cursor: pointer;
        border-radius: 6px;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 0;
        color: #fff;
    }

    .sidebar-hamburger:hover { background: rgba(255, 255, 255, .1); }

    .sidebar-hamburger-box { display: block; }

    .sidebar-hamburger-line {
        display: block;
        width: 22px;
        height: 2px;
        background: #fff;
        margin: 5px 0;
        border-radius: 1px;
        transition: opacity .2s ease;
    }

    @media (min-width: 992px) {
        .sidebar-hamburger { display: flex; }
    }

    @media (max-width: 991px) {
        .sidebar-hamburger { display: none !important; }
    }

    .sidebar-menu {
        list-style: none;
        padding: 2px 0 0;
        margin: 0;
    }

    .sidebar-menu > li {
        margin-bottom: 0;
    }

    .sidebar-menu > li > a {
        display: flex;
        align-items: center;
        padding: 8px 18px;
        color: var(--sb-text);
        font-size: 13px;
        font-weight: 500;
        line-height: 1.5;
        text-decoration: none;
        border-radius: 4px;
        transition: background .18s ease, color .18s ease, box-shadow .18s ease;
    }

    .sidebar-menu > li > a:hover {
        background: rgba(255, 255, 255, .08);
        color: #fff;
    }

    .sidebar-menu > li > a.active:not([href="#"]) {
        background: var(--sb-bg-open);
        color: #fff;
        box-shadow: inset 3px 0 0 rgba(255, 255, 255, .95);
    }

    .sidebar-menu li > a .fa:first-child,
    .sidebar-menu li > a .bi:first-child {
        width: 20px;
        text-align: center;
        margin-right: 12px;
        font-size: 14px;
        opacity: .95;
    }

    .has-submenu {
        border-radius: 6px;
        transition: background .2s ease;
    }

    .has-submenu.open {
        background: var(--sb-bg-open);
        padding-bottom: 2px;
    }

    .has-submenu.open > a {
        color: #fff;
    }

    .submenu {
        list-style: none;
        margin: 2px 0 0;
        padding: 0 0 2px 44px;
        display: none;
    }

    .submenu.open { display: block; }

    .submenu li > a {
        display: flex;
        align-items: center;
        color: var(--sb-text-soft);
        text-decoration: none;
        font-size: 13px;
        font-weight: 400;
        line-height: 1.5;
        padding: 5px 18px 5px 0;
        border-radius: 4px;
        transition: color .15s ease, background .15s ease;
    }

    .submenu li > a > .fa:first-child,
    .submenu li > a > .bi:first-child {
        width: 18px;
        text-align: center;
        margin-right: 8px;
        font-size: 12px;
        opacity: .92;
        flex-shrink: 0;
    }

    .submenu li > a:hover {
        color: #fff;
        background: rgba(255, 255, 255, .06);
    }

    .submenu li > a.active {
        color: #fff;
        font-weight: 600;
        background: rgba(0, 0, 0, .15);
        padding-left: 10px;
        margin-left: -10px;
    }

    .menu-caret {
        margin-left: auto;
        font-size: 12px;
        opacity: .85;
        transition: transform .22s ease;
    }

    .has-submenu.open > a .menu-caret { transform: rotate(90deg); }

    .submenu .has-submenu > a {
        display: flex;
        align-items: center;
        padding: 5px 0;
        font-size: 13px;
        font-weight: 500;
        color: var(--sb-text-soft);
        text-decoration: none;
        border-radius: 4px;
        line-height: 1.5;
    }

    .submenu .has-submenu > a:hover { color: #fff; }
    .submenu .submenu {
        padding-left: 12px;
        margin: 4px 0 6px;
        border-left: 1px solid rgba(255, 255, 255, .12);
    }

    .submenu .submenu li > a {
        font-size: 13px;
        line-height: 1.5;
        padding: 4px 14px 4px 0;
    }

    .submenu .submenu li > a > .fa:first-child,
    .submenu .submenu li > a > .bi:first-child {
        width: 16px;
        font-size: 11px;
        margin-right: 6px;
    }

    .submenu .submenu li > a.active {
        padding-left: 10px;
        margin-left: -10px;
    }

    .sidebar-menu .sidebar-logout {
        color: #f5b0b0 !important;
    }

    .sidebar-menu .sidebar-logout:hover {
        background: rgba(220, 80, 80, .22) !important;
        color: #fff !important;
    }

    .sidebar-menu .mt-4 { margin-top: 24px !important; }

    /* Collapsed (desktop): icon rail */
    body.admin-sidebar-collapsed .dashboard-sidebar {
        width: var(--sidebar-collapsed-width, 78px) !important;
        padding-left: 6px;
        padding-right: 6px;
    }

    body.admin-sidebar-collapsed .sidebar-header {
        flex-direction: column;
        padding: 8px 4px 8px;
        gap: 8px;
    }

    body.admin-sidebar-collapsed .sidebar-logo {
        justify-content: center;
    }

    body.admin-sidebar-collapsed .sidebar-logo img {
        max-width: 42px;
    }

    body.admin-sidebar-collapsed .sidebar-menu > li > a .nav-item,
    body.admin-sidebar-collapsed .sidebar-menu > li > a .menu-caret {
        display: none !important;
    }

    body.admin-sidebar-collapsed .sidebar-menu .submenu {
        display: none !important;
    }

    body.admin-sidebar-collapsed .has-submenu.open {
        background: transparent;
        padding-bottom: 0;
    }

    body.admin-sidebar-collapsed .sidebar-menu > li > a {
        justify-content: center;
        padding: 8px 10px;
        box-shadow: none !important;
    }

    body.admin-sidebar-collapsed .sidebar-menu li > a .fa:first-child,
    body.admin-sidebar-collapsed .sidebar-menu li > a .bi:first-child {
        margin-right: 0;
        width: auto;
        font-size: 16px;
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .35);
        z-index: 1035;
    }

    @media (max-width: 991px) {
        .dashboard-sidebar {
            width: min(288px, 88vw);
        }
        body.admin-sidebar-collapsed .dashboard-sidebar {
            width: min(288px, 88vw) !important;
        }
        body.admin-sidebar-collapsed .sidebar-menu > li > a .nav-item,
        body.admin-sidebar-collapsed .sidebar-menu > li > a .menu-caret {
            display: flex !important;
        }
        body.admin-sidebar-collapsed .sidebar-menu .submenu.open {
            display: block !important;
        }
        body.admin-sidebar-collapsed .sidebar-header {
            flex-direction: row;
        }
        body.admin-sidebar-collapsed .sidebar-menu > li > a {
            justify-content: flex-start;
            padding: 8px 18px;
        }
        body.admin-sidebar-collapsed .sidebar-menu li > a .fa:first-child,
        body.admin-sidebar-collapsed .sidebar-menu li > a .bi:first-child {
            margin-right: 12px;
            width: 20px;
        }
        .dashboard-sidebar { transform: translateX(-100%); }
        .dashboard-sidebar.active { transform: translateX(0); }
        .sidebar-overlay.show { display: block; }
    }
</style>


<!-- SIDEBAR -->
<div id="sidebar" class="dashboard-sidebar">

    <div class="sidebar-header">
        <a class="sidebar-logo" href="<?= BASE_URL ?>/admin/dashboard" title="Admin dashboard">
            <img src="<?= BASE_URL ?>/assets/images/admin_logo.png" alt="Logo">
        </a>
        <button type="button" class="sidebar-hamburger" id="adminSidebarToggle" aria-expanded="true" aria-label="Collapse sidebar">
            <span class="sidebar-hamburger-box" aria-hidden="true">
                <span class="sidebar-hamburger-line"></span>
                <span class="sidebar-hamburger-line"></span>
                <span class="sidebar-hamburger-line"></span>
            </span>
        </button>
    </div>

    <ul class="nav flex-column sidebar-menu">
        <li>
            <a href="<?= BASE_URL ?>/admin/dashboard" class="<?= $isActivePath('/admin/dashboard', true) ? 'active' : '' ?>">
                <i class="fa fa-dashboard"></i>
                <span class="nav-item">Dashboard</span>
            </a>
        </li>

        <?php
        $incomeRegistrationPath = '/admin/accounts/income/registration-fee';
        $incomeRishtaPath = '/admin/accounts/income/rishta-fee';
        $accountsIncomeOpen = $isActivePath('/admin/accounts/income', false);
        $accountsSectionOpen = $isActivePath('/admin/sales-report', false)
            || $accountsIncomeOpen;
        ?>
        <li class="has-submenu <?= $accountsSectionOpen ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-calculator"></i>
                <span class="nav-item">Accounts</span>
                <i class="fa fa-chevron-right menu-caret"></i>
            </a>
            <ul class="submenu <?= $accountsSectionOpen ? 'open' : '' ?>">
                <li class="has-submenu <?= $accountsIncomeOpen ? 'open' : '' ?>">
                    <a href="#" data-menu-toggle>
                        <i class="fa fa-line-chart"></i>
                        <span class="nav-item">Income</span>
                        <i class="fa fa-chevron-right menu-caret"></i>
                    </a>
                    <ul class="submenu <?= $accountsIncomeOpen ? 'open' : '' ?>">
                        <li>
                            <a href="<?= BASE_URL . $incomeRegistrationPath ?>"
                               class="<?= ($requestPath === $incomeRegistrationPath) ? 'active' : '' ?>">
                                <i class="fa fa-money"></i>Registration Fee
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL . $incomeRishtaPath ?>"
                               class="<?= ($requestPath === $incomeRishtaPath) ? 'active' : '' ?>">
                                <i class="fa fa-heart"></i>Rishta Fee
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/sales-report" class="<?= $isActivePath('/admin/sales-report', true) ? 'active' : '' ?>">
                        <i class="fa fa-bar-chart"></i>Members Sales Report
                    </a>
                </li>
            </ul>
        </li>

        <li class="has-submenu <?= $isActivePath('/admin/team-management', false) ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-sitemap"></i>
                <span class="nav-item">Team Management</span>
                <i class="fa fa-chevron-right menu-caret"></i>
            </a>
            <ul class="submenu <?= $isActivePath('/admin/team-management', false) ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/team-management" class="<?= $isActivePath('/admin/team-management', false) ? 'active' : '' ?>">
                        <i class="fa fa-users"></i>Teams
                    </a>
                </li>
            </ul>
        </li>

        <?php
        $commOpen = $isActivePath('/admin/mail/inbox', false)
            || $isActivePath('/admin/mail/compose', false)
            || $isActivePath('/admin/contact-messages', false);
        ?>
        <li class="has-submenu <?= $commOpen ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-envelope"></i>
                <span class="nav-item">Communication</span>
                <i class="fa fa-chevron-right menu-caret"></i>
            </a>
            <ul class="submenu <?= $commOpen ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/mail/inbox" class="<?= $isActivePath('/admin/mail/inbox', false) ? 'active' : '' ?>">
                        <i class="fa fa-inbox"></i>Unified Inbox
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/mail/compose" class="<?= $isActivePath('/admin/mail/compose', false) ? 'active' : '' ?>">
                        <i class="fa fa-edit"></i>Compose Mail
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/contact-messages" class="<?= $isActivePath('/admin/contact-messages', false) ? 'active' : '' ?>">
                        <i class="fa fa-comments"></i>Contact Messages
                    </a>
                </li>
            </ul>
        </li>

        <?php
        $memberSectionOpen = $isActivePath('/admin/users', false)
            || $isActivePath('/admin/advanced-search', false)
            || $isActivePath('/admin/add-user', false)
            || $isActivePath('/admin/paid-to-spotlight', false)
            || $isActivePath('/admin/change-membership-plan', false)
            || $isActivePath('/admin/expired-members', false)
            || $isActivePath('/admin/member-followup-report', false);
        ?>
        <li class="has-submenu <?= $memberSectionOpen ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-user"></i>
                <span class="nav-item">Member</span>
                <i class="fa fa-chevron-right menu-caret"></i>
            </a>
            <ul class="submenu <?= $memberSectionOpen ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/users" class="<?= $isActivePath('/admin/users', false) ? 'active' : '' ?>">
                        <i class="fa fa-users"></i>All Members
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/advanced-search" class="<?= $isActivePath('/admin/advanced-search', false) ? 'active' : '' ?>">
                        <i class="fa fa-search"></i>Advanced Search
                    </a>
                </li>
                <!-- <li>
                    <a href="<?= BASE_URL ?>/admin/add-user" class="<?= $isActivePath('/admin/add-user', false) ? 'active' : '' ?>">Add New Members</a>
                </li> -->
                <li>
                    <a href="<?= BASE_URL ?>/admin/paid-to-spotlight" class="<?= $isActivePath('/admin/paid-to-spotlight', false) ? 'active' : '' ?>">
                        <i class="fa fa-star"></i>Paid to Spotlight
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/change-membership-plan" class="<?= $isActivePath('/admin/change-membership-plan', false) ? 'active' : '' ?>">
                        <i class="fa fa-exchange"></i>Change Membership Plan
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/expired-members" class="<?= $isActivePath('/admin/expired-members', false) ? 'active' : '' ?>">
                        <i class="fa fa-clock-o"></i>Expired Member
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/member-followup-report" class="<?= $isActivePath('/admin/member-followup-report', false) ? 'active' : '' ?>">
                        <i class="fa fa-list-alt"></i>Member Follow-up Report
                    </a>
                </li>
            </ul>
        </li>

        <?php
        $matchMakingOpen = $isActivePath('/admin/match-making', false)
            || $isActivePath('/admin/manual-profile-match-making', false)
            || $isActivePath('/admin/accepted-matches', false);
        ?>
        <li class="has-submenu <?= $matchMakingOpen ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-random"></i>
                <span class="nav-item">Match Making</span>
                <i class="fa fa-chevron-right menu-caret"></i>
            </a>
            <ul class="submenu <?= $matchMakingOpen ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/match-making" class="<?= $isActivePath('/admin/match-making', false) ? 'active' : '' ?>">
                        <i class="fa fa-random"></i>Match Making
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/manual-profile-match-making" class="<?= $isActivePath('/admin/manual-profile-match-making', false) ? 'active' : '' ?>">
                        <i class="fa fa-sliders"></i>Manual Profile Match Making
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/accepted-matches" class="<?= $isActivePath('/admin/accepted-matches', false) ? 'active' : '' ?>">
                        <i class="fa fa-check-circle"></i>Accepted Matches
                    </a>
                </li>
            </ul>
        </li>

        <?php
        $leadGenOpen = $isActivePath('/admin/lead-generation', false);
        $leadGenMainActive = strpos($requestPath, '/admin/lead-generation') === 0
            && $requestPath !== '/admin/lead-generation/report'
            && $requestPath !== '/admin/lead-generation/followup-report';
        ?>
        <li class="has-submenu <?= $leadGenOpen ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-bullhorn"></i>
                <span class="nav-item">Lead Generation</span>
                <i class="fa fa-chevron-right menu-caret"></i>
            </a>
            <ul class="submenu <?= $leadGenOpen ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/lead-generation" class="<?= $leadGenMainActive ? 'active' : '' ?>">
                        <i class="fa fa-bullhorn"></i>Lead Generation
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/lead-generation/report" class="<?= $isActivePath('/admin/lead-generation/report', false) ? 'active' : '' ?>">
                        <i class="fa fa-file-text-o"></i>Lead Generation Report
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/lead-generation/followup-report" class="<?= $isActivePath('/admin/lead-generation/followup-report', false) ? 'active' : '' ?>">
                        <i class="fa fa-phone"></i>Leads Followup Report
                    </a>
                </li>
            </ul>
        </li>

        <?php
        $reportsPath = '/admin/reports';
        $reportsMatchMakingPath = '/admin/reports/match-making';
        $reportsAutoEmailPath = '/admin/reports/match-making/auto-match-email';
        $cronHistoryPath = '/admin/reports/match-making/auto-match-email/cron-history';
        $autoGenPath = '/admin/reports/match-making/auto-match-email/auto-generated-match';
        $deferredMatchesPath = '/admin/reports/match-making/auto-match-email/deferred-matches';
        $reportsStaffMgmtPath = '/admin/reports/staff-management';
        $staffAllActivityPath = '/admin/reports/staff-management/staff-all-activity';
        $staffActivitySummaryPath = '/admin/reports/staff-management/staff-activity-summary';
        $reportsPaymentsPath = '/admin/reports/payments';
        $registrationFeePath = '/admin/reports/payments/registration-fee';
        $rishtaFeePath = '/admin/reports/payments/rishta-fee';
        $reportsMeetingsPath = '/admin/reports/meetings';
        $meetingSummaryPath = '/admin/reports/meetings/meeting-summary';
        $reportsMembersPath = '/admin/reports/members';
        $membersEmailVerificationPath = '/admin/reports/members/members-email-verification';
        $membersSummaryReportPath = '/admin/reports/members/members-summary';
        $unsubscribeMemberPath = '/admin/reports/members/unsubscribe-member';
        $membersAllActivityPath = '/admin/reports/members/members-all-activity';
        $reportsOpen = strpos($requestPath, $reportsPath) === 0;
        $reportsMmOpen = strpos($requestPath, $reportsMatchMakingPath) === 0;
        $reportsAutoEmailOpen = strpos($requestPath, $reportsAutoEmailPath) === 0;
        $reportsStaffMgmtOpen = strpos($requestPath, $reportsStaffMgmtPath) === 0;
        $reportsPaymentsOpen = strpos($requestPath, $reportsPaymentsPath) === 0
            || $requestPath === '/admin/accounts/income/registration-fee'
            || $requestPath === '/admin/accounts/income/rishta-fee';
        $reportsMeetingsOpen = strpos($requestPath, $reportsMeetingsPath) === 0;
        $reportsMembersOpen = strpos($requestPath, $reportsMembersPath) === 0;
        ?>
        <li class="has-submenu <?= $reportsOpen ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-file-text-o"></i>
                <span class="nav-item">Reports</span>
                <i class="fa fa-chevron-right menu-caret"></i>
            </a>
            <ul class="submenu <?= $reportsOpen ? 'open' : '' ?>">
                <li class="has-submenu <?= $reportsMmOpen ? 'open' : '' ?>">
                    <a href="#" data-menu-toggle>
                        <i class="fa fa-random"></i>
                        <span class="nav-item">Match Making</span>
                        <i class="fa fa-chevron-right menu-caret"></i>
                    </a>
                    <ul class="submenu <?= $reportsMmOpen ? 'open' : '' ?>">
                        <li class="has-submenu <?= $reportsAutoEmailOpen ? 'open' : '' ?>">
                            <a href="#" data-menu-toggle>
                                <i class="fa fa-envelope-o"></i>
                                <span class="nav-item">Auto Match Emails</span>
                                <i class="fa fa-chevron-right menu-caret"></i>
                            </a>
                            <ul class="submenu <?= $reportsAutoEmailOpen ? 'open' : '' ?>">
                                <li>
                                    <a href="<?= BASE_URL . $cronHistoryPath ?>"
                                       class="<?= $requestPath === $cronHistoryPath ? 'active' : '' ?>">
                                        <i class="fa fa-history"></i>Cron History
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL . $autoGenPath ?>"
                                       class="<?= $requestPath === $autoGenPath ? 'active' : '' ?>">
                                        <i class="fa fa-magic"></i>Auto Generated Match
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL . $deferredMatchesPath ?>"
                                       class="<?= $requestPath === $deferredMatchesPath ? 'active' : '' ?>">
                                        <i class="fa fa-hourglass-half"></i>Deferred Matches
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="has-submenu <?= $reportsStaffMgmtOpen ? 'open' : '' ?>">
                    <a href="#" data-menu-toggle>
                        <i class="fa fa-black-tie"></i>
                        <span class="nav-item">Staff Management</span>
                        <i class="fa fa-chevron-right menu-caret"></i>
                    </a>
                    <ul class="submenu <?= $reportsStaffMgmtOpen ? 'open' : '' ?>">
                        <li>
                            <a href="<?= BASE_URL . $staffAllActivityPath ?>"
                               class="<?= $requestPath === $staffAllActivityPath ? 'active' : '' ?>">
                                <i class="fa fa-list"></i>Staff All Activity
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL . $staffActivitySummaryPath ?>"
                               class="<?= $requestPath === $staffActivitySummaryPath ? 'active' : '' ?>">
                                <i class="fa fa-pie-chart"></i>Staff Activity Summary
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="has-submenu <?= $reportsPaymentsOpen ? 'open' : '' ?>">
                    <a href="#" data-menu-toggle>
                        <i class="fa fa-money"></i>
                        <span class="nav-item">Payments</span>
                        <i class="fa fa-chevron-right menu-caret"></i>
                    </a>
                    <ul class="submenu <?= $reportsPaymentsOpen ? 'open' : '' ?>">
                        <li>
                            <a href="<?= BASE_URL . $registrationFeePath ?>"
                               class="<?= ($requestPath === $registrationFeePath) ? 'active' : '' ?>">
                                <i class="fa fa-file-text-o"></i>Registration Fee
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL . $rishtaFeePath ?>"
                               class="<?= ($requestPath === $rishtaFeePath) ? 'active' : '' ?>">
                                <i class="fa fa-heart"></i>Rishta Fee
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="has-submenu <?= $reportsMeetingsOpen ? 'open' : '' ?>">
                    <a href="#" data-menu-toggle>
                        <i class="fa fa-calendar"></i>
                        <span class="nav-item">Meetings</span>
                        <i class="fa fa-chevron-right menu-caret"></i>
                    </a>
                    <ul class="submenu <?= $reportsMeetingsOpen ? 'open' : '' ?>">
                        <li>
                            <a href="<?= BASE_URL . $meetingSummaryPath ?>"
                               class="<?= $requestPath === $meetingSummaryPath ? 'active' : '' ?>">
                                <i class="fa fa-calendar-check-o"></i>Meetings Summary
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="has-submenu <?= $reportsMembersOpen ? 'open' : '' ?>">
                    <a href="#" data-menu-toggle>
                        <i class="fa fa-user"></i>
                        <span class="nav-item">Members</span>
                        <i class="fa fa-chevron-right menu-caret"></i>
                    </a>
                    <ul class="submenu <?= $reportsMembersOpen ? 'open' : '' ?>">
                        <li>
                            <a href="<?= BASE_URL . $membersEmailVerificationPath ?>"
                               class="<?= $requestPath === $membersEmailVerificationPath ? 'active' : '' ?>">
                                <i class="fa fa-envelope-o"></i>Members Email Verification
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL . $membersSummaryReportPath ?>"
                               class="<?= $requestPath === $membersSummaryReportPath ? 'active' : '' ?>">
                                <i class="fa fa-bar-chart"></i>Members Summary
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL . $unsubscribeMemberPath ?>"
                               class="<?= $requestPath === $unsubscribeMemberPath ? 'active' : '' ?>">
                                <i class="fa fa-ban"></i>Unsubscribe Member
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL . $membersAllActivityPath ?>"
                               class="<?= $requestPath === $membersAllActivityPath ? 'active' : '' ?>">
                                <i class="fa fa-list-ul"></i>Members All Activity
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>

        <li>
            <a href="<?= BASE_URL ?>/admin/system/database-migrations"
               class="<?= $isActivePath('/admin/system/database-migrations', true) ? 'active' : '' ?>">
                <i class="fa fa-database"></i>
                <span class="nav-item">DB Migrations</span>
            </a>
        </li>

        <li class="has-submenu <?= ($isActivePath('/admin/blogs', false) || $isActivePath('/admin/blog/create', false)) ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-pencil-square-o"></i>
                <span class="nav-item">Blogs</span>
                <i class="fa fa-chevron-right menu-caret"></i>
            </a>
            <ul class="submenu <?= ($isActivePath('/admin/blogs', false) || $isActivePath('/admin/blog/create', false)) ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/blogs" class="<?= $isActivePath('/admin/blogs', false) ? 'active' : '' ?>">
                        <i class="fa fa-book"></i>All Blogs
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/blog/create" class="<?= $isActivePath('/admin/blog/create', false) ? 'active' : '' ?>">
                        <i class="fa fa-plus-circle"></i>Create Blog
                    </a>
                </li>
            </ul>
        </li>

        <li class="mt-4">
            <a href="<?= BASE_URL ?>/admin/logout"
                onclick="return confirm('Logout?')"
                class="sidebar-logout">
                <i class="fa fa-sign-out"></i>
                <span class="nav-item">Logout</span>
            </a>
        </li>
    </ul>
</div>
<div id="sidebarOverlay" class="sidebar-overlay"></div>

<script>
(function () {
    function syncSidebarToggleAria() {
        var btn = document.getElementById('adminSidebarToggle');
        if (!btn) return;
        var collapsed = document.body.classList.contains('admin-sidebar-collapsed');
        btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        btn.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
    }

    function applySidebarCollapsedFromStorage() {
        if (window.innerWidth < 992) {
            document.body.classList.remove('admin-sidebar-collapsed');
        } else if (localStorage.getItem('adminSidebarCollapsed') === '1') {
            document.body.classList.add('admin-sidebar-collapsed');
        } else {
            document.body.classList.remove('admin-sidebar-collapsed');
        }
        syncSidebarToggleAria();
    }

    var toggleBtn = document.getElementById('adminSidebarToggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            if (window.innerWidth < 992) return;
            document.body.classList.toggle('admin-sidebar-collapsed');
            localStorage.setItem('adminSidebarCollapsed', document.body.classList.contains('admin-sidebar-collapsed') ? '1' : '0');
            syncSidebarToggleAria();
        });
    }

    window.addEventListener('resize', applySidebarCollapsedFromStorage);
    applySidebarCollapsedFromStorage();

    document.querySelectorAll('[data-menu-toggle]').forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            if (document.body.classList.contains('admin-sidebar-collapsed') && window.innerWidth >= 992) {
                e.preventDefault();
                document.body.classList.remove('admin-sidebar-collapsed');
                localStorage.setItem('adminSidebarCollapsed', '0');
                syncSidebarToggleAria();
                return;
            }
            e.preventDefault();
            var parent = this.closest('.has-submenu');
            if (!parent) return;
            parent.classList.toggle('open');
            var submenu = null;
            for (var i = 0; i < parent.children.length; i++) {
                var ch = parent.children[i];
                if (ch.tagName === 'UL' && ch.classList.contains('submenu')) {
                    submenu = ch;
                    break;
                }
            }
            if (submenu) submenu.classList.toggle('open');
        });
    });
})();
</script>