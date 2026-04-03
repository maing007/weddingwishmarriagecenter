<?php
$title = "Basic User Details";
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/sidebar.php';
require __DIR__ . '/data_array.php';
$currentStep = 'physical';
require __DIR__ . '/_step_header.php';
?>

<style>
    .flex {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    /* ===== LABELS ===== */
    .form-horizontal label {
        font-size: 14px;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 6px;
        display: block;
    }

    /* Required star */
    .control-label::after {
        content: " *";
        color: #ef4444;
        font-weight: 600;
    }

    .form-horizontal .flex label::after {
        content: "";
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

    .spacer { margin-top: 0; }
</style>


<div class="container spacer pacer">
    <div class="card">
    <h4 class="mb-4">Physical Info</h4>

    <div class="col-lg-12">
        <form id="physicalForm" method="post" action="<?= BASE_URL ?>/admin/user/physical" enctype="multipart/form-data" class="form-horizontal">

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Height *</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="height" class="form-control">
                        <option value="">Select Height</option>
                        <?php foreach ($heights as $height) : ?>
                            <option value="<?= $height ?>"><?= $height ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Weight *</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="weight" class="form-control">
                        <option value="">Select Weight</option>
                        <?php foreach ($weights as $weight) : ?>
                            <option value="<?= $weight ?>"><?= $weight ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Eating Habits *</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="eating_habits" class="form-control">
                        <option value="">Select Eating Habits</option>
                        <option value="Does not matter">Does not matter</option>
                        <option value="Occasionally Non-Veg">Occasionally Non-Veg</option>
                        <option value="Veg">Veg</option>
                        <option value="Eggetarian">Eggetarian</option>
                        <option value="Non-Veg">Non-Veg</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Smoking *</label>
                <div class="col-sm-9 col-lg-7 flex">
                    <label><input type="radio" name="smoking" value="Does not matter"> Does not matter</label>
                    <label><input type="radio" name="smoking" value="No"> No</label>
                    <label><input type="radio" name="smoking" value="Yes"> Yes</label>
                    <label><input type="radio" name="smoking" value="Occasionally"> Occasionally</label>
                </div>
            </div>

            <div class="form-group flex">
                <label class="col-sm-3 col-lg-3 control-label ">Drinking *</label>
                <div class="col-sm-9 col-lg-7 flex">
                    <label><input type="radio" name="drinking" value="Does not matter"> Does not matter</label>
                    <label><input type="radio" name="drinking" value="No"> No</label>
                    <label><input type="radio" name="drinking" value="Yes"> Yes</label>
                    <label><input type="radio" name="drinking" value="Occasionally"> Occasionally</label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Body Type *</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="body_type" class="form-control">
                        <option value="">Select Body Type</option>
                        <option value="Does not matter">Does not matter</option>
                        <option value="Slim">Slim</option>
                        <option value="Average">Average</option>
                        <option value="Athletic">Athletic</option>
                        <option value="Heavy">Heavy</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Skin Tone *</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="skin_tone" class="form-control">
                        <option value="">Select Skin Tone</option>
                        <option value="Does not matter">Does not matter</option>
                        <option value="Wheatish">Wheatish</option>
                        <option value="Very Fair">Very Fair</option>
                        <option value="Fair">Fair</option>
                        <option value="Wheatish Brown">Wheatish Brown</option>
                        <option value="Dark">Dark</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-3 control-label">Blood Group</label>
                <div class="col-sm-9 col-lg-7">
                    <select name="blood_group" class="form-control">
                        <option value="">Select Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
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

<script>
    // ===== JS validation =====
    document.getElementById("physicalForm").addEventListener("submit", function(e) {
        let valid = true;
        const requiredFields = ["height", "weight", "body_type", "skin_tone", "eating_habits", "smoking", "drinking"];
        requiredFields.forEach(id => {
            const field = document.getElementsByName(id)[0];
            if (!field.value) {
                field.style.borderColor = "#ef4444";
                valid = false;
            } else {
                field.style.borderColor = "#d1d5db";
            }
        });
        if (!valid) {
            e.preventDefault();
            alert("Please fill all required fields!");
        }
    });
</script>

<?php require __DIR__ . '/_step_footer.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>