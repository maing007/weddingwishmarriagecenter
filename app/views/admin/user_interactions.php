<?php
$title = $reportTitle ?? 'User Interactions';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>

<div class="admin-main">
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><?= htmlspecialchars($reportTitle ?? 'Interactions') ?></h5>
                    <a href="<?= BASE_URL ?>/admin/users" class="btn btn-sm btn-primary">Back</a>
                </div>

                <?php if (empty($rows)): ?>
                    <div class="alert alert-warning mb-0">No records found.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Count</th>
                                    <th>Last Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= (int)($r['user_id'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars(trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''))) ?></td>
                                        <td><?= htmlspecialchars($r['email'] ?? '-') ?></td>
                                        <td><?= (int)($r['action_count'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars($r['last_action_at'] ?? '-') ?></td>
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
