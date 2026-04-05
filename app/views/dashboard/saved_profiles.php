<?php
require __DIR__ . '/../partials/left-panel.php';

$error = $error ?? '';
$success = $success ?? '';
$csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div class="dash-content-wrapper">
    <div class="container mt-4">

        <h3 style="font-weight: 700;">Saved profiles</h3>
        <p class="text-muted">Bookmarks you added from the feed or profile pages.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (empty($savedProfiles)): ?>
            <div class="alert alert-info">You haven’t saved any profiles yet. Use <strong>Save to list</strong> on the dashboard feed.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($savedProfiles as $profile): ?>
                    <?php
                    if (!empty($profile['avatar'])) {
                        $profileImg = public_url_for_path((string) $profile['avatar']);
                    } else {
                        $gSv = strtolower(trim((string) ($profile['gender'] ?? '')));
                        $profileImg = ($gSv === 'female' || strncmp($gSv, 'female', 6) === 0)
                            ? public_url_for_path('assets/images/female.png')
                            : public_url_for_path('assets/images/male.png');
                    }

                    $fullName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
                    $age = (!empty($profile['dob']) && $profile['dob'] !== '0000-00-00')
                        ? (new DateTime())->diff(new DateTime($profile['dob']))->y
                        : '-';
                    $pid = (int)($profile['id'] ?? 0);
                    ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="thumbnail text-center" style="padding: 15px; border-radius: 8px; border: 1px solid #e5e5e5; background: #fff;">
                            <img src="<?= htmlspecialchars($profileImg, ENT_QUOTES, 'UTF-8') ?>" class="img-circle" style="width:100px;height:100px;object-fit:cover;margin-bottom:10px;" alt="">
                            <h5 style="font-weight: 600;"><?= htmlspecialchars($fullName ?: 'Member', ENT_QUOTES, 'UTF-8') ?></h5>
                            <p class="text-muted small mb-2">
                                <?= htmlspecialchars($profile['gender'] ?? '-', ENT_QUOTES, 'UTF-8') ?> · Age: <?= htmlspecialchars((string)$age, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <a href="<?= BASE_URL ?>/dashboard/user/<?= $pid ?>" class="btn btn-primary btn-sm btn-block">View profile</a>
                            <form method="post" action="<?= BASE_URL ?>/dashboard/save-profile" style="margin-top: 8px;">
                                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                <input type="hidden" name="user_id" value="<?= $pid ?>">
                                <input type="hidden" name="return" value="saved">
                                <button type="submit" class="btn btn-default btn-sm btn-block"><i class="fa fa-bookmark"></i> Save again</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>
