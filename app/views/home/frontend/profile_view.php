<?php
$name = trim(($profile->first_name ?? '') . ' ' . ($profile->second_name ?? ''));
$dob = (string)($profile->dob ?? '');
$age = '-';
if ($dob !== '' && $dob !== '0000-00-00') {
    try {
        $age = (new DateTime())->diff(new DateTime($dob))->y . ' years';
    } catch (Exception $e) {
        $age = '-';
    }
}

$rawPhoto = (string)($profile->photo2_url ?? $profile->photo1_status ?? $profile->avatar ?? '');
$photoUrl = $rawPhoto !== ''
    ? BASE_URL . '/' . ltrim($rawPhoto, '/')
    : BASE_URL . '/assets/images/default-avatar.png';
?>

<style>
    .public-profile-wrap {
        margin-top: 30px;
        margin-bottom: 40px;
    }
    .public-profile-card {
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 12px;
        box-shadow: 0 6px 22px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .public-profile-head {
        background: linear-gradient(135deg, #123f73, #2d7cc2);
        color: #fff;
        padding: 20px;
    }
    .public-profile-name {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
    }
    .public-profile-sub {
        margin-top: 6px;
        opacity: 0.92;
        font-size: 14px;
    }
    .public-profile-body {
        padding: 20px;
    }
    .public-profile-photo {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 2px 14px rgba(0,0,0,.15);
    }
    .public-kv {
        border: 1px solid #efefef;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 12px;
        background: #fafafa;
    }
    .public-kv .label {
        display: inline-block;
        font-size: 12px;
        margin-bottom: 6px;
        background: #d9edf7;
        color: #31708f;
    }
    .public-kv .value {
        font-size: 15px;
        color: #333;
        font-weight: 600;
    }
    .public-about {
        margin-top: 8px;
        line-height: 1.7;
        color: #4b4b4b;
    }
</style>

<div class="container public-profile-wrap">
    <div class="public-profile-card">
        <div class="public-profile-head">
            <h1 class="public-profile-name"><?= htmlspecialchars($name !== '' ? $name : 'Profile', ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="public-profile-sub">
                <?= htmlspecialchars((string)($profile->gender ?? '-'), ENT_QUOTES, 'UTF-8') ?> •
                <?= htmlspecialchars((string)($profile->religion ?? '-'), ENT_QUOTES, 'UTF-8') ?> •
                <?= htmlspecialchars($age, ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>

        <div class="public-profile-body">
            <div class="row">
                <div class="col-md-4 text-center" style="margin-bottom: 20px;">
                    <img src="<?= htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Profile photo" class="public-profile-photo">
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="public-kv">
                                <span class="label">Matri ID</span>
                                <div class="value"><?php $midPv = matri_id_display((string) ($profile->matri_id ?? ''), (int) ($profile->id ?? 0)); ?><?= htmlspecialchars($midPv !== '' ? $midPv : '-', ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="public-kv">
                                <span class="label">Date of Birth</span>
                                <div class="value"><?= htmlspecialchars($dob !== '' ? $dob : '-', ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="public-kv">
                                <span class="label">City</span>
                                <div class="value"><?= htmlspecialchars((string)($profile->city ?? '-'), ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="public-kv">
                                <span class="label">Country</span>
                                <div class="value"><?= htmlspecialchars((string)($profile->country ?? '-'), ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <h4 style="margin-top: 0; font-weight: 700;">About</h4>
            <p class="public-about"><?= nl2br(htmlspecialchars((string)($profile->about_us ?? 'No details added.'), ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
    </div>
</div>
