<?php
$title = "Member Evaluation Form";
require __DIR__.'/partials/header.php';
require __DIR__.'/partials/sidebar.php';

$fullName = trim((string)($user['first_name'] ?? '') . ' ' . (string)($user['second_name'] ?? ''));
$matri = !empty($user['matri_id']) ? (string)$user['matri_id'] : ('NG' . (int)$user['id']);
$ans = static function (string $key) use ($answers): string {
    return strtolower((string)($answers[$key] ?? ''));
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
        <div class="page-head">Member Evaluation Form</div>
        <div class="eval-wrap">
            <a class="btn btn-primary btn-sm mb-3" href="<?= BASE_URL ?>/admin/match-making"><i class="fa fa-arrow-left"></i> Go Back</a>

            <div class="eval-title-row">
                <div>
                    <h2><?= htmlspecialchars($matri) ?> - <?= htmlspecialchars($fullName) ?></h2>
                    <div class="subtitle">Member Evaluation Form</div>
                </div>
                <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="logo">
            </div>

            <div class="section-head">BASIC INFORMATION</div>

            <form method="post" action="<?= BASE_URL ?>/admin/member-evaluation">
                <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">

                <div class="q-row"><strong>Q1:</strong> Have you called the client and verified all his data entry <span class="rq">*</span>
                    <div class="yn"><label><input type="radio" name="q1" value="yes" <?= $ans('q1') === 'yes' ? 'checked' : '' ?> required> Yes</label><label><input type="radio" name="q1" value="no" <?= $ans('q1') === 'no' ? 'checked' : '' ?>> No</label></div>
                </div>
                <div class="q-row"><strong>Q2:</strong> Have you verified email id <span class="rq">*</span>
                    <div class="yn"><label><input type="radio" name="q2" value="yes" <?= $ans('q2') === 'yes' ? 'checked' : '' ?> required> Yes</label><label><input type="radio" name="q2" value="no" <?= $ans('q2') === 'no' ? 'checked' : '' ?>> No</label></div>
                </div>
                <div class="q-row"><strong>Q3:</strong> Have you got complete home address and location PIN <span class="rq">*</span>
                    <div class="yn"><label><input type="radio" name="q3" value="yes" <?= $ans('q3') === 'yes' ? 'checked' : '' ?> required> Yes</label><label><input type="radio" name="q3" value="no" <?= $ans('q3') === 'no' ? 'checked' : '' ?>> No</label></div>
                </div>
                <div class="q-row"><strong>Q4:</strong> Have you confirmed rishta fee agreed which is minimum Rs. 50,000, If he saying less then inform to admin <span class="rq">*</span>
                    <div class="yn"><label><input type="radio" name="q4" value="yes" <?= $ans('q4') === 'yes' ? 'checked' : '' ?> required> Yes</label><label><input type="radio" name="q4" value="no" <?= $ans('q4') === 'no' ? 'checked' : '' ?>> No</label></div>
                </div>
                <div class="q-row"><strong>Q5:</strong> Have you verified all partner requirements and edited in profile <span class="rq">*</span>
                    <div class="yn"><label><input type="radio" name="q5" value="yes" <?= $ans('q5') === 'yes' ? 'checked' : '' ?> required> Yes</label><label><input type="radio" name="q5" value="no" <?= $ans('q5') === 'no' ? 'checked' : '' ?>> No</label></div>
                </div>
                <div class="q-row"><strong>Q6:</strong> Have you received atleast two pics of the client <span class="rq">*</span>
                    <div class="yn"><label><input type="radio" name="q6" value="yes" <?= $ans('q6') === 'yes' ? 'checked' : '' ?> required> Yes</label><label><input type="radio" name="q6" value="no" <?= $ans('q6') === 'no' ? 'checked' : '' ?>> No</label></div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Save &amp; Exit</button>
                    <button type="submit" class="btn btn-info text-white btn-sm">Next <i class="fa fa-arrow-right"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
</main>
</div>

<style>
.admin-content{padding:14px;background:#efefef}.page-head{font-size:13px;font-weight:700;color:#535353;margin-bottom:8px}
.eval-wrap{background:#f8f8f8;border:1px solid #d7d7d7;border-radius:3px;padding:10px}
.eval-title-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0 10px}
.eval-title-row h2{margin:0;font-size:40px;font-size:38px;font-weight:500;color:#4f4f4f}
.eval-title-row .subtitle{font-size:22px;font-size:20px;color:#666}
.eval-title-row img{height:52px;object-fit:contain}
.section-head{background:#7f0030;color:#fff;text-align:center;font-weight:700;padding:8px 10px;font-size:23px;font-size:21px;margin-bottom:6px}
.q-row{padding:10px 0;border-bottom:1px solid #ececec;color:#444;font-size:23px;font-size:21px}
.yn{display:flex;gap:28px;margin-top:6px;font-size:30px;font-size:28px}
.yn label{font-weight:500}
.rq{color:#cc4b4b}
.actions{display:flex;justify-content:space-between;align-items:center;padding-top:10px}
@media(max-width:991px){.eval-title-row{flex-wrap:wrap;gap:8px}.eval-title-row h2{font-size:28px}.eval-title-row .subtitle{font-size:16px}.q-row{font-size:15px}.yn{font-size:14px}}
</style>

<?php require __DIR__.'/partials/footer.php'; ?>
