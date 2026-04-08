<?php
require __DIR__ . '/../partials/left-panel.php';

/** @var array $member */
/** @var array|null $profileDetails */
/** @var object|null $assignment */
/** @var int $memberId */

$error = $error ?? '';
$success = $success ?? '';
$assignment = $assignment ?? null;
$profileDetails = $profileDetails ?? null;
$isDiscoverContext = $isDiscoverContext ?? false;
$canDiscover = $canDiscover ?? false;
$feedInteraction = $feedInteraction ?? null;
$showContactDetails = $showContactDetails ?? ($assignment !== null);

$fullName = trim(($member['first_name'] ?? '') . ' ' . ($member['second_name'] ?? $member['last_name'] ?? ''));
$status = $assignment ? (string)($assignment->status ?? '') : '';
$assignmentId = $assignment ? (int)$assignment->id : 0;
$csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$bio = trim((string)($member['about_us'] ?? $member['bio'] ?? ''));

$viewedAt = $feedInteraction['viewed_at'] ?? null;
$approvedAt = $feedInteraction['approved_at'] ?? null;
$deferredAt = $feedInteraction['deferred_at'] ?? null;
?>
<div class="dash-content-wrapper">
    <div class="container mt-4 feed-detail-page">
        <style>
            .feed-detail-page .fd-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 4px 18px rgba(0,0,0,.06);
            }
            .feed-detail-page .fd-avatar {
                width: 140px;
                height: 140px;
                border-radius: 50%;
                object-fit: cover;
                border: 3px solid #eee;
            }
            .feed-detail-page .fd-actions form { display: inline-block; margin-right: 6px; margin-bottom: 6px; }
            .feed-detail-page .fd-actions .btn { margin-right: 4px; margin-bottom: 6px; }
            .feed-detail-page .fd-table th { width: 36%; background: #f8f9fa; font-weight: 600; }
        </style>

        <p class="text-muted" style="margin-bottom: 12px;">
            <a href="<?= BASE_URL ?>/dashboard"><i class="fa fa-arrow-left"></i> Back to feed</a>
        </p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="fd-card">
            <div class="row">
                <div class="col-sm-4 text-center" style="margin-bottom: 15px;">
                    <img src="<?= htmlspecialchars($profileImgUrl, ENT_QUOTES, 'UTF-8') ?>" class="fd-avatar" alt="">
                </div>
                <div class="col-sm-8">
                    <h2 style="margin-top: 0; font-size: 22px;"><?= htmlspecialchars($fullName ?: 'Member', ENT_QUOTES, 'UTF-8') ?></h2>
                    <p class="text-muted">
                        <?= htmlspecialchars($member['gender'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        <?php if (!empty($age)): ?> · Age <?= (int)$age ?><?php endif; ?>
                        <?php if (!empty($member['religion'])): ?>
                            · <?= htmlspecialchars($member['religion'], ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </p>
                    <?php if ($assignment && $assignmentId): ?>
                        <p style="margin-bottom: 10px;">
                            <?php if ($status === 'pending'): ?>
                                <span class="label label-warning">Pending</span>
                            <?php elseif ($status === 'opened'): ?>
                                <span class="label label-info">Opened</span>
                            <?php elseif ($status === 'accepted'): ?>
                                <span class="label label-success">Accepted</span>
                            <?php elseif ($status === 'declined'): ?>
                                <span class="label label-danger">Declined</span>
                            <?php endif; ?>
                        </p>
                    <?php elseif ($isDiscoverContext && $canDiscover): ?>
                        <p style="margin-bottom: 10px;">
                            <?php if (!empty($viewedAt)): ?>
                                <span class="label label-default">Viewed</span>
                            <?php else: ?>
                                <span class="label label-info">New</span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>

                    <div class="fd-actions">
                        <form method="post" action="<?= BASE_URL ?>/dashboard/save-profile">
                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                            <input type="hidden" name="user_id" value="<?= (int)$memberId ?>">
                            <input type="hidden" name="return" value="<?= ($isDiscoverContext && $canDiscover) ? 'discover-profile' : ($assignmentId ? 'assignment' : 'dashboard') ?>">
                            <?php if ($assignmentId): ?>
                                <input type="hidden" name="assignment_id" value="<?= $assignmentId ?>">
                            <?php endif; ?>
                            <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-bookmark"></i> Save profile</button>
                        </form>

                        <?php if ($assignment && $assignmentId && ($status === 'pending' || $status === 'opened')): ?>
                            <a class="btn btn-success btn-sm" href="<?= BASE_URL ?>/dashboard/accept-assignment?id=<?= $assignmentId ?>">Accept</a>
                            <a class="btn btn-danger btn-sm" href="<?= BASE_URL ?>/dashboard/decline-assignment?id=<?= $assignmentId ?>"
                               onclick="return confirm('Decline this assignment?');">Decline</a>
                        <?php elseif ($isDiscoverContext && $canDiscover): ?>
                            <form method="post" action="<?= BASE_URL ?>/dashboard/feed-action" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="target_user_id" value="<?= (int)$memberId ?>">
                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                            </form>
                            <form method="post" action="<?= BASE_URL ?>/dashboard/feed-action" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                <input type="hidden" name="action" value="deferred">
                                <input type="hidden" name="target_user_id" value="<?= (int)$memberId ?>">
                                <button type="submit" class="btn btn-warning btn-sm">Deferred</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <?php if ($showContactDetails && (!empty($member['phone']) || !empty($member['email']))): ?>
                        <div class="small text-muted" style="margin-top: 12px;">
                            <?php if (!empty($member['email'])): ?>
                                <div><strong>Email:</strong> <?= htmlspecialchars((string)$member['email'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                            <?php if (!empty($member['phone'])): ?>
                                <div><strong>Phone:</strong> <?= htmlspecialchars(trim(($member['country_code'] ?? '') . ' ' . (string)$member['phone']), ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>
                    <?php elseif (!$showContactDetails): ?>
                        <p class="text-muted small" style="margin-top: 12px;"><em>Contact details are hidden. <?php if ($assignment): ?>They may be shared when your match is confirmed by admin.<?php else ?>Contact admin or support if you wish to proceed.<?php endif ?></em></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($profileDetails): ?>
            <div class="fd-card">
                <h4 style="margin-top: 0; font-size: 16px; font-weight: 700;">Profile details</h4>
                <div class="table-responsive">
                    <table class="table table-bordered fd-table">
                        <tbody>
                        <tr><th>Education</th><td><?= htmlspecialchars((string)($profileDetails['education'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '—' ?></td></tr>
                        <tr><th>Occupation</th><td><?= htmlspecialchars((string)($profileDetails['occupation'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '—' ?></td></tr>
                        <tr><th>Annual income</th><td><?= htmlspecialchars((string)($profileDetails['annual_income'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '—' ?></td></tr>
                        <tr><th>Height</th><td><?= htmlspecialchars((string)($profileDetails['height'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '—' ?></td></tr>
                        <tr><th>Mother tongue</th><td><?= htmlspecialchars((string)($profileDetails['mother_tongue'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '—' ?></td></tr>
                        <tr><th>Lifestyle</th><td><?= htmlspecialchars(trim(implode(' · ', array_filter([
                            $profileDetails['eating_habits'] ?? '',
                            $profileDetails['drinking'] ?? '',
                            $profileDetails['smoking'] ?? '',
                        ]))), ENT_QUOTES, 'UTF-8') ?: '—' ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <div class="fd-card">
            <h4 style="margin-top: 0; font-size: 16px; font-weight: 700;">About</h4>
            <?php if ($bio !== ''): ?>
                <p><?= nl2br(htmlspecialchars($bio, ENT_QUOTES, 'UTF-8')) ?></p>
            <?php else: ?>
                <p class="text-muted">No bio provided.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
