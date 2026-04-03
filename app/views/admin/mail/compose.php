<?php
$title = 'Compose Mail';
$accountEmail = 'info@nikahglobal.pk';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/sidebar.php';
?>

<div class="admin-main cmp-page">
<div class="admin-topbar cmp-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="cmp-topbar-row">
            <a href="<?= BASE_URL ?>/admin/mail/inbox" class="cmp-back" title="Back to Inbox"><i class="fa fa-arrow-left"></i></a>
            <div>
                <div class="cmp-topbar-title">Compose</div>
                <div class="cmp-topbar-sub">New message from <?= htmlspecialchars($accountEmail, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        </div>
    </div>
    <div class="admin-profile" id="adminProfileTrigger">
        <div class="admin-profile-box"><span><?= htmlspecialchars($this->displayadminname(), ENT_QUOTES, 'UTF-8') ?></span><i class="fa fa-user"></i></div>
        <div class="admin-dropdown" id="adminDropdown">
            <a href="<?= BASE_URL ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
            <a href="<?= BASE_URL ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
        </div>
    </div>
</div>

<main class="admin-page cmp-main">
    <div class="cmp-shell">
        <div class="cmp-card">
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert alert-success mb-3" role="alert"><?= htmlspecialchars((string) $_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-danger mb-3" role="alert"><?= htmlspecialchars((string) $_SESSION['error']); unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form method="post" action="<?= BASE_URL ?>/admin/mail/send" id="cmpForm">
                <div class="cmp-field">
                    <label class="cmp-label" for="cmpTo">To</label>
                    <input type="email" name="to" id="cmpTo" class="cmp-input" placeholder="recipient@example.com" required autocomplete="off">
                </div>
                <div class="cmp-field-row">
                    <button type="button" class="cmp-link-btn" id="cmpToggleCc">Cc</button>
                    <button type="button" class="cmp-link-btn" id="cmpToggleBcc">Bcc</button>
                </div>
                <div class="cmp-field cmp-extra d-none" id="cmpCcWrap">
                    <label class="cmp-label" for="cmpCc">Cc</label>
                    <input type="text" name="cc" id="cmpCc" class="cmp-input" placeholder="Optional" autocomplete="off">
                </div>
                <div class="cmp-field cmp-extra d-none" id="cmpBccWrap">
                    <label class="cmp-label" for="cmpBcc">Bcc</label>
                    <input type="text" name="bcc" id="cmpBcc" class="cmp-input" placeholder="Optional" autocomplete="off">
                </div>
                <div class="cmp-field">
                    <label class="cmp-label" for="cmpSubject">Subject</label>
                    <input type="text" name="subject" id="cmpSubject" class="cmp-input" placeholder="Subject" required>
                </div>
                <div class="cmp-field">
                    <label class="cmp-label" for="cmpMsg">Message</label>
                    <textarea name="message" id="cmpMsg" class="cmp-textarea" rows="14" placeholder="Write your message..." required></textarea>
                </div>
                <div class="cmp-actions">
                    <button type="submit" class="cmp-btn-send"><i class="fa fa-paper-plane"></i> Send</button>
                    <a href="<?= BASE_URL ?>/admin/mail/inbox" class="cmp-btn-discard">Discard</a>
                </div>
            </form>
        </div>
    </div>
</main>
</div>

<style>
    .cmp-page .cmp-topbar {
        justify-content: space-between;
        padding-left: 12px;
        padding-right: 16px;
        background: #fff;
        border-bottom: 1px solid #e7e7e7;
    }
    .cmp-topbar-row {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .cmp-back {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #444;
        background: #f1f3f4;
        text-decoration: none;
        transition: background .15s;
    }
    .cmp-back:hover { background: #e8eaed; color: #111; }
    .cmp-topbar-title { font-size: 16px; font-weight: 700; color: #202124; }
    .cmp-topbar-sub { font-size: 12px; color: #5f6368; margin-top: 2px; }
    .cmp-main { background: #f4f6f9; padding: 24px 18px 40px; min-height: calc(100vh - 56px); }
    .cmp-shell { max-width: 920px; margin: 0 auto; }
    .cmp-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        border: 1px solid #e8ebf0;
        padding: 28px 32px 32px;
    }
    .cmp-field { margin-bottom: 18px; }
    .cmp-label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #444;
        margin-bottom: 6px;
        letter-spacing: .02em;
    }
    .cmp-input, .cmp-textarea {
        width: 100%;
        border: 1px solid #dadce0;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 14px;
        transition: border-color .15s, box-shadow .15s;
    }
    .cmp-input:focus, .cmp-textarea:focus {
        outline: none;
        border-color: #2196F3;
        box-shadow: 0 0 0 3px rgba(33,150,243,.15);
    }
    .cmp-textarea { resize: vertical; min-height: 220px; line-height: 1.5; }
    .cmp-field-row { display: flex; gap: 16px; margin: -6px 0 14px; }
    .cmp-link-btn {
        border: 0;
        background: none;
        padding: 0;
        font-size: 13px;
        font-weight: 600;
        color: #2196F3;
        cursor: pointer;
    }
    .cmp-link-btn:hover { text-decoration: underline; }
    .cmp-actions {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-top: 8px;
        padding-top: 20px;
        border-top: 1px solid #f1f3f4;
    }
    .cmp-btn-send {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 0;
        background: #2196F3;
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        padding: 12px 28px;
        border-radius: 8px;
        cursor: pointer;
        transition: filter .15s;
    }
    .cmp-btn-send:hover { filter: brightness(0.95); color: #fff; }
    .cmp-btn-discard {
        font-size: 14px;
        font-weight: 600;
        color: #5f6368;
        text-decoration: none;
    }
    .cmp-btn-discard:hover { color: #202124; }
    .cmp-card .alert { border-radius: 8px; font-size: 13px; }
</style>
<script>
(function(){
    var cc = document.getElementById('cmpCcWrap');
    var bcc = document.getElementById('cmpBccWrap');
    document.getElementById('cmpToggleCc') && document.getElementById('cmpToggleCc').addEventListener('click', function() {
        cc && cc.classList.toggle('d-none');
    });
    document.getElementById('cmpToggleBcc') && document.getElementById('cmpToggleBcc').addEventListener('click', function() {
        bcc && bcc.classList.toggle('d-none');
    });
})();
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
