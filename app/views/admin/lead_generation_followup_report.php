<?php
$title = 'Leads Follow-up Report';
$withFollow = array_values(array_filter($allLeads, static function ($L) {
    return !empty($L['next_followup']);
}));
usort($withFollow, static function ($a, $b) {
    return strcmp((string) ($a['next_followup'] ?? ''), (string) ($b['next_followup'] ?? ''));
});
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-manage-members.css">

<div class="admin-main lead-gen-report-page">
<div class="admin-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title">Leads Follow-up Report</div>
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
    <div class="container-fluid">
        <div class="report-panel pb-3">
            <a href="<?= BASE_URL ?>/admin/lead-generation" class="btn btn-sm btn-secondary mb-3"><i class="fa fa-arrow-left"></i> Lead list</a>
            <p class="small text-muted mb-3">Leads with a next follow-up date set (earliest first).</p>
            <div class="table-responsive lgr-table-wrap">
                <table class="table table-sm table-striped mb-0 lgr-table">
                    <thead class="table-dark"><tr><th>Follow-up</th><th>Name</th><th>Lead Id</th><th>Interest</th><th>Phone 1</th></tr></thead>
                    <tbody>
                    <?php if (empty($withFollow)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No follow-up dates recorded.</td></tr>
                    <?php else: ?>
                        <?php foreach ($withFollow as $L): ?>
                            <tr>
                                <td><?= htmlspecialchars($L['next_followup'] ?? '') ?></td>
                                <td><?= htmlspecialchars($L['full_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($L['lead_code'] ?? '') ?></td>
                                <td><?= htmlspecialchars($L['interest_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($L['phone1'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
