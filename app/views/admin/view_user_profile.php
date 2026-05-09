<?php
$title = "Profile Users";
require __DIR__.'/partials/header.php';
$profileImgUrl = '';
if (!empty($user['avatar'])) {
    $profileImgUrl = public_url_for_path((string) $user['avatar']);
}
if ($profileImgUrl === '') {
    foreach (['photo1_status', 'photo2_url', 'photo3_url'] as $pk) {
        if (!empty($user[$pk])) {
            $profileImgUrl = public_url_for_path((string) $user[$pk]);
            break;
        }
    }
}
if ($profileImgUrl === '') {
    $g = strtolower(trim((string) ($user['gender'] ?? '')));
    $profileImgUrl = ($g === 'female' || strncmp($g, 'female', 6) === 0)
        ? public_url_for_path('assets/images/female.png')
        : public_url_for_path('assets/images/male.png');
}
require __DIR__.'/partials/sidebar.php';
?>
<?php
$paid = [];
$fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$gender   = $user['gender']   ?? '';
$religion = $user['religion'] ?? '';
$phone    = $user['phone']    ?? '';
$email    = $user['email']    ?? '';
$bio      = $user['bio']      ?? '';
$dob      = $user['dob']      ?? '';
?>
<style>
/* ===== PROFILE PAGE STYLES ===== */
.card { border-radius:10px; overflow:hidden; box-shadow:0 5px 15px rgba(0,0,0,0.05); }
.card-header { background:#f8f9fa; font-weight:600; font-size:16px; }
.card-body p { font-size:14px; line-height:1.5; color:#555; }
.profile-top { display:flex; flex-wrap:wrap; align-items:center; gap:20px; }
.profile-top .avatar { width:140px; height:140px; border-radius:50%; object-fit:cover; border:3px solid #eee; }
.profile-top .info { flex:1; min-width:180px; }
.profile-top h3 { margin-bottom:5px; font-size:22px; }
.table th { width:30%; font-weight:600; background-color:#f8f9fa; }
.table td { font-size:14px; color:#555; }
@media (max-width:991px) { .profile-top { flex-direction:column; align-items:center; text-align:center; } }
@media print { button { display:none !important; } }

/* ===== HIDDEN PDF HEADER & WATERMARK ===== */
#pdf-header, #pdf-watermark { display:block; } /* visible in cloned node */
@media screen {
    #pdf-header, #pdf-watermark { display:none !important; } /* hidden on screen */
}

/* Watermark styling */
.pdf-watermark {
    position: absolute;
    top:50%;
    left:50%;
    transform: translate(-50%, -50%) rotate(-45deg);
    font-size:60px;
    color: rgba(0,0,0,0.05);
    pointer-events: none;
    z-index:0;
}
</style>

<div id="profile-pdf" class="container mt-4">

    <!-- ===== HIDDEN PDF HEADER ===== -->
    <div id="pdf-header">
        <div style="text-align:center; margin-bottom:20px;">
            <img src="<?= htmlspecialchars(public_url_for_path('assets/images/logo.png'), ENT_QUOTES, 'UTF-8') ?>" style="width:120px;">
            <h2>Wedding Matrimony</h2>
            <p>www.weddingwishcenter.com</p>
            <hr>
        </div>
    </div>

    <!-- ===== HIDDEN PDF WATERMARK ===== -->
    <div id="pdf-watermark" class="pdf-watermark">Wedding Matrimony</div>

    <div class="row">
        <div class="col-md-9 col-sm-12 col-xs-12">

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <!-- TOP CARD: Avatar + Name + Basic -->
            <div class="card d-flex mb-4 shadow-sm">
                <div class="card-body d-flex flex-wrap align-items-center">
                    <div class="text-center me-3 mb-3">
                        <img src="<?= htmlspecialchars($profileImgUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Profile avatar" class="rounded-circle" style="width:140px;height:140px;object-fit:cover;border:3px solid #eee;" />
                    </div>
                    <div>
                        <h3 class="mb-1"><?= htmlspecialchars($fullName !== '' ? $fullName : 'Unnamed User', ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="mb-1 text-muted">
                            <?php if ($gender): ?><strong>Gender:</strong> <?= htmlspecialchars($gender, ENT_QUOTES, 'UTF-8') ?> &nbsp; • &nbsp;<?php endif; ?>
                            <?php if (!empty($age)): ?><strong>Age:</strong> <?= (int)$age ?> years<?php endif; ?>
                        </p>
                        <?php if ($religion): ?><p class="mb-1"><strong>Religion:</strong> <?= htmlspecialchars($religion, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                        <?php if ($email): ?><p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                        <?php if ($phone): ?><p class="mb-0"><strong>Phone:</strong> <?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                    </div>
                    <div class="ms-auto">
                        <a href="<?= BASE_URL ?>/admin/user-profile/edit/?id=<?= $user['id'] ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-edit"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            <button onclick="downloadProfilePDF()" class="btn btn-danger btn-sm mb-3">
                <i class="fa fa-file-pdf"></i> Download Profile PDF
            </button>

            <!-- ABOUT / BIO -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header"><strong>About Me</strong></div>
                <div class="card-body">
                    <?php if (trim($bio) !== ''): ?>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($bio, ENT_QUOTES, 'UTF-8')) ?></p>
                    <?php else: ?>
                        <p class="text-muted mb-0">You haven't added any bio yet. Go to update your details.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- BASIC DETAILS TABLE -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header"><strong>Basic Details</strong></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <tbody>
                            <tr><th style="width:30%;">Full Name</th><td id='fullName'><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></td></tr>
                            <tr><th>Gender</th><td><?= htmlspecialchars($gender, ENT_QUOTES, 'UTF-8') ?></td></tr>
                            <tr><th>Age</th><td><?= !empty($age) ? (int)$age . ' years' : '-' ?></td></tr>
                            <tr><th>Date of Birth</th><td><?= htmlspecialchars($dob ?: '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
                            <tr><th>Religion</th><td><?= htmlspecialchars($religion ?: '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
                            <tr><th>Phone</th><td><?= htmlspecialchars($phone ?: '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
                            <tr><th>Email</th><td><?= htmlspecialchars($email ?: '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- HTML2PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadProfilePDF() {
    const profileElement = document.getElementById('profile-pdf');
    const headerElement = document.getElementById('pdf-header');
    const watermarkElement = document.getElementById('pdf-watermark');

    // Clone the profile content
    const clone = profileElement.cloneNode(true);

    // Prepend header and watermark
    clone.insertBefore(headerElement.cloneNode(true), clone.firstChild);
    clone.appendChild(watermarkElement.cloneNode(true));

    const fullName = document.getElementById('fullName').textContent || 'profile';
    const opt = {
        margin: 0.5,
        filename: fullName + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(clone).save();
}
</script>
