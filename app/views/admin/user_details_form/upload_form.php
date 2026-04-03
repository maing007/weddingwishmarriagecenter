<?php
$title = "Basic User Details";
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/sidebar.php';
$currentStep = 'upload';
require __DIR__ . '/_step_header.php';
?>

<style>
    .flex {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    /* ===== LABELS ===== */
    label {
        font-size: 14px;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 6px;
        display: block;
    }

    /* Required star */
    label::after {
        content: " *";
        color: #ef4444;
        font-weight: 600;
    }

    /* ===== INPUT FIELDS ===== */
    .form-control {
        height: 42px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        background-color: #f9fafb;
        font-size: 14px;
        padding: 8px 12px;
        transition: all 0.2s ease;
    }

    /* Focus effect (green like image) */
    .form-control:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.15);
        background-color: #ffffff;
    }

    /* ===== SELECT DROPDOWN ===== */
    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml;utf8,<svg fill='%236b7280' height='20' viewBox='0 0 20 20' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M5.5 7l4.5 4 4.5-4'/></svg>");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 16px;
    }

    /* ===== RADIO BUTTONS ===== */
    input[type="radio"] {
        accent-color: #2563eb;
        justify-items: center;
        justify-content: center;
        align-items: center;
        margin-right: 6px;
        margin-bottom: 9px;
        transform: scale(1.05);
    }

    .radio-group,
    .form-check-inline {
        display: inline-flex;
        align-items: center;
        margin-right: 18px;
        font-size: 14px;
        color: #374151;
    }

    /* ===== ROW SPACING ===== */
    .row>div {
        margin-bottom: 18px;
    }

    /* ===== SECTION DIVIDER LINES ===== */
    .form-section {
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    /* ===== PLACEHOLDER TEXT ===== */
    ::placeholder {
        color: #9ca3af;
        font-size: 13px;
    }

    /* ===== MOBILE INPUT STYLE ===== */


    /* Optional flag style simulation */
    .mobile-wrapper {
        position: relative;
    }

    /* ===== BUTTON ===== */
    button[type="submit"] {
        height: 45px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 15px;
        background-color: #2563eb;
        border: none;
        transition: 0.2s;
    }

    button[type="submit"]:hover {
        background-color: #1d4ed8;
    }

    /* ===== ALERTS ===== */
    .alert {
        border-radius: 6px;
        font-size: 14px;
    }

    /* ===== CUSTOM INPUT (OTHER FIELD) ===== */
    #customCaste,
    #customEducation {
        background-color: #ffffff;
        border: 1px dashed #d1d5db;
    }

    /* ===== SMALL TEXT ===== */
    .text-muted {
        font-size: 12px;
        color: #9ca3af !important;
    }

    .spacer {
        margin-top: 120px;
    }
</style>

<div class="container spacer mt-5">
    <div class="card">
        <h4 class="mb-4">Upload Photos</h4>
        <div class="col-lg-12">

            <form id="uploadForm" action="<?= BASE_URL; ?>/admin/user/submit" method="POST" enctype="multipart/form-data">


                <!-- Photo 1 -->
                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Photo 1 Status *</label>
                    <div class="col-sm-9 col-lg-7 flex">
                        <label><input type="radio" name="photo1_review_status" value="APPROVED"> APPROVED</label>
                        <label><input type="radio" name="photo1_review_status" value="UNAPPROVED" checked> UNAPPROVED</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Upload Photo 1</label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="file" name="photo1_status" class="form-control">
                    </div>
                </div>

                <!-- Photo 2 -->
                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Photo 2 Status *</label>
                    <div class="col-sm-9 col-lg-7 flex">
                        <label><input type="radio" name="photo2_review_status" value="APPROVED"> APPROVED</label>
                        <label><input type="radio" name="photo2_review_status" value="UNAPPROVED" checked> UNAPPROVED</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Upload Photo 2</label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="file" name="photo2_url" class="form-control">
                    </div>
                </div>

                <!-- Photo 3 -->
                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Photo 3 Status *</label>
                    <div class="col-sm-9 col-lg-7 flex">
                        <label><input type="radio" name="photo3_review_status" value="APPROVED"> APPROVED</label>
                        <label><input type="radio" name="photo3_review_status" value="UNAPPROVED" checked> UNAPPROVED</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Upload Photo 3</label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="file" name="photo3_url" class="form-control">
                    </div>
                </div>

                <!-- Photo 4 -->
                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Photo 4 Status *</label>
                    <div class="col-sm-9 col-lg-7 flex">
                        <label><input type="radio" name="photo4_review_status" value="APPROVED"> APPROVED</label>
                        <label><input type="radio" name="photo4_review_status" value="UNAPPROVED" checked> UNAPPROVED</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Upload Photo 4</label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="file" name="photo4_url" class="form-control">
                    </div>
                </div>

                <!-- Photo 5 -->
                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Photo 5 Status *</label>
                    <div class="col-sm-9 col-lg-7 flex">
                        <label><input type="radio" name="photo5_review_status" value="APPROVED"> APPROVED</label>
                        <label><input type="radio" name="photo5_review_status" value="UNAPPROVED" checked> UNAPPROVED</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Upload Photo 5</label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="file" name="photo5_url" class="form-control">
                    </div>
                </div>

                <!-- Photo 6 -->
                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Photo 6 Status *</label>
                    <div class="col-sm-9 col-lg-7 flex">
                        <label><input type="radio" name="photo6_review_status" value="APPROVED"> APPROVED</label>
                        <label><input type="radio" name="photo6_review_status" value="UNAPPROVED" checked> UNAPPROVED</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Upload Photo 6</label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="file" name="photo6_url" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <!-- ID Proof -->
                    <h3>ID Proof</h3>
                    <div class="form-group">
                        <label class="col-sm-3 col-lg-3 control-label">ID Proof Status *</label>
                        <div class="col-sm-9 col-lg-7 flex">
                            <label><input type="radio" name="id_proof_status" value="APPROVED"> APPROVED</label>
                            <label><input type="radio" name="id_proof_status" value="UNAPPROVED" checked> UNAPPROVED</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">ID Proof</label>
                        <div class="col-sm-4">
                            <input type="file" name="id_proof_file" class="form-control">
                            <p class="help-block">Allowed file types: jpg, png, jpeg, gif, bmp. Max size: 100 KB.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- CV -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">CV</label>
                        <div class="col-sm-4">
                            <input type="file" name="cv_file" class="form-control">
                            <p class="help-block">Allowed file types: jpg, png, jpeg, gif, bmp, pdf, doc, docx. Max size: 1024 KB.</p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3"></label>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="#" class="btn btn-default">Back</a>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>


<?php require __DIR__ . '/_step_footer.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>