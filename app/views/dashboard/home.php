<?php require __DIR__ . '/../partials/left-panel.php'; ?>

<?php
$error = $error ?? '';
$success = $success ?? '';
$csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div class="dash-content-wrapper">
    <div class="container mt-4">

        <h3 class="mt-0" style="font-weight: 700;">Assigned members</h3>
        <p class="text-muted">Open a profile, then accept, decline, or save it to your list.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="row">

            <?php if (!empty($assignments)): ?>
                <?php foreach ($assignments as $assignment): ?>

                    <?php
                    $avatar = !empty($assignment->avatar)
                        ? BASE_URL . '/' . ltrim((string)$assignment->avatar, '/')
                        : BASE_URL . '/assets/images/default-avatar.png';

                    $age = '-';
                    if (!empty($assignment->dob) && $assignment->dob !== '0000-00-00') {
                        try {
                            $dob = new DateTime($assignment->dob);
                            $now = new DateTime();
                            $age = $now->diff($dob)->y;
                        } catch (Exception $e) {
                            $age = '-';
                        }
                    }

                    $status = $assignment->status;
                    $aid = (int)$assignment->id;
                    $mid = (int)$assignment->assigned_member;
                    ?>

                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="insta-card shadow-sm" style="border-radius: 8px; overflow: hidden; position: relative;">

                            <div style="position:absolute; top:8px; right:8px; z-index: 2;">
                                <?php if ($status == 'pending'): ?>
                                    <span class="label label-warning">Pending</span>
                                <?php elseif ($status == 'opened'): ?>
                                    <span class="label label-info">Opened</span>
                                <?php elseif ($status == 'accepted'): ?>
                                    <span class="label label-success">Accepted</span>
                                <?php elseif ($status == 'declined'): ?>
                                    <span class="label label-danger">Declined</span>
                                <?php endif; ?>
                            </div>

                            <div class="insta-card-img">
                                <img src="<?= htmlspecialchars($avatar) ?>" alt="" style="width:100%; height:240px; object-fit:cover;">
                            </div>

                            <div class="insta-card-body text-center" style="padding: 12px;">

                                <h5 class="mb-1" style="font-weight: 600; font-size: 16px;">
                                    <?= htmlspecialchars(trim(($assignment->first_name ?? '') . ' ' . ($assignment->last_name ?? ''))) ?>
                                </h5>

                                <p class="text-muted small mb-1">
                                    <?= htmlspecialchars($assignment->gender ?? '-') ?>
                                    · <?= htmlspecialchars($assignment->religion ?? '-') ?>
                                </p>

                                <p class="text-muted small mb-2">Age: <?= htmlspecialchars((string)$age) ?></p>

                                <p class="small text-muted mb-2">
                                    Opened <?= (int)$assignment->opened_count ?> time(s)
                                </p>

                                <?php if (!empty($assignment->admin_comment)): ?>
                                    <p class="small text-primary text-left" style="margin-bottom: 10px;">
                                        <strong>Note:</strong><br>
                                        <?= htmlspecialchars($assignment->admin_comment) ?>
                                    </p>
                                <?php endif; ?>

                                <div style="margin-top: 10px;">
                                    <a href="<?= BASE_URL ?>/dashboard/openAssignment?id=<?= $aid ?>"
                                       class="btn btn-primary btn-sm btn-block" style="margin-bottom: 6px;">
                                        View profile
                                    </a>

                                    <?php if ($status == 'pending' || $status == 'opened'): ?>
                                        <a href="<?= BASE_URL ?>/dashboard/accept-assignment?id=<?= $aid ?>"
                                           class="btn btn-success btn-sm btn-block" style="margin-bottom: 4px;">Accept</a>
                                        <a href="<?= BASE_URL ?>/dashboard/decline-assignment?id=<?= $aid ?>"
                                           class="btn btn-danger btn-sm btn-block"
                                           onclick="return confirm('Decline this assignment?');">Decline</a>
                                    <?php endif; ?>

                                    <form method="post" action="<?= BASE_URL ?>/dashboard/save-profile" style="margin-top: 8px;">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                        <input type="hidden" name="user_id" value="<?= $mid ?>">
                                        <input type="hidden" name="return" value="dashboard">
                                        <button type="submit" class="btn btn-default btn-sm btn-block">
                                            <i class="fa fa-bookmark"></i> Save to list
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">No assigned profiles yet.</div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
