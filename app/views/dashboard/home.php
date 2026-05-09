<?php require __DIR__ . '/../partials/left-panel.php'; ?>

<?php
$error = $error ?? '';
$success = $success ?? '';
$csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$assignments = $assignments ?? [];
$discoverMembers = $discoverMembers ?? [];
$feedApprovePopup = !empty($feedApprovePopup);
?>

<div class="dash-content-wrapper">
    <div class="container mt-4">

        <h3 class="mt-0" style="font-weight: 700;">Member feed</h3>
        <p class="text-muted">Profiles assigned by admin and suggested opposite-gender profiles. Contact details stay hidden until admin confirms your match.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <h4 style="font-weight: 700; margin-top: 24px; font-size: 18px;">Assigned by admin</h4>
        <p class="text-muted small">Open a profile, then accept, decline, or save it to your list.</p>

        <div class="row">

            <?php if (!empty($assignments)): ?>
                <?php foreach ($assignments as $assignment): ?>

                    <?php
                    if (!empty($assignment->avatar)) {
                        $avatar = public_url_for_path((string) $assignment->avatar);
                    } else {
                        $gAs = strtolower(trim((string) ($assignment->gender ?? '')));
                        $avatar = ($gAs === 'female' || strncmp($gAs, 'female', 6) === 0)
                            ? public_url_for_path('assets/images/female.png')
                            : public_url_for_path('assets/images/male.png');
                    }

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
                    <div class="alert alert-info">No admin-assigned profiles yet.</div>
                </div>
            <?php endif; ?>

        </div>

        <hr style="margin: 28px 0;">

        <h4 style="font-weight: 700; font-size: 18px;">Discover</h4>
        <p class="text-muted small">Opposite-gender members (not assigned to you). Approve or defer to update your feed; contact details stay hidden here.</p>

        <div class="row">
            <?php if (!empty($discoverMembers)): ?>
                <?php foreach ($discoverMembers as $dm): ?>
                    <?php
                    $did = (int)($dm['id'] ?? 0);
                    if ($did <= 0) {
                        continue;
                    }
                    if (!empty($dm['avatar'])) {
                        $davatar = public_url_for_path((string) $dm['avatar']);
                    } else {
                        $gDm = strtolower(trim((string) ($dm['gender'] ?? '')));
                        $davatar = ($gDm === 'female' || strncmp($gDm, 'female', 6) === 0)
                            ? public_url_for_path('assets/images/female.png')
                            : public_url_for_path('assets/images/male.png');
                    }
                    $dage = '-';
                    if (!empty($dm['dob']) && $dm['dob'] !== '0000-00-00') {
                        try {
                            $ddob = new DateTime($dm['dob']);
                            $dage = (new DateTime())->diff($ddob)->y;
                        } catch (Exception $e) {
                            $dage = '-';
                        }
                    }
                    ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="insta-card shadow-sm" style="border-radius: 8px; overflow: hidden;">
                            <div class="insta-card-img">
                                <img src="<?= htmlspecialchars($davatar) ?>" alt="" style="width:100%; height:240px; object-fit:cover;">
                            </div>
                            <div class="insta-card-body text-center" style="padding: 12px;">
                                <h5 class="mb-1" style="font-weight: 600; font-size: 16px;">
                                    <?= htmlspecialchars(trim(($dm['first_name'] ?? '') . ' ' . ($dm['second_name'] ?? ''))) ?>
                                </h5>
                                <p class="text-muted small mb-1">
                                    <?= htmlspecialchars($dm['gender'] ?? '-') ?>
                                    · <?= htmlspecialchars($dm['religion'] ?? '-') ?>
                                </p>
                                <p class="text-muted small mb-2">Age: <?= htmlspecialchars((string)$dage) ?></p>

                                <a href="<?= BASE_URL ?>/dashboard/user/<?= $did ?>?context=discover"
                                   class="btn btn-primary btn-sm btn-block" style="margin-bottom: 6px;">View profile</a>

                                <form method="post" action="<?= BASE_URL ?>/dashboard/feed-action" style="margin-bottom: 4px;">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="target_user_id" value="<?= $did ?>">
                                    <button type="submit" class="btn btn-success btn-sm btn-block">Approve</button>
                                </form>
                                <form method="post" action="<?= BASE_URL ?>/dashboard/feed-action" style="margin-bottom: 4px;">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="action" value="deferred">
                                    <input type="hidden" name="target_user_id" value="<?= $did ?>">
                                    <button type="submit" class="btn btn-warning btn-sm btn-block">Deferred</button>
                                </form>
                                <form method="post" action="<?= BASE_URL ?>/dashboard/save-profile">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="user_id" value="<?= $did ?>">
                                    <input type="hidden" name="return" value="dashboard">
                                    <button type="submit" class="btn btn-default btn-sm btn-block"><i class="fa fa-bookmark"></i> Save to list</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">No discovery profiles right now. Complete your gender in your profile, or check back later.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($feedApprovePopup): ?>
<div id="feedApproveOverlay" style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:10050;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;padding:24px;border-radius:10px;max-width:420px;width:100%;box-shadow:0 8px 32px rgba(0,0,0,.2);">
        <h4 style="margin-top:0;font-weight:700;">Next steps</h4>
        <p style="margin-bottom:20px;">Thank you for your interest. Please <strong>contact admin or support</strong> so we can take the next steps with this match.</p>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('feedApproveOverlay').style.display='none'">OK</button>
    </div>
</div>
<?php endif; ?>
