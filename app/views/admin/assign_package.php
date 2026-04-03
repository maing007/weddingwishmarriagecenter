<?php
$title = "Assign Package";
require __DIR__.'/partials/header.php';
require __DIR__.'/partials/sidebar.php';
?>

<style>
.admin-content {
    background: #f5f5f5;
    min-height: 100vh;
    padding: 25px;
}

.page-title {
    font-size: 24px;
    font-weight: 600;
    color: #444;
    margin-bottom: 25px;
}

.form-card {
    background: #fff;
    padding: 30px;
    margin-bottom: 25px;
    border-radius: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
}

.form-card h4 {
    font-size: 20px;
    margin-bottom: 25px;
    color: #555;
    font-weight: 500;
}

.form-label {
    font-weight: 500;
    color: #555;
    margin-bottom: 8px;
}

.form-control,
.form-select {
    height: 48px;
    border-radius: 0;
    border: 1px solid #ddd;
    box-shadow: none;
}

.form-control:focus,
.form-select:focus {
    border-color: #2196f3;
    box-shadow: none;
}

.btn-custom-primary {
    background: #2196f3;
    color: #fff;
    border: none;
    padding: 10px 28px;
    font-weight: 500;
}

.btn-custom-primary:hover {
    background: #1976d2;
    color: #fff;
}

.btn-custom-success {
    background: #2ecc71;
    color: #fff;
    border: none;
    padding: 10px 28px;
    font-weight: 500;
}

.btn-custom-success:hover {
    background: #27ae60;
    color: #fff;
}

@media (max-width: 768px) {
    .form-card {
        padding: 20px;
    }
}
</style>

<div class="admin-content">

    <div class="page-title">Assign Package</div>

    <!-- First Form -->
    <div class="form-card">

        <h4>Assign Existing Package to User</h4>

        <form method="post" action="<?= BASE_URL ?>/admin/assign-package">

            <div class="mb-3">
                <label class="form-label">User</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Select User</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>">
                            <?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Package</label>
                <select name="package_id" class="form-control" required>
                    <option value="">Select Package</option>
                    <?php foreach ($packages as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= htmlspecialchars($p['name']) ?> (<?= $p['duration_days'] ?> days)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn btn-custom-primary">
                Assign Package
            </button>

        </form>

    </div>

    <?php
    $package = $package ?? [
        'package_name' => '',
        'price'        => '',
        'status'       => 'active',
        'expires_at'   => ''
    ];
    ?>

    <!-- Second Form -->
    <div class="form-card">

        <h4>Create & Assign New Package</h4>

        <form method="post" action="<?= BASE_URL ?>/admin/packages/store">

            <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Package Name</label>
                <input type="text"
                       name="package_name"
                       class="form-control"
                       value="<?= htmlspecialchars($package['package_name']) ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number"
                       name="price"
                       class="form-control"
                       value="<?= htmlspecialchars($package['price']) ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="active" <?= $package['status']=='active'?'selected':'' ?>>Active</option>
                    <option value="expired" <?= $package['status']=='expired'?'selected':'' ?>>Expired</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Expiry Date</label>
                <input type="date"
                       name="expires_at"
                       class="form-control"
                       value="<?= htmlspecialchars($package['expires_at']) ?>">
            </div>

            <button class="btn btn-custom-success">
                Assign Package
            </button>

        </form>

    </div>

</div>

<?php require __DIR__ . '/partials/footer.php'; ?>