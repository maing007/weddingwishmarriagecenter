<?php
$title = "Basic User Details";
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/sidebar.php';
require __DIR__ . '/data_array.php';
$currentStep = 'partner';
require __DIR__ . '/_step_header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@4.0.1/dist/css/multi-select-tag.min.css">

<style>
    .container {
        max-width: 700px;
    }

    .card {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        box-shadow: none;
        padding: 25px 30px;
        margin-bottom: 30px;
    }

    label {
        font-size: 14px;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 6px;
        display: block;
    }

    label::after {
        content: " *";
        color: #ef4444;
        font-weight: 600;
    }

    .form-control {
        height: 42px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        background-color: #f9fafb;
        font-size: 14px;
        padding: 8px 12px;
        transition: all 0.2s ease;
        width: 100%;
    }

    .form-control:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.15);
        background-color: #ffffff;
    }

    textarea.form-control {
        height: auto;
        resize: none;
    }

    button.btn {
        height: 45px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 15px;
        background-color: #2563eb;
        border: none;
        transition: 0.2s;
        width: 100%;
        color: white;
    }

    button.btn:hover {
        background-color: #1d4ed8;
    }

    .alert {
        border-radius: 6px;
        font-size: 14px;
    }

    .spacer {
        margin-top: 50px;
    }
</style>

<?php require __DIR__ . '/_wizard_flash.php'; ?>

<div class="container spacer mt-5">
    <div class="card">
        <h4 class="mb-4">Partner Preference</h4>

        <form action="<?= BASE_URL; ?>/admin/user/partner" method="POST" novalidate>

            <div class="container">

                <h3>Partner Preferences</h3>

                <!-- Looking For -->
                <div class="form-group">
                    <label>Looking For *</label>
                    <select name="looking_for[]" multiple required class="form-control">
                        <option>Does not matter</option>
                        <option>Unmarried</option>
                        <option>Widow/Widower</option>
                        <option>Divorcee</option>
                        <option>Separated</option>
                        <option>Married</option>
                    </select>
                </div>

                <!-- Complexion -->
                <div class="form-group">
                    <label>Partner Complexion</label>
                    <select name="partner_complexion[]" multiple class="form-control">
                        <option>Does not matter</option>
                        <option>Wheatish</option>
                        <option>Very Fair</option>
                        <option>Fair</option>
                        <option>Wheatish Brown</option>
                        <option>Dark</option>
                    </select>
                </div>

                <!-- Age -->
                <div class="form-group">
                    <label>Partner From Age *</label>
                    <select name="partner_from_age" class="form-control" required>
                        <option value="">Select Age</option>
                        <option value="0">Any</option>
                        <?php foreach ($ages as $age): ?>
                            <option value="<?= $age ?>"><?= $age ?> Years</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Partner To Age *</label>
                    <select name="partner_to_age" class="form-control" required>
                        <option value="">Select Age</option>
                        <?php foreach ($ages as $age): ?>
                            <option value="<?= $age ?>"><?= $age ?> Years</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Height -->
                <div class="form-group">
                    <label>Partner From Height *</label>
                    <select name="partner_from_height" class="form-control" required>
                    <option value="">Select Height</option>
                        <?php foreach ($heights as $height) : ?>
                            <option value="<?= $height ?>"><?= $height ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Partner To Height *</label>
                    <select name="partner_to_height" class="form-control" required>
                    <option value="">Select Height</option>
                        <?php foreach ($heights as $height) : ?>
                            <option value="<?= $height ?>"><?= $height ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Body Type -->
                <div class="form-group">
                    <label>Partner Body Type</label>
                    <select name="partner_body_type[]" multiple class="form-control">
                        <option>Does not matter</option>
                        <option>Slim</option>
                        <option>Average</option>
                        <option>Athletic</option>
                        <option>Heavy</option>
                    </select>
                </div>

                <!-- Habits -->
                <div class="form-group">
                    <label>Partner Eating Habit</label>
                    <select name="partner_eating_habit[]" multiple class="form-control">
                        <option>Does not matter</option>
                        <option>Veg</option>
                        <option>Non-Veg</option>
                        <option>Eggetarian</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Partner Smoking Habit</label>
                    <select name="partner_smoking_habit[]" multiple class="form-control">
                        <option>Does not matter</option>
                        <option>No</option>
                        <option>Yes</option>
                        <option>Occasionally</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Partner Drinking Habit</label>
                    <select name="partner_drinking_habit[]" multiple class="form-control">
                        <option>Does not matter</option>
                        <option>No</option>
                        <option>Yes</option>
                        <option>Occasionally</option>
                    </select>
                </div>

                <!-- Language -->
                <div class="form-group">
                    <label>Partner Mother Tongue</label>
                    <select name="partner_mother_tongue" class="form-control">
                        <option value="">Select Mother Tongue</option>
                        <?php foreach ($languages as $language): ?>
                            <option value="<?= $language ?>"><?= $language ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Expectations -->
                <div class="form-group">
                    <label>Expectations</label>
                    <textarea name="expectations" class="form-control"></textarea>
                </div>

                <h4>Religious Preferences</h4>

                <div class="form-group">
                    <label>Partner Religion *</label>
                    <select name="partner_religion" class="form-control" required>
                        <option value="">Select Religion</option>
                        <option value="">Select religion</option>
                                <option value="islam">Islam</option>
                                <option value="christianity">Christianity</option>
                                <option value="hinduism">Hinduism</option>
                                <option value="buddhism">Buddhism</option>
                                <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Partner Caste</label>
                    <select name="partner_caste" class="form-control">
                        <option value="">Select Partner Caste</option>
                        <option value="Does not matter">Does not matter</option>
                        <?php foreach ($castes as $caste): ?>
                            <option value="<?= $caste ?>"><?= $caste ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Partner Caste Exception</label>
                    <select name="partner_caste_exception" class="form-control">
                        <option value="">Select Partner Caste Exception</option>
                        <option value="Does not matter">Does not matter</option>
                        <?php foreach ($castes as $caste): ?>
                            <option value="<?= $caste ?>"><?= $caste ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Partner Sect</label>
                    <select name="partner_sect" class="form-control">
                        <option value="">Select Sect</option>
                        <option value="sunni">Sunni</option>
                        <option value="shia">Shia</option>
                        <option value="ahmadiyya">Ahmadiyya</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Partner Maslak</label>
                    <input type="text" name="partner_maslak" class="form-control">
                </div>

                <div class="form-group">
                    <label>Partner Maslak Exception</label>
                    <input type="text" name="partner_maslak_exception" class="form-control">
                </div>

                <h4>Education / Occupation</h4>

                <!-- Education -->
                <div class="form-group">
                    <label>Partner Education</label>
                    <select name="partner_education[]" multiple class="form-control">
                        <option>Does not matter</option>
                        <?php foreach ($educations as $education): ?>
                            <option value="<?= $education ?>"><?= $education ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Employed In -->
                <div class="form-group">
                    <label>Partner Employed In</label>
                    <select name="partner_employed_in[]" multiple class="form-control">
                        <option>Does not matter</option>
                        <option>Private</option>
                        <option>Government</option>
                        <option>Business</option>
                        <option>Defence</option>
                        <option>Others</option>
                    </select> 
                </div>

                <!-- Occupation -->
                <div class="form-group">
                    <label>Partner Occupation</label>
                    <select name="partner_occupation[]" multiple class="form-control">
                        <option>Does not matter</option>
                        <?php foreach ($jobs as $job): ?>
                            <option value="<?= $job ?>"><?= $job ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Designation -->
                <div class="form-group">
                    <label>Partner Designation</label>
                    <select name="partner_designation[]" multiple class="form-control">
                        <option>Does not matter</option>
                        <?php foreach ($designations as $designation): ?>
                            <option value="<?= $designation ?>"><?= $designation ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Income -->
                <div class="form-group">
                    <label>Partner Annual Income</label>
                    <input type="text" name="partner_annual_income" class="form-control">
                </div>

                <h4>Location Preferences</h4>

                <!-- Country -->
                <div class="form-group">
                    <label>Partner Country</label>
                    <select name="partner_country[]" multiple class="form-control">
                        <?php foreach ($countries_names as $country): ?>
                            <option value="<?= $country ?>"><?= $country ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- City -->
                <div class="form-group">
                    <label>Partner City</label>
                    <select name="partner_city[]" multiple class="form-control">
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= $city ?>"><?= $city ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Area -->
                <div class="form-group">
                    <label>Partner Area</label>
                    <select name="partner_area[]" multiple class="form-control">
                        <?php foreach ($areas as $area): ?>
                            <option value="<?= $area ?>"><?= $area ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- House Size -->
                <div class="form-group">
                    <label>Partner House Size From</label>
                    <input type="number" name="partner_house_size_from" class="form-control">
                </div>

                <div class="form-group">
                    <label>Partner House Size To</label>
                    <input type="number" name="partner_house_size_to" class="form-control">
                </div>

                <!-- Residence -->
                <div class="form-group">
                    <label>Partner Residence Status</label>
                    <input type="text" name="partner_residence_status" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>

            </div>

        </form>
    </div>
</div>

<!-- End of <body> -->
<script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@4.0.1/dist/js/multi-select-tag.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Get all multi-select fields
        const multiSelects = document.querySelectorAll('select[multiple]');

        multiSelects.forEach((select, index) => {

            // Assign unique ID if not present
            if (!select.id) {
                select.id = "multi_select_" + index;
            }

            // Initialize MultiSelectTag
            new MultiSelectTag(select.id, {
                placeholder: "Select options",
                onChange: function(values) {
                    console.log(select.name, values);
                }
            });

        });

    });
</script>

<?php require __DIR__ . '/_step_footer.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>