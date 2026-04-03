<?php
$title = 'Edit Profile (Steps)';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';

$stepMap = [
    'basic' => [],
    'residence' => [],
    'family' => [],
    'partner' => [],
    'media' => [],
];

foreach ($columns as $col) {
    if (strpos($col, 'partner_') === 0) {
        $stepMap['partner'][] = $col;
    } elseif (in_array($col, ['country', 'state', 'city', 'area', 'address', 'house_type', 'house_size', 'house_size_marla', 'residence', 'location_pin'], true)) {
        $stepMap['residence'][] = $col;
    } elseif (strpos($col, 'photo') === 0 || in_array($col, ['id_proof_file', 'id_proof_status', 'cv_file'], true)) {
        $stepMap['media'][] = $col;
    } elseif (strpos($col, 'father_') === 0 || strpos($col, 'mother_') === 0 || strpos($col, 'family_') === 0 || strpos($col, 'no_of_') === 0) {
        $stepMap['family'][] = $col;
    } else {
        $stepMap['basic'][] = $col;
    }
}
?>
<div class="admin-main">
    <div class="admin-topbar">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
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
                <a href="<?= BASE_URL ?>/admin/users" class="btn btn-sm btn-primary mb-3">Back</a>
                <form method="post" action="<?= BASE_URL ?>/admin/users/edit-steps" class="card">
                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                    <div class="card-header">
                        <strong>Edit Profile - Step by Step</strong>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3" id="stepTabs">
                            <li class="nav-item"><button type="button" class="nav-link active" data-step="basic">Basic</button></li>
                            <li class="nav-item"><button type="button" class="nav-link" data-step="residence">Residence</button></li>
                            <li class="nav-item"><button type="button" class="nav-link" data-step="family">Family</button></li>
                            <li class="nav-item"><button type="button" class="nav-link" data-step="partner">Partner</button></li>
                            <li class="nav-item"><button type="button" class="nav-link" data-step="media">Media</button></li>
                        </ul>
                        <?php foreach ($stepMap as $step => $cols): ?>
                            <div class="step-pane" data-pane="<?= $step ?>" style="<?= $step === 'basic' ? '' : 'display:none;' ?>">
                                <div class="row">
                                    <?php foreach ($cols as $col): ?>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $col))) ?></label>
                                            <?php if ($col === 'gender'): ?>
                                                <select class="form-select" name="<?= htmlspecialchars($col) ?>">
                                                    <option value="">Select</option>
                                                    <option value="Male" <?= (($user[$col] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                                                    <option value="Female" <?= (($user[$col] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                                                    <option value="Other" <?= (($user[$col] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                                </select>
                                            <?php else: ?>
                                                <input class="form-control" name="<?= htmlspecialchars($col) ?>" value="<?= htmlspecialchars((string)($user[$col] ?? '')) ?>">
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="prevStepBtn">Previous</button>
                        <button type="button" class="btn btn-info text-white" id="nextStepBtn">Next</button>
                        <button type="submit" class="btn btn-success">Update All Details</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
<script>
(() => {
    const steps = ['basic','residence','family','partner','media'];
    let idx = 0;
    const tabButtons = Array.from(document.querySelectorAll('#stepTabs .nav-link'));
    function setStep(i){
        idx = Math.max(0, Math.min(steps.length - 1, i));
        const key = steps[idx];
        tabButtons.forEach((b) => b.classList.toggle('active', b.dataset.step === key));
        document.querySelectorAll('.step-pane').forEach((pane) => {
            pane.style.display = pane.dataset.pane === key ? '' : 'none';
        });
    }
    tabButtons.forEach((btn) => btn.addEventListener('click', () => setStep(steps.indexOf(btn.dataset.step))));
    document.getElementById('prevStepBtn').addEventListener('click', () => setStep(idx - 1));
    document.getElementById('nextStepBtn').addEventListener('click', () => setStep(idx + 1));
})();
</script>
<?php require __DIR__ . '/partials/footer.php'; ?>
