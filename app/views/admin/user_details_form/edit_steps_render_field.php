<?php

declare(strict_types=1);

/**
 * Normalize DB value for multi-select fields (JSON array, CSV, or PHP array).
 *
 * @param mixed $raw
 * @return list<string>
 */
function admin_edit_steps_parse_multi($raw): array
{
    if ($raw === null || $raw === '') {
        return [];
    }
    if (is_array($raw)) {
        return array_values(array_filter(array_map('strval', $raw), static function ($v) {
            return trim($v) !== '';
        }));
    }
    $s = trim((string) $raw);
    $decoded = json_decode($s, true);
    if (is_array($decoded)) {
        return array_values(array_filter(array_map('strval', $decoded), static function ($v) {
            return trim($v) !== '';
        }));
    }
    return array_values(array_filter(array_map('trim', explode(',', $s)), static function ($v) {
        return $v !== '';
    }));
}

function admin_edit_steps_in_multi_array(string $needle, array $haystack): bool
{
    $n = trim($needle);
    foreach ($haystack as $h) {
        if (strcasecmp(trim((string) $h), $n) === 0) {
            return true;
        }
    }
    return false;
}

/**
 * @param array<string, mixed> $user
 * @param list<array<string, mixed>>|null $admin_details
 * @param array<string, mixed> $D data arrays from data_array.php (languages, castes, …)
 */
function admin_edit_steps_render_field(string $col, array $user, ?array $admin_details, array $D): void
{
    $raw = $user[$col] ?? null;
    $fid = 'fld_' . $col;
    $fidEsc = htmlspecialchars($fid, ENT_QUOTES, 'UTF-8');
    $nameEsc = htmlspecialchars($col, ENT_QUOTES, 'UTF-8');

    if ($col === 'password') {
        echo '<input type="password" class="form-control" id="' . $fidEsc . '" name="password" value="" placeholder="Leave blank to keep current password" autocomplete="new-password">';

        return;
    }

    if ($col === 'gender') {
        $g = strtolower(trim((string) ($user[$col] ?? '')));
        ?>
        <div class="edit-steps-radio-row">
            <label class="edit-steps-inline-label"><input type="radio" name="<?= $nameEsc ?>" value="male" <?= $g === 'male' ? 'checked' : '' ?>> Male</label>
            <label class="edit-steps-inline-label"><input type="radio" name="<?= $nameEsc ?>" value="female" <?= $g === 'female' ? 'checked' : '' ?>> Female</label>
        </div>
        <?php
        return;
    }

    if ($col === 'marital_status') {
        $v = strtolower(trim((string) ($user[$col] ?? '')));
        $opts = ['single', 'married', 'divorced', 'separated', 'widowed'];
        ?>
        <div class="edit-steps-radio-row">
            <?php foreach ($opts as $opt): ?>
                <label class="edit-steps-inline-label"><input type="radio" name="<?= $nameEsc ?>" value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>" <?= $v === $opt ? 'checked' : '' ?>> <?= htmlspecialchars(ucfirst($opt), ENT_QUOTES, 'UTF-8') ?></label>
            <?php endforeach; ?>
        </div>
        <?php
        return;
    }

    if ($col === 'status_children') {
        $v = (string) ($raw ?? '');
        ?>
        <div class="edit-steps-radio-row">
            <label class="edit-steps-inline-label"><input type="radio" name="<?= $nameEsc ?>" value="Living_with_me" <?= $v === 'Living_with_me' ? 'checked' : '' ?>> Living with me</label>
            <label class="edit-steps-inline-label"><input type="radio" name="<?= $nameEsc ?>" value="Not_Living_with_me" <?= $v === 'Not_Living_with_me' ? 'checked' : '' ?>> Not living with me</label>
        </div>
        <?php
        return;
    }

    if ($col === 'smoking' || $col === 'drinking') {
        $v = trim((string) ($raw ?? ''));
        $opts = ['Does not matter', 'No', 'Yes', 'Occasionally'];
        ?>
        <div class="edit-steps-radio-row">
            <?php foreach ($opts as $opt): ?>
                <label class="edit-steps-inline-label"><input type="radio" name="<?= $nameEsc ?>" value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>" <?= strcasecmp($v, $opt) === 0 ? 'checked' : '' ?>> <?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></label>
            <?php endforeach; ?>
        </div>
        <?php
        return;
    }

    if (preg_match('/^photo[1-6]_review_status$/', $col) || $col === 'id_proof_status') {
        $v = strtoupper(trim((string) ($raw ?? '')));
        $isApproved = ($v === 'APPROVED');
        ?>
        <div class="edit-steps-radio-row">
            <label class="edit-steps-inline-label"><input type="radio" name="<?= $nameEsc ?>" value="APPROVED" <?= $isApproved ? 'checked' : '' ?>> APPROVED</label>
            <label class="edit-steps-inline-label"><input type="radio" name="<?= $nameEsc ?>" value="UNAPPROVED" <?= !$isApproved ? 'checked' : '' ?>> UNAPPROVED</label>
        </div>
        <?php
        return;
    }

    if ($col === 'lead' && !empty($admin_details)) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select lead</option>';
        foreach ($admin_details as $admin) {
            $nm = (string) ($admin['name'] ?? '');
            if ($nm === '') {
                continue;
            }
            $sel = strcasecmp($v, $nm) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars($nm, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($nm, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'country_code' && !empty($D['countries']) && is_array($D['countries'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Code</option>';
        foreach ($D['countries'] as $country) {
            if (!is_array($country)) {
                continue;
            }
            $code = (string) ($country['code'] ?? '');
            $flag = (string) ($country['flag'] ?? '');
            $sel = strcasecmp($v, $code) === 0 ? ' selected' : '';
            $label = trim($flag . ' ' . $code);
            echo '<option value="' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'mother_tongue') {
        $v = strtolower(trim((string) ($raw ?? '')));
        $opts = ['' => 'Select mother tongue', 'urdu' => 'Urdu', 'punjabi' => 'Punjabi', 'sindhi' => 'Sindhi', 'pashto' => 'Pashto', 'balochi' => 'Balochi', 'english' => 'English', 'other' => 'Other'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = $v === strtolower((string) $val) ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'religion') {
        $v = strtolower(trim((string) ($raw ?? '')));
        $opts = ['' => 'Select religion', 'islam' => 'Islam', 'christianity' => 'Christianity', 'hinduism' => 'Hinduism', 'buddhism' => 'Buddhism', 'other' => 'Other'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = $v === strtolower((string) $val) ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'maslak') {
        $v = strtolower(trim((string) ($raw ?? '')));
        $opts = ['' => 'Select sect', 'sunni' => 'Sunni', 'shia' => 'Shia', 'ahmadiyya' => 'Ahmadiyya', 'other' => 'Other'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = $v === strtolower((string) $val) ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'caste' && !empty($D['castes'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select caste</option>';
        foreach ($D['castes'] as $c) {
            $sel = strcasecmp($v, (string) $c) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '<option value="other"' . (strtolower($v) === 'other' ? ' selected' : '') . '>Other</option></select>';

        return;
    }

    if ($col === 'education' && !empty($D['educations'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select education</option>';
        foreach ($D['educations'] as $e) {
            $sel = strcasecmp($v, (string) $e) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '<option value="other"' . (strtolower($v) === 'other' ? ' selected' : '') . '>Other</option></select>';

        return;
    }

    if ($col === 'employed_in') {
        $v = strtolower(trim((string) ($raw ?? '')));
        $opts = ['' => 'Select employed in', 'private' => 'Private Sector', 'public' => 'Public Sector', 'self_employed' => 'Self Employed', 'other' => 'Other'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = $v === strtolower((string) $val) ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'annual_income') {
        $v = trim((string) ($raw ?? ''));
        $opts = ['' => 'Select annual income', 'below_500k' => 'Below 500,000 PKR', '500k_1m' => '500,000 - 1,000,000 PKR', '1m_2m' => '1,000,000 - 2,000,000 PKR', 'above_2m' => 'Above 2,000,000 PKR'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = strcasecmp($v, (string) $val) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'occupation' && !empty($D['jobs'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select occupation</option>';
        foreach ($D['jobs'] as $j) {
            $sel = strcasecmp($v, (string) $j) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $j, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $j, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'designation' && !empty($D['designations'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select designation</option>';
        foreach ($D['designations'] as $d) {
            $sel = strcasecmp($v, (string) $d) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $d, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $d, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'country' && !empty($D['countries_names'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select country</option>';
        foreach ($D['countries_names'] as $c) {
            $sel = strcasecmp($v, (string) $c) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'state' && !empty($D['states'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select state</option>';
        foreach ($D['states'] as $c) {
            $sel = strcasecmp($v, (string) $c) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'city' && !empty($D['cities'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control search-long-select" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select city</option>';
        foreach ($D['cities'] as $c) {
            $sel = strcasecmp($v, (string) $c) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'area' && !empty($D['areas'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control search-long-select" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select area</option>';
        foreach ($D['areas'] as $c) {
            $sel = strcasecmp($v, (string) $c) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'house_type') {
        $v = trim((string) ($raw ?? ''));
        $opts = ['' => 'Select house type', 'Does not matter' => 'Does not matter', 'Rented' => 'Rented', 'Owned' => 'Owned', 'On Lease' => 'On Lease'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = strcasecmp($v, (string) $val) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'residence') {
        $v = trim((string) ($raw ?? ''));
        $opts = ['' => 'Select residence', 'Does not matter' => 'Does not matter', 'Citizen' => 'Citizen', 'Permanent Resident' => 'Permanent Resident', 'Student Visa' => 'Student Visa', 'Temporary Visa' => 'Temporary Visa', 'Work permit' => 'Work permit'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = strcasecmp($v, (string) $val) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'contact_person_relation') {
        $v = strtolower(trim((string) ($raw ?? '')));
        $opts = ['' => 'Select relation', 'self' => 'Self', 'brother' => 'Brother', 'sister' => 'Sister', 'parent' => 'Parent', 'relative' => 'Relative'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = $v === strtolower((string) $val) ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'height' && !empty($D['heights'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select height</option>';
        foreach ($D['heights'] as $h) {
            $sel = strcasecmp($v, (string) $h) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $h, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $h, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'weight' && !empty($D['weights'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select weight</option>';
        foreach ($D['weights'] as $h) {
            $sel = strcasecmp($v, (string) $h) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $h, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $h, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'eating_habits') {
        $v = trim((string) ($raw ?? ''));
        $opts = ['' => 'Select eating habits', 'Does not matter' => 'Does not matter', 'Occasionally Non-Veg' => 'Occasionally Non-Veg', 'Veg' => 'Veg', 'Eggetarian' => 'Eggetarian', 'Non-Veg' => 'Non-Veg'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = strcasecmp($v, (string) $val) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'body_type') {
        $v = trim((string) ($raw ?? ''));
        $opts = ['' => 'Select body type', 'Does not matter' => 'Does not matter', 'Slim' => 'Slim', 'Average' => 'Average', 'Athletic' => 'Athletic', 'Heavy' => 'Heavy'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = strcasecmp($v, (string) $val) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'skin_tone') {
        $v = trim((string) ($raw ?? ''));
        $opts = ['' => 'Select skin tone', 'Does not matter' => 'Does not matter', 'Wheatish' => 'Wheatish', 'Very Fair' => 'Very Fair', 'Fair' => 'Fair', 'Wheatish Brown' => 'Wheatish Brown', 'Dark' => 'Dark'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = strcasecmp($v, (string) $val) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'blood_group') {
        $v = trim((string) ($raw ?? ''));
        $opts = ['', 'A+', 'A-', 'AB+', 'AB-', 'B+', 'B-', 'O+', 'O-'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select blood group</option>';
        foreach (array_slice($opts, 1) as $bg) {
            $sel = strcasecmp($v, $bg) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars($bg, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($bg, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'profile_by') {
        $v = trim((string) ($raw ?? ''));
        $opts = ['' => 'Select profile by', 'Does not matter' => 'Does not matter', 'Self' => 'Self', 'Parents' => 'Parents', 'Guardian' => 'Guardian', 'Friends' => 'Friends', 'Sibling' => 'Sibling', 'Relatives' => 'Relatives'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = strcasecmp($v, (string) $val) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'reference') {
        $v = trim((string) ($raw ?? ''));
        $opts = ['' => 'Select reference', 'Does not matter' => 'Does not matter', 'Advertisements' => 'Advertisements', 'Friends' => 'Friends', 'Search Engines' => 'Search Engines', 'Others' => 'Others'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = strcasecmp($v, (string) $val) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'family_type') {
        $v = trim((string) ($raw ?? ''));
        $opts = ['' => 'Select family type', 'Does not matter' => 'Does not matter', 'Separate Family' => 'Separate Family', 'Joint Family' => 'Joint Family'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = strcasecmp($v, (string) $val) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'family_status') {
        $v = trim((string) ($raw ?? ''));
        $opts = ['' => 'Select family status', 'Does not matter' => 'Does not matter', 'Rich' => 'Rich', 'Upper Middle Class' => 'Upper Middle Class', 'Middle Class' => 'Middle Class', 'Lower Middle Class' => 'Lower Middle Class', 'Poor Family' => 'Poor Family'];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = strcasecmp($v, (string) $val) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'total_children') {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select number of children</option>';
        foreach (['0', '1', '2', '3', '4', '5+'] as $n) {
            $sel = strcasecmp($v, $n) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars($n, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($n, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'no_of_brothers' || $col === 'no_of_sisters') {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select</option>';
        echo '<option value="Does not matter"' . (strcasecmp($v, 'Does not matter') === 0 ? ' selected' : '') . '>Does not matter</option>';
        foreach (['0', '1', '2', '3', '4', '4+'] as $n) {
            $sel = strcasecmp($v, $n) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars($n, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($n, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'no_of_married_brother') {
        $v = trim((string) ($raw ?? ''));
        $opts = [
            '' => 'Select',
            'Does not matter' => 'Does not matter',
            'No married brother' => 'No married brother',
            'One married brother' => 'One married brother',
            'Two married brothers' => 'Two married brothers',
            'Three married brothers' => 'Three married brothers',
            'Four married brothers' => 'Four married brothers',
            'Above four married brothers' => 'Above four married brothers',
        ];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = strcasecmp($v, (string) $val) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'no_of_married_sister') {
        $v = trim((string) ($raw ?? ''));
        $opts = [
            '' => 'Select',
            'Does not matter' => 'Does not matter',
            'No married sister' => 'No married sister',
            'One married sister' => 'One married sister',
            'Two married sisters' => 'Two married sisters',
            'Three married sisters' => 'Three married sisters',
            'Four married sisters' => 'Four married sisters',
            'Above four married sisters' => 'Above four married sisters',
        ];
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '">';
        foreach ($opts as $val => $label) {
            $sel = strcasecmp($v, (string) $val) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if (($col === 'partner_from_age' || $col === 'partner_to_age') && !empty($D['ages'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select age</option>';
        echo '<option value="0"' . ($v === '0' || strcasecmp($v, 'any') === 0 ? ' selected' : '') . '>Any</option>';
        foreach ($D['ages'] as $a) {
            $sel = strcasecmp($v, (string) $a) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $a, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $a, ENT_QUOTES, 'UTF-8') . ' years</option>';
        }
        echo '</select>';

        return;
    }

    if (($col === 'partner_caste' || $col === 'partner_caste_exception') && !empty($D['castes'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select</option>';
        echo '<option value="Does not matter"' . (strcasecmp($v, 'Does not matter') === 0 ? ' selected' : '') . '>Does not matter</option>';
        foreach ($D['castes'] as $c) {
            $sel = strcasecmp($v, (string) $c) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'partner_state' && !empty($D['states'])) {
        $v = trim((string) ($raw ?? ''));
        echo '<select class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '"><option value="">Select state</option>';
        foreach ($D['states'] as $c) {
            $sel = strcasecmp($v, (string) $c) === 0 ? ' selected' : '';
            echo '<option value="' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars((string) $c, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    // ----- Multi-select (stored as JSON or CSV; POST as name[]) -----
    $multiDefs = [
        'language_known' => ['name' => 'language_known', 'options' => $D['languages'] ?? []],
        'looking_for' => ['name' => 'looking_for', 'options' => ['Does not matter', 'Unmarried', 'Widow/Widower', 'Divorcee', 'Separated', 'Married']],
        'partner_complexion' => ['name' => 'partner_complexion', 'options' => ['Does not matter', 'Wheatish', 'Very Fair', 'Fair', 'Wheatish Brown', 'Dark']],
        'partner_body_type' => ['name' => 'partner_body_type', 'options' => ['Does not matter', 'Slim', 'Average', 'Athletic', 'Heavy']],
        'partner_eating_habit' => ['name' => 'partner_eating_habit', 'options' => ['Does not matter', 'Veg', 'Non-Veg', 'Eggetarian']],
        'partner_smoking_habit' => ['name' => 'partner_smoking_habit', 'options' => ['Does not matter', 'No', 'Yes', 'Occasionally']],
        'partner_drinking_habit' => ['name' => 'partner_drinking_habit', 'options' => ['Does not matter', 'No', 'Yes', 'Occasionally']],
        'partner_education' => ['name' => 'partner_education', 'options' => array_merge(['Does not matter'], $D['educations'] ?? [])],
        'partner_employed_in' => ['name' => 'partner_employed_in', 'options' => ['Does not matter', 'Private', 'Government', 'Business', 'Defence', 'Others']],
        'partner_occupation' => ['name' => 'partner_occupation', 'options' => array_merge(['Does not matter'], $D['jobs'] ?? [])],
        'partner_designation' => ['name' => 'partner_designation', 'options' => array_merge(['Does not matter'], $D['designations'] ?? [])],
        'partner_country' => ['name' => 'partner_country', 'options' => $D['countries_names'] ?? []],
        'partner_country_exception' => ['name' => 'partner_country_exception', 'options' => $D['countries_names'] ?? []],
        'partner_city' => ['name' => 'partner_city', 'options' => $D['cities'] ?? []],
        'partner_area' => ['name' => 'partner_area', 'options' => $D['areas'] ?? []],
    ];

    if (isset($multiDefs[$col])) {
        $def = $multiDefs[$col];
        $parsed = admin_edit_steps_parse_multi($raw);
        $fieldName = $def['name'] . '[]';
        $multiId = 'ms_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $col);
        echo '<select class="form-control edit-steps-multi search-long-select" multiple id="' . htmlspecialchars($multiId, ENT_QUOTES, 'UTF-8') . '" name="' . htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') . '">';
        foreach ($def['options'] as $opt) {
            $opt = (string) $opt;
            $sel = admin_edit_steps_in_multi_array($opt, $parsed) ? ' selected' : '';
            echo '<option value="' . htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';

        return;
    }

    if ($col === 'dob') {
        $dval = $raw && $raw !== '0000-00-00' ? htmlspecialchars(substr((string) $raw, 0, 10), ENT_QUOTES, 'UTF-8') : '';
        echo '<input type="date" class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '" value="' . $dval . '">';

        return;
    }

    if ($col === 'birth_time') {
        $tval = $raw ? htmlspecialchars(substr((string) $raw, 0, 5), ENT_QUOTES, 'UTF-8') : '';
        echo '<input type="time" class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '" value="' . $tval . '">';

        return;
    }

    if ($col === 'email') {
        echo '<input type="email" class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '" value="' . htmlspecialchars((string) ($raw ?? ''), ENT_QUOTES, 'UTF-8') . '">';

        return;
    }

    if (in_array($col, ['registration_fee', 'final_fee', 'house_size_marla', 'partner_house_size_from', 'partner_house_size_to'], true)) {
        echo '<input type="number" class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '" value="' . htmlspecialchars((string) ($raw ?? ''), ENT_QUOTES, 'UTF-8') . '">';

        return;
    }

    $textareaPattern = '/bio|about|address|comment|description|details|summary|notes|experience|story|hobby|expectations|family_details/i';
    if (preg_match($textareaPattern, $col) || $col === 'about_us') {
        echo '<textarea class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '" rows="4">'
            . htmlspecialchars((string) ($raw ?? ''), ENT_QUOTES, 'UTF-8') . '</textarea>';

        return;
    }

    echo '<input type="text" class="form-control" id="' . $fidEsc . '" name="' . $nameEsc . '" value="'
        . htmlspecialchars((string) ($raw ?? ''), ENT_QUOTES, 'UTF-8') . '">';
}
