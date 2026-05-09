<?php
$title = 'Database migrations';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
/** @var array $status */
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-manage-members.css">

<div class="admin-main">
<div class="admin-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title">Database migrations</div>
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
    <div class="container-fluid py-3">

        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show small"><?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show small"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">How it works</h5>
                <p class="card-text small text-muted mb-2">
                    <strong>Auto-run:</strong> On every request, the app runs pending migration files from <code>app/migrations/*.php</code>
                    (unless <code>SKIP_DB_MIGRATIONS=1</code> is set on the server).
                </p>
                <p class="card-text small text-muted mb-0">
                    Use the button below to run pending migrations immediately (same as deploy auto-run, but ignores <code>SKIP_DB_MIGRATIONS</code>).
                </p>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <span class="badge <?= !empty($status['auto_run']) ? 'bg-success' : 'bg-secondary' ?> me-1">Auto-run</span>
                    <span class="small"><?= !empty($status['auto_run']) ? 'Enabled on each request' : 'Disabled (SKIP_DB_MIGRATIONS=1)' ?></span>
                </div>
                <form method="post" action="<?= BASE_URL ?>/admin/system/database-migrations/run" class="mb-0">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <button type="submit" class="btn btn-primary btn-sm" <?= empty($status['db_ok']) ? 'disabled' : '' ?>>
                        <i class="fa fa-play me-1"></i> Run pending migrations now
                    </button>
                </form>
            </div>
        </div>

        <?php if (!empty($status['error'])): ?>
            <div class="alert alert-warning small">
                <strong>Database:</strong> <?= htmlspecialchars($status['error']) ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Migration file</th>
                        <th style="width:120px">Status</th>
                        <th>Applied at</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($status['migrations'])): ?>
                        <tr><td colspan="3" class="text-muted small">No migration PHP files in <code>app/migrations/</code> (excluding <code>run_cli.php</code>).</td></tr>
                    <?php else: ?>
                        <?php foreach ($status['migrations'] as $m): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($m['file']) ?></code></td>
                            <td>
                                <?php if (!empty($m['applied'])): ?>
                                    <span class="badge bg-success">Applied</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="small"><?= !empty($m['applied_at']) ? htmlspecialchars($m['applied_at']) : '—' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <p class="small text-muted mt-3 mb-0">
            See <code>app/migrations/MIGRATIONS_README.txt</code> for file format. CLI: <code>php app/migrations/run_cli.php</code>
        </p>
    </div>
</div>
</main>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
