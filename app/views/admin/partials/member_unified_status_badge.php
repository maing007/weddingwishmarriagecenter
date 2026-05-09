<?php
/**
 * Unified profile status badge + data attributes for live polling.
 *
 * @var array $cardUser member row with id, status or user_status, optional registration_fee_paid
 */
if (!function_exists('admin_member_unified_badge_meta')) {
    require_once dirname(__DIR__, 3) . '/helpers/admin_member_status.php';
}
$uid = (int) ($cardUser['id'] ?? 0);
if ($uid <= 0) {
    return;
}
$meta = admin_member_unified_badge_meta($cardUser);
$v = htmlspecialchars($meta['variant'], ENT_QUOTES, 'UTF-8');
$lab = htmlspecialchars($meta['label'], ENT_QUOTES, 'UTF-8');
$ic = htmlspecialchars($meta['icon'], ENT_QUOTES, 'UTF-8');
$ti = htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8');
?>
<span class="admin-profile-status-chip approved-badge status-<?= $v ?>"
      data-user-id="<?= $uid ?>"
      title="<?= $ti ?>">
    <i class="fa <?= $ic ?>" aria-hidden="true"></i>
    <?= $lab ?>
</span>
