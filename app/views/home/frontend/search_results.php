<?php
$keyword = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$profiles = $profiles ?? [];
?>
<div class="container mt-4 mb-5">
    <div class="mega-box-new" style="background:#fff;border-radius:12px;padding:24px;border:1px solid #eee;">
        <?php if ($keyword !== ''): ?>
            <h2 class="mt-0" style="font-size:22px;font-weight:700;">
                Results for “<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>”
            </h2>
            <p class="text-muted">Profiles that mention this in name, religion, city, caste, sect, mother tongue, or about.</p>
        <?php else: ?>
            <h2 class="mt-0" style="font-size:22px;font-weight:700;">Search results</h2>
        <?php endif; ?>

        <?php if (!empty($profiles)): ?>
            <div class="row" style="margin-top:20px;">
                <?php foreach ($profiles as $profile): ?>
                    <?php
                    $pid = (int)($profile->id ?? 0);
                    $name = trim(($profile->first_name ?? '') . ' ' . ($profile->second_name ?? $profile->last_name ?? ''));
                    $img = !empty($profile->avatar_path)
                        ? BASE_URL . '/' . ltrim((string)$profile->avatar_path, '/')
                        : BASE_URL . '/assets/images/male.svg';
                    $ageStr = isset($profile->age) && $profile->age !== null ? (int)$profile->age . ' yrs' : '-';
                    ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="thumbnail text-center" style="padding:12px;border-radius:8px;">
                            <a href="<?= BASE_URL ?>/profile/<?= $pid ?>">
                                <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" class="img-responsive" style="width:100%;max-height:220px;object-fit:cover;border-radius:6px;" alt="">
                            </a>
                            <h4 style="font-size:16px;margin-top:10px;">
                                <a href="<?= BASE_URL ?>/profile/<?= $pid ?>">
                                    <?= htmlspecialchars($name !== '' ? $name : 'Member', ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </h4>
                            <p class="text-muted small mb-0">
                                <?= htmlspecialchars((string)($profile->gender ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                · <?= htmlspecialchars($ageStr, ENT_QUOTES, 'UTF-8') ?>
                                <?php if (!empty($profile->religion)): ?>
                                    · <?= htmlspecialchars((string)$profile->religion, ENT_QUOTES, 'UTF-8') ?>
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($profile->city) || !empty($profile->country)): ?>
                                <p class="small text-muted">
                                    <?= htmlspecialchars(trim(implode(', ', array_filter([(string)($profile->city ?? ''), (string)($profile->country ?? '')]))), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted mt-3">No profiles found. Try another keyword or use <a href="<?= BASE_URL ?>/search">advanced search</a>.</p>
        <?php endif; ?>
    </div>
</div>
