<?php
$title = "Member Advanced Search";
require __DIR__.'/partials/header.php';
require __DIR__.'/partials/sidebar.php';

$v = static function (array $src, string $key, string $default = ''): string {
    return htmlspecialchars((string)($src[$key] ?? $default), ENT_QUOTES, 'UTF-8');
};
$isChecked = static function (array $src, string $key, string $value, string $default = 'All'): string {
    $current = (string)($src[$key] ?? $default);
    return strcasecmp($current, $value) === 0 ? 'checked' : '';
};
$renderOptions = static function (array $items, string $selectedValue, string $placeholder) {
    echo '<option value="">' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '</option>';
    foreach ($items as $item) {
        $val = is_array($item) ? (string)($item['id'] ?? '') : (string)$item;
        $lbl = is_array($item) ? (string)($item['name'] ?? $item['id']) : (string)$item;
        $selected = ((string)$selectedValue === $val) ? ' selected' : '';
        echo '<option value="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"' . $selected . '>' . htmlspecialchars($lbl, ENT_QUOTES, 'UTF-8') . '</option>';
    }
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
        <div class="page-head">Member Advanced Search</div>

        <div class="advanced-wrap">
            <form method="post" action="<?= BASE_URL ?>/admin/advanced-search" class="advanced-form">
                <div class="form-row">
                    <label>Gender <span class="req">*</span></label>
                    <div class="field radio-group">
                        <label><input type="radio" name="gender" value="All" <?= $isChecked($filters, 'gender', 'All') ?>> All</label>
                        <label><input type="radio" name="gender" value="Male" <?= $isChecked($filters, 'gender', 'Male') ?>> Male</label>
                        <label><input type="radio" name="gender" value="Female" <?= $isChecked($filters, 'gender', 'Female') ?>> Female</label>
                    </div>
                </div>
                <div class="form-row">
                    <label>Featured Member <span class="req">*</span></label>
                    <div class="field radio-group">
                        <label><input type="radio" name="fstatus" value="All" <?= $isChecked($filters, 'fstatus', 'All') ?>> All</label>
                        <label><input type="radio" name="fstatus" value="Featured" <?= $isChecked($filters, 'fstatus', 'Featured') ?>> Featured</label>
                        <label><input type="radio" name="fstatus" value="Unfeatured" <?= $isChecked($filters, 'fstatus', 'Unfeatured') ?>> Unfeatured</label>
                    </div>
                </div>
                <div class="form-row">
                    <label>Assignment Status <span class="req">*</span></label>
                    <div class="field radio-group">
                        <label><input type="radio" name="assignment" value="All" <?= $isChecked($filters, 'assignment', 'All') ?>> All</label>
                        <label><input type="radio" name="assignment" value="Assign" <?= $isChecked($filters, 'assignment', 'Assign') ?>> Assign</label>
                        <label><input type="radio" name="assignment" value="Unassign" <?= $isChecked($filters, 'assignment', 'Unassign') ?>> Unassign</label>
                    </div>
                </div>
                <div class="form-row">
                    <label>Filter (lead)</label>
                    <div class="field radio-group">
                        <label><input type="radio" name="lead_scope" value="All" <?= $isChecked($filters, 'lead_scope', 'All', 'All') ?>> All</label>
                        <label><input type="radio" name="lead_scope" value="Own" <?= $isChecked($filters, 'lead_scope', 'Own', 'All') ?>> Own (my members)</label>
                    </div>
                </div>
                <div class="form-row">
                    <label>Team filter (staff role)</label>
                    <div class="field"><select name="team_filter" class="form-select form-control"><?php $renderOptions($options['team_role'] ?? [], (string)($filters['team_filter'] ?? ''), 'Select team / role'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Height range</label>
                    <div class="field two-col">
                        <select name="height_from" class="form-select form-control"><?php $renderOptions($options['heights'] ?? [], (string)($filters['height_from'] ?? ''), 'From'); ?></select>
                        <select name="height_to" class="form-select form-control"><?php $renderOptions($options['heights'] ?? [], (string)($filters['height_to'] ?? ''), 'To'); ?></select>
                    </div>
                </div>
                <div class="form-row">
                    <label>Sect (maslak)</label>
                    <div class="field"><select name="sect" class="form-select form-control"><?php $renderOptions($options['sect'] ?? ($options['maslak'] ?? []), (string)($filters['sect'] ?? ''), 'Select sect'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Maslak</label>
                    <div class="field"><select name="maslak" class="form-select form-control"><?php $renderOptions($options['maslak'] ?? [], (string)($filters['maslak'] ?? ''), 'Select maslak'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>House area</label>
                    <div class="field"><select name="area" class="form-select form-control"><?php $renderOptions($options['areas'] ?? [], (string)($filters['area'] ?? ''), 'Select house area'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>House type</label>
                    <div class="field"><select name="house_type" class="form-select form-control"><?php $renderOptions($options['house_type'] ?? [], (string)($filters['house_type'] ?? ''), 'Select house type'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>House size (Marla) range</label>
                    <div class="field two-col">
                        <input type="number" class="form-control" name="house_marla_from" step="0.01" min="0" value="<?= $v($filters, 'house_marla_from') ?>" placeholder="From">
                        <input type="number" class="form-control" name="house_marla_to" step="0.01" min="0" value="<?= $v($filters, 'house_marla_to') ?>" placeholder="To">
                    </div>
                </div>
                <div class="form-row">
                    <label>Employed in</label>
                    <div class="field"><select name="employed_in" class="form-select form-control"><?php $renderOptions($options['employed_in'] ?? [], (string)($filters['employed_in'] ?? ''), 'Select employed in'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Annual income</label>
                    <div class="field"><select name="annual_income" class="form-select form-control"><?php $renderOptions($options['annual_income'] ?? [], (string)($filters['annual_income'] ?? ''), 'Select annual income'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Designation</label>
                    <div class="field"><select name="designation" class="form-select form-control"><?php $renderOptions($options['designation'] ?? [], (string)($filters['designation'] ?? ''), 'Select designation'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Weight range</label>
                    <div class="field two-col">
                        <select name="weight_from" class="form-select form-control"><?php $renderOptions($options['weights'] ?? [], (string)($filters['weight_from'] ?? ''), 'From'); ?></select>
                        <select name="weight_to" class="form-select form-control"><?php $renderOptions($options['weights'] ?? [], (string)($filters['weight_to'] ?? ''), 'To'); ?></select>
                    </div>
                </div>
                <div class="form-row">
                    <label>Diet (eating habits)</label>
                    <div class="field"><select name="eating_habits" class="form-select form-control"><?php $renderOptions($options['eating_habits'] ?? [], (string)($filters['eating_habits'] ?? ''), 'Select diet'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Smoke</label>
                    <div class="field">
                        <div class="radio-group" style="margin-bottom:8px">
                            <label><input type="radio" name="smoking_mode" value="all" <?= $isChecked($filters, 'smoking_mode', 'all', 'all') ?>> All</label>
                            <label><input type="radio" name="smoking_mode" value="match" <?= $isChecked($filters, 'smoking_mode', 'match', 'all') ?>> Match value</label>
                        </div>
                        <select name="smoking" class="form-select form-control"><?php $renderOptions($options['smoking'] ?? [], (string)($filters['smoking'] ?? ''), 'Select smoking'); ?></select>
                    </div>
                </div>
                <div class="form-row">
                    <label>Drink</label>
                    <div class="field">
                        <div class="radio-group" style="margin-bottom:8px">
                            <label><input type="radio" name="drinking_mode" value="all" <?= $isChecked($filters, 'drinking_mode', 'all', 'all') ?>> All</label>
                            <label><input type="radio" name="drinking_mode" value="match" <?= $isChecked($filters, 'drinking_mode', 'match', 'all') ?>> Match value</label>
                        </div>
                        <select name="drinking" class="form-select form-control"><?php $renderOptions($options['drinking'] ?? [], (string)($filters['drinking'] ?? ''), 'Select drinking'); ?></select>
                    </div>
                </div>
                <div class="form-row">
                    <label>Body type</label>
                    <div class="field"><select name="body_type" class="form-select form-control"><?php $renderOptions($options['body_type'] ?? [], (string)($filters['body_type'] ?? ''), 'Select body type'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Skin tone</label>
                    <div class="field"><select name="skin_tone" class="form-select form-control"><?php $renderOptions($options['skin_tone'] ?? [], (string)($filters['skin_tone'] ?? ''), 'Select skin tone'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Blood group</label>
                    <div class="field"><select name="blood_group" class="form-select form-control"><?php $renderOptions($options['blood_group'] ?? [], (string)($filters['blood_group'] ?? ''), 'Select blood group'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Profile by</label>
                    <div class="field"><select name="profile_by" class="form-select form-control"><?php $renderOptions($options['profile_by'] ?? [], (string)($filters['profile_by'] ?? ''), 'Select profile by'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Reference</label>
                    <div class="field"><select name="reference" class="form-select form-control"><?php $renderOptions($options['reference'] ?? [], (string)($filters['reference'] ?? ''), 'Select reference'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Family type</label>
                    <div class="field">
                        <div class="radio-group" style="margin-bottom:8px">
                            <label><input type="radio" name="family_type_mode" value="all" <?= $isChecked($filters, 'family_type_mode', 'all', 'all') ?>> All</label>
                            <label><input type="radio" name="family_type_mode" value="match" <?= $isChecked($filters, 'family_type_mode', 'match', 'all') ?>> Match value</label>
                        </div>
                        <select name="family_type" class="form-select form-control"><?php $renderOptions($options['family_type'] ?? [], (string)($filters['family_type'] ?? ''), 'Select family type'); ?></select>
                    </div>
                </div>
                <div class="form-row">
                    <label>Family status</label>
                    <div class="field"><select name="family_status" class="form-select form-control"><?php $renderOptions($options['family_status'] ?? [], (string)($filters['family_status'] ?? ''), 'Select family status'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>No. of brothers</label>
                    <div class="field"><select name="no_of_brothers" class="form-select form-control"><?php $renderOptions($options['no_of_brothers'] ?? [], (string)($filters['no_of_brothers'] ?? ''), 'Select no. of brothers'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>No. of married brothers</label>
                    <div class="field"><select name="no_of_married_brother" class="form-select form-control"><?php $renderOptions($options['no_of_married_brother'] ?? [], (string)($filters['no_of_married_brother'] ?? ''), 'Select no. of married brothers'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>No. of sisters</label>
                    <div class="field"><select name="no_of_sisters" class="form-select form-control"><?php $renderOptions($options['no_of_sisters'] ?? [], (string)($filters['no_of_sisters'] ?? ''), 'Select no. of sisters'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>No. of married sisters</label>
                    <div class="field"><select name="no_of_married_sister" class="form-select form-control"><?php $renderOptions($options['no_of_married_sister'] ?? [], (string)($filters['no_of_married_sister'] ?? ''), 'Select no. of married sisters'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Mobile verify status</label>
                    <div class="field radio-group">
                        <label><input type="radio" name="mobile_verify_status" value="All" <?= $isChecked($filters, 'mobile_verify_status', 'All', 'All') ?>> All</label>
                        <label><input type="radio" name="mobile_verify_status" value="Verified" <?= $isChecked($filters, 'mobile_verify_status', 'Verified', 'All') ?>> Verified</label>
                        <label><input type="radio" name="mobile_verify_status" value="NotVerified" <?= $isChecked($filters, 'mobile_verify_status', 'NotVerified', 'All') ?>> Not verified</label>
                    </div>
                </div>
                <div class="form-row">
                    <label>Email verify status</label>
                    <div class="field radio-group">
                        <label><input type="radio" name="email_verify_status" value="All" <?= $isChecked($filters, 'email_verify_status', 'All', 'All') ?>> All</label>
                        <label><input type="radio" name="email_verify_status" value="Verified" <?= $isChecked($filters, 'email_verify_status', 'Verified', 'All') ?>> Verified</label>
                        <label><input type="radio" name="email_verify_status" value="NotVerified" <?= $isChecked($filters, 'email_verify_status', 'NotVerified', 'All') ?>> Not verified</label>
                    </div>
                </div>
                <div class="form-row">
                    <label>Registered from</label>
                    <div class="field">
                        <select name="registration_source" class="form-select form-control">
                            <option value=""<?= trim((string)($filters['registration_source'] ?? '')) === '' ? ' selected' : '' ?>>All</option>
                            <option value="website"<?= strtolower(trim((string)($filters['registration_source'] ?? ''))) === 'website' ? ' selected' : '' ?>>Website</option>
                            <option value="mobile_app"<?= strtolower(trim((string)($filters['registration_source'] ?? ''))) === 'mobile_app' ? ' selected' : '' ?>>Mobile app</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label>Plan expired on (date)</label>
                    <div class="field"><select name="plan_expires_on" class="form-select form-control"><?php $renderOptions($options['plan_expire_dates'] ?? [], (string)($filters['plan_expires_on'] ?? ''), 'Select plan expiry date'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Photo setting <span class="req">*</span></label>
                    <div class="field radio-group">
                        <label><input type="radio" name="photo_setting" value="All" <?= $isChecked($filters, 'photo_setting', 'All', 'All') ?>> All</label>
                        <label><input type="radio" name="photo_setting" value="WithPhoto" <?= $isChecked($filters, 'photo_setting', 'WithPhoto', 'All') ?>> With photo</label>
                        <label><input type="radio" name="photo_setting" value="WithoutPhoto" <?= $isChecked($filters, 'photo_setting', 'WithoutPhoto', 'All') ?>> Without photo</label>
                    </div>
                </div>
                <div class="form-row">
                    <label>Department Filter</label>
                    <div class="field"><select name="department_filter" class="form-select form-control"><?php $renderOptions($options['department'] ?? [], (string)($filters['department_filter'] ?? ''), 'Select Department Filter'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Team Leader Filter</label>
                    <div class="field"><select name="team_leader_filter" class="form-select form-control"><?php $renderOptions($options['team_leader'] ?? [], (string)($filters['team_leader_filter'] ?? ''), 'Select Team Leader Filter'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Added By Filter</label>
                    <div class="field"><select name="added_by_filter" class="form-select form-control"><?php $renderOptions($options['added_by'] ?? [], (string)($filters['added_by_filter'] ?? ''), 'Select Added By Filter'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Keyword</label>
                    <div class="field"><input type="text" class="form-control" name="keyword" value="<?= $v($filters, 'keyword') ?>" placeholder="Search with Name, Matri ID, Email, Mobile, Country, State, City"></div>
                </div>
                <div class="form-row">
                    <label>Registered Between</label>
                    <div class="field two-col">
                        <input type="date" class="form-control" name="from_reg_date" value="<?= $v($filters, 'from_reg_date') ?>">
                        <input type="date" class="form-control" name="to_reg_date" value="<?= $v($filters, 'to_reg_date') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <label>Age Range</label>
                    <div class="field two-col">
                        <input type="number" class="form-control" name="from_age" min="18" max="90" value="<?= $v($filters, 'from_age') ?>" placeholder="From">
                        <input type="number" class="form-control" name="to_age" min="18" max="90" value="<?= $v($filters, 'to_age') ?>" placeholder="To">
                    </div>
                </div>
                <div class="form-row">
                    <label>Mothertongue</label>
                    <div class="field"><select name="mother_tongue" class="form-select form-control"><?php $renderOptions($options['mother_tongue'] ?? [], (string)($filters['mother_tongue'] ?? ''), 'Select Mothertongue'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Marital Status</label>
                    <div class="field"><select name="marital_status" class="form-select form-control"><?php $renderOptions($options['marital_status'] ?? [], (string)($filters['marital_status'] ?? ''), 'Select Marital Status'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Religion</label>
                    <div class="field"><select name="religion" class="form-select form-control"><?php $renderOptions($options['religion'] ?? [], (string)($filters['religion'] ?? ''), 'Select Religion'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Caste</label>
                    <div class="field"><select name="caste" class="form-select form-control"><?php $renderOptions($options['caste'] ?? [], (string)($filters['caste'] ?? ''), 'Select Caste'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Country / State / City</label>
                    <div class="field three-col">
                        <select name="country" class="form-select form-control"><?php $renderOptions($options['country'] ?? [], (string)($filters['country'] ?? ''), 'Country'); ?></select>
                        <select name="state" class="form-select form-control"><?php $renderOptions($options['state'] ?? [], (string)($filters['state'] ?? ''), 'State'); ?></select>
                        <select name="city" class="form-select form-control"><?php $renderOptions($options['city'] ?? [], (string)($filters['city'] ?? ''), 'City'); ?></select>
                    </div>
                </div>
                <div class="form-row">
                    <label>Education</label>
                    <div class="field"><select name="education" class="form-select form-control"><?php $renderOptions($options['education'] ?? [], (string)($filters['education'] ?? ''), 'Select Education'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Occupation</label>
                    <div class="field"><select name="occupation" class="form-select form-control"><?php $renderOptions($options['occupation'] ?? [], (string)($filters['occupation'] ?? ''), 'Select Occupation'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Residence</label>
                    <div class="field"><select name="residence" class="form-select form-control"><?php $renderOptions($options['residence'] ?? [], (string)($filters['residence'] ?? ''), 'Select Residence'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Plan Name</label>
                    <div class="field"><select name="plan_name" class="form-select form-control"><?php $renderOptions($options['plan_name'] ?? [], (string)($filters['plan_name'] ?? ''), 'Select Plan Name'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Plan Status</label>
                    <div class="field">
                        <select name="plan_status" class="form-select form-control">
                            <option value="All" <?= strtolower((string)($filters['plan_status'] ?? 'all')) === 'all' ? 'selected' : '' ?>>All</option>
                            <option value="Paid" <?= strtolower((string)($filters['plan_status'] ?? '')) === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="Not_Paid" <?= strtolower((string)($filters['plan_status'] ?? '')) === 'not_paid' ? 'selected' : '' ?>>Not Paid</option>
                            <option value="Expired" <?= strtolower((string)($filters['plan_status'] ?? '')) === 'expired' ? 'selected' : '' ?>>Expired</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="<?= BASE_URL ?>/admin/advanced-search" class="btn btn-light border">Reset</a>
                </div>
            </form>
        </div>

        <div class="result-wrap mt-3">
            <div class="result-head">Search Result (<?= (int)count($rows) ?>)</div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>ID</th><th>Name</th><th>Gender</th><th>Email</th><th>Phone</th><th>Country</th><th>City</th><th>Status</th><th>Featured</th><th>Added By</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="10" class="text-center">No members found.</td></tr>
                    <?php else: foreach ($rows as $r): ?>
                        <tr>
                            <td><?= (int)$r['id'] ?></td>
                            <td><?= htmlspecialchars(trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''))) ?></td>
                            <td><?= htmlspecialchars((string)($r['gender'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars((string)($r['email'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars((string)($r['phone'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars((string)($r['country'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars((string)($r['city'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars((string)($r['user_status'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars((string)($r['featured_status'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars((string)($r['added_by_name'] ?? '-')) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<style>
.admin-content{padding:14px;background:#efefef}.page-head{font-size:13px;font-weight:700;color:#535353;margin-bottom:10px}
.advanced-wrap{background:#f8f8f8;border:1px solid #d7d7d7;border-radius:3px;padding:14px}
.advanced-form .form-row{display:flex;gap:16px;padding:10px 0;border-bottom:1px solid #ececec;align-items:flex-start}
.advanced-form .form-row:last-of-type{border-bottom:0}
.advanced-form label{width:210px;flex:0 0 210px;font-size:12px;font-weight:600;color:#555;margin:7px 0 0}
.advanced-form .field{flex:1}.advanced-form .req{color:#d9534f}
.radio-group{display:flex;flex-wrap:wrap;gap:14px;font-size:12px;color:#444;padding-top:4px}
.radio-group input{margin-right:4px}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:10px}.three-col{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px}
.form-actions{padding-top:12px;display:flex;gap:8px}
.result-wrap{background:#fff;border:1px solid #d7d7d7;border-radius:3px}
.result-head{padding:10px 12px;font-size:12px;font-weight:700;background:#f7f7f7;border-bottom:1px solid #e5e5e5}
@media(max-width:991px){.advanced-form .form-row{flex-wrap:wrap}.advanced-form label{width:100%;flex-basis:100%;margin-top:0}.two-col,.three-col{grid-template-columns:1fr}}
</style>

<?php require __DIR__.'/partials/footer.php'; ?>
