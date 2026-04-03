<?php
$title = 'Profile View';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';

$fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['second_name'] ?? ''));

$steps = [
    'basic' => [
        'title' => 'Step 1: Basic Details',
        'fields' => ['matri_id', 'lead', 'first_name', 'second_name', 'gender', 'dob', 'email', 'phone', 'mobile_number', 'time_to_call', 'contact_person_name', 'contact_person_relation', 'user_status', 'featured_status', 'created_at'],
    ],
    'residence' => [
        'title' => 'Step 2: Residence',
        'fields' => ['country_code', 'country', 'state', 'city', 'area', 'address', 'location_pin', 'house_type', 'house_size', 'house_size_marla', 'residence'],
    ],
    'physical' => [
        'title' => 'Step 3: Physical & Lifestyle',
        'fields' => ['height', 'weight', 'skin_tone', 'body_type', 'blood_group', 'eating_habits', 'smoking', 'drinking', 'mother_tongue', 'language_known'],
    ],
    'other' => [
        'title' => 'Step 4: Other & Family',
        'fields' => ['religion', 'maslak', 'caste', 'sub_caste', 'marital_status', 'total_children', 'status_children', 'education', 'occupation', 'designation', 'annual_income', 'work_detail', 'birth_place', 'birth_time', 'profile_by', 'reference', 'family_type', 'father_name', 'father_occupation', 'mother_name', 'mother_occupation', 'family_status', 'no_of_brothers', 'no_of_married_brother', 'no_of_sisters', 'no_of_married_sister', 'family_details', 'about_us', 'hobby'],
    ],
    'partner' => [
        'title' => 'Step 5: Partner Preference',
        'fields' => ['looking_for', 'partner_complexion', 'partner_from_age', 'partner_to_age', 'partner_from_height', 'partner_to_height', 'partner_body_type', 'partner_eating_habit', 'partner_smoking_habit', 'partner_drinking_habit', 'partner_mother_tongue', 'expectations', 'partner_religion', 'partner_caste', 'partner_caste_exception', 'partner_manglik', 'partner_star', 'partner_sect', 'partner_maslak', 'partner_maslak_exception', 'partner_denomination', 'partner_division', 'partner_gotra', 'partner_education', 'partner_employed_in', 'partner_occupation', 'partner_designation', 'partner_annual_income', 'partner_country', 'partner_state', 'partner_city', 'partner_country_exception', 'partner_area', 'partner_house_size_from', 'partner_house_size_to', 'partner_residence_status'],
    ],
    'upload' => [
        'title' => 'Step 6: Uploads & Fee',
        'fields' => ['photo1_status', 'photo_visibility', 'photo2_url', 'photo3_url', 'photo4_url', 'photo5_url', 'photo6_url', 'id_proof_status', 'id_proof_file', 'cv_file', 'registration_fee', 'final_fee'],
    ],
];

$formatLabel = static function ($key) {
    return ucwords(str_replace('_', ' ', (string)$key));
};

$formatValue = static function ($value) {
    if ($value === null || $value === '') {
        return '-';
    }
    if (is_string($value) && (str_starts_with($value, '[') || str_starts_with($value, '{'))) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($decoded)) {
                return implode(', ', array_map(static fn($item) => (string)$item, $decoded));
            }
            return (string)$decoded;
        }
    }
    return (string)$value;
};
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
                <div class="profile-head">
                    <div>
                        <h4 class="mb-1"><?= htmlspecialchars($fullName !== '' ? $fullName : 'Member Profile') ?></h4>
                        <small>ID: <?= (int)($user['id'] ?? 0) ?> | Matri ID: <?= htmlspecialchars((string)($user['matri_id'] ?? '-')) ?></small>
                    </div>
                    <a href="<?= BASE_URL ?>/admin/users" class="btn btn-sm btn-primary">Back</a>
                </div>

                <ul class="nav nav-pills profile-steps mb-3" id="viewStepTabs">
                    <?php $stepIndex = 1; foreach ($steps as $key => $step): ?>
                        <li class="nav-item">
                            <button type="button" class="nav-link <?= $stepIndex === 1 ? 'active' : '' ?>" data-step="<?= htmlspecialchars($key) ?>">
                                <?= $stepIndex ?>. <?= htmlspecialchars(str_replace('Step ' . $stepIndex . ': ', '', $step['title'])) ?>
                            </button>
                        </li>
                    <?php $stepIndex++; endforeach; ?>
                </ul>

                <?php $stepIndex = 1; foreach ($steps as $key => $step): ?>
                    <div class="card profile-step-pane <?= $stepIndex === 1 ? 'active' : '' ?>" data-pane="<?= htmlspecialchars($key) ?>">
                        <div class="card-header">
                            <strong><?= htmlspecialchars($step['title']) ?></strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($step['fields'] as $field): ?>
                                    <div class="col-lg-6 mb-2">
                                        <div class="detail-row">
                                            <span class="detail-label"><?= htmlspecialchars($formatLabel($field)) ?></span>
                                            <span class="detail-value"><?= htmlspecialchars($formatValue($user[$field] ?? null)) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php $stepIndex++; endforeach; ?>
            </div>
        </div>
    </main>
</div>

<style>
    .profile-head{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:14px;padding:12px 14px;background:#fff;border:1px solid #dfe3e8;border-radius:6px}
    .profile-head small{color:#6c757d}
    .profile-steps{display:flex;flex-wrap:wrap;gap:8px}
    .profile-steps .nav-link{border:1px solid #d8dee5;background:#f6f9fc;color:#3b4b5a;font-size:12px}
    .profile-steps .nav-link.active{background:#22a7d8;border-color:#22a7d8;color:#fff}
    .profile-step-pane{display:none}
    .profile-step-pane.active{display:block}
    .detail-row{display:flex;justify-content:space-between;gap:16px;border-bottom:1px dashed #e7ebef;padding:7px 0}
    .detail-label{font-weight:600;color:#3f4f5e}
    .detail-value{color:#5d6d7e;text-align:right;max-width:58%}
    @media (max-width: 768px){
        .detail-row{flex-direction:column;gap:2px}
        .detail-value{text-align:left;max-width:100%}
    }
</style>

<script>
(() => {
    const tabs = Array.from(document.querySelectorAll('#viewStepTabs .nav-link'));
    const panes = Array.from(document.querySelectorAll('.profile-step-pane'));
    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const key = tab.dataset.step;
            tabs.forEach((t) => t.classList.toggle('active', t === tab));
            panes.forEach((pane) => pane.classList.toggle('active', pane.dataset.pane === key));
        });
    });
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
