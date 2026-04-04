<?php
$title = 'Profile View';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';

$fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['second_name'] ?? ''));
$genderLabel = trim((string) ($user['gender'] ?? ''));
$uid = (int) ($user['id'] ?? 0);
$headLine = '(NG' . $uid . ') ' . ($fullName !== '' ? $fullName : 'Member Profile');
if ($genderLabel !== '') {
    $headLine .= ' — ' . $genderLabel;
}

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
        'fields' => ['photo_visibility', 'registration_fee', 'final_fee', 'cv_file'],
    ],
];

$formatLabel = static function ($key) {
    return ucwords(str_replace('_', ' ', (string) $key));
};

$formatValue = static function ($value) {
    if ($value === null || $value === '') {
        return 'N/A';
    }
    if (is_string($value) && (str_starts_with($value, '[') || str_starts_with($value, '{'))) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($decoded)) {
                return implode(', ', array_map(static fn ($item) => (string) $item, $decoded));
            }

            return (string) $decoded;
        }
    }

    return (string) $value;
};

$proseFields = ['about_us', 'family_details', 'expectations', 'hobby', 'work_detail'];
$badgeFields = ['user_status', 'featured_status'];

$profileBadgeClass = static function (string $field, $raw): string {
    $v = strtolower(trim((string) $raw));
    if ($field === 'featured_status') {
        if ($v === '' || strpos($v, 'non') !== false) {
            return 'nonfeatured';
        }

        return 'featured';
    }
    $v = preg_replace('/[^a-z]/', '', $v);
    if ($v === 'approved') {
        return 'approved';
    }
    if ($v === 'suspended') {
        return 'suspended';
    }

    return 'unapproved';
};

$photoSlots = [
    ['key' => 'photo1_status', 'label' => 'Photo 1'],
    ['key' => 'photo2_url', 'label' => 'Photo 2'],
    ['key' => 'photo3_url', 'label' => 'Photo 3'],
    ['key' => 'photo4_url', 'label' => 'Photo 4'],
    ['key' => 'photo5_url', 'label' => 'Photo 5'],
    ['key' => 'photo6_url', 'label' => 'Photo 6'],
];

$resolveMediaUrl = static function (string $path): string {
    $path = trim($path);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
};

$memberListStatus = strtolower((string) ($user['user_status'] ?? 'unapproved'));
$photoBadgeApproved = in_array($memberListStatus, ['approved'], true);
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-manage-members.css">

<div class="admin-main admin-profile-view-page">
    <div class="admin-topbar">
        <div class="admin-topbar-left">
            <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
            <div class="apv-topbar-titles">
                <div class="apv-topbar-title"><?= htmlspecialchars($headLine) ?></div>
                <div class="apv-topbar-sub">Matri ID: <?= htmlspecialchars((string) ($user['matri_id'] ?? '—')) ?></div>
            </div>
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
                <div class="apv-back-wrap">
                    <a href="<?= BASE_URL ?>/admin/users" class="btn-action btn-action-cyan"><i class="fa fa-arrow-left"></i> Back to list</a>
                </div>

                <div class="apv-accordion">
                    <?php
                    $stepIndex = 0;
                    foreach ($steps as $key => $step):
                        $stepIndex++;
                        $openAttr = $stepIndex === 1 ? ' open' : '';
                        ?>
                    <details class="apv-details"<?= $openAttr ?>>
                        <summary class="apv-acc-header">
                            <span><?= htmlspecialchars($step['title']) ?></span>
                            <i class="fa fa-chevron-down apv-acc-chevron" aria-hidden="true"></i>
                        </summary>
                        <div class="apv-acc-body">
                            <?php if ($key === 'upload'): ?>
                                <h3 class="apv-subsection-title">Member Photos</h3>
                                <div class="apv-photo-grid">
                                    <?php foreach ($photoSlots as $slot):
                                        $raw = (string) ($user[$slot['key']] ?? '');
                                        $src = $resolveMediaUrl($raw);
                                        ?>
                                    <div class="apv-photo-slot">
                                        <div class="apv-photo-label"><?= htmlspecialchars($slot['label']) ?></div>
                                        <div class="apv-photo-frame">
                                            <?php if ($src !== ''): ?>
                                                <img src="<?= htmlspecialchars($src) ?>" alt="">
                                            <?php else: ?>
                                                <span class="apv-photo-placeholder" aria-hidden="true"><i class="fa fa-picture-o"></i></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="apv-photo-badge <?= $photoBadgeApproved ? 'is-approved' : 'is-unapproved' ?>">
                                            <?php if ($photoBadgeApproved): ?>
                                                <i class="fa fa-thumbs-up" aria-hidden="true"></i> APPROVED
                                            <?php else: ?>
                                                <i class="fa fa-thumbs-down" aria-hidden="true"></i> UNAPPROVED
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <h3 class="apv-subsection-title">Member ID Proof</h3>
                                <div class="apv-id-proof-row">
                                    <?php
                                    $idPath = (string) ($user['id_proof_file'] ?? '');
                                    $idSrc = $resolveMediaUrl($idPath);
                                    ?>
                                    <div class="apv-photo-slot apv-photo-slot-wide">
                                        <div class="apv-photo-label">ID document</div>
                                        <div class="apv-photo-frame apv-photo-frame-wide">
                                            <?php if ($idSrc !== ''): ?>
                                                <img src="<?= htmlspecialchars($idSrc) ?>" alt="ID proof">
                                            <?php else: ?>
                                                <span class="apv-photo-placeholder" aria-hidden="true"><i class="fa fa-file-text-o"></i></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="apv-detail-row apv-detail-row-inline mt-2">
                                            <span class="apv-detail-label">ID proof status</span>
                                            <span class="apv-detail-value"><?= htmlspecialchars($formatValue($user['id_proof_status'] ?? null)) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="row apv-field-rows">
                                <?php foreach ($step['fields'] as $field):
                                    if (in_array($field, $proseFields, true)): ?>
                                <div class="col-12">
                                    <div class="apv-detail-prose">
                                        <span class="apv-detail-label"><?= htmlspecialchars($formatLabel($field)) ?></span>
                                        <?php
                                            $val = $formatValue($user[$field] ?? null);
                                            $isNa = ($val === 'N/A' || $val === '-');
                                        ?>
                                        <div class="apv-detail-value <?= $isNa ? 'apv-value-na' : '' ?>"><?= $isNa ? 'N/A' : htmlspecialchars($val) ?></div>
                                    </div>
                                </div>
                                    <?php continue; endif;
                                    if (in_array($field, $badgeFields, true)):
                                        $rawBadge = $user[$field] ?? null;
                                        $bc = $profileBadgeClass($field, $rawBadge);
                                        $disp = strtoupper((string) $formatValue($rawBadge));
                                        if ($disp === '-' || $disp === '') {
                                            $disp = 'N/A';
                                        }
                                        ?>
                                <div class="col-lg-6">
                                    <div class="apv-detail-row">
                                        <span class="apv-detail-label"><?= htmlspecialchars($formatLabel($field)) ?></span>
                                        <span class="apv-detail-value apv-detail-value-badge">
                                            <span class="approved-badge status-<?= htmlspecialchars($bc) ?>">
                                                <?php if ($field === 'user_status'): ?>
                                                    <?php if ($bc === 'approved'): ?>
                                                        <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                                                    <?php elseif ($bc === 'suspended'): ?>
                                                        <i class="fa fa-user-times" aria-hidden="true"></i>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($disp) ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                    <?php continue; endif;
                                    $val = $formatValue($user[$field] ?? null);
                                    $isNa = ($val === 'N/A' || $val === '-');
                                    ?>
                                <div class="col-lg-6">
                                    <div class="apv-detail-row">
                                        <span class="apv-detail-label"><?= htmlspecialchars($formatLabel($field)) ?></span>
                                        <span class="apv-detail-value <?= $isNa ? 'apv-value-na' : '' ?>"><?= $isNa ? 'N/A' : htmlspecialchars($val) ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<button type="button" class="apv-scroll-top" id="apvScrollTop" aria-label="Scroll to top"><i class="fa fa-arrow-up"></i></button>

<script>
(function () {
    var btn = document.getElementById('apvScrollTop');
    if (btn) {
        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
