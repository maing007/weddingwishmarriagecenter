<?php
declare(strict_types=1);

$e = static function ($v): string {
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
};

$fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['second_name'] ?? $user['last_name'] ?? ''));
if ($fullName === '') {
    $fullName = 'Member';
}

$matriRaw = matri_id_display((string) ($user['matri_id'] ?? ''), (int) ($user['id'] ?? 0), true);
$matriDisplay = '(' . $matriRaw . ')';

$pdfFileTitle = $matriDisplay . ' ' . $fullName;

$ageStr = '—';
if (!empty($user['dob'])) {
    try {
        $dob = new DateTimeImmutable((string) $user['dob']);
        $ageStr = (string) $dob->diff(new DateTimeImmutable('today'))->y;
    } catch (Exception $ex) {
        $ageStr = '—';
    }
}

$resolveMediaUrl = static function (string $path): string {
    return public_url_for_path($path);
};

$photoRaw = trim((string) ($user['photo2_url'] ?? ''));
if ($photoRaw === '') {
    $photoRaw = trim((string) ($user['photo1_status'] ?? ''));
}
if ($photoRaw === '') {
    $photoRaw = trim((string) ($user['avatar'] ?? ''));
}
foreach (['photo3_url', 'photo4_url', 'photo5_url', 'photo6_url'] as $pk) {
    if ($photoRaw === '') {
        $photoRaw = trim((string) ($user[$pk] ?? ''));
    }
}

$photoUsable = $photoRaw !== '';
if ($photoUsable) {
    $norm = strtolower(str_replace('\\', '/', $photoRaw));
    if (strpos($norm, 'uploads/avatars/default') !== false
        || strpos($norm, 'default-avatar') !== false
        || strpos($norm, 'avatar-placeholder') !== false) {
        $photoUsable = false;
    }
}
$profileImageUrl = '';
if ($photoUsable) {
    $profileImageUrl = $resolveMediaUrl($photoRaw);
}
if ($profileImageUrl === '') {
    $g = strtolower(trim((string) ($user['gender'] ?? '')));
    if ($g === 'female' || strncmp($g, 'female', 6) === 0) {
        $profileImageUrl = public_url_for_path('assets/images/female.png');
    } elseif ($g === 'male' || strncmp($g, 'male', 4) === 0) {
        $profileImageUrl = public_url_for_path('assets/images/male.png');
    } else {
        $profileImageUrl = public_url_for_path('assets/images/male.png');
    }
}

$heightStr = trim((string) ($user['height'] ?? ''));
if ($heightStr === '') {
    $heightStr = '—';
}

$houseLocParts = array_filter([
    trim((string) ($user['country'] ?? '')),
    trim((string) ($user['state'] ?? '')),
    trim((string) ($user['city'] ?? '')),
    trim((string) ($user['area'] ?? '')),
]);
$houseLocation = $houseLocParts !== [] ? implode(', ', $houseLocParts) : '—';

$marla = trim((string) ($user['house_size_marla'] ?? ''));
$houseSizeStr = $marla !== '' ? $marla . ' (Marla)' : trim((string) ($user['house_size'] ?? ''));
if ($houseSizeStr === '') {
    $houseSizeStr = '—';
}

$ownership = trim((string) ($user['residence'] ?? ''));
if ($ownership === '') {
    $ownership = trim((string) ($user['house_type'] ?? ''));
}
if ($ownership === '') {
    $ownership = '—';
}

$b = (int) ($user['no_of_brothers'] ?? 0);
$s = (int) ($user['no_of_sisters'] ?? 0);
$siblingsLine = $b . ' brother' . ($b === 1 ? '' : 's') . ', ' . $s . ' sister' . ($s === 1 ? '' : 's');

$mb = (int) ($user['no_of_married_brother'] ?? 0);
$ms = (int) ($user['no_of_married_sister'] ?? 0);
if ($mb === 0 && $ms === 0) {
    $marriedSiblingsLine = 'No married brother, No married sister';
} else {
    $marriedSiblingsLine = $mb . ' married brother' . ($mb === 1 ? '' : 's') . ', ' . $ms . ' married sister' . ($ms === 1 ? '' : 's');
}

$siteUrl = rtrim(BASE_URL, '/');
$copyrightYear = (int) date('Y');

$backUrl = rtrim(BASE_URL, '/') . '/admin/users';
$from = strtolower(trim((string) ($_GET['from'] ?? '')));
if (in_array($from, ['match', 'match-making'], true)) {
    $backUrl = rtrim(BASE_URL, '/') . '/admin/match-making';
} elseif ($from === 'income-rishta') {
    $backUrl = rtrim(BASE_URL, '/') . '/admin/accounts/income/rishta-fee';
} elseif ($from === 'income-reg' || $from === 'income-registration') {
    $backUrl = rtrim(BASE_URL, '/') . '/admin/accounts/income/registration-fee';
}

$empty = static function ($v): string {
    $t = trim((string) ($v ?? ''));
    return $t !== '' ? $t : '';
};

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
        .page {
            background-color: #4da7ba;
            width: 794px;
            min-height: 1123px;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }
        .top-level {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            flex-wrap: wrap;
            padding: 10px;
        }
        .logo-text {
            font-size: 40px;
    margin: 0;
    padding: 4px 0;
    line-height: 1;
    font-weight: 400;
    letter-spacing: 3px;
    color: #6e032a;
    /* text-transform: uppercase; */
    font-family: "Dancing Script", serif !important;
        }
        .logo { 
            /* flex: 1;  */
            text-align: left;
            display: flex;
            align-items: center;
            filter: saturate(150%) contrast(110%) brightness(105%);
            filter: drop-shadow(0 0 10px rgba(255, 255, 0, 0.8)); /* Yellow glow */
  /* transform: scale(1.05); Slight zoom */
  transition: 0.3s ease; /* Smooth effect */
        }
        .top-lev-img { max-height: 60px; width: auto; height: auto; display: block; }
        .middle-level {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            flex-wrap: wrap;
            flex: 1;
        }
        .column-1 { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .column-2 { flex: 2; min-width: 0; display: flex; flex-direction: column; }
        .title-column {
            width: 50%;
            text-align: end;
            padding-right: 5px;
            font-weight: bold;
            color: #fff;
            font-size: 13px;
            line-height: 1.5;
            vertical-align: top;
        }
        .text-column {
            width: 50%;
            text-align: start;
            padding-left: 5px;
            color: #fff;
            font-size: 13px;
            line-height: 1.5;
            vertical-align: top;
        }
        .userprofile1 {
            background-color: #6e032a;
            padding: 20px;
            margin: 10px 10px 10px 20px;
            color: #fff;
            border-radius: 10px;
            flex: 1;
            box-sizing: border-box;
        }
        #member-name {
            text-align: center;
            font-family: Georgia, "Times New Roman", serif;
            font-weight: 400;
            font-size: 1.65rem;
            margin: 12px 0 4px;
            padding: 0;
            color: #fff;
            line-height: 1.25;
        }
        .matri-heading {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0 0 12px;
            color: #fff;
        }
        .profile-image {
            max-width: 80%;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 4px;
        }
        .mem-info-2 { font-size: 13px; width: 100%; border-collapse: collapse; }
        .mem-info-1 {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin: 16px 0 0;
        }
        .mem-info-1 td,
        .mem-info-2 td { padding: 3px 0; }
        .userprofile2 {
            background-color: #6e032a;
            padding: 10px 14px 14px;
            margin: 5px 20px 0 5px;
            margin-bottom: 10px;
            color: #fff;
            border-radius: 10px;
            box-sizing: border-box;
        }
        .userprofile2 h3 {
            margin: 0 0 8px;
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
        }
        .userprofile2 hr {
            border: 0;
            border-top: 1px solid rgba(255, 255, 255, 0.85);
            margin: 0 0 10px;
        }
        .tit-column {
            width: 30%;
            text-align: end;
            font-weight: bold;
            padding-right: 5px;
            color: #fff;
            font-size: 13px;
            line-height: 1.5;
            vertical-align: top;
        }
        .txt-column {
            width: 70%;
            text-align: start;
            padding-left: 5px;
            color: #fff;
            font-size: 13px;
            line-height: 1.5;
            vertical-align: top;
        }
        .bottom-level {
            font-size: 13px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            justify-content: flex-end;
            padding: 0 16px 16px;
        }
        .bottom-level hr {
            border: 0;
            border-top: 1px solid rgba(255, 255, 255, 0.5);
            margin: 12px 0 10px;
        }
        .bottom-level p {
            margin: 0;
            text-align: center;
            color: #fff;
            font-size: 13px;
            line-height: 1.5;
        }
        .arabic-text {
            font-family: "Gulzar", serif;
            font-weight: 400;
            font-style: normal;
        }
        .userprofile-container { flex: 1; display: flex; flex-direction: column; min-height: 0; }
        @media (max-width: 820px) {
            .admin-pdf-page .page { width: 100%; min-height: auto; overflow-x: auto; }
            .middle-level { flex-direction: column; }
            .userprofile1 { margin: 10px; }
            .userprofile2 { margin: 8px 10px !important; }
            .tit-column { width: 38%; }
            .txt-column { width: 62%; }
        }
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

<div class="page" id="content">
    <div class="top-level">
        <div class="logo">
            <img src="<?= $e(public_url_for_path('assets/images/logo.png')) ?>"
                 alt="Logo"
                 class="top-lev-img"
                 height="60"
                 onerror="this.onerror=null;this.src='<?= $e(public_url_for_path('assets/images/logo.png')) ?>';">
                     </div>  <span class="logo-text">Wedding Wish Marriage Center</span>
  
    </div>

    <div class="middle-level">
        <div class="column-1">
            <div class="userprofile-container">
                <div class="userprofile1 profile-container">
                    <div class="profile">
                        <img src="<?= $e($profileImageUrl) ?>" alt="" class="profile-image" crossorigin="anonymous">
                    </div>
                    <h1 id="member-name"><?= $e($fullName) ?></h1>
                    <h4 class="matri-heading"><?= $e($matriDisplay) ?></h4>
                    <table class="mem-info-1">
                        <tbody>
                        <tr>
                            <td class="title-column">Age:</td>
                            <td class="text-column"><?= $e($ageStr) ?></td>
                        </tr>
                        <tr>
                            <td class="title-column">Height:</td>
                            <td class="text-column"><?= $e($heightStr) ?></td>
                        </tr>
                        <tr>
                            <td class="title-column">Religion:</td>
                            <td class="text-column"><?= $e($empty($user['religion'] ?? '') ?: '—') ?></td>
                        </tr>
                        <tr>
                            <td class="title-column">Sect:</td>
                            <td class="text-column"><?= $e($empty($user['maslak'] ?? '') ?: '—') ?></td>
                        </tr>
                        <tr>
                            <td class="title-column">Caste:</td>
                            <td class="text-column"><?= $e($empty($user['caste'] ?? '') ?: '—') ?></td>
                        </tr>
                        <tr>
                            <td class="title-column">Marital Status:</td>
                            <td class="text-column"><?= $e($empty($user['marital_status'] ?? '') ?: '—') ?></td>
                        </tr>
                        <tr>
                            <td class="title-column">Mother Tongue:</td>
                            <td class="text-column"><?= $e($empty($user['mother_tongue'] ?? '') ?: '—') ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="column-2">
            <div class="userprofile-container userprofile2-container">
                <div class="userprofile2">
                    <h3>Qualification</h3>
                    <hr>
                    <table class="mem-info-2">
                        <tbody>
                        <tr>
                            <td class="tit-column">Education:</td>
                            <td class="txt-column"><?= $e($empty($user['education'] ?? '')) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">Work Detail:</td>
                            <td class="txt-column"><?= $e($empty($user['work_detail'] ?? '')) ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="userprofile2">
                    <h3>Parents &nbsp;Info</h3>
                    <hr>
                    <table class="mem-info-2">
                        <tbody>
                        <tr>
                            <td class="tit-column">Father Name:</td>
                            <td class="txt-column"><?= $e($empty($user['father_name'] ?? '')) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">Mother Name:</td>
                            <td class="txt-column"><?= $e($empty($user['mother_name'] ?? '')) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">Father Occupation:</td>
                            <td class="txt-column"><?= $e($empty($user['father_occupation'] ?? '')) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">Mother Occupation:</td>
                            <td class="txt-column"><?= $e($empty($user['mother_occupation'] ?? '')) ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="userprofile2">
                    <h3>Siblings Info</h3>
                    <hr>
                    <table class="mem-info-2">
                        <tbody>
                        <tr>
                            <td class="tit-column">Siblings:</td>
                            <td class="txt-column"><?= $e($siblingsLine) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">Married Siblings:</td>
                            <td class="txt-column"><?= $e($marriedSiblingsLine) ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="userprofile2">
                    <h3>Residence &nbsp;Info</h3>
                    <hr>
                    <table class="mem-info-2">
                        <tbody>
                        <tr>
                            <td class="tit-column">House Location:</td>
                            <td class="txt-column"><?= $e($houseLocation) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">House Size:</td>
                            <td class="txt-column"><?= $e($houseSizeStr) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">House Ownership:</td>
                            <td class="txt-column"><?= $e($ownership) ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="bottom-level">
        <hr>
        <p>Copyright © <?= (int) $copyrightYear ?> &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp; <?= $e($siteUrl) ?>/</p>
    </div>
</div>

<button type="button" class="btn btn-primary btn-lg" style="margin-top: 10px;" id="pdfDownloadBtn">Download PDF</button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
(function () {
    var safeName = <?= json_encode($pdfFileTitle, JSON_UNESCAPED_UNICODE) ?>;
    var dl = document.getElementById('pdfDownloadBtn');
    if (!dl) return;
    dl.addEventListener('click', function () {
        var el = document.querySelector('.admin-pdf-page .page');
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

<?php require __DIR__ . '/partials/footer.php'; ?>
