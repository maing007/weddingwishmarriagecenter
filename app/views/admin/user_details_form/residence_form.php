<?php
$title = "Basic User Details";
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/data_array.php';
$currentStep = 'residence';
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

<!-- FLASH -->
<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger container mt-3">
        <?= $_SESSION['flash_error'];
        unset($_SESSION['flash_error']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success container mt-3">
        <?= $_SESSION['flash_success'];
        unset($_SESSION['flash_success']); ?>
    </div>
<?php endif; ?>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<div class="spacer">
    <div class="row">
        <div class="col-lg-12">
            <form method="post" action="<?= BASE_URL ?>/admin/user/residence" enctype="multipart/form-data" class="form-horizontal">

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Country *</label>
                    <div class="col-sm-9 col-lg-7">
                        <select name="country" class="form-control">
                            <option value="">Select Country</option>
                            <?php foreach ($countries_names as $country) : ?>
                                <option value="<?= $country ?>"><?= $country ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">State *</label>
                    <div class="col-sm-9 col-lg-7">
                        <select name="state" class="form-control">
                            <option value="">Select State</option>
                            <?php foreach ($states as $state) : ?>
                                <option value="<?= $state ?>"><?= $state ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">City *</label>
                    <div class="col-sm-9 col-lg-7">
                        <select name="city" class="form-control">
                            <option value="">Select City</option>
                            <?php foreach ($cities as $city) : ?>
                                <option value="<?= $city ?>"><?= $city ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Area</label>
                    <div class="col-sm-9 col-lg-7">
                        <select name="area" class="form-control">
                            <option value="">Select Area</option>
                            <?php foreach ($areas as $area) : ?>
                                <option value="<?= $area ?>"><?= $area ?></option>
                            <?php endforeach; ?>

                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Address</label>
                    <div class="col-sm-9 col-lg-7">
                        <textarea name="address" class="form-control" rows="4" placeholder="Address"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Location Pin</label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="text" name="location_pin" class="form-control" placeholder="Location Pin">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">House Type</label>
                    <div class="col-sm-9 col-lg-7">
                        <select name="house_type" class="form-control">
                            <option value="">Select House Type</option>
                            <option value="Does not matter">Does not matter</option>
                            <option value="Rented">Rented</option>
                            <option value="Owned">Owned</option>
                            <option value="On Lease">On Lease</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">House Size (Marla) *</label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="number" name="house_size_marla" class="form-control" placeholder="House Size (Marla)">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Phone</label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="text" name="phone" class="form-control" placeholder="Phone">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Time To Call</label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="text" name="time_to_call" class="form-control" placeholder="Time To Call">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Contact Person Name</label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="text" name="contact_person_name" class="form-control" placeholder="Contact Person Name">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Contact Person Relation</label>
                    <div class="col-sm-9 col-lg-7">
                        <select name="contact_person_relation" class="form-control">
                            <option value="">Select Contact Person Relation</option>
                            <option value="self">Self</option>
                            <option value="brother">Brother</option>
                            <option value="sister">Sister</option>
                            <option value="parent">Parent</option>
                            <option value="relative">Relative</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-3 control-label">Residence</label>
                    <div class="col-sm-9 col-lg-7">
                        <select name="residence" class="form-control">
                            <option value="">Select Residence</option>
                            <option value="Does not matter">Does not matter</option>
                            <option value="Citizen">Citizen</option>
                            <option value="Permanent Resident">Permanent Resident</option>
                            <option value="Student Visa">Student Visa</option>
                            <option value="Temporary Visa">Temporary Visa</option>
                            <option value="Work permit">Work permit</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3"></label>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/_step_footer.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>