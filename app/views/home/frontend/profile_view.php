<?php
/**
 * Public profile — same layout as admin PDF card (profile_pdf_card partial).
 *
 * @var object $profile user_details row (from SearchModel::getProfile)
 */

$user = json_decode(json_encode($profile), true);
if (!is_array($user)) {
    $user = [];
}
if (empty($user['work_detail']) && !empty($user['occupation'])) {
    $user['work_detail'] = (string) $user['occupation'];
}

require_once dirname(__DIR__, 3) . '/helpers/profile_pdf_template.php';
$profilePdfPreferAdminMemberPhoto = false;
$__pv = profile_pdf_template_compute_vars($user, false);
$pdfTitleForJs = $__pv['pdfFileTitle'];
?>

<div class="container" style="margin-top: 24px; margin-bottom: 48px;">
    <div class="public-profile-pdf-wrap" style="overflow-x: auto; padding-bottom: 12px;">
        <?php require dirname(__DIR__, 2) . '/partials/profile_pdf_card.php'; ?>
    </div>

    <button type="button" class="btn btn-primary" style="margin-top: 10px;" id="publicProfilePdfDownloadBtn">Download PDF</button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
(function () {
    var safeName = <?= json_encode($pdfTitleForJs, JSON_UNESCAPED_UNICODE) ?>;
    var dl = document.getElementById('publicProfilePdfDownloadBtn');
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
            alert('Failed to generate PDF. If photos are hosted on another domain, try again later.');
        }).finally(function () {
            btn.disabled = false;
        });
    });
})();
</script>
