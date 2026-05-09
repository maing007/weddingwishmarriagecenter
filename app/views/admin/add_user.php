<?php
$title = "Add Users";
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
    body {
        background: #f5f7fb;
    }

    /* CARD */
    .form-card {
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        border: none;
    }

    /* STEP HEADER */
    .step-wizard .step-label {
        flex: 1;
        text-align: center;
        font-size: 13px;
        color: #aaa;
    }

    .step-wizard .step-label.active {
        color: #0d6efd;
        font-weight: 600;
    }

    /* PROGRESS */
    .progress {
        height: 6px;
        border-radius: 20px;
    }

    /* BUTTONS */
    .btn {
        border-radius: 10px;
    }

    /* 🔥 CORE FIX */
    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
        animation: fadeIn 0.3s ease-in-out;
    }

    /* 🔥 HIDE BUTTONS FROM OTHER STEPS */
    .tab-pane:not(.active) button {
        display: none !important;
    }

    /* OPTIONAL: CLEAN SPACING */
    .step-form {
        padding: 10px 5px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="admin-content mt-5 p-4" style="margin-left:260px;">

    <div class="card form-card p-4">

        <!-- STEP PROGRESS -->
        <div class="step-wizard mb-4">

            <div class="progress mb-3">
                <div class="progress-bar bg-primary" id="progressBar" style="width:16%"></div>
            </div>

            <div class="d-flex">
                <div class="step-label active">Basic</div>
                <div class="step-label">Residence</div>
                <div class="step-label">Physical</div>
                <div class="step-label">Other</div>
                <div class="step-label">Partner</div>
                <div class="step-label">Photos</div>
            </div>
        </div>

        <!-- FLASH -->
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['flash_error'];
                unset($_SESSION['flash_error']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['flash_success'];
                unset($_SESSION['flash_success']); ?>
            </div>
        <?php endif; ?>


        <div class="tab-content">

            <!-- STEP 1 -->
            <div class="tab-pane active">
                <div class="step-form">
                    <form action="<?= BASE_URL; ?>/admin/user/basic" method="POST">

                        <?php require_once __DIR__ . '/user_details_form/basic_details_form.php'; ?>
                        <button type="submit">Save</button>
                    </form>
                </div>

                <div class="mt-4 text-end">
                    <button type="button" class="btn btn-primary next-step">Next</button>
                </div>
            </div>

            <!-- STEP 2 -->
            <div class="tab-pane">
                <div class="step-form">
                    <form action="<?= BASE_URL; ?>/admin/user/residence" method="POST">
                        <?php require_once __DIR__ . '/user_details_form/residence_form.php'; ?>
                        <button type="submit">Save</button>
                    </form>
                </div>

            </div>

            <!-- STEP 3 -->
            <div class="tab-pane">
                <div class="step-form">
                    <form action="<?= BASE_URL; ?>/admin/user/physical" method="POST">
                        <?php require_once __DIR__ . '/user_details_form/physical_form.php'; ?>
                        <button type="submit">Save</button>
                    </form>
                </div>


            </div>

            <!-- STEP 4 -->
            <div class="tab-pane">
                <div class="step-form">
                    <form action="<?= BASE_URL; ?>/admin/user/other" method="POST">
                        <?php require_once __DIR__ . '/user_details_form/other_info.php'; ?>
                        <button type="submit">Save</button>
                    </form>
                </div>


            </div>

            <!-- STEP 5 -->
            <div class="tab-pane">
                <div class="step-form">
                    <form action="<?= BASE_URL; ?>/admin/user/partner" method="POST">
                        <?php require_once __DIR__ . '/user_details_form/partner_form.php'; ?>
                        <button type="submit">Save</button>
                    </form>
                </div>


            </div>

            <!-- STEP 6 -->
            <div class="tab-pane">
                <div class="step-form">
                    <form action="<?= BASE_URL; ?>/admin/user/upload" method="POST" enctype="multipart/form-data">
                        <?php require_once __DIR__ . '/user_details_form/upload_form.php'; ?>
                        <button type="submit">Save</button>
                    </form>
                </div>

            </div>

        </div>

    </div>
</div>

<script>
    let currentStep = 0;

    // Get step from URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('step')) {
        currentStep = parseInt(urlParams.get('step'));
    }

    const steps = document.querySelectorAll(".step");
    const labels = document.querySelectorAll("#stepLabels span");
    const progressBar = document.getElementById("progressBar");

    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle("active", i === index);
        });

        labels.forEach((label, i) => {
            label.classList.toggle("active", i <= index);
        });

        progressBar.style.width = ((index + 1) / steps.length) * 100 + "%";
    }

    function prevStep() {
        if (currentStep > 0) {
            window.location.href = "?step=" + (currentStep - 1);
        }
    }

    showStep(currentStep);
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>