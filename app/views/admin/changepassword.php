<?php
$title = "Change Password";
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>
<style>
    .spacer {
        margin-top: 123px;
    }
</style>

<div class="container spacer mt-5">

    <div class="card p-4" style="max-width: 500px; margin:auto;">
        <h4 class="mb-3">Change Password</h4>

        <form method="POST" action="<?= BASE_URL ?>/admin/update-password">

            <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control">
            </div>

            <?php if (!empty($error)): ?>
                <div class="text-danger"><?= $error ?></div>
            <?php endif; ?>

            <button class="btn btn-primary">Update Password</button>
        </form>
    </div>

</div>

<?php require __DIR__ . '/partials/footer.php'; ?>