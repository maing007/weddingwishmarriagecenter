<?php
/**
 * Member card photo + optional download (admin). Expects $cardUser (array with id, gender, avatar and/or photo* keys).
 */
if (empty($cardUser) || !is_array($cardUser)) {
    return;
}
$u = $cardUser;
$firstRel = admin_member_first_upload_relative_path($u);
$memberId = (int) ($u['id'] ?? 0);
// Serve through admin PHP (same path resolution as download) so production lists work when direct /uploads/ URLs 404 or mis-resolve.
if ($memberId > 0 && $firstRel !== '') {
    $listPhotoSrc = rtrim(BASE_URL, '/') . '/admin/users/member-photo?id=' . $memberId;
} else {
    $listPhotoSrc = admin_user_card_photo_url($u);
}
$listPhotoFallback = admin_user_default_avatar_url($u);
$showDl = $memberId > 0 && $firstRel !== '';
$dlHref = BASE_URL . '/admin/users/download-member-photo?id=' . $memberId;
?>
<div class="profile-image-stack">
    <div class="profile-image-box">
        <img src="<?= htmlspecialchars($listPhotoSrc, ENT_QUOTES, 'UTF-8') ?>"
             alt=""
             loading="lazy"
             data-fallback="<?= htmlspecialchars($listPhotoFallback, ENT_QUOTES, 'UTF-8') ?>"
             onerror="(function(el){var fb=el.dataset.fallback;if(!fb||el.dataset._fbok)return;el.dataset._fbok='1';if(el.src!==fb){el.src=fb;}})(this)">
    </div>
    <?php if ($showDl): ?>
        <a class="member-photo-download"
           href="<?= htmlspecialchars($dlHref, ENT_QUOTES, 'UTF-8') ?>"
           title="Download original uploaded photo"
           aria-label="Download original uploaded photo">
            <i class="fa fa-download" aria-hidden="true"></i>
        </a>
    <?php endif; ?>
</div>
