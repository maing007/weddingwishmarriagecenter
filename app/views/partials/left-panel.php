<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="robots" content="index, follow" />
    <!-- ====== CSS from /public/assets/css ====== -->
    <!-- Font Awesome CDN -->
    <link
        rel="preload"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        as="style"
        onload="this.rel='stylesheet'" />
    <noscript>
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    </noscript>

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/all.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/responsive.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/jquery.mCustomScrollbar.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/owl.carousel.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/owl.theme.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/notification_popup.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/mega2.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/chosen.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/select2.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/intlTelInput.css" />
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <!-- =================== JS LIBRARIES =================== -->

    <!-- jQuery (local) -->
    <script src="<?= BASE_URL ?>/assets/js/jquery.min.js"></script>

    <!-- Bootstrap JS (CDN – fixes $(...).modal error) -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"
            integrity="sha384-a5N7Y/aK3q0E1hFN3RLXbCwH6lFFhDC1zp+7tYEFYRHvH+8abtTE1Pi6jizoUqKk"
            crossorigin="anonymous"></script> -->

    <!-- Select2 JS (CDN – fixes select2 404) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Other local plugins -->
    <script src="<?= BASE_URL ?>/assets/js/owl.carousel.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/intlTelInput.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/utils.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/js.cookie.min.js"></script>
    <style>
        /* Dashboard Sidebar */
        .dashboard-sidebar {
            width: 260px;
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #eee;
            padding: 20px 15px;
            position: fixed;
            left: 0;
            top: 0;
        }

        /* Profile Box */
        .profile-box {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        .profile-box h5 {
            margin-top: 10px;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .matri-id {
            font-size: 13px;
            color: #777;
        }

        /* Menu */
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            color: #333;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.2s;
        }

        .sidebar-menu li a i {
            width: 22px;
        }

        .sidebar-menu li a:hover {
            background: #f5f5f5;
        }

        /* Dashboard content spacing */
        .dash-content-wrapper {
            margin-left: 260px;
            padding: 20px;
        }

        /* ==============================
   MEMBERS FEED
================================ */

        .insta-card {
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            transition: 0.3s ease;
        }

        .insta-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        /* Profile Image */
        .insta-card-img {
            width: 100%;
            height: 240px;
            background: #f4f4f4;
            overflow: hidden;
        }

        .insta-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Card Body */
        .insta-card-body {
            padding: 12px 10px;
        }

        .insta-card-body h5 {
            font-size: 16px;
            font-weight: 600;
        }

        .insta-card-body p {
            margin-bottom: 4px;
        }

        /* Buttons */
        .insta-card-body .btn {
            padding: 5px 10px;
            font-size: 13px;
        }

        /* Bookmark button */
        .insta-card-body .btn-outline-secondary {
            border-color: #ccc;
            color: #555;
        }

        .insta-card-body .btn-outline-secondary:hover {
            background: #f1f1f1;
        }

        /* Primary button */
        .insta-card-body .btn-primary {
            background: #007bff;
            border: none;
        }

        .insta-card-body .btn-primary:hover {
            background: #0056b3;
        }

        /* ==============================
   RESPONSIVE
================================ */

        @media (max-width: 1200px) {
            .insta-card-img {
                height: 220px;
            }
        }

        @media (max-width: 992px) {
            .insta-card-img {
                height: 200px;
            }
        }

        @media (max-width: 768px) {
            .insta-card-img {
                height: 190px;
            }

            .insta-card-body h5 {
                font-size: 15px;
            }
        }

        @media (max-width: 576px) {
            .insta-card-img {
                height: 260px;
            }
        }

        /* ==============================
   MOBILE FIX – DASHBOARD
================================ */

        @media (max-width: 768px) {

            /* RESET BODY OVERFLOW */
            body {
                overflow-x: hidden;
            }

            /* SIDEBAR FIX */
            .dashboard-sidebar {
                position: relative;
                /* 🔥 remove fixed */
                width: 100%;
                /* full width */
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid #e5e5e5;
                padding: 15px;
            }

            /* PROFILE CENTER */
            .profile-box {
                text-align: center;
                margin-bottom: 15px;
            }

            .profile-img {
                width: 70px;
                height: 70px;
            }

            /* MENU GRID */
            .sidebar-menu {
                display: flex;
                flex-wrap: wrap;
            }

            .sidebar-menu li {
                width: 50%;
                margin-bottom: 6px;
            }

            .sidebar-menu li a {
                justify-content: center;
                font-size: 14px;
                padding: 8px;
            }

            /* MAIN CONTENT */
            .dash-content-wrapper {
                margin-left: 0;
                /* 🔥 MOST IMPORTANT */
                padding: 12px;
            }

            /* MEMBERS GRID */
            .row>[class*="col-"] {
                width: 100%;
            }

            /* CARD FIX */
            .insta-card-img {
                height: 260px;
            }

            .insta-card-body .d-flex {
                flex-direction: column;
                gap: 8px;
            }

            .insta-card-body .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body
    class="overflow-x-h"
    style="
      background: url('<?= BASE_URL ?>/assets/images/site-back-img.png');
      background-color: whitesmoke;
    ">





    <?php
    // app/views/partials/left-panel.php

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Security: only logged-in users
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    // Load model
    require_once __DIR__ . '/../../models/User.php';
    $userModel = new User();
    $user = $userModel->findById($_SESSION['user_id']);

    // User name
    $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['second_name'] ?? ''));
    if ($fullName === '') {
        $fullName = 'User';
    }

    // Avatar
    if (!empty($user['photo1_status'])) {
        $avatar = BASE_URL . '/' . ltrim($user['photo1_status'], '/');
    } else {
        $avatar = BASE_URL . '/assets/images/default-avatar.png';
    }
    ?>

    <!-- LEFT PANEL -->
    <div class="dashboard-sidebar">

        <div class="profile-box">
            <img src="<?= htmlspecialchars($avatar) ?>" class="profile-img" alt="Profile">

            <h5><?= htmlspecialchars($fullName) ?></h5>

            <?php if (!empty($user['matri_id'])): ?>
                <span class="matri-id"><?= htmlspecialchars($user['matri_id']) ?></span>
            <?php endif; ?>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="<?= BASE_URL ?>/dashboard">
                    <i class="fa fa-home"></i> Dashboard
                </a>
            </li>

            <li>
                <a href="<?= BASE_URL ?>/dashboard/profile">
                    <i class="fa fa-user"></i> Edit Profile
                </a>
            </li>

            <li>
                <a href="<?= BASE_URL ?>/dashboard/profile-complete">
                    <i class="fa fa-id-card"></i> Profile Completion
                </a>
            </li>

            <li>
                <a href="<?= BASE_URL ?>/dashboard/profile/view?id=<?= (int)$_SESSION['user_id'] ?>">
                    <i class="fa fa-users"></i> View Profile
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/dashboard/saved-profiles">
                    <i class="fa fa-bookmark"></i> Saved Profiles
                </a>
            </li>


            <li>
                <a href="<?= BASE_URL ?>/logout"
                    onclick="return confirm('Logout from your account?')">
                    <i class="fa fa-sign-out"></i> Logout
                </a>
            </li>
        </ul>

    </div>