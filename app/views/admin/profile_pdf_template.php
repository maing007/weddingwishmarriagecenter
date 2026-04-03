<?php
$fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['second_name'] ?? ''));
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profile PDF</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body{font-family:Arial,sans-serif;background:#f1f4f8;padding:20px}
        .toolbar{margin-bottom:15px}
        .sheet{background:#fff;max-width:850px;margin:0 auto;padding:24px;border:1px solid #ddd}
        .head{text-align:center;border-bottom:1px solid #ddd;padding-bottom:10px;margin-bottom:12px}
        .head img{max-height:55px}
        .grid{display:grid;grid-template-columns:1fr 1fr;gap:8px 20px}
        .item{font-size:13px}
        .item b{display:inline-block;min-width:170px}
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="<?= BASE_URL ?>/admin/users" class="btn btn-primary">Back</a>
        <button onclick="downloadPdf()" class="btn btn-success">Download PDF</button>
    </div>
    <div id="pdfArea" class="sheet">
        <div class="head">
            <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Logo">
            <h3 style="margin:6px 0 0">Wedding Wish Marriage Centre</h3>
        </div>
        <h4 style="margin-top:0"><?= htmlspecialchars($fullName !== '' ? $fullName : 'Member Profile') ?></h4>
        <div class="grid">
            <?php foreach ($user as $key => $value): ?>
                <div class="item"><b><?= htmlspecialchars(ucwords(str_replace('_', ' ', $key))) ?>:</b> <?= htmlspecialchars((string)($value ?? '-')) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
    function downloadPdf(){
        const opt = {
            margin: 0.4,
            filename: "Profile-<?= (int)$user['id'] ?>.pdf",
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().from(document.getElementById('pdfArea')).set(opt).save();
    }
    </script>
</body>
</html>
