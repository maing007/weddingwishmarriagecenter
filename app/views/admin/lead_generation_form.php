<?php
$isEdit = !empty($lead['id'] ?? null);
$title = $isEdit ? 'Edit Lead' : 'Add New Lead';

$v = static function ($key) use ($lead) {
    return htmlspecialchars((string) ($lead[$key] ?? ''));
};

$selCountryId = '';
if ($isEdit) {
    if (!empty($lead['country_id'])) {
        $selCountryId = (string) (int) $lead['country_id'];
    } elseif (!empty($lead['country'])) {
        foreach ($countries as $kid => $nm) {
            if ($kid !== '' && strcasecmp((string) $nm, (string) $lead['country']) === 0) {
                $selCountryId = (string) $kid;
                break;
            }
        }
    }
}

$selSourceKey = '';
if ($isEdit && !empty($lead['source_name'])) {
    foreach ($sources as $sk => $sl) {
        if ($sk !== '' && (string) $sl === (string) $lead['source_name']) {
            $selSourceKey = (string) $sk;
            break;
        }
    }
}

$selTeamId = '';
if ($isEdit) {
    $tid = (int) ($lead['team_assign_id'] ?? 0);
    if ($tid > 0) {
        $selTeamId = (string) $tid;
    } elseif (!empty($lead['team_assign'])) {
        foreach ($adminUsers as $au) {
            if (strcasecmp((string) $au['name'], (string) $lead['team_assign']) === 0) {
                $selTeamId = (string) (int) $au['id'];
                break;
            }
        }
    }
}

$genderVal = (string) ($lead['gender'] ?? '');
$interestVal = (string) ($lead['interest_name'] ?? '');
$importanceVal = (string) ($lead['importance'] ?? '');
$stateVal = (string) ($lead['state'] ?? '');
$cityVal = (string) ($lead['city'] ?? '');

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>

<div class="admin-main lead-form-wrap">
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
        <div class="col-lg-12">
            <a href="<?= BASE_URL ?>/admin/lead-generation" class="btn btn-sm btn-default bordered-back mb-3"><i class="fa fa-arrow-left"></i> Back</a>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show small">
                    <?= htmlspecialchars($_SESSION['flash_error']);
                unset($_SESSION['flash_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" id="common_insert_update" name="common_insert_update" class="form-horizontal bordered-group bg-white p-3 p-md-4 border rounded" novalidate action="<?= $isEdit ? BASE_URL . '/admin/lead-generation/update' : BASE_URL . '/admin/lead-generation/store' ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="created_by" id="created_by" value="<?= (int) ($_SESSION['admin_id'] ?? 0) ?>">
                <input type="hidden" name="mode" id="mode" value="<?= $isEdit ? 'edit' : 'add' ?>">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" id="id" value="<?= (int) $lead['id'] ?>">
                <?php else: ?>
                    <input type="hidden" name="id" id="id" value="">
                <?php endif; ?>

                <div class="form-group row mb-3">
                    <label class="col-sm-2 col-lg-2 control-label">Name <span class="sub_title_mem">*</span></label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="text" required name="username" id="username" class="form-control" placeholder="Name" value="<?= $v('full_name') ?>" aria-required="true">
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label class="col-sm-2 col-lg-2 control-label">Email</label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?= $v('email') ?>">
                    </div>
                </div>

                <div class="form-group row mb-3 GENDER Gender">
                    <label class="col-sm-2 col-lg-2 control-label">Gender</label>
                    <div class="col-sm-9 col-lg-7">
                        <div class="radio pt-1">
                            <label class="me-3"><input type="radio" name="gender" id="Male" value="Male" <?= $genderVal === 'Male' || (!$isEdit && $genderVal === '') ? 'checked' : '' ?>> Male</label>
                            <label><input type="radio" name="gender" id="Female" value="Female" <?= $genderVal === 'Female' ? 'checked' : '' ?>> Female</label>
                        </div>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label class="col-sm-2 col-lg-2 control-label">Address</label>
                    <div class="col-sm-9 col-lg-7 address_edit">
                        <textarea rows="4" name="address" id="address" class="form-control" placeholder="Address"><?= $v('address') ?></textarea>
                    </div>
                </div>

                <?php
                $ph = 'Without zero and spaces like 3111444041';
foreach (['1' => 'phone_no_1', '2' => 'phone_no_2', '3' => 'phone_no_3', '4' => 'phone_no_4'] as $num => $fname):
    $dbKey = 'phone' . $num;
                    ?>
                <div class="form-group row mb-3">
                    <label class="col-sm-2 col-lg-2 control-label">Phone No. <?= $num ?></label>
                    <div class="col-sm-9 col-lg-7">
                        <input type="text" name="<?= $fname ?>" id="<?= $fname ?>" class="form-control check_mobile" placeholder="<?= htmlspecialchars($ph) ?>" value="<?= $v($dbKey) ?>">
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="form-group row mb-3" id="COUNTRY_ID">
                    <label class="col-sm-2 col-lg-2 control-label">Country <span class="sub_title_mem">*</span></label>
                    <div class="col-sm-9 col-lg-7">
                        <select required name="country_id" id="country_id" class="form-control" aria-required="true">
                            <?php foreach ($countries as $cid => $cname): ?>
                                <?php if ($cid === '') { ?>
                                <option value=""><?= htmlspecialchars($cname) ?></option>
                                <?php continue; } ?>
                            <option value="<?= htmlspecialchars((string) $cid) ?>" <?= ((string) $cid === $selCountryId) ? 'selected' : '' ?>><?= htmlspecialchars($cname) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-3" id="STATE_ID">
                    <label class="col-sm-2 col-lg-2 control-label">State</label>
                    <div class="col-sm-9 col-lg-7">
                        <select name="state_id" id="state_id" class="form-control">
                            <option value="">Select State</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-3" id="CITY">
                    <label class="col-sm-2 col-lg-2 control-label">City</label>
                    <div class="col-sm-9 col-lg-7">
                        <select name="city" id="city" class="form-control">
                            <option value="">Select City</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-3" id="INTEREST">
                    <label class="col-sm-2 col-lg-2 control-label">Interest <span class="sub_title_mem">*</span></label>
                    <div class="col-sm-9 col-lg-7">
                        <select required name="interest" id="interest" class="form-control" aria-required="true">
                            <option value=""><?= $isEdit ? 'Select Interest' : 'Select Interest' ?></option>
                            <option value="In-Process-M" <?= $interestVal === 'In-Process-M' ? 'selected' : '' ?>>In-Process-M</option>
                            <option value="Registered" <?= $interestVal === 'Registered' ? 'selected' : '' ?>>Registered</option>
                            <option value="Closed-M" <?= $interestVal === 'Closed-M' ? 'selected' : '' ?>>Closed-M</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-3" id="SOURCE">
                    <label class="col-sm-2 col-lg-2 control-label">Source</label>
                    <div class="col-sm-9 col-lg-7">
                        <select name="source" id="source" class="form-control">
                            <?php foreach ($sources as $sid => $sname): ?>
                                <?php if ($sid === '') { ?>
                                <option value=""><?= htmlspecialchars($sname) ?></option>
                                <?php continue; } ?>
                            <option value="<?= htmlspecialchars((string) $sid) ?>" <?= ((string) $sid === $selSourceKey) ? 'selected' : '' ?>><?= htmlspecialchars($sname) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-3" id="IMPORTANCE">
                    <label class="col-sm-2 col-lg-2 control-label">Importance <span class="sub_title_mem">*</span></label>
                    <div class="col-sm-9 col-lg-7">
                        <select required name="importance" id="importance" class="form-control" aria-required="true">
                            <option value="">Select Importance</option>
                            <option value="Important" <?= $importanceVal === 'Important' ? 'selected' : '' ?>>Important</option>
                            <option value="Moderate" <?= $importanceVal === 'Moderate' ? 'selected' : '' ?>>Moderate</option>
                            <option value="Not Important" <?= $importanceVal === 'Not Important' ? 'selected' : '' ?>>Not Important</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-3" id="TEAM_ASSIGN">
                    <label class="col-sm-2 col-lg-2 control-label">Assign Team</label>
                    <div class="col-sm-9 col-lg-7">
                        <select name="team_assign" id="team_assign_sel" class="form-control">
                            <option value="">Select Assign Team</option>
                            <?php foreach ($adminUsers as $au): ?>
                                <option value="<?= (int) $au['id'] ?>" <?= ((string) (int) $au['id'] === $selTeamId) ? 'selected' : '' ?>><?= htmlspecialchars($au['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-9 col-lg-7">
                        <button type="submit" class="btn btn-primary mr10">Submit</button>
                        <a href="<?= BASE_URL ?>/admin/lead-generation" class="btn btn-default btn-secondary">Back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</main>
</div>

<style>
    .lead-form-wrap .bordered-group { border: 1px solid #dcdcdc !important; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    .lead-form-wrap .form-horizontal .control-label { text-align: right; font-weight: 600; font-size: 13px; color: #444; padding-top: 7px; }
    @media (max-width: 575.98px) {
        .lead-form-wrap .form-horizontal .control-label { text-align: left; padding-top: 0; }
    }
    .sub_title_mem { color: #c0392b; font-weight: 700; }
    .lead-form-wrap .mr10 { margin-right: 10px; }
    .lead-form-wrap .btn-default { border: 1px solid #ccc; background: #f7f7f7; color: #333; }
    .lead-form-wrap .bordered-back { border: 1px solid #ccc; background: #f7f7f7; color: #333; }
</style>

<script>
(function(){
    const PK = '167';
    const pkData = {
        'Punjab': ['Lahore','Faisalabad','Rawalpindi','Multan','Gujranwala','Sialkot','Sargodha','Bahawalpur'],
        'Sindh': ['Karachi','Hyderabad','Sukkur','Larkana','Nawabshah','Mirpur Khas'],
        'Khyber Pakhtunkhwa': ['Peshawar','Abbottabad','Mardan','Swat','Mingora','Kohat'],
        'Balochistan': ['Quetta','Gwadar','Turbat','Khuzdar'],
        'Islamabad': ['Islamabad'],
        'Gilgit-Baltistan': ['Gilgit','Skardu'],
        'Azad Kashmir': ['Muzaffarabad','Mirpur','Kotli']
    };
    const stateSel = document.getElementById('state_id');
    const citySel = document.getElementById('city');
    const countrySel = document.getElementById('country_id');
    const initialState = <?= json_encode($stateVal) ?>;
    const initialCity = <?= json_encode($cityVal) ?>;

    function onStateChange(selectCity) {
        const st = stateSel.value;
        citySel.innerHTML = '<option value="">Select City</option>';
        if (!st || !pkData[st]) return;
        pkData[st].forEach(function(c) {
            const o = document.createElement('option');
            o.value = c; o.textContent = c;
            citySel.appendChild(o);
        });
        if (selectCity) citySel.value = selectCity;
    }

    function fillStatesPK(selectState, selectCity) {
        stateSel.innerHTML = '<option value="">Select State</option>';
        Object.keys(pkData).sort().forEach(function(s) {
            const o = document.createElement('option');
            o.value = s; o.textContent = s;
            stateSel.appendChild(o);
        });
        if (selectState) stateSel.value = selectState;
        onStateChange(selectCity);
    }

    function resetNonPKGeneric() {
        stateSel.innerHTML = '<option value="">Select State</option>';
        citySel.innerHTML = '<option value="">Select City</option>';
        const o = document.createElement('option');
        o.value = 'Other'; o.textContent = 'Other / N/A';
        stateSel.appendChild(o);
        const c = document.createElement('option');
        c.value = 'Other'; c.textContent = 'Other / N/A';
        citySel.appendChild(c);
    }

    function onCountryChange() {
        const v = countrySel.value;
        if (v === PK) {
            fillStatesPK(null, null);
        } else if (v) {
            resetNonPKGeneric();
        } else {
            stateSel.innerHTML = '<option value="">Select State</option>';
            citySel.innerHTML = '<option value="">Select City</option>';
        }
    }

    function initEditPlaceholders() {
        if (countrySel.value === PK) {
            fillStatesPK(initialState || null, initialCity || null);
        } else if (countrySel.value) {
            stateSel.innerHTML = '<option value="">Select State</option>';
            citySel.innerHTML = '<option value="">Select City</option>';
            if (initialState) {
                const o1 = document.createElement('option');
                o1.value = initialState; o1.textContent = initialState;
                stateSel.appendChild(o1); stateSel.value = initialState;
            } else {
                const o = document.createElement('option');
                o.value = 'Other'; o.textContent = 'Other / N/A';
                stateSel.appendChild(o); stateSel.value = 'Other';
            }
            if (initialCity) {
                const o2 = document.createElement('option');
                o2.value = initialCity; o2.textContent = initialCity;
                citySel.appendChild(o2); citySel.value = initialCity;
            } else {
                const c = document.createElement('option');
                c.value = 'Other'; c.textContent = 'Other / N/A';
                citySel.appendChild(c); citySel.value = 'Other';
            }
        }
    }

    stateSel.addEventListener('change', function(){ onStateChange(null); });
    countrySel.addEventListener('change', onCountryChange);

    initEditPlaceholders();
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
