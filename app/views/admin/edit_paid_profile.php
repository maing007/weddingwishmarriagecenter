<?php
$title = "Edit Paid Profiles";
require __DIR__.'/partials/header.php';
require __DIR__.'/partials/sidebar.php';
?>




<?php if (!$package): die('Invalid package'); endif; ?>
<div class="container mt-4">
<form method="post" action="<?= BASE_URL ?>/admin/paid-profiles/update">

<input type="hidden" name="id" value="<?= $package['id'] ?>">

<label>Start Date</label>
<input type="date" name="start_date"
       value="<?= $package['start_date'] ?>" class="form-control">

<label>End Date</label>
<input type="date" name="end_date"
       value="<?= $package['end_date'] ?>" class="form-control">

<label>Status</label>
<select name="is_paid" class="form-control">
    <option value="1" <?= $package['is_paid'] ? 'selected' : '' ?>>Paid</option>
    <option value="0" <?= !$package['is_paid'] ? 'selected' : '' ?>>Free</option>
</select>

<br>
<button class="btn btn-success">Update Package</button>

</form>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
