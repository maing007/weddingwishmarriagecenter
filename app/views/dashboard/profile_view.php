<?php
require __DIR__ . '/../partials/left-panel.php';

/** @var array $user */
/** @var array|null $profileDetails */
/** @var array|null $myPackage */

$error = $error ?? '';
$success = $success ?? '';
$profileDetails = $profileDetails ?? null;
$myPackage = $myPackage ?? null;

$fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['second_name'] ?? $user['last_name'] ?? ''));
$gender = $user['gender'] ?? '';
$religion = $user['religion'] ?? '';
$phone = $user['phone'] ?? '';
$email = $user['email'] ?? '';
$bio = $user['about_us'] ?? $user['bio'] ?? '';
$dob = $user['dob'] ?? '';
$age = $age ?? null;

$pkgLabel = 'Free member';
if ($myPackage) {
    $paid = !empty($myPackage['is_paid']);
    $pkgLabel = $paid ? 'Paid member' : 'Member';
    if (!empty($myPackage['package_name'])) {
        $pkgLabel .= ' — ' . $myPackage['package_name'];
    }
}

$dash = static function ($v): string {
    if ($v === null || $v === '') {
        return '—';
    }
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
};
?>
<div class="dash-content-wrapper">
    <div class="container py-3 profile-view-page">
    <style>

/* BASE */
.profile-view-page {
    font-size: 18px; /* master control */
}

/* HERO SECTION */
.profile-view-page .pv-hero {
    background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 55%, #3b82f6 100%);
    color: #fff;
    border-radius: 16px;
    padding: 2.2rem;
    box-shadow: 0 18px 40px rgba(37, 99, 235, 0.25);
    overflow: hidden;
}

.profile-view-page .pv-hero-inner {
    display: table;
    width: 100%;
}

.profile-view-page .pv-hero-cell {
    display: table-cell;
    vertical-align: middle;
    padding-right: 15px;
}

.profile-view-page .pv-hero-cell:last-child {
    text-align: right;
    padding-right: 0;
}

/* MOBILE HERO FIX */
@media (max-width: 767px) {
    .profile-view-page .pv-hero-inner,
    .profile-view-page .pv-hero-cell {
        display: block;
        text-align: center !important;
        padding-right: 0;
    }
}

/* AVATAR */
.profile-view-page .pv-avatar {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255, 255, 255, 0.35);
}

/* NAME */
.profile-view-page .pv-hero h1 {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0 0 0.4rem;
}

/* META */
.profile-view-page .pv-meta {
    opacity: 0.95;
    font-size: 1.2rem;
    margin: 0;
}

/* BADGES */
.profile-view-page .pv-badges .label {
    font-size: 14px;
    margin-right: 6px;
}

/* CARD */
.profile-view-page .pv-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
}

/* CARD HEADER */
.profile-view-page .pv-card h2 {
    font-size: 1.2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6b7280;
    margin: 0;
    padding: 1.2rem 1.5rem;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

/* TABLE */
.profile-view-page .pv-table {
    margin: 0;
}

.profile-view-page .pv-table th {
    width: 34%;
    background: #fafafa;
    font-weight: 600;
    font-size: 1.1rem;
    color: #4b5563;
    padding: 1rem 1.5rem;
    border-color: #eee;
}

.profile-view-page .pv-table td {
    font-size: 1.1rem;
    padding: 1rem 1.5rem;
    border-color: #eee;
    color: #111827;
}

/* ABOUT */
.profile-view-page .pv-about {
    padding: 1.4rem 1.5rem;
    font-size: 1.2rem;
    line-height: 1.8;
    color: #374151;
}

/* BUTTONS */
.profile-view-page .pv-actions .btn {
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    padding: 0.6rem 1.2rem;
}

/* SMALL DEVICES */
@media (max-width: 576px) {
    .profile-view-page {
        font-size: 16px;
    }

    .profile-view-page .pv-hero h1 {
        font-size: 1.8rem;
    }

    .profile-view-page .pv-table th,
    .profile-view-page .pv-table td {
        font-size: 1rem;
        padding: 0.7rem 1rem;
    }

    .profile-view-page .pv-about {
        font-size: 1.1rem;
    }
}

</style>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="pv-hero mb-4">
            <div class="pv-hero-inner">
                <div class="pv-hero-cell" style="width: 130px;">
                    <img src="<?= htmlspecialchars($profileImgUrl, ENT_QUOTES, 'UTF-8') ?>" alt="" class="pv-avatar">
                </div>
                <div class="pv-hero-cell">
                    <h1><?= $dash($fullName !== '' ? $fullName : 'Your profile') ?></h1>
                    <p class="pv-meta mb-2">
                        <?php if ($gender): ?><span><?= $dash($gender) ?></span><?php endif; ?>
                        <?php if ($gender && !empty($age)): ?> · <?php endif; ?>
                        <?php if (!empty($age)): ?><span><?= (int)$age ?> years</span><?php endif; ?>
                    </p>
                    <div class="pv-badges">
                        <?php if ($religion): ?>
                            <span class="label label-default"><?= $dash($religion) ?></span>
                        <?php endif; ?>
                        <span class="label label-warning"><?= $dash($pkgLabel) ?></span>
                    </div>
                </div>
                <div class="pv-hero-cell pv-actions">
                    <a class="btn btn-default" style="background: #fff; color: #2563eb; font-weight: 600;" href="<?= BASE_URL ?>/dashboard/profile">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6" style="margin-bottom: 24px;">
                <div class="pv-card mb-4">
                    <h2>Contact</h2>
                    <table class="table pv-table mb-0">
                        <tbody>
                        <tr><th>Email</th><td><?= $dash($email) ?></td></tr>
                        <tr><th>Phone</th><td><?= $dash($phone) ?></td></tr>
                        <tr><th>Date of birth</th><td><?= $dash($dob && $dob !== '0000-00-00' ? $dob : '') ?></td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="pv-card">
                    <h2>Extended profile</h2>
                    <table class="table pv-table mb-0">
                        <tbody>
                        <?php if ($profileDetails): ?>
                            <tr><th>Education</th><td><?= $dash($profileDetails['education'] ?? '') ?></td></tr>
                            <tr><th>Occupation</th><td><?= $dash($profileDetails['occupation'] ?? '') ?></td></tr>
                            <tr><th>Annual income</th><td><?= $dash($profileDetails['annual_income'] ?? '') ?></td></tr>
                            <tr><th>Height</th><td><?= $dash($profileDetails['height'] ?? '') ?></td></tr>
                            <tr><th>Mother tongue</th><td><?= $dash($profileDetails['mother_tongue'] ?? '') ?></td></tr>
                            <tr><th>Eating habits</th><td><?= $dash($profileDetails['eating_habits'] ?? '') ?></td></tr>
                            <tr><th>Drinking</th><td><?= $dash($profileDetails['drinking'] ?? '') ?></td></tr>
                            <tr><th>Smoking</th><td><?= $dash($profileDetails['smoking'] ?? '') ?></td></tr>
                            <tr><th>Appearance</th><td><?= $dash($profileDetails['appearance'] ?? '') ?></td></tr>
                            <tr><th>Complexion</th><td><?= $dash($profileDetails['complexion'] ?? '') ?></td></tr>
                            <tr><th>Body type</th><td><?= $dash($profileDetails['body_type'] ?? '') ?></td></tr>
                            <tr><th>Horoscope</th><td><?= $dash($profileDetails['horoscope_details'] ?? '') ?></td></tr>
                            <tr><th>Caste / community</th><td><?= $dash($profileDetails['cast'] ?? '') ?></td></tr>
                        <?php else: ?>
                            <tr><td colspan="2" class="text-muted py-4 px-3">You haven’t completed the extended profile yet. <a href="<?= BASE_URL ?>/dashboard/profile-complete">Add details</a>.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6" style="margin-bottom: 24px;">
                <div class="pv-card mb-4">
                    <h2>Membership</h2>
                    <table class="table pv-table mb-0">
                        <tbody>
                        <?php if ($myPackage): ?>
                            <tr><th>Package</th><td><?= $dash($myPackage['package_name'] ?? '') ?></td></tr>
                            <tr><th>Status</th><td><?= $dash($myPackage['status'] ?? '') ?></td></tr>
                            <tr><th>Paid</th><td><?= !empty($myPackage['is_paid']) ? 'Yes' : 'No' ?></td></tr>
                            <tr><th>Started</th><td><?= $dash($myPackage['started_at'] ?? '') ?></td></tr>
                            <tr><th>Expires</th><td><?= $dash($myPackage['expires_at'] ?? '') ?></td></tr>
                        <?php else: ?>
                            <tr><td colspan="2" class="text-muted py-4 px-3">No package on file for this account.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="pv-card">
                    <h2>About</h2>
                    <div class="pv-about">
                        <?php if (trim((string)$bio) !== ''): ?>
                            <?= nl2br($dash($bio)) ?>
                        <?php else: ?>
                            <span class="text-muted">No bio yet. <a href="<?= BASE_URL ?>/dashboard/profile">Add one when you edit your profile</a>.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
