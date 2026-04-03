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
                    <label>Department Filter</label>
                    <div class="field"><select name="department_filter" class="form-select"><?php $renderOptions($options['department'] ?? [], (string)($filters['department_filter'] ?? ''), 'Select Department Filter'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Team Leader Filter</label>
                    <div class="field"><select name="team_leader_filter" class="form-select"><?php $renderOptions($options['team_leader'] ?? [], (string)($filters['team_leader_filter'] ?? ''), 'Select Team Leader Filter'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Added By Filter</label>
                    <div class="field"><select name="added_by_filter" class="form-select"><?php $renderOptions($options['added_by'] ?? [], (string)($filters['added_by_filter'] ?? ''), 'Select Added By Filter'); ?></select></div>
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
                    <div class="field"><select name="mother_tongue" class="form-select"><?php $renderOptions($options['mother_tongue'] ?? [], (string)($filters['mother_tongue'] ?? ''), 'Select Mothertongue'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Marital Status</label>
                    <div class="field"><select name="marital_status" class="form-select"><?php $renderOptions($options['marital_status'] ?? [], (string)($filters['marital_status'] ?? ''), 'Select Marital Status'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Religion</label>
                    <div class="field"><select name="religion" class="form-select"><?php $renderOptions($options['religion'] ?? [], (string)($filters['religion'] ?? ''), 'Select Religion'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Caste</label>
                    <div class="field"><select name="caste" class="form-select"><?php $renderOptions($options['caste'] ?? [], (string)($filters['caste'] ?? ''), 'Select Caste'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Country / State / City</label>
                    <div class="field three-col">
                        <select name="country" class="form-select"><?php $renderOptions($options['country'] ?? [], (string)($filters['country'] ?? ''), 'Country'); ?></select>
                        <select name="state" class="form-select"><?php $renderOptions($options['state'] ?? [], (string)($filters['state'] ?? ''), 'State'); ?></select>
                        <select name="city" class="form-select"><?php $renderOptions($options['city'] ?? [], (string)($filters['city'] ?? ''), 'City'); ?></select>
                    </div>
                </div>
                <div class="form-row">
                    <label>Education</label>
                    <div class="field"><select name="education" class="form-select"><?php $renderOptions($options['education'] ?? [], (string)($filters['education'] ?? ''), 'Select Education'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Occupation</label>
                    <div class="field"><select name="occupation" class="form-select"><?php $renderOptions($options['occupation'] ?? [], (string)($filters['occupation'] ?? ''), 'Select Occupation'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Residence</label>
                    <div class="field"><select name="residence" class="form-select"><?php $renderOptions($options['residence'] ?? [], (string)($filters['residence'] ?? ''), 'Select Residence'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Plan Name</label>
                    <div class="field"><select name="plan_name" class="form-select"><?php $renderOptions($options['plan_name'] ?? [], (string)($filters['plan_name'] ?? ''), 'Select Plan Name'); ?></select></div>
                </div>
                <div class="form-row">
                    <label>Plan Status</label>
                    <div class="field">
                        <select name="plan_status" class="form-select">
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
