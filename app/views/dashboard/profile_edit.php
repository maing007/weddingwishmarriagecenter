<?php
require __DIR__ . '/../partials/left-panel.php';

$error = $error ?? '';
$success = $success ?? '';

$lastName = $user['second_name'] ?? $user['last_name'] ?? '';
$phone = $user['phone'] ?? '';
$countryCode = $user['country_code'] ?? '';
$currentReligion = $user['religion'] ?? '';

$dobRaw = $user['dob'] ?? '';
$dobTs = ($dobRaw && $dobRaw !== '0000-00-00') ? strtotime($dobRaw) : false;
$birthDay = $dobTs ? (int)date('j', $dobTs) : 1;
$birthMonth = $dobTs ? (int)date('n', $dobTs) : 1;
$birthYear = $dobTs ? (int)date('Y', $dobTs) : ((int)date('Y') - 25);

$imgPath = $user['photo2_url'] ?? $user['avatar'] ?? ($user['photo1_status'] ?? '');
$profileImgUrl = !empty($imgPath)
    ? BASE_URL . '/' . ltrim((string)$imgPath, '/')
    : BASE_URL . '/assets/images/default-avatar.png';

$bioVal = $user['about_us'] ?? $user['bio'] ?? '';
?>
<div class="dash-content-wrapper">
    <div class="container py-3 profile-edit-page">
        <style>
            .profile-edit-page { max-width: 640px; }
            .profile-edit-page .hero-card {
                background: #fff;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
                padding: 1.5rem;
            }
            .profile-edit-page .avatar-ring {
                width: 112px;
                height: 112px;
                border-radius: 50%;
                object-fit: cover;
                border: 3px solid #e5e7eb;
            }
            .profile-edit-page label { font-weight: 600; color: #374151; font-size: 0.9rem; }
            .profile-edit-page .form-control {
                border-radius: 8px;
            }
            .profile-edit-page .iti { width: 100%; }
            .profile-edit-page .iti__flag-container { border-radius: 8px 0 0 8px; }
        </style>

        <h1 style="font-size: 22px; font-weight: 700; margin-top: 0;">Edit profile</h1>
        <p class="text-muted small" style="margin-bottom: 20px;">Update your basic details. Changes are saved to your member record.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="hero-card">
            <div class="text-center mb-4">
                <img src="<?= htmlspecialchars($profileImgUrl, ENT_QUOTES, 'UTF-8') ?>" alt="" class="avatar-ring">
            </div>

            <form id="profile_edit_form"
                  action="<?= BASE_URL ?>/dashboard/profile"
                  method="post"
                  enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name">First name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="<?= htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_name">Last name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="<?= htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="phone_input">Phone</label>
                            <input type="tel" name="phone" id="phone_input" class="form-control"
                                   value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>" autocomplete="tel">
                            <input type="hidden" name="mobile_number" id="mobile_number" value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="country_code" id="country_code" value="<?= htmlspecialchars($countryCode, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="birth_date">Birth day</label>
                            <select class="form-control" id="birth_date" name="birth_date" required>
                            <?php for ($d = 1; $d <= 31; $d++): ?>
                                <option value="<?= $d ?>" <?= $birthDay === $d ? 'selected' : '' ?>><?= $d ?></option>
                            <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="birth_month">Month</label>
                            <select class="form-control" id="birth_month" name="birth_month" required>
                            <?php
                            $months = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
                                7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
                            foreach ($months as $num => $label): ?>
                                <option value="<?= $num ?>" <?= $birthMonth === $num ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="birth_year">Year</label>
                            <select class="form-control" id="birth_year" name="birth_year" required>
                            <?php
                            $yEnd = (int)date('Y');
                            for ($y = $yEnd; $y >= $yEnd - 80; $y--): ?>
                                <option value="<?= $y ?>" <?= $birthYear === $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="religion">Religion</label>
                            <select class="form-control" name="religion" id="religion" required>
                            <option value="">Select religion</option>
                            <?php
                            $religions = ['Muslim', 'Sikh', 'Hindu', 'Christian', 'Qadiyani'];
                            $inList = $currentReligion !== '' && in_array($currentReligion, $religions, true);
                            if ($currentReligion !== '' && !$inList): ?>
                                <option value="<?= htmlspecialchars($currentReligion, ENT_QUOTES, 'UTF-8') ?>" selected><?= htmlspecialchars($currentReligion, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endif;
                            foreach ($religions as $r): ?>
                                <option value="<?= htmlspecialchars($r, ENT_QUOTES, 'UTF-8') ?>" <?= $currentReligion === $r ? 'selected' : '' ?>><?= htmlspecialchars($r, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="bio">About you</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4"><?= htmlspecialchars($bioVal, ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="profile_image">Profile photo</label>
                            <input type="file" class="form-control" id="profile_image" name="avatar" accept="image/*">
                            <small class="text-muted">Optional — JPG, PNG, WebP. Shown on your public profile card.</small>
                        </div>
                    </div>
                </div>

                <div class="clearfix" style="margin-top: 15px;">
                    <a href="<?= BASE_URL ?>/dashboard/profile/view?id=<?= (int)$_SESSION['user_id'] ?>" class="btn btn-default">View profile</a>
                    <button type="submit" class="btn btn-primary pull-right">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.querySelector('#phone_input');
    if (!input || typeof window.intlTelInput !== 'function') return;

    var iti = window.intlTelInput(input, {
        initialCountry: 'auto',
        separateDialCode: true,
        autoPlaceholder: 'polite',
        nationalMode: false,
        preferredCountries: ['pk', 'us', 'gb'],
        utilsScript: '<?= BASE_URL ?>/assets/js/utils.js'
    });

    var savedCountry = '<?= htmlspecialchars(strtolower((string)$countryCode), ENT_QUOTES, 'UTF-8') ?>';
    var savedNumber = '<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>';

    if (savedCountry && savedCountry.length === 2) {
        try { iti.setCountry(savedCountry); } catch (e) {}
    }
    if (savedNumber) {
        try { iti.setNumber(savedNumber); } catch (e) {}
    }

    var form = document.getElementById('profile_edit_form');
    form.addEventListener('submit', function () {
        var mb = document.getElementById('mobile_number');
        var cc = document.getElementById('country_code');
        if (mb) mb.value = iti.getNumber();
        var data = iti.getSelectedCountryData();
        if (cc && data && data.iso2) cc.value = data.iso2.toUpperCase();
    });
});
</script>
