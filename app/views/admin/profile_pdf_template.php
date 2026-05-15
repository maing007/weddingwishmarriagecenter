<?php
declare(strict_types=1);

require_once APP_ROOT . '/app/helpers/profile_pdf_template.php';

$e = static function ($v): string {
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
};

$profilePdfPreferAdminMemberPhoto = true;
$pv = profile_pdf_template_compute_vars($user, true);
$pdfFileTitle = $pv['pdfFileTitle'];

$backUrl = rtrim(BASE_URL, '/') . '/admin/users';
$from = strtolower(trim((string) ($_GET['from'] ?? '')));
if (in_array($from, ['match', 'match-making'], true)) {
    $backUrl = rtrim(BASE_URL, '/') . '/admin/match-making';
} elseif ($from === 'income-rishta') {
    $backUrl = rtrim(BASE_URL, '/') . '/admin/accounts/income/rishta-fee';
} elseif ($from === 'income-reg' || $from === 'income-registration') {
    $backUrl = rtrim(BASE_URL, '/') . '/admin/accounts/income/registration-fee';
}

$title = 'Profile PDF — ' . $pdfFileTitle;
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Gulzar&display=swap" rel="stylesheet">
<style>
        .admin-pdf-topbar {
            justify-content: flex-start;
            padding: 0 18px;
        }
        .admin-pdf-topbar .mobile-menu-btn {
            margin-right: 14px;
            flex-shrink: 0;
        }
        .admin-pdf-topbar .admin-topbar-heading {
            font-size: 13px;
            font-weight: 600;
            color: #565656;
            line-height: 1.5;
            max-width: min(520px, 55vw);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .admin-pdf-topbar .admin-profile {
            margin-left: auto;
        }
        .admin-pdf-page {
            padding-top: 12px;
        }
        .pdf-toolbar { margin-bottom: 12px; max-width: 794px; }
    </style>

<div class="admin-main">
    <div class="admin-topbar admin-pdf-topbar">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu">
            <i class="fa fa-bars"></i>
        </button>
        <span class="admin-topbar-heading" title="<?= $e($pdfFileTitle) ?>"><?= $e($pdfFileTitle) ?></span>
        <div class="admin-profile" id="adminProfileTrigger">
            <div class="admin-profile-box">
                <span><?= htmlspecialchars($this->displayadminname()) ?></span>
                <i class="fa fa-user"></i>
            </div>
            <div class="admin-dropdown" id="adminDropdown">
                <a href="<?= $e(BASE_URL) ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
                <a href="<?= $e(BASE_URL) ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </div>

    <main class="admin-page admin-pdf-page">

<div class="pdf-toolbar">
    <a class="btn btn-info" style="margin-bottom: 10px;" href="<?= $e($backUrl) ?>">
        <i class="fa fa-arrow-left"></i> Back to list
    </a>
</div>

<?php require __DIR__ . '/../partials/profile_pdf_card.php'; ?>

<button type="button" class="btn btn-primary btn-lg" style="margin-top: 10px;" id="pdfDownloadBtn">Download PDF</button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
(function () {
    var safeName = <?= json_encode($pdfFileTitle, JSON_UNESCAPED_UNICODE) ?>;
    var dl = document.getElementById('pdfDownloadBtn');
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

    </main>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
