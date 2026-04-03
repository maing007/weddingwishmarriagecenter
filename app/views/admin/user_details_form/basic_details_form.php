<?php
$title = "Basic User Details";
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/data_array.php';
$currentStep = 'basic';
require __DIR__ . '/_step_header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@4.0.1/dist/css/multi-select-tag.min.css">

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

<div class="container spacer  mt-5">

    <form action="<?= BASE_URL ?>/admin/user/basic" method="POST" enctype="multipart/form-data" class="col-lg-12 mb-5">
        <div class="col-mb-4 card">
            <div class="">
                <h4 class="mb-4">Basic User Details</h4>
                <div class="row">
                    <div class="col-md-12">
                        <label for="lead">Lead</label>
                        <select id="lead" name="lead" class="form-control" placeholder="Enter lead" required>
                            <option value="">Select Lead</option>
                            <?php if (!empty($admin_details)) : ?>
                                <?php foreach ($admin_details as $admin) : ?>
                                    <option value="<?= $admin['name']; ?>">
                                        <?= $admin['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </select>
                    </div>
                </div>

                <div class="row">
                    <label for="gender">Gender</label>
                    <div class=" col-md-12 flex items-center">

                        <input type="radio" name="gender" id="male" value="male">
                        <label for="male">Male</label>
                        <input type="radio" name="gender" id="female" value="female">
                        <label for="female">Female</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Enter first name" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="second_name" class="form-control" placeholder="Enter last name" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter email address" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="mobile_number">Mobile Number</label>

                        <div style="display:flex; gap:10px;">

                            <!-- Country Dropdown -->
                            <select name="country_code" class="form-control" style="max-width:180px;">
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?= $country['code'] ?>">
                                        <?= $country['flag'] ?> <?= $country['code'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Mobile Input -->
                            <input type="text"
                                id="mobile_number"
                                name="mobile_number"
                                class="form-control"
                                placeholder="Enter mobile number"
                                required>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 flex items-center">
                        <label for="marital_status">Marital Status</label>
                        <input type="radio" name="marital_status" id="single" value="single">
                        <label for="single">Single</label>
                        <input type="radio" name="marital_status" id="married" value="married">
                        <label for="married">Married</label>
                        <input type="radio" name="marital_status" id="divorced" value="divorced">
                        <label for="divorced">Divorced</label>
                        <input type="radio" name="marital_status" id="separated" value="separated">
                        <label for="separated">Separated</label>
                        <input type="radio" name="marital_status" id="widowed" value="widowed">
                        <label for="widowed">Widow/Widower</label>
                    </div>
                </div>
                <div id="children_section">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="number_of_children">Number of Children</label>
                            <select id="number_of_children" name="total_children" class="form-control">
                                <option value="">Select number of children</option>
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5+">5+</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 flex items-center">
                            <label for="status">Status</label>
                            <input type="radio" name="status_children" value="Living_with_me"> Living with me
                            <input type="radio" name="status_children" value="Not_Living_with_me"> Not Living with me
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="mother_tongue">Mother Tongue</label>
                        <select id="mother_tongue" name="mother_tongue" class="form-control" placeholder="Select mother tongue">
                            <option value="">Select mother tongue</option>
                            <option value="urdu">Urdu</option>
                            <option value="punjabi">Punjabi</option>
                            <option value="sindhi">Sindhi</option>
                            <option value="pashto">Pashto</option>
                            <option value="balochi">Balochi</option>
                            <option value="english">English</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">


                        <label for="languages_known">Languages Known</label>
                        <?php $selectedLanguages = $_POST['language_known'] ?? []; ?>
                        <select name="language_known[]" id="languages" class="form-control" multiple>
                            <?php foreach ($languages as $lang): ?>
                                <option value="<?= $lang ?>"
                                    <?= in_array($lang, $selectedLanguages) ? 'selected' : '' ?>>
                                    <?= $lang ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="dob" class="form-control" placeholder="Select date of birth">
                        </div>
                    </div>
                    <div class="row">
                        <h2> Religious Information</h2>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="religion">Religion</label>
                            <select id="religion" name="religion" class="form-control" placeholder="Select religion">
                                <option value="">Select religion</option>
                                <option value="islam">Islam</option>
                                <option value="christianity">Christianity</option>
                                <option value="hinduism">Hinduism</option>
                                <option value="buddhism">Buddhism</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="sect">Sect</label>
                            <select id="sect" name="maslak" class="form-control" placeholder="Select sect">
                                <option value="">Select sect</option>
                                <option value="sunni">Sunni</option>
                                <option value="shia">Shia</option>
                                <option value="ahmadiyya">Ahmadiyya</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="caste">Caste</label>
                            <select id="caste" name="caste" class="form-control" placeholder="Select caste">
                                <option value="">Select Caste</option>

                                <?php foreach ($castes as $caste): ?>
                                    <option value="<?= $caste ?>"><?= $caste ?></option>
                                <?php endforeach; ?>
                                <option value="other">Other</option>
                            </select>
                            <!-- <input type="text" id="customCaste" name="customCaste" class="form-control mt-2" placeholder="Enter custom caste" style="display: none;"> -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="sub_caste">Sub Caste</label>
                            <input type="text" name="sub_caste" id="sub_caste" class="form-control" placeholder="Enter sub caste">
                        </div>
                    </div>
                    <div class="row">
                        <h2> Education / Occupation Details</h2>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="education">Education</label>
                            <select id="education" name="education" class="form-control" placeholder="Select education">
                                <option value="">Select education</option>
                                <?php foreach ($educations as $education): ?>
                                    <option value="<?= $education ?>"><?= $education ?></option>
                                <?php endforeach; ?>
                                <option value="other">Other</option>
                            </select>
                            <input type="text" id="customEducation" name="education" class="form-control mt-2" placeholder="Enter custom education" style="display: none;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="employee_in">Employee In</label>
                            <select id="employee_in" name="employed_in" class="form-control" placeholder="Select employee in">
                                <option value="">Select employee in</option>
                                <option value="private">Private Sector</option>
                                <option value="public">Public Sector</option>
                                <option value="self_employed">Self Employed</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="annual_income">Annual Income</label>
                            <select id="annual_income" name="annual_income" class="form-control" placeholder="Select annual income">
                                <option value="">Select annual income</option>
                                <option value="below_500k">Below 500,000 PKR</option>
                                <option value="500k_1m">500,000 - 1,000,000 PKR</option>
                                <option value="1m_2m">1,000,000 - 2,000,000 PKR</option>
                                <option value="above_2m">Above 2,000,000 PKR</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="occupation">Occupation</label>
                            <select id="occupation" name="occupation" class="form-control" placeholder="Select occupation">
                                <option value="">Select occupation</option>
                                <?php foreach ($jobs as $job): ?>
                                    <option value="<?= $job ?>"><?= $job ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="designation">Designation</label>
                            <select id="designation" name="designation" class="form-control" placeholder="Select designation">
                                <option value="">Select designation</option>
                                <?php foreach ($designations as $designation): ?>
                                    <option value="<?= $designation ?>"><?= $designation ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="work_detail">Work Detail</label>
                            <input type="text" id="work_detail" name="work_detail" class="form-control" placeholder="Enter work detail">
                        </div>
                    </div>
                    <div class="row">
                        <h2> FEE Details</h2>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="registration_fee">Registration Fee</label>
                            <input type="number" name="registration_fee" id="registration_fee" class="form-control" placeholder="Enter registration fee">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="final_fee">Final Fee</label>
                            <input type="number" name="final_fee" id="final_fee" class="form-control" placeholder="Enter final fee">
                        </div>
                    </div>
                    <div class="row">
                        <h2> Assign Team</h2>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="assign_team">Assign Team</label>
                            <select id="assign_team" name="" class="form-control" placeholder="Select team to assign">
                                <option value="">Select team</option>
                                <?php if (!empty($admin_details)) : ?>
                                    <?php foreach ($admin_details as $admin) : ?>
                                        <option value="<?= $admin['name']; ?>">
                                            <?= $admin['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Save Details</button>
                </div>
            </div>
    </form>


</div>


<!-- End of <body> -->
<script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@4.0.1/dist/js/multi-select-tag.min.js"></script>

<script>
    var tagSelector = new MultiSelectTag('languages', {
        maxSelection: 5, // default unlimited.
        required: true, // default false.
        placeholder: 'Search tags', // default 'Search'.
        onChange: function(selected) { // Callback when selection changes.
            console.log('Selection changed:', selected);
        }
    });
    document.querySelector("form").addEventListener("submit", function() {
        let code = document.querySelector("select[name='country_code']").value;
        let number = document.getElementById("mobile_number").value;
        document.getElementById("full_mobile").value = code + number;
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const maritalRadios = document.querySelectorAll('input[name="marital_status"]');
        const childrenSection = document.getElementById("children_section");

        function toggleChildrenSection() {
            let selected = document.querySelector('input[name="marital_status"]:checked');

            if (selected && selected.value === "single") {
                childrenSection.style.display = "none";
            } else {
                childrenSection.style.display = "block";
            }
        }

        // Run on change
        maritalRadios.forEach(radio => {
            radio.addEventListener("change", toggleChildrenSection);
        });

        // Run on page load (important for edit forms)
        toggleChildrenSection();
    });
</script>




<?php require __DIR__ . '/_step_footer.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>