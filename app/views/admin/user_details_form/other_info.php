<?php
$title = "Basic User Details";
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/sidebar.php';
$currentStep = 'other';
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
<?php
$suggested_about_us = $suggested_about_us ?? '';
require __DIR__ . '/_wizard_flash.php';
?>
<div class="container spacer mt-5">
    <div class="col-lg-12">
        <form method="post" action="<?= BASE_URL; ?>/admin/user/other" enctype="multipart/form-data" class="form-horizontal" novalidate>

            <h3>About</h3>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">About Us</label>
                <div class="col-sm-9 col-lg-7">
                    <textarea rows="4" name="about_us" class="form-control" placeholder="About Us"><?= htmlspecialchars($suggested_about_us, ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Hobby</label>
                <div class="col-sm-9 col-lg-7">
                    <textarea rows="4" name="hobby" class="form-control" placeholder="Hobby"><?= htmlspecialchars(wz('other', 'hobby'), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Birth Place</label>
                <div class="col-sm-9 col-lg-7">
                    <input type="text" name="birth_place" class="form-control" placeholder="Birth Place" value="<?= htmlspecialchars(wz('other', 'birth_place'), ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Birth Time</label>
                <div class="col-sm-9 col-lg-7">
                    <input type="time" name="birth_time" class="form-control" placeholder="Birth Time" value="<?= htmlspecialchars(wz('other', 'birth_time'), ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Profile By *</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="profile_by" class="form-control" required>
                        <option value="">Select Profile By</option>
                        <option value="Does not matter"<?= wz_sel('other', 'profile_by', 'Does not matter') ?>>Does not matter</option>
                        <option value="Self"<?= (trim((string) wz('other', 'profile_by')) === '' || strcasecmp(trim((string) wz('other', 'profile_by')), 'Self') === 0) ? ' selected' : '' ?>>Self</option>
                        <option value="Parents"<?= wz_sel('other', 'profile_by', 'Parents') ?>>Parents</option>
                        <option value="Guardian"<?= wz_sel('other', 'profile_by', 'Guardian') ?>>Guardian</option>
                        <option value="Friends"<?= wz_sel('other', 'profile_by', 'Friends') ?>>Friends</option>
                        <option value="Sibling"<?= wz_sel('other', 'profile_by', 'Sibling') ?>>Sibling</option>
                        <option value="Relatives"<?= wz_sel('other', 'profile_by', 'Relatives') ?>>Relatives</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Reference *</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="reference" class="form-control" required>
                        <option value="">Select Reference</option>
                        <option value="Does not matter"<?= wz_sel('other', 'reference', 'Does not matter') ?>>Does not matter</option>
                        <option value="Advertisements"<?= wz_sel('other', 'reference', 'Advertisements') ?>>Advertisements</option>
                        <option value="Friends"<?= wz_sel('other', 'reference', 'Friends') ?>>Friends</option>
                        <option value="Search Engines"<?= wz_sel('other', 'reference', 'Search Engines') ?>>Search Engines</option>
                        <option value="Others"<?= wz_sel('other', 'reference', 'Others') ?>>Others</option>
                    </select>
                </div>
            </div>

            <h3>Family Details</h3>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Family Type</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="family_type" class="form-control">
                        <option value="">Select Family Type</option>
                        <option value="Does not matter">Does not matter</option>
                        <option value="Separate Family">Separate Family</option>
                        <option value="Joint Family">Joint Family</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Father Name</label>
                <div class="col-sm-9 col-lg-7">
                    <input type="text" name="father_name" class="form-control" placeholder="Father Name">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Father Occupation</label>
                <div class="col-sm-9 col-lg-7">
                    <input type="text" name="father_occupation" class="form-control" placeholder="Father Occupation">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Mother Name</label>
                <div class="col-sm-9 col-lg-7">
                    <input type="text" name="mother_name" class="form-control" placeholder="Mother Name">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Mother Occupation</label>
                <div class="col-sm-9 col-lg-7">
                    <input type="text" name="mother_occupation" class="form-control" placeholder="Mother Occupation">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Family Status</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="family_status" class="form-control">
                        <option value="">Select Family Status</option>
                        <option value="Does not matter">Does not matter</option>
                        <option value="Rich">Rich</option>
                        <option value="Upper Middle Class">Upper Middle Class</option>
                        <option value="Middle Class">Middle Class</option>
                        <option value="Lower Middle Class">Lower Middle Class</option>
                        <option value="Poor Family">Poor Family</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">No Of Brothers</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="no_of_brothers" class="form-control">
                        <option value="">Select No Of Brothers</option>
                        <option value="Does not matter">Does not matter</option>
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="4+">4+</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">No Of Married Brother</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="no_of_married_brother" class="form-control">
                        <option value="">Select No Of Married Brother</option>
                        <option value="Does not matter">Does not matter</option>
                        <option value="No married brother">No married brother</option>
                        <option value="One married brother">One married brother</option>
                        <option value="Two married brothers">Two married brothers</option>
                        <option value="Three married brothers">Three married brothers</option>
                        <option value="Four married brothers">Four married brothers</option>
                        <option value="Above four married brothers">Above four married brothers</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">No Of Sisters</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="no_of_sisters" class="form-control">
                        <option value="">Select No Of Sisters</option>
                        <option value="Does not matter">Does not matter</option>
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="4+">4+</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">No Of Married Sister</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="no_of_married_sister" class="form-control">
                        <option value="">Select No Of Married Sister</option>
                        <option value="Does not matter">Does not matter</option>
                        <option value="No married sister">No married sister</option>
                        <option value="One married sister">One married sister</option>
                        <option value="Two married sisters">Two married sisters</option>
                        <option value="Three married sisters">Three married sisters</option>
                        <option value="Four married sisters">Four married sisters</option>
                        <option value="Above four married sisters">Above four married sisters</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Family Details</label>
                <div class="col-sm-9 col-lg-7">
                    <textarea rows="4" name="family_details" class="form-control" placeholder="Family Details"><?= htmlspecialchars(wz('other', 'family_details'), ENT_QUOTES, 'UTF-8') ?></textarea>
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


<?php require __DIR__ . '/_step_footer.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>