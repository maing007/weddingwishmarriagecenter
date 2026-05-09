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

$stepLabels = [
    'basic' => 'Basic Details',
    'residence' => 'Residence',
    'family' => 'Family Info',
    'partner' => 'Partner Preference',
    'media' => 'Photos & Docs',
];

require_once __DIR__ . '/user_details_form/data_array.php';
require_once __DIR__ . '/user_details_form/edit_steps_render_field.php';

$admin_details = $admin_details ?? null;
$D = [
    'languages' => $languages,
    'castes' => $castes,
    'countries_names' => $countries_names,
    'states' => $states,
    'cities' => $cities,
    'areas' => $areas,
    'heights' => $heights,
    'weights' => $weights,
    'jobs' => $jobs,
    'designations' => $designations,
    'educations' => $educations,
    'ages' => $ages,
    'countries' => $countries,
];

$edit_basic_incomplete_message = $edit_basic_incomplete_message ?? null;
$edit_basic_locked = !empty($edit_basic_locked);
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@4.0.1/dist/css/multi-select-tag.min.css">
<style>
    .admin-user-steps .admin-content {
        padding: 12px;
        background: #efefef;
    }
    .admin-user-steps .step-page-title {
        font-size: 13px;
        font-weight: 700;
        color: #595959;
        margin-bottom: 10px;
    }
    .admin-user-steps .step-card {
        background: #f7f7f7;
        border: 1px solid #d9d9d9;
        border-radius: 3px;
        padding: 10px;
    }
    .admin-user-steps .step-nav {
        display: grid;
        grid-template-columns: repeat(5, minmax(100px, 1fr));
        gap: 6px;
        margin-bottom: 10px;
    }
    .admin-user-steps .step-link {
        display: block;
        text-align: center;
        background: #e7e7e7;
        color: #636363;
        border: 1px solid #dadada;
        border-radius: 3px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 6px;
    }
    .admin-user-steps button.step-link {
        width: 100%;
        cursor: pointer;
        font: inherit;
        line-height: 1.3;
    }
    .admin-user-steps .step-link.active,
    .admin-user-steps button.step-link.active {
        background: #3b7fc4;
        border-color: #2f6ea8;
        color: #fff;
    }
    .admin-user-steps button.step-link.step-link-disabled,
    .admin-user-steps button.step-link:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }
    .admin-user-steps .step-form-wrap {
        background: #fff;
        border: 1px solid #e4e4e4;
        border-radius: 2px;
        padding: 14px;
    }
    .admin-user-steps .step-form-wrap label.form-label {
        font-size: 14px;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 6px;
        display: block;
    }
    .admin-user-steps .step-form-wrap .form-control,
    .admin-user-steps .step-form-wrap select.form-control {
        height: auto;
        min-height: 42px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        background-color: #f9fafb;
        font-size: 14px;
        padding: 8px 12px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }
    .admin-user-steps .step-form-wrap textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }
    .admin-user-steps .step-form-wrap .form-control:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.15);
        background-color: #ffffff;
        outline: none;
    }
    .admin-user-steps .step-form-wrap select.form-control:not([multiple]) {
        appearance: none;
        background-image: url("data:image/svg+xml;utf8,<svg fill='%236b7280' height='20' viewBox='0 0 20 20' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M5.5 7l4.5 4 4.5-4'/></svg>");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 16px;
    }
    .admin-user-steps .step-form-wrap select.form-control[multiple] {
        min-height: 120px;
        height: auto;
        padding-top: 8px;
        background-image: none;
    }
    .admin-user-steps .edit-steps-radio-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 18px;
        align-items: center;
    }
    .admin-user-steps .edit-steps-radio-row .edit-steps-inline-label {
        margin: 0;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
    }
    .admin-user-steps .edit-steps-radio-row input[type="radio"] {
        accent-color: #2563eb;
        margin-right: 6px;
        vertical-align: middle;
    }
    .admin-user-steps .edit-steps-back {
        display: inline-block;
        margin-bottom: 12px;
        font-size: 13px;
        font-weight: 600;
        color: #3b7fc4;
        text-decoration: none;
    }
    .admin-user-steps .edit-steps-back:hover {
        text-decoration: underline;
        color: #2f6ea8;
    }
    .admin-user-steps .edit-steps-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-top: 8px;
        padding-top: 14px;
        border-top: 1px solid #e5e7eb;
    }
    .admin-user-steps .edit-steps-actions .btn-step {
        height: 42px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
        padding: 0 20px;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease, opacity 0.2s ease;
    }
    .admin-user-steps .edit-steps-actions .btn-step-prev {
        background: #e7e7e7;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    .admin-user-steps .edit-steps-actions .btn-step-prev:hover {
        background: #dcdcdc;
    }
    .admin-user-steps .edit-steps-actions .btn-step-next {
        background: #2563eb;
        color: #fff;
    }
    .admin-user-steps .edit-steps-actions .btn-step-next:hover {
        background: #1d4ed8;
    }
    .admin-user-steps .edit-steps-actions .btn-step-submit {
        background: #059669;
        color: #fff;
        margin-left: auto;
    }
    .admin-user-steps .edit-steps-actions .btn-step-submit:hover {
        background: #047857;
    }
    .admin-user-steps .step-empty-msg {
        color: #6b7280;
        font-size: 14px;
        padding: 12px 0;
    }
    .admin-user-steps .step-form-wrap .container,
    .admin-user-steps .step-form-wrap .card {
        max-width: 100%;
        width: 100%;
        border: 0;
        box-shadow: none;
        padding: 0;
        margin: 0;
        background: transparent;
    }
    @media (max-width: 1100px) {
        .admin-user-steps .step-nav {
            grid-template-columns: repeat(3, minmax(100px, 1fr));
        }
    }
    @media (max-width: 700px) {
        .admin-user-steps .step-nav {
            grid-template-columns: repeat(2, minmax(100px, 1fr));
        }
    }
</style>
<div class="admin-main admin-user-steps">
    <div class="admin-topbar">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-profile" id="adminProfileTrigger">
            <div class="admin-profile-box"><span><?= htmlspecialchars($this->displayadminname(), ENT_QUOTES, 'UTF-8') ?></span><i class="fa fa-user"></i></div>
            <div class="admin-dropdown" id="adminDropdown">
                <a href="<?= BASE_URL ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
                <a href="<?= BASE_URL ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </div>
    <main class="admin-page">
        <div class="admin-content">
            <div class="step-page-title">
                Edit member — <?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['second_name'] ?? $user['last_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="step-card">
                <a href="<?= BASE_URL ?>/admin/users" class="edit-steps-back">← Back to members</a>
                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger mb-3" style="border-radius: 6px;"><?= $_SESSION['flash_error'];
                    unset($_SESSION['flash_error']); ?></div>
                <?php endif; ?>
                <?php if (!empty($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success mb-3" style="border-radius: 6px;"><?= $_SESSION['flash_success'];
                    unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>
                <?php if (!empty($edit_basic_incomplete_message)): ?>
                    <div class="alert alert-warning mb-3" style="border-radius: 6px;"><?= htmlspecialchars($edit_basic_incomplete_message, ENT_QUOTES, 'UTF-8') ?>
                        <strong>Other steps are disabled</strong> until Basic Details are complete. Fill the Basic tab, then click <em>Update all details</em>.
                    </div>
                <?php endif; ?>
                <form method="post" action="<?= BASE_URL ?>/admin/users/edit-steps" class="m-0" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                    <div class="step-nav" id="editStepNav">
                        <?php $first = true; ?>
                        <?php foreach ($stepLabels as $key => $label): ?>
                            <button type="button" class="step-link<?= $first ? ' active' : '' ?><?= !empty($edit_basic_locked) && $key !== 'basic' ? ' step-link-disabled' : '' ?>"
                                data-step="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                                <?= !empty($edit_basic_locked) && $key !== 'basic' ? 'disabled' : '' ?>>
                                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                            </button>
                            <?php $first = false; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="step-form-wrap">
                        <?php foreach ($stepMap as $step => $cols): ?>
                            <div class="step-pane" data-pane="<?= htmlspecialchars($step, ENT_QUOTES, 'UTF-8') ?>" style="<?= $step === 'basic' ? '' : 'display:none;' ?>">
                                <?php if (count($cols) === 0): ?>
                                    <p class="step-empty-msg mb-0">No fields in this section.</p>
                                <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($cols as $col): ?>
                                            <div class="col-lg-6 col-md-6 mb-3">
                                                <label class="form-label" for="fld_<?= htmlspecialchars($col, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $col)), ENT_QUOTES, 'UTF-8') ?></label>
                                                <?php admin_edit_steps_render_field($col, $user, $admin_details, $D); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="edit-steps-actions">
                            <button type="button" class="btn-step btn-step-prev" id="prevStepBtn" <?= !empty($edit_basic_locked) ? 'disabled' : '' ?>>Previous</button>
                            <button type="button" class="btn-step btn-step-next" id="nextStepBtn" <?= !empty($edit_basic_locked) ? 'disabled' : '' ?>>Next</button>
                            <button type="submit" class="btn-step btn-step-submit">Update all details</button>
                        </div>
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
    const basicLocked = <?= !empty($edit_basic_locked) ? 'true' : 'false' ?>;
    const tabButtons = Array.from(document.querySelectorAll('#editStepNav .step-link'));
    function setStep(i) {
        if (basicLocked && i > 0) {
            i = 0;
        }
        idx = Math.max(0, Math.min(steps.length - 1, i));
        const key = steps[idx];
        tabButtons.forEach((b) => b.classList.toggle('active', b.dataset.step === key));
        document.querySelectorAll('.step-pane').forEach((pane) => {
            pane.style.display = pane.dataset.pane === key ? '' : 'none';
        });
    }
    tabButtons.forEach((btn) => btn.addEventListener('click', () => {
        if (basicLocked && btn.dataset.step !== 'basic') {
            return;
        }
        setStep(steps.indexOf(btn.dataset.step));
    }));
    const prevBtn = document.getElementById('prevStepBtn');
    const nextBtn = document.getElementById('nextStepBtn');
    if (prevBtn) {
        prevBtn.addEventListener('click', () => { if (!basicLocked) setStep(idx - 1); });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', () => { if (!basicLocked) setStep(idx + 1); });
    }
    setStep(0);
})();
</script>
<script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@4.0.1/dist/js/multi-select-tag.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('select.edit-steps-multi').forEach(function (select, index) {
        if (!select.id) {
            select.id = 'edit_ms_' + index;
        }
        try {
            new MultiSelectTag(select.id, { placeholder: 'Search' });
        } catch (e) {}
    });
});
</script>
<?php require __DIR__ . '/partials/footer.php'; ?>
