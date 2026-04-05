<?php
/**
 * Member card photo + optional download (admin). Expects $cardUser (array with id, gender, avatar and/or photo* keys).
 */
if (empty($cardUser) || !is_array($cardUser)) {
    return;
}
$u = $cardUser;
$firstRel = admin_member_first_upload_relative_path($u);
if ($firstRel !== '') {
    $u['avatar'] = $firstRel;
}
$listPhotoSrc = admin_user_card_photo_url($u);
$listPhotoFallback = admin_user_default_avatar_url($u);
$memberId = (int) ($u['id'] ?? 0);
$showDl = $memberId > 0 && $firstRel !== '';
$dlHref = BASE_URL . '/admin/users/download-member-photo?id=' . $memberId;
?>
<div class="profile-image-stack">
    <div class="profile-image-box">
        <img src="<?= htmlspecialchars($listPhotoSrc, ENT_QUOTES, 'UTF-8') ?>"
             alt=""
             data-fallback="<?= htmlspecialchars($listPhotoFallback, ENT_QUOTES, 'UTF-8') ?>"
             onerror="if(this.dataset.fallback && this.src !== this.dataset.fallback){this.src=this.dataset.fallback;}">
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
