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
        width: 240px;
        min-height: 100vh;
        background: #4b5873;
        padding: 14px 12px;
        position: fixed !important;
        left: 0;
        top: 0;
        z-index: 1040;
        overflow-y: auto;
        transition: transform .25s ease;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin-top: 14px;
    }

    .sidebar-menu li { margin-bottom: 2px; }

    .sidebar-menu > li > a {
        display: flex;
        align-items: center;
        padding: 9px 11px;
        color: #fff;
        font-size: 13px;
        text-decoration: none;
        border-radius: 2px;
        opacity: .95;
        transition: background .2s ease;
    }

    .sidebar-menu li a i:first-child {
        width: 18px;
        text-align: center;
        margin-right: 9px;
    }

    .sidebar-menu > li > a:hover { background: rgba(255, 255, 255, .1); }
    .sidebar-menu > li > a.active { background: #3f485a; }

    .submenu {
        list-style: none;
        margin: 2px 0 6px;
        padding: 0 0 0 26px;
        display: none;
    }

    .submenu.open { display: block; }

    .submenu li a {
        display: block;
        color: #e6e9f0;
        text-decoration: none;
        font-size: 12px;
        padding: 7px 0;
        opacity: .92;
    }

    .submenu li a:hover { opacity: 1; }
    .submenu li a.active {
        color: #ffffff;
        font-weight: 700;
        opacity: 1;
        background: #3f485a;
        border-radius: 2px;
        padding: 7px 10px;
        margin: 2px 0 2px -10px;
    }

    .menu-caret {
        margin-left: auto;
        font-size: 11px;
        transition: transform .2s ease;
    }

    .has-submenu.open > a .menu-caret { transform: rotate(180deg); }

    .submenu .has-submenu > a {
        display: flex;
        align-items: center;
        padding: 7px 0;
        font-size: 11px;
        color: #dce1ea;
        text-decoration: none;
        border-radius: 2px;
    }
    .submenu .has-submenu > a:hover { opacity: 1; color: #fff; }
    .submenu .submenu {
        padding-left: 10px;
        margin: 2px 0 4px;
        border-left: 1px solid rgba(255, 255, 255, .18);
    }
    .submenu .submenu li a {
        font-size: 11px;
        padding: 5px 0;
    }
    .submenu .submenu li a.active {
        padding: 5px 10px;
        margin-top: 2px;
        margin-bottom: 2px;
    }

    .sidebar-logo {
        text-align: center;
        padding: 4px 0 10px;
    }

    .sidebar-logo img {
        width: 75px;
        /* filter: brightness(0) invert(1); */
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .35);
        z-index: 1035;
    }

    @media (max-width: 768px) {
        .dashboard-sidebar { transform: translateX(-100%); }
        .dashboard-sidebar.active { transform: translateX(0); }
        .sidebar-overlay.show { display: block; }
    }
</style>


<!-- SIDEBAR -->
<div id="sidebar" class="dashboard-sidebar text-white">

    <div class="sidebar-logo">
        <a href="<?= BASE_URL ?>/">
            <img src="<?= BASE_URL ?>/assets/images/admin_logo.png" alt="Logo">
        </a>
    </div>

    <ul class="nav flex-column sidebar-menu">
        <li>
            <a href="<?= BASE_URL ?>/admin/dashboard" class="<?= $isActivePath('/admin/dashboard', true) ? 'active' : '' ?>">
                <i class="fa fa-dashboard"></i>
                <span class="nav-item">Dashboard</span>
            </a>
        </li>

        <li class="has-submenu <?= ($isActivePath('/admin/sales-report', true)) ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-calculator"></i>
                <span class="nav-item">Accounts</span>
                <i class="fa fa-chevron-down menu-caret"></i>
            </a>
            <ul class="submenu <?= ($isActivePath('/admin/sales-report', true)) ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/dashboard" class="<?= $isActivePath('/admin/dashboard', true) ? 'active' : '' ?>">Income</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/sales-report" class="<?= $isActivePath('/admin/sales-report', true) ? 'active' : '' ?>">Members Sales Report</a>
                </li>
            </ul>
        </li>

        <li class="has-submenu <?= $isActivePath('/admin/team-management', false) ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-sitemap"></i>
                <span class="nav-item">Team Management</span>
                <i class="fa fa-chevron-down menu-caret"></i>
            </a>
            <ul class="submenu <?= $isActivePath('/admin/team-management', false) ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/team-management" class="<?= $isActivePath('/admin/team-management', false) ? 'active' : '' ?>">Teams</a>
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
                <i class="fa fa-chevron-down menu-caret"></i>
            </a>
            <ul class="submenu <?= $commOpen ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/mail/inbox" class="<?= $isActivePath('/admin/mail/inbox', false) ? 'active' : '' ?>">Unified Inbox</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/mail/compose" class="<?= $isActivePath('/admin/mail/compose', false) ? 'active' : '' ?>">Compose Mail</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/contact-messages" class="<?= $isActivePath('/admin/contact-messages', false) ? 'active' : '' ?>">Contact Messages</a>
                </li>
            </ul>
        </li>

        <li class="has-submenu <?= ($isActivePath('/admin/users', false) || $isActivePath('/admin/advanced-search', false) || $isActivePath('/admin/add-user', false) || $isActivePath('/admin/paid-to-spotlight', false) || $isActivePath('/admin/change-membership-plan', false) || $isActivePath('/admin/expired-members', false) || $isActivePath('/admin/member-followup-report', false)) ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-user"></i>
                <span class="nav-item">Members</span>
                <i class="fa fa-chevron-down menu-caret"></i>
            </a>
            <ul class="submenu <?= ($isActivePath('/admin/users', false) || $isActivePath('/admin/advanced-search', false) || $isActivePath('/admin/add-user', false) || $isActivePath('/admin/paid-to-spotlight', false) || $isActivePath('/admin/change-membership-plan', false) || $isActivePath('/admin/expired-members', false) || $isActivePath('/admin/member-followup-report', false)) ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/users" class="<?= $isActivePath('/admin/users', false) ? 'active' : '' ?>">All Members</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/advanced-search" class="<?= $isActivePath('/admin/advanced-search', false) ? 'active' : '' ?>">Advanced Search</a>
                </li>
                <!-- <li>
                    <a href="<?= BASE_URL ?>/admin/add-user" class="<?= $isActivePath('/admin/add-user', false) ? 'active' : '' ?>">Add New Members</a>
                </li> -->
                <li>
                    <a href="<?= BASE_URL ?>/admin/paid-to-spotlight" class="<?= $isActivePath('/admin/paid-to-spotlight', false) ? 'active' : '' ?>">Paid to Spotlight</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/change-membership-plan" class="<?= $isActivePath('/admin/change-membership-plan', false) ? 'active' : '' ?>">Change Membership Plan</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/expired-members" class="<?= $isActivePath('/admin/expired-members', false) ? 'active' : '' ?>">Expired Member</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/member-followup-report" class="<?= $isActivePath('/admin/member-followup-report', false) ? 'active' : '' ?>">Member Follow-up Report</a>
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
                <i class="fa fa-chevron-down menu-caret"></i>
            </a>
            <ul class="submenu <?= $matchMakingOpen ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/match-making" class="<?= $isActivePath('/admin/match-making', false) ? 'active' : '' ?>">Match Making</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/match-making" class="<?= $isActivePath('/admin/manual-profile-match-making', false) ? 'active' : '' ?>">Manual Profile Match Making</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/accepted-matches" class="<?= $isActivePath('/admin/accepted-matches', false) ? 'active' : '' ?>">Accepted Matches</a>
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
                <i class="fa fa-chevron-down menu-caret"></i>
            </a>
            <ul class="submenu <?= $leadGenOpen ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/lead-generation" class="<?= $leadGenMainActive ? 'active' : '' ?>">Lead Generation</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/lead-generation/report" class="<?= $isActivePath('/admin/lead-generation/report', false) ? 'active' : '' ?>">Lead Generation Report</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/lead-generation/followup-report" class="<?= $isActivePath('/admin/lead-generation/followup-report', false) ? 'active' : '' ?>">Leads Followup Report</a>
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
        $reportsPaymentsOpen = strpos($requestPath, $reportsPaymentsPath) === 0;
        $reportsMeetingsOpen = strpos($requestPath, $reportsMeetingsPath) === 0;
        $reportsMembersOpen = strpos($requestPath, $reportsMembersPath) === 0;
        ?>
        <li class="has-submenu <?= $reportsOpen ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-file-text-o"></i>
                <span class="nav-item">Reports</span>
                <i class="fa fa-chevron-down menu-caret"></i>
            </a>
            <ul class="submenu <?= $reportsOpen ? 'open' : '' ?>">
                <li class="has-submenu <?= $reportsMmOpen ? 'open' : '' ?>">
                    <a href="#" data-menu-toggle>
                        <span class="nav-item">Match Making</span>
                        <i class="fa fa-chevron-down menu-caret"></i>
                    </a>
                    <ul class="submenu <?= $reportsMmOpen ? 'open' : '' ?>">
                        <li class="has-submenu <?= $reportsAutoEmailOpen ? 'open' : '' ?>">
                            <a href="#" data-menu-toggle>
                                <span class="nav-item">Auto Match Emails</span>
                                <i class="fa fa-chevron-down menu-caret"></i>
                            </a>
                            <ul class="submenu <?= $reportsAutoEmailOpen ? 'open' : '' ?>">
                                <li>
                                    <a href="<?= BASE_URL . $cronHistoryPath ?>"
                                       class="<?= $requestPath === $cronHistoryPath ? 'active' : '' ?>">Cron History</a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL . $autoGenPath ?>"
                                       class="<?= $requestPath === $autoGenPath ? 'active' : '' ?>">Auto Generated Match</a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL . $deferredMatchesPath ?>"
                                       class="<?= $requestPath === $deferredMatchesPath ? 'active' : '' ?>">Deferred Matches</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="has-submenu <?= $reportsStaffMgmtOpen ? 'open' : '' ?>">
                    <a href="#" data-menu-toggle>
                        <span class="nav-item">Staff Management</span>
                        <i class="fa fa-chevron-down menu-caret"></i>
                    </a>
                    <ul class="submenu <?= $reportsStaffMgmtOpen ? 'open' : '' ?>">
                        <li>
                            <a href="<?= BASE_URL . $staffAllActivityPath ?>"
                               class="<?= $requestPath === $staffAllActivityPath ? 'active' : '' ?>">Staff All Activity</a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL . $staffActivitySummaryPath ?>"
                               class="<?= $requestPath === $staffActivitySummaryPath ? 'active' : '' ?>">Staff Activity Summary</a>
                        </li>
                    </ul>
                </li>
                <li class="has-submenu <?= $reportsPaymentsOpen ? 'open' : '' ?>">
                    <a href="#" data-menu-toggle>
                        <span class="nav-item">Payments</span>
                        <i class="fa fa-chevron-down menu-caret"></i>
                    </a>
                    <ul class="submenu <?= $reportsPaymentsOpen ? 'open' : '' ?>">
                        <li>
                            <a href="<?= BASE_URL . $registrationFeePath ?>"
                               class="<?= $requestPath === $registrationFeePath ? 'active' : '' ?>">Registration Fee</a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL . $rishtaFeePath ?>"
                               class="<?= $requestPath === $rishtaFeePath ? 'active' : '' ?>">Rishta Fee</a>
                        </li>
                    </ul>
                </li>
                <li class="has-submenu <?= $reportsMeetingsOpen ? 'open' : '' ?>">
                    <a href="#" data-menu-toggle>
                        <span class="nav-item">Meetings</span>
                        <i class="fa fa-chevron-down menu-caret"></i>
                    </a>
                    <ul class="submenu <?= $reportsMeetingsOpen ? 'open' : '' ?>">
                        <li>
                            <a href="<?= BASE_URL . $meetingSummaryPath ?>"
                               class="<?= $requestPath === $meetingSummaryPath ? 'active' : '' ?>">Meetings Summary</a>
                        </li>
                    </ul>
                </li>
                <li class="has-submenu <?= $reportsMembersOpen ? 'open' : '' ?>">
                    <a href="#" data-menu-toggle>
                        <span class="nav-item">Members</span>
                        <i class="fa fa-chevron-down menu-caret"></i>
                    </a>
                    <ul class="submenu <?= $reportsMembersOpen ? 'open' : '' ?>">
                        <li>
                            <a href="<?= BASE_URL . $membersEmailVerificationPath ?>"
                               class="<?= $requestPath === $membersEmailVerificationPath ? 'active' : '' ?>">Members Email Verification</a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL . $membersSummaryReportPath ?>"
                               class="<?= $requestPath === $membersSummaryReportPath ? 'active' : '' ?>">Members Summary</a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL . $unsubscribeMemberPath ?>"
                               class="<?= $requestPath === $unsubscribeMemberPath ? 'active' : '' ?>">Unsubscribe Member</a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL . $membersAllActivityPath ?>"
                               class="<?= $requestPath === $membersAllActivityPath ? 'active' : '' ?>">Members All Activity</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>

        <li class="has-submenu <?= ($isActivePath('/admin/blogs', false) || $isActivePath('/admin/blog/create', false)) ? 'open' : '' ?>">
            <a href="#" data-menu-toggle>
                <i class="fa fa-pencil-square-o"></i>
                <span class="nav-item">Blogs</span>
                <i class="fa fa-chevron-down menu-caret"></i>
            </a>
            <ul class="submenu <?= ($isActivePath('/admin/blogs', false) || $isActivePath('/admin/blog/create', false)) ? 'open' : '' ?>">
                <li>
                    <a href="<?= BASE_URL ?>/admin/blogs" class="<?= $isActivePath('/admin/blogs', false) ? 'active' : '' ?>">All Blogs</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/blog/create" class="<?= $isActivePath('/admin/blog/create', false) ? 'active' : '' ?>">Create Blog</a>
                </li>
            </ul>
        </li>

        <li class="mt-4">
            <a href="<?= BASE_URL ?>/admin/logout"
                onclick="return confirm('Logout?')"
                class="text-danger">
                <i class="fa fa-sign-out"></i>
                <span class="nav-item">Logout</span>
            </a>
        </li>
    </ul>
</div>
<div id="sidebarOverlay" class="sidebar-overlay"></div>

<script>
    document.querySelectorAll('[data-menu-toggle]').forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
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
</script>