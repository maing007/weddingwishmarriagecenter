<?php
declare(strict_types=1);

/**
 * Wedding Wish profile card (same layout as admin profile PDF preview).
 * Expects $user (array) — full user_details-style row.
 */
if (!isset($user) || !is_array($user)) {
    return;
}

$__appRoot = defined('APP_ROOT') ? APP_ROOT : dirname(__DIR__, 3);
require_once $__appRoot . '/app/helpers/profile_pdf_template.php';

$pv = profile_pdf_template_compute_vars($user, (bool) ($profilePdfPreferAdminMemberPhoto ?? false));
extract($pv, EXTR_OVERWRITE);

$e = static function ($v): string {
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
};

$empty = static function ($v): string {
    $t = trim((string) ($v ?? ''));

    return $t !== '' ? $t : '';
};

?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Gulzar&display=swap" rel="stylesheet">
<style>
        .ww-profile-pdf-page {
            background-color: #4da7ba;
            width: 794px;
            max-width: 100%;
            min-height: 1123px;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }
        .ww-profile-pdf-page .top-level {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            flex-wrap: wrap;
            padding: 10px;
        }
        .ww-profile-pdf-page .logo-text {
            font-size: 30px;
            margin: 0;
            padding: 4px 0;
            line-height: 1;
            font-weight: 400;
            letter-spacing: 3px;
            color: #6e032a;
        }
        .ww-profile-pdf-page .logo {
            text-align: left;
            display: flex;
            margin-left: -80px;
            align-items: center;
            filter: drop-shadow(0 0 10px rgba(255, 255, 0, 0.8));
            transition: 0.3s ease;
        }
        .ww-profile-pdf-page .top-lev-img { max-height: 60px; width: auto; height: auto; display: block; }
        .ww-profile-pdf-page .middle-level {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            flex-wrap: wrap;
            flex: 1;
        }
        .ww-profile-pdf-page .column-1 { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .ww-profile-pdf-page .column-2 { flex: 2; min-width: 0; display: flex; flex-direction: column; }
        .ww-profile-pdf-page .title-column {
            width: 50%;
            text-align: end;
            padding-right: 5px;
            font-weight: bold;
            color: #fff;
            font-size: 13px;
            line-height: 1.5;
            vertical-align: top;
        }
        .ww-profile-pdf-page .text-column {
            width: 50%;
            text-align: start;
            padding-left: 0px;
            color: #fff;
            font-size: 13px;
            line-height: 1.5;
            vertical-align: top;
        }
        .ww-profile-pdf-page .userprofile1 {
            background-color: #6e032a;
            padding: 20px;
            margin: 10px 10px 10px 20px;
            color: #fff;
            border-radius: 10px;
            /* flex: 1; */
            padding-bottom: 50px;
            box-sizing: border-box;
        }
        .ww-profile-pdf-page .ww-pdf-member-name {
            text-align: center;
            font-family: Georgia, "Times New Roman", serif;
            font-weight: 400;
            font-size: 1.65rem;
            margin: 12px 0 4px;
            padding: 0;
            color: #fff;
            line-height: 1.25;
        }
        .ww-profile-pdf-page .matri-heading {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0 0 12px;
            color: #fff;
        }
        .ww-profile-pdf-page .profile-image {
            max-width: 80%;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 4px;
        }
        .ww-profile-pdf-page .mem-info-2 { font-size: 13px; width: 100%; border-collapse: collapse; }
        .ww-profile-pdf-page .mem-info-1 {
            width: auto;
            min-width: min(100%, 320px);
            max-width: 100%;
            margin: 20px auto 0;
            border-collapse: separate;
            border-spacing: 0 10px;
            font-size: 13px;
        }
        .ww-profile-pdf-page .mem-info-1 tbody tr {
            display: table-row;
        }
        .ww-profile-pdf-page .mem-info-1 .title-column,
        .ww-profile-pdf-page .mem-info-1 .text-column {
            width: 57%;
            /* text-align: center; */
            vertical-align: middle;
            /* padding: 6px 10px; */
            line-height: 1.45;
            padding-right:0px !important;
        }
        .ww-profile-pdf-page .mem-info-1 .title-column {
            font-weight: 700;
            padding-right: 8px;
        }
        .ww-profile-pdf-page .mem-info-1 .text-column {
            /* padding-left: 8px; */
        }
        .ww-profile-pdf-page .mem-info-2 td { padding: 3px 0; }
        .ww-profile-pdf-page .userprofile2 {
            background-color: #6e032a;
            padding: 10px 14px 14px;
            margin: 5px 20px 0 5px;
            margin-bottom: 10px;
            color: #fff;
            border-radius: 10px;
            box-sizing: border-box;
        }
        .ww-profile-pdf-page .userprofile2 h3 {
            margin: 0 0 8px;
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
        }
        .ww-profile-pdf-page .userprofile2 hr {
            border: 0;
            border-top: 1px solid rgba(255, 255, 255, 0.85);
            margin: 0 0 10px;
        }
        .ww-profile-pdf-page .tit-column {
            width: 30%;
            text-align: end;
            font-weight: bold;
            padding-right: 5px;
            color: #fff;
            font-size: 13px;
            line-height: 1.5;
            vertical-align: top;
        }
        .ww-profile-pdf-page .txt-column {
            width: 70%;
            text-align: start;
            padding-left: 5px;
            color: #fff;
            font-size: 13px;
            line-height: 1.5;
            vertical-align: top;
        }
        .ww-profile-pdf-page .bottom-level {
            font-size: 13px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            justify-content: flex-end;
            padding: 0 16px 16px;
        }
        .ww-profile-pdf-page .bottom-level hr {
            border: 0;
            border-top: 1px solid rgba(255, 255, 255, 0.5);
            margin: 12px 0 10px;
        }
        .ww-profile-pdf-page .bottom-level p {
            margin: 0;
            text-align: center;
            color: #fff;
            font-size: 13px;
            line-height: 1.5;
        }
        .ww-profile-pdf-page .arabic-text {
            font-family: "Gulzar", serif;
            font-weight: 400;
            font-style: normal;
        }
        .ww-profile-pdf-page .userprofile-container { flex: 1; display: flex; flex-direction: column; min-height: 0; }
        @media (max-width: 820px) {
            .ww-profile-pdf-page { width: 100%; min-height: auto; overflow-x: auto; }
            .ww-profile-pdf-page .middle-level { flex-direction: column; }
            .ww-profile-pdf-page .userprofile1 { margin: 10px; }
            .ww-profile-pdf-page .userprofile2 { margin: 8px 10px !important; }
            .ww-profile-pdf-page .tit-column { width: 38%; }
            .ww-profile-pdf-page .txt-column { width: 62%; }
        }
    </style>

<div class="ww-profile-pdf-page" id="wwProfilePdfContent">
    <div class="top-level">
        <div class="logo">
            <img src="<?= $e(public_url_for_path('assets/images/logo.png')) ?>"
                 alt="Logo"
                 class="top-lev-img"
                 height="60"
                 onerror="this.onerror=null;this.src='<?= $e(public_url_for_path('assets/images/logo.png')) ?>';">
        </div>
        <span class="logo-text">Wedding Wish Marriage Center</span>
    </div>

    <div class="middle-level">
        <div class="column-1">
            <div class="userprofile-container">
                <div class="userprofile1 profile-container">
                    <div class="profile">
                        <img src="<?= $e($profileImageUrl) ?>" alt="" class="profile-image" crossorigin="anonymous">
                    </div>
                    <h1 class="ww-pdf-member-name"><?= $e($fullName) ?></h1>
                    <h4 class="matri-heading"><?= $e($matriDisplay) ?></h4>
                    <table class="mem-info-1">
                        <tbody>
                        <tr>
                            <td class="title-column">Age : &nbsp; </td>
                            <td class="text-column"><?= $e($ageStr) ?></td>
                        </tr>
                        <tr>
                            <td class="title-column">Height : &nbsp; </td>
                            <td class="text-column"><?= $e($heightStr) ?></td>
                        </tr>
                        <tr>
                            <td class="title-column">Religion : &nbsp; </td>
                            <td class="text-column"><?= $e($empty($user['religion'] ?? '') ?: '—') ?></td>
                        </tr>
                        <tr>
                            <td class="title-column">Sect :  &nbsp;</td>
                            <td class="text-column"><?= $e($empty($user['maslak'] ?? '') ?: '—') ?></td>
                        </tr>
                        <tr>
                            <td class="title-column">Caste : &nbsp; </td>
                            <td class="text-column"><?= $e($empty($user['caste'] ?? '') ?: '—') ?></td>
                        </tr>
                        <tr>
                            <td class="title-column">Marital Status:&nbsp;</td>
                            <td class="text-column"><?= $e($empty($user['marital_status'] ?? '') ?: '—') ?></td>
                        </tr>
                        <tr>
                            <td class="title-column">Mother Tongue:&nbsp;</td>
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
                            <td class="tit-column">Education : &nbsp;</td>
                            <td class="txt-column"><?= $e($educationDisplay ?? '—') ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">Work Detail :&nbsp;</td>
                            <td class="txt-column"><?= $e($workDetailDisplay ?? '—') ?></td>
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
                            <td class="tit-column">Father Name: &nbsp;</td>
                            <td class="txt-column"><?= $e($empty($user['father_name'] ?? '')) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">Mother Name: &nbsp;</td>
                            <td class="txt-column"><?= $e($empty($user['mother_name'] ?? '')) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">Father Occupation:&nbsp;</td>
                            <td class="txt-column"><?= $e($empty($user['father_occupation'] ?? '')) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">Mother Occupation:&nbsp;</td>
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
                            <td class="tit-column">Siblings&nbsp;:&nbsp;</td>
                            <td class="txt-column"><?= $e($siblingsLine) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">Married Siblings&nbsp;:&nbsp;</td>
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
                            <td class="tit-column">House Location:&nbsp;</td>
                            <td class="txt-column"><?= $e($houseLocation) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">House Size:&nbsp;</td>
                            <td class="txt-column"><?= $e($houseSizeStr) ?></td>
                        </tr>
                        <tr>
                            <td class="tit-column">House Ownership:&nbsp;</td>
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
