<?php
$currentStep = $currentStep ?? 'basic';
$steps = [
    'basic' => ['label' => 'Basic Details', 'href' => BASE_URL . '/admin/add_user/user/basic'],
    'residence' => ['label' => 'Residence', 'href' => BASE_URL . '/admin/add_user/user/residence'],
    'physical' => ['label' => 'Physical Info', 'href' => BASE_URL . '/admin/add_user/user/physical'],
    'other' => ['label' => 'Other Info', 'href' => BASE_URL . '/admin/add_user/user/other'],
    'partner' => ['label' => 'Partner Preference', 'href' => BASE_URL . '/admin/add_user/user/partner'],
    'upload' => ['label' => 'Upload Photos', 'href' => BASE_URL . '/admin/add_user/user/upload'],
];
require_once __DIR__ . '/_wizard_step_session.php';
?>
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
        grid-template-columns: repeat(6, minmax(120px, 1fr));
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
    .admin-user-steps .step-link.active {
        background: #3b7fc4;
        border-color: #2f6ea8;
        color: #fff;
    }
    .admin-user-steps .step-form-wrap {
        background: #fff;
        border: 1px solid #e4e4e4;
        border-radius: 2px;
        padding: 14px;
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
    .admin-user-steps .step-form-wrap .spacer {
        margin-top: 0 !important;
    }
    @media (max-width: 1100px) {
        .admin-user-steps .step-nav {
            grid-template-columns: repeat(3, minmax(120px, 1fr));
        }
    }
    @media (max-width: 700px) {
        .admin-user-steps .step-nav {
            grid-template-columns: repeat(2, minmax(120px, 1fr));
        }
    }
</style>

<div class="admin-main admin-user-steps">
    <div class="admin-topbar">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu">
            <i class="fa fa-bars"></i>
        </button>
        <div class="admin-profile" id="adminProfileTrigger">
            <div class="admin-profile-box">
                <span><?= htmlspecialchars($this->displayadminname()) ?></span>
                <i class="fa fa-user"></i>
            </div>
            <div class="admin-dropdown" id="adminDropdown">
                <a href="<?= BASE_URL ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
                <a href="<?= BASE_URL ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </div>
    <main class="admin-page">
        <div class="admin-content">
            <div class="step-page-title">Add Member</div>
            <div class="step-card">
                <div class="step-nav">
                    <?php foreach ($steps as $key => $step): ?>
                        <a class="step-link <?= $key === $currentStep ? 'active' : '' ?>" href="<?= $step['href'] ?>">
                            <?= htmlspecialchars($step['label']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div class="step-form-wrap">
