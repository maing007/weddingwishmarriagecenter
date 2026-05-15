<?php
require __DIR__ . '/../partials/left-panel.php';

/** @var array $profilePdfUser */
/** @var array $member */
/** @var object|null $assignment */
/** @var int $memberId */

$error = $error ?? '';
$success = $success ?? '';
$assignment = $assignment ?? null;
$isDiscoverContext = $isDiscoverContext ?? false;
$canDiscover = $canDiscover ?? false;
$feedInteraction = $feedInteraction ?? null;
$showContactDetails = $showContactDetails ?? ($assignment !== null);
$profilePdfUser = $profilePdfUser ?? [];

$status = $assignment ? (string)($assignment->status ?? '') : '';
$assignmentId = $assignment ? (int)$assignment->id : 0;
$csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');

$viewedAt = $feedInteraction['viewed_at'] ?? null;

$user = $profilePdfUser;
?>

<div class="dash-content-wrapper member-feed-pdf-view">
    <style>
        .member-feed-pdf-view { padding-bottom: 24px; }
        .member-feed-pdf-toolbar {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
        }
        .member-feed-pdf-toolbar .fd-actions form { display: inline-block; margin-right: 6px; margin-bottom: 6px; }
        .member-feed-pdf-toolbar .fd-actions .btn { margin-right: 4px; margin-bottom: 6px; }
        .member-feed-pdf-card-wrap { overflow-x: auto; padding-bottom: 8px; }
    </style>

    <div class="container-fluid" style="padding-top: 12px; max-width: 900px;">
        <p class="text-muted" style="margin-bottom: 12px;">
            <a href="<?= BASE_URL ?>/dashboard"><i class="fa fa-arrow-left"></i> Back to feed</a>
        </p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="member-feed-pdf-toolbar">
            <div class="row">
                <div class="col-sm-7">
                    <?php if ($assignment && $assignmentId): ?>
                        <p style="margin: 0 0 8px;">
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
                        <p style="margin: 0 0 8px;">
                            <?php if (!empty($viewedAt)): ?>
                                <span class="label label-default">Viewed</span>
                            <?php else: ?>
                                <span class="label label-info">New</span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

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
                <p class="text-muted small" style="margin-top: 12px; margin-bottom: 0;"><em>Contact details are hidden. <?php if ($assignment): ?>They may be shared when your match is confirmed by admin.<?php else: ?>Contact admin or support if you wish to proceed.<?php endif; ?></em></p>
            <?php endif; ?>
        </div>

        <div class="member-feed-pdf-card-wrap">
            <?php $profilePdfPreferAdminMemberPhoto = false;
            require __DIR__ . '/../partials/profile_pdf_card.php'; ?>
        </div>

        <button type="button" class="btn btn-primary" style="margin-top: 10px;" id="memberFeedPdfDownloadBtn">Download PDF</button>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script>
        (function () {
            var safeName = <?= json_encode($title ?? '', JSON_UNESCAPED_UNICODE) ?>;
            var dl = document.getElementById('memberFeedPdfDownloadBtn');
            if (!dl) return;
            dl.addEventListener('click', function () {
                var el = document.getElementById('wwProfilePdfContent');
                if (!el || typeof html2canvas === 'undefined' || !window.jspdf) {
                    alert('PDF tools failed to load. Check your network connection.');
                    return;
                }
                var btn = this;
                btn.disabled = true;
                html2canvas(el, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#4da7ba',
                    logging: false
                }).then(function (canvas) {
                    var img = canvas.toDataURL('image/png');
                    var doc = new jspdf.jsPDF({ unit: 'pt', format: 'a4', orientation: 'portrait' });
                    var pageWidth = doc.internal.pageSize.getWidth();
                    var pageHeight = doc.internal.pageSize.getHeight();
                    var imgWidth = canvas.width;
                    var imgHeight = canvas.height;
                    var scale = Math.min(pageWidth / imgWidth, pageHeight / imgHeight);
                    var w = imgWidth * scale;
                    var h = imgHeight * scale;
                    var x = (pageWidth - w) / 2;
                    var y = (pageHeight - h) / 2;
                    doc.addImage(img, 'PNG', x, y, w, h);
                    doc.save(safeName.replace(/[^\w\- ().]+/g, '_') + '.pdf');
                }).catch(function (err) {
                    console.error('PDF generation failed:', err);
                    alert('Failed to generate PDF. If photos are hosted on another domain, try opening images from this site only.');
                }).finally(function () {
                    btn.disabled = false;
                });
            });
        })();
        </script>
    </div>
</div>
