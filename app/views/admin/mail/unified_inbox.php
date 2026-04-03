<?php
$title = 'Unified Inbox';

$accountEmail = 'info@nikahglobal.pk';

$folders = [
    ['slug' => 'inbox', 'label' => 'Inbox', 'icon' => 'inbox', 'badge' => 54, 'active' => true],
    ['slug' => 'drafts', 'label' => 'Drafts', 'icon' => 'file-o', 'badge' => null],
    ['slug' => 'sent', 'label' => 'Sent', 'icon' => 'paper-plane-o', 'badge' => null],
    ['slug' => 'junk', 'label' => 'Junk', 'icon' => 'exclamation-circle', 'badge' => 1],
    ['slug' => 'trash', 'label' => 'Trash', 'icon' => 'trash-o', 'badge' => null],
    ['slug' => 'accounts', 'label' => 'Accounts Inbox', 'icon' => 'folder-o', 'badge' => null],
    ['slug' => 'fail', 'label' => 'Mail Delivery Fail', 'icon' => 'warning', 'badge' => null],
    ['slug' => 'marketing', 'label' => 'Marketing Inbox', 'icon' => 'bullhorn', 'badge' => null],
    ['slug' => 'shared', 'label' => 'Shared Inbox', 'icon' => 'users', 'badge' => null],
];

$messages = [
    [
        'id' => 1,
        'from' => 'Mail Delivery Subsystem',
        'from_email' => 'mailer-daemon@googlemail.com',
        'initials' => 'MD',
        'subject' => 'Undelivered Mail Returned to Sender',
        'preview' => 'Your message could not be delivered to one or more of its recipients. Reason: The email account that you tried to reach does not exist.',
        'time' => 'Tue 22:02',
        'time_rel' => '2 hours ago',
        'to' => $accountEmail,
        'date_full' => 'Tue, 30 Mar 2026 20:15:00 +0000',
        'body' => "Your message could not be delivered to one or more of its recipients.\n\nReason: The email account that you tried to reach does not exist.\n\nPlease check the recipient's email address and try again.\n\n-- This is an automatically generated message.",
        'row_style' => 'gray',
    ],
    [
        'id' => 2,
        'from' => 'Resham Yousaf',
        'from_email' => 'resham.y@gmail.com',
        'initials' => 'RY',
        'subject' => 'Profile update completed',
        'preview' => 'Hi team, I have updated my profile photos as requested. Please review when you have a moment.',
        'time' => 'Mon 14:30',
        'time_rel' => 'Yesterday',
        'to' => $accountEmail,
        'date_full' => 'Mon, 29 Mar 2026 14:30:22 +0000',
        'body' => "Hi team,\n\nI have updated my profile photos as requested. Please review when you have a moment.\n\nThanks,\nResham",
        'row_style' => 'blue',
    ],
    [
        'id' => 3,
        'from' => 'Support Nikah Global',
        'from_email' => 'support@nikahglobal.pk',
        'initials' => 'SN',
        'subject' => 'Weekly digest',
        'preview' => 'Summary of new registrations and leads for the past 7 days.',
        'time' => 'Sun 09:00',
        'time_rel' => '3 days ago',
        'to' => $accountEmail,
        'date_full' => 'Sun, 28 Mar 2026 09:00:00 +0000',
        'body' => "Here is your weekly digest of new registrations and leads.\n\nTotal new members: 12\nLeads follow-up pending: 5\n\nRegards,\nSupport",
        'row_style' => '',
    ],
];

$defaultMsg = $messages[0];

require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/sidebar.php';
?>

<div class="admin-main ui-page">
<div class="admin-topbar ui-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="ui-topbar-title">Unified Inbox</div>
    </div>
    <div class="admin-profile" id="adminProfileTrigger">
        <div class="admin-profile-box"><span><?= htmlspecialchars($this->displayadminname(), ENT_QUOTES, 'UTF-8') ?></span><i class="fa fa-user"></i></div>
        <div class="admin-dropdown" id="adminDropdown">
            <a href="<?= BASE_URL ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
            <a href="<?= BASE_URL ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
        </div>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
<div class="ui-flash ui-flash-success alert alert-success py-2 px-3 mb-0 rounded-0 border-0"><?= htmlspecialchars((string) $_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
<div class="ui-flash ui-flash-danger alert alert-danger py-2 px-3 mb-0 rounded-0 border-0"><?= htmlspecialchars((string) $_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>

<main class="admin-page ui-inbox-main">
<div class="ui-shell">
    <aside class="ui-col ui-folder-col">
        <div class="ui-account-head">
            <button type="button" class="ui-icon-btn" aria-label="Menu"><i class="fa fa-bars"></i></button>
            <span class="ui-account-email"><?= htmlspecialchars($accountEmail) ?></span>
        </div>
        <a href="<?= BASE_URL ?>/admin/mail/compose" class="ui-compose-btn"><i class="fa fa-plus"></i> Compose</a>
        <nav class="ui-folder-list">
            <?php foreach ($folders as $f): ?>
                <button type="button" class="ui-folder-item <?= !empty($f['active']) ? 'active' : '' ?>" data-folder="<?= htmlspecialchars($f['slug']) ?>">
                    <i class="fa fa-<?= htmlspecialchars($f['icon']) ?>"></i>
                    <span class="ui-folder-label"><?= htmlspecialchars($f['label']) ?></span>
                    <?php if ($f['badge'] !== null && $f['badge'] !== ''): ?>
                        <span class="ui-folder-badge"><?= (int) $f['badge'] ?></span>
                    <?php endif; ?>
                </button>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="ui-col ui-list-col">
        <div class="ui-list-toolbar">
            <label class="ui-chk"><input type="checkbox" id="uiSelectAll" aria-label="Select all"></label>
            <button type="button" class="ui-tool-btn" aria-label="Chat"><i class="fa fa-comment-o"></i></button>
            <button type="button" class="ui-tool-btn" aria-label="Settings"><i class="fa fa-cog"></i></button>
            <button type="button" class="ui-tool-btn" aria-label="Refresh"><i class="fa fa-refresh"></i></button>
        </div>
        <div class="ui-search-wrap">
            <i class="fa fa-search ui-search-icon"></i>
            <input type="search" class="ui-search-input" placeholder="Search..." id="uiSearchList" aria-label="Search messages">
        </div>
        <div class="ui-msg-list" id="uiMsgList">
            <?php foreach ($messages as $m): ?>
            <button type="button"
                class="ui-msg-card<?= $m['row_style'] === 'gray' ? ' is-sel-gray' : ($m['row_style'] === 'blue' ? ' is-sel-blue' : '') ?>"
                data-id="<?= (int) $m['id'] ?>"
                data-from="<?= htmlspecialchars($m['from'], ENT_QUOTES, 'UTF-8') ?>"
                data-from-email="<?= htmlspecialchars($m['from_email'], ENT_QUOTES, 'UTF-8') ?>"
                data-subject="<?= htmlspecialchars($m['subject'], ENT_QUOTES, 'UTF-8') ?>"
                data-to="<?= htmlspecialchars($m['to'], ENT_QUOTES, 'UTF-8') ?>"
                data-date-full="<?= htmlspecialchars($m['date_full'], ENT_QUOTES, 'UTF-8') ?>"
                data-time-rel="<?= htmlspecialchars($m['time_rel'], ENT_QUOTES, 'UTF-8') ?>"
                data-initials="<?= htmlspecialchars($m['initials'], ENT_QUOTES, 'UTF-8') ?>"
                data-body="<?= htmlspecialchars($m['body'], ENT_QUOTES, 'UTF-8') ?>"
                data-preview="<?= htmlspecialchars($m['preview'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="ui-msg-avatar"><?= htmlspecialchars($m['initials']) ?></div>
                <div class="ui-msg-body">
                    <div class="ui-msg-top">
                        <span class="ui-msg-from"><?= htmlspecialchars($m['from']) ?></span>
                        <span class="ui-msg-time"><?= htmlspecialchars($m['time']) ?></span>
                    </div>
                    <div class="ui-msg-subj"><?= htmlspecialchars($m['subject']) ?></div>
                    <div class="ui-msg-preview"><?= htmlspecialchars($m['preview']) ?></div>
                </div>
            </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="ui-col ui-detail-col" id="uiDetailCol">
        <div class="ui-detail-toolbar">
            <button type="button" class="ui-dtool" title="Reply"><i class="fa fa-reply"></i></button>
            <button type="button" class="ui-dtool" title="Reply all"><i class="fa fa-reply-all"></i></button>
            <button type="button" class="ui-dtool" title="Forward"><i class="fa fa-share"></i></button>
            <button type="button" class="ui-dtool" title="Delete"><i class="fa fa-trash-o"></i></button>
            <button type="button" class="ui-dtool" title="Block"><i class="fa fa-ban"></i></button>
            <button type="button" class="ui-dtool" title="Move"><i class="fa fa-folder-o"></i></button>
            <button type="button" class="ui-dtool" title="Flag"><i class="fa fa-flag-o"></i></button>
            <button type="button" class="ui-dtool" title="Print"><i class="fa fa-print"></i></button>
            <button type="button" class="ui-dtool" title="Archive"><i class="fa fa-archive"></i></button>
            <span class="ui-detail-toolbar-spacer"></span>
            <button type="button" class="ui-assign-btn">Assign</button>
            <button type="button" class="ui-dtool" title="More"><i class="fa fa-ellipsis-v"></i></button>
        </div>
        <div class="ui-detail-inner">
            <div class="ui-detail-head">
                <h1 class="ui-detail-subject" id="uiDetailSubject"><?= htmlspecialchars($defaultMsg['subject']) ?></h1>
                <span class="ui-detail-pill" id="uiDetailRel"><?= htmlspecialchars($defaultMsg['time_rel']) ?></span>
            </div>
            <div class="ui-detail-meta">
                <div class="ui-detail-avatar" id="uiDetailAvatar"><?= htmlspecialchars($defaultMsg['initials']) ?></div>
                <div class="ui-detail-addresses">
                    <div><strong>From:</strong> <span id="uiDetailFrom"><?= htmlspecialchars($defaultMsg['from']) ?></span> <span class="text-muted" id="uiDetailFromEmail">&lt;<?= htmlspecialchars($defaultMsg['from_email']) ?>&gt;</span></div>
                    <div><strong>To:</strong> <span id="uiDetailTo"><?= htmlspecialchars($defaultMsg['to']) ?></span></div>
                    <div><strong>Date:</strong> <span id="uiDetailDate"><?= htmlspecialchars($defaultMsg['date_full']) ?></span></div>
                </div>
            </div>
            <div class="ui-detail-body" id="uiDetailBody"><?= nl2br(htmlspecialchars($defaultMsg['body'])) ?></div>
        </div>
    </div>
</div>
</main>
</div>

<style>
    .ui-inbox-main { padding: 0 !important; }
    .ui-page .ui-topbar {
        justify-content: space-between;
        padding-left: 12px;
        padding-right: 16px;
    }
    .ui-topbar-title {
        font-size: 15px;
        font-weight: 600;
        color: #111;
    }
    .ui-shell {
        display: flex;
        height: calc(100vh - 56px);
        background: #f4f6f9;
        overflow: hidden;
    }
    .ui-flash { border-bottom: 1px solid rgba(0,0,0,.06) !important; }
    .ui-col { min-width: 0; display: flex; flex-direction: column; background: #fff; border-right: 1px solid #e4e7ec; }
    .ui-folder-col { width: 260px; flex-shrink: 0; background: #fafbfc; }
    .ui-list-col { width: 360px; flex-shrink: 0; }
    .ui-detail-col { flex: 1; background: #fff; border-right: 0; }
    .ui-account-head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 14px 10px;
        border-bottom: 1px solid #e8ebf0;
    }
    .ui-icon-btn {
        border: 0;
        background: transparent;
        padding: 4px;
        color: #333;
        cursor: pointer;
        font-size: 16px;
    }
    .ui-account-email { font-size: 12px; font-weight: 600; color: #222; word-break: break-all; }
    .ui-compose-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin: 12px 14px;
        padding: 11px 16px;
        background: #2196F3;
        color: #fff !important;
        font-weight: 600;
        font-size: 14px;
        border-radius: 6px;
        text-decoration: none;
        transition: filter .15s;
    }
    .ui-compose-btn:hover { filter: brightness(0.95); color: #fff !important; }
    .ui-folder-list { padding: 4px 8px 16px; overflow-y: auto; flex: 1; }
    .ui-folder-item {
        display: flex;
        align-items: center;
        width: 100%;
        border: 0;
        background: transparent;
        padding: 9px 10px;
        border-radius: 6px;
        cursor: pointer;
        text-align: left;
        font-size: 13px;
        color: #222;
        gap: 10px;
    }
    .ui-folder-item i { width: 18px; text-align: center; color: #444; }
    .ui-folder-item:hover { background: rgba(33,150,243,.08); }
    .ui-folder-item.active {
        background: #2196F3;
        color: #fff;
    }
    .ui-folder-item.active i { color: #fff; }
    .ui-folder-label { flex: 1; }
    .ui-folder-badge {
        min-width: 22px;
        height: 22px;
        padding: 0 6px;
        border-radius: 11px;
        background: rgba(0,0,0,.08);
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .ui-folder-item.active .ui-folder-badge { background: rgba(255,255,255,.25); color: #fff; }
    .ui-list-toolbar {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 10px 12px;
        border-bottom: 1px solid #e8ebf0;
    }
    .ui-chk { margin: 0 4px 0 0; cursor: pointer; }
    .ui-tool-btn {
        border: 0;
        background: transparent;
        width: 34px;
        height: 34px;
        border-radius: 6px;
        color: #555;
        cursor: pointer;
    }
    .ui-tool-btn:hover { background: #f0f2f5; color: #222; }
    .ui-search-wrap {
        position: relative;
        padding: 8px 12px;
        border-bottom: 1px solid #e8ebf0;
    }
    .ui-search-icon {
        position: absolute;
        left: 22px;
        top: 50%;
        transform: translateY(-50%);
        color: #9aa0a6;
        font-size: 14px;
        pointer-events: none;
    }
    .ui-search-input {
        width: 100%;
        height: 38px;
        padding: 0 12px 0 36px;
        border: 1px solid #e0e3e8;
        border-radius: 8px;
        font-size: 13px;
        background: #f8f9fb;
    }
    .ui-search-input:focus {
        outline: none;
        border-color: #2196F3;
        background: #fff;
    }
    .ui-msg-list { overflow-y: auto; flex: 1; padding: 10px; gap: 8px; display: flex; flex-direction: column; }
    .ui-msg-card {
        display: flex;
        gap: 12px;
        text-align: left;
        border: 1px solid #e8ebf0;
        border-radius: 10px;
        padding: 12px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        cursor: pointer;
        transition: box-shadow .15s, border-color .15s, background .15s;
    }
    .ui-msg-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    .ui-msg-card.is-sel-gray { background: #f3f4f6; border-color: #dadce0; }
    .ui-msg-card.is-sel-blue { background: #e3f2fd; border-color: #90caf9; }
    .ui-msg-card.ui-msg-active { outline: 2px solid #2196F3; outline-offset: 0; }
    .ui-msg-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7986cb, #5c6bc0);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .ui-msg-body { min-width: 0; flex: 1; }
    .ui-msg-top { display: flex; justify-content: space-between; align-items: baseline; gap: 8px; margin-bottom: 4px; }
    .ui-msg-from { font-weight: 700; font-size: 13px; color: #1a1a1a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ui-msg-time { font-size: 11px; color: #5f6368; flex-shrink: 0; }
    .ui-msg-subj { font-size: 12px; font-weight: 600; color: #333; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ui-msg-preview { font-size: 12px; color: #5f6368; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .ui-detail-toolbar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 4px;
        padding: 10px 14px;
        border-bottom: 1px solid #e8ebf0;
        background: #fff;
    }
    .ui-dtool {
        border: 0;
        background: transparent;
        width: 36px;
        height: 36px;
        border-radius: 6px;
        color: #5f6368;
        cursor: pointer;
    }
    .ui-dtool:hover { background: #f1f3f4; color: #202124; }
    .ui-detail-toolbar-spacer { flex: 1; min-width: 8px; }
    .ui-assign-btn {
        border: 1px solid #dadce0;
        background: #fff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #333;
        cursor: pointer;
    }
    .ui-assign-btn:hover { background: #f8f9fa; }
    .ui-detail-inner { padding: 24px 28px 40px; overflow-y: auto; flex: 1; }
    .ui-detail-head { display: flex; flex-wrap: wrap; align-items: flex-start; gap: 12px; margin-bottom: 20px; }
    .ui-detail-subject {
        font-size: 22px;
        font-weight: 700;
        color: #202124;
        margin: 0;
        flex: 1;
        min-width: 200px;
        line-height: 1.3;
    }
    .ui-detail-pill {
        font-size: 12px;
        color: #5f6368;
        background: #f1f3f4;
        padding: 6px 12px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .ui-detail-meta { display: flex; gap: 16px; margin-bottom: 24px; }
    .ui-detail-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #dadce0;
        color: #444;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }
    .ui-detail-addresses { font-size: 13px; line-height: 1.7; color: #202124; }
    .ui-detail-body {
        font-size: 14px;
        line-height: 1.65;
        color: #202124;
        max-width: 720px;
    }
    @media (max-width: 1199px) {
        .ui-shell { flex-direction: column; height: auto; min-height: calc(100vh - 56px); }
        .ui-folder-col, .ui-list-col { width: 100%; border-right: 0; max-height: 320px; }
        .ui-detail-col { min-height: 400px; }
    }
</style>
<script>
(function(){
    function setDetail(card) {
        if (!card) return;
        document.querySelectorAll('.ui-msg-card').forEach(function(c) { c.classList.remove('ui-msg-active'); });
        card.classList.add('ui-msg-active');
        document.getElementById('uiDetailSubject').textContent = card.getAttribute('data-subject') || '';
        document.getElementById('uiDetailRel').textContent = card.getAttribute('data-time-rel') || '';
        document.getElementById('uiDetailFrom').textContent = card.getAttribute('data-from') || '';
        document.getElementById('uiDetailFromEmail').textContent = '<' + (card.getAttribute('data-from-email') || '') + '>';
        document.getElementById('uiDetailTo').textContent = card.getAttribute('data-to') || '';
        document.getElementById('uiDetailDate').textContent = card.getAttribute('data-date-full') || '';
        document.getElementById('uiDetailAvatar').textContent = card.getAttribute('data-initials') || '';
        var body = card.getAttribute('data-body') || '';
        document.getElementById('uiDetailBody').innerHTML = body.replace(/\n/g, '<br>');
    }
    document.querySelectorAll('.ui-msg-card').forEach(function(card) {
        card.addEventListener('click', function() { setDetail(card); });
    });
    var first = document.querySelector('.ui-msg-card');
    if (first) setDetail(first);
    var search = document.getElementById('uiSearchList');
    if (search) {
        search.addEventListener('input', function() {
            var q = (search.value || '').toLowerCase().trim();
            document.querySelectorAll('.ui-msg-card').forEach(function(card) {
                var blob = (card.getAttribute('data-preview') || '') + ' ' + (card.getAttribute('data-subject') || '') + ' ' + (card.getAttribute('data-from') || '');
                card.style.display = !q || blob.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
            });
        });
    }
})();
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
