<?php
$title = 'Open Task — Lead';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-manage-members.css">

<div class="admin-main lead-gen-report-page">
<div class="admin-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title">Open Task — Lead</div>
    </div>
    <div class="admin-profile" id="adminProfileTrigger">
        <div class="admin-profile-box"><span><?= htmlspecialchars($this->displayadminname()) ?></span><i class="fa fa-user"></i></div>
        <div class="admin-dropdown" id="adminDropdown">
            <a href="<?= BASE_URL ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
            <a href="<?= BASE_URL ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
        </div>
    </div>
</div>
<main class="admin-page">
<div class="admin-content">
    <div class="container-fluid" style="max-width:720px;">
        <div class="report-panel">
            <a href="<?= BASE_URL ?>/admin/lead-generation" class="btn btn-sm btn-secondary mb-3"><i class="fa fa-arrow-left"></i> Back to leads</a>
            <h1 class="h6 mb-3">Open Task</h1>
            <?php if (!empty($lead)): ?>
                <p class="small text-muted mb-2">Lead: <strong><?= htmlspecialchars($lead['full_name'] ?? '') ?></strong> (<?= htmlspecialchars($lead['lead_code'] ?? '') ?>)</p>
                <p class="small">Member task workflows are tied to registered users. After you add the person as a member, use <strong>Open Task</strong> from the members list.</p>
                <a class="btn btn-sm btn-primary mt-2" href="<?= BASE_URL ?>/admin/users">Go to members</a>
            <?php else: ?>
                <p class="text-danger small mb-0">Lead not found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</main>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
