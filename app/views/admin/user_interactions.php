<?php
$title = ($reportTitle ?? 'Interactions') . ' — Match activity';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';

$m = $interaction_member ?? [];
$uid = (int)($interaction_user_id ?? 0);
$current = strtolower((string)($interaction_action ?? 'opened'));
$memberName = trim((string)(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? '')));

$tabs = [
    ['key' => 'opened', 'label' => 'Opened', 'count' => (int)($m['opened_count'] ?? 0), 'variant' => 'opened'],
    ['key' => 'deferred', 'label' => 'Deferred', 'count' => (int)($m['deferred_count'] ?? 0), 'variant' => 'deferred'],
    ['key' => 'declined', 'label' => 'Declined', 'count' => (int)($m['declined_count'] ?? 0), 'variant' => 'declined'],
    ['key' => 'meeting', 'label' => 'Meeting', 'count' => (int)($m['meeting_count'] ?? 0), 'variant' => 'meeting'],
    ['key' => 'accepted', 'label' => 'Accepted', 'count' => (int)($m['accepted_count'] ?? 0), 'variant' => 'accepted'],
];
$rowCount = is_array($rows ?? null) ? count($rows) : 0;
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-manage-members.css">
<style>
    .user-interactions-page .ui-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .user-interactions-page .ui-member-meta {
        font-size: 14px;
        color: #555;
    }
    .user-interactions-page .ui-member-meta strong {
        color: #222;
    }
    .user-interactions-page .interaction-status-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 14px;
    }
    .user-interactions-page .ist-tab {
        flex: 1;
        min-width: 110px;
        text-align: center;
        padding: 12px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        border: 2px solid transparent;
        transition: transform 0.12s ease, box-shadow 0.12s ease, opacity 0.12s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }
    .user-interactions-page .ist-tab:hover {
        transform: translateY(-1px);
        opacity: 0.95;
        text-decoration: none;
    }
    .user-interactions-page .ist-tab--opened {
        background: #fff;
        color: #1a1a1a;
        border-color: #cfd4db;
    }
    .user-interactions-page .ist-tab--opened.ist-tab--active {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.25);
        color: #1a1a1a;
    }
    .user-interactions-page .ist-tab--deferred {
        background: #e67e22;
        color: #fff;
    }
    .user-interactions-page .ist-tab--deferred.ist-tab--active {
        box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.45);
    }
    .user-interactions-page .ist-tab--declined {
        background: #e74c3c;
        color: #fff;
    }
    .user-interactions-page .ist-tab--declined.ist-tab--active {
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.45);
    }
    .user-interactions-page .ist-tab--meeting {
        background: #1abc9c;
        color: #fff;
    }
    .user-interactions-page .ist-tab--meeting.ist-tab--active {
        box-shadow: 0 0 0 3px rgba(26, 188, 156, 0.45);
    }
    .user-interactions-page .ist-tab--accepted {
        background: #27ae60;
        color: #fff;
    }
    .user-interactions-page .ist-tab--accepted.ist-tab--active {
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.45);
    }
    .user-interactions-page .ui-subtabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e5e5e5;
    }
    .user-interactions-page .ui-subtab {
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        color: #555;
        background: #f0f0f0;
        border: 1px solid #ddd;
        text-decoration: none;
    }
    .user-interactions-page .ui-subtab:hover {
        background: #e8e8e8;
        text-decoration: none;
        color: #333;
    }
    .user-interactions-page .ui-subtab--active {
        background: #e8e8e8;
        color: #222;
        border-color: #ccc;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
    }
    .user-interactions-page .ui-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }
    .user-interactions-page .ui-toolbar label {
        font-size: 13px;
        color: #555;
        margin: 0;
    }
    .user-interactions-page .ui-empty {
        background: #fdecea;
        color: #c0392b;
        border: 1px solid #f5c6cb;
        border-radius: 6px;
        padding: 16px 20px;
        font-weight: 600;
        text-align: center;
    }
    .user-interactions-page .table-responsive {
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #e5e5e5;
    }
    .user-interactions-page table.table {
        margin-bottom: 0;
        font-size: 13px;
    }
    .user-interactions-page table.table thead th {
        background: #f7f9fc;
        font-weight: 600;
        color: #444;
        border-bottom: 2px solid #e0e0e0;
    }
</style>

<div class="admin-main user-interactions-page">
    <div class="admin-topbar">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu">
            <i class="fa fa-bars"></i>
        </button>
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
        <div class="admin-content">
            <div class="container-fluid">
                <div class="ui-head">
                    <div>
                        <div class="page-head mb-1" style="font-size: 15px;">Match activity</div>
                        <div class="ui-member-meta">
                            <strong><?= htmlspecialchars($memberName !== '' ? $memberName : 'Member', ENT_QUOTES, 'UTF-8') ?></strong>
                            &nbsp;·&nbsp; ID <?= $uid ?>
                            <?php if (!empty($m['email'])): ?>
                                &nbsp;·&nbsp; <?= htmlspecialchars((string)$m['email'], ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/admin/users" class="btn btn-sm btn-primary">← Back to members</a>
                </div>

                <nav class="interaction-status-tabs" aria-label="Interaction type">
                    <?php foreach ($tabs as $t):
                        $isActive = $current === $t['key'];
                        $cls = 'ist-tab ist-tab--' . $t['variant'] . ($isActive ? ' ist-tab--active' : '');
                        $href = BASE_URL . '/admin/users/interactions?id=' . $uid . '&action=' . rawurlencode($t['key']);
                        ?>
                        <a class="<?= htmlspecialchars($cls, ENT_QUOTES, 'UTF-8') ?>" href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($t['label'], ENT_QUOTES, 'UTF-8') ?> ( <?= (int)$t['count'] ?> )
                        </a>
                    <?php endforeach; ?>
                </nav>

                <div class="ui-subtabs">
                    <span class="ui-subtab ui-subtab--active">All (<?= (int)$rowCount ?>)</span>
                    <span class="ui-subtab" title="Filtered by status above">Filtered: <?= htmlspecialchars($reportTitle ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <div class="ui-toolbar">
                    <div>
                        <label>Show <?= (int)$rowCount ?> <?= $rowCount === 1 ? 'entry' : 'entries' ?></label>
                    </div>
                    <div>
                        <label class="mb-0">Sort: <strong>Latest descending</strong></label>
                    </div>
                </div>

                <?php if (empty($rows)): ?>
                    <div class="ui-empty mb-0" role="status">No Record found</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Count</th>
                                    <th>Last activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= (int)($r['user_id'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars(trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($r['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= (int)($r['action_count'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars($r['last_action_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
