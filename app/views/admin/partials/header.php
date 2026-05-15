<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Admin Panel') ?></title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/css/bootstrap-icons.css">

    <style>
        :root {
            --sidebar-width: 268px;
            --sidebar-collapsed-width: 78px;
            --sidebar-bg: #495469;
            --sidebar-bg-active: #3e4759;
            --content-bg: #efefef;
            --topbar-height: 56px;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, "Helvetica Neue", Arial, sans-serif;
            background: var(--content-bg);
            color: #444;
        }

        .admin-wrapper { min-height: 100vh; }

        .admin-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left .28s ease;
        }

        body.admin-sidebar-collapsed .admin-main {
            margin-left: var(--sidebar-collapsed-width);
        }

        .admin-topbar {
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e7e7e7;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 16px;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .admin-page {
            padding: 14px;
        }

        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 6px;
            color: #fff;
            background: var(--sidebar-bg);
            margin-right: auto;
        }

        .admin-profile {
            position: relative;
            cursor: pointer;
            user-select: none;
        }

        .admin-profile-box {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #666;
        }

        .admin-profile-box i {
            font-size: 16px;
            color: #666;
        }

        .admin-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            width: 180px;
            background: #fff;
            border: 1px solid #e8e8e8;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
            display: none;
        }

        .admin-dropdown a {
            display: block;
            padding: 10px 12px;
            text-decoration: none;
            color: #525252;
            font-size: 13px;
            border-bottom: 1px solid #f1f1f1;
        }
        .admin-dropdown a:last-child { border-bottom: 0; }
        /* .admin-dropdown a:hover { background: #f8f9fb; } */
        .nav .open > a,
.nav .open > a:hover,
.nav .open > a:focus { background: none!important; }

        @media (max-width: 991.98px) {
            .admin-main { margin-left: 0 !important; }
            body.admin-sidebar-collapsed .admin-main { margin-left: 0 !important; }
            .mobile-menu-btn { display: inline-flex; align-items: center; justify-content: center; }
        }
    </style>
</head>
<body data-admin-base-url="<?= htmlspecialchars(rtrim((string) (defined('BASE_URL') ? BASE_URL : ''), '/'), ENT_QUOTES, 'UTF-8') ?>">
<div class="admin-wrapper">