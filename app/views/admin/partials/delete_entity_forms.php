<?php
/**
 * Optional forms: set $deleteUserId (int), $deleteFeeId (int), $deleteRedirect (path like /admin/users).
 */
$deleteRedirect = isset($deleteRedirect) ? (string) $deleteRedirect : '/admin/users';
if ($deleteRedirect === '' || $deleteRedirect[0] !== '/') {
    $deleteRedirect = '/admin/users';
}
$csrf = htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8');
$du = isset($deleteUserId) ? (int) $deleteUserId : 0;
$df = isset($deleteFeeId) ? (int) $deleteFeeId : 0;
?>
<?php if ($du > 0): ?>
<form method="post" action="<?= htmlspecialchars(rtrim(BASE_URL, '/') . '/admin/user/delete', ENT_QUOTES, 'UTF-8') ?>" class="btn-action-form d-inline" onsubmit="return confirm('Permanently delete this member and related data (assignments, comments, fee rows linked to this profile, packages)? This cannot be undone.');">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="id" value="<?= $du ?>">
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($deleteRedirect, ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit" class="btn-action btn-action-danger" title="Delete member">Delete member</button>
</form>
<?php endif; ?>
<?php if ($df > 0): ?>
<form method="post" action="<?= htmlspecialchars(rtrim(BASE_URL, '/') . '/admin/accounts/income/delete-fee', ENT_QUOTES, 'UTF-8') ?>" class="btn-action-form d-inline" onsubmit="return confirm('Delete this fee / sales row and its payment proofs? This cannot be undone.');">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="fee_id" value="<?= $df ?>">
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($deleteRedirect, ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit" class="btn-action btn-action-danger" title="Delete fee row">Delete fee</button>
</form>
<?php endif; ?>
