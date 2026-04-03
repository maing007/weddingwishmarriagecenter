<?php
$title = "Admin Register";
if (session_status() === PHP_SESSION_NONE) session_start();
require __DIR__ . '/partials/header.php';
?>

<style>
    body {
        background-color: #f1f1f1;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .register-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    .register-card {
        background: #fff;
        padding: 40px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        width: 360px;
        text-align: center;
    }
    .register-card img.logo {
        width: 120px;
        margin-bottom: 20px;
    }
    .register-card h2 {
        font-weight: 600;
        margin-bottom: 30px;
        color: #23282d;
    }
    .register-card input.form-control {
        border: 1px solid #ccd0d4;
        border-radius: 4px;
        padding: 10px;
        margin-bottom: 20px;
    }
    .register-card button.btn-register {
        background-color: #0073aa;
        border: none;
        color: #fff;
        padding: 10px;
        width: 100%;
        border-radius: 4px;
        font-size: 16px;
        transition: background 0.3s;
    }
    .register-card button.btn-register:hover {
        background-color: #006799;
    }
    .alert-danger {
        font-size: 14px;
        margin-bottom: 15px;
        padding: 8px 12px;
    }
    .login-link {
        margin-top: 15px;
        display: block;
        font-size: 14px;
        color: #0073aa;
        text-decoration: none;
    }
    .login-link:hover {
        text-decoration: underline;
    }
</style>

<div class="register-container">
    <div class="register-card">

        <!-- Site Logo -->
        <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Site Logo" class="logo">

        <h2>Admin Register</h2>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>/admin/register">
            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button class="btn-register">Register</button>
        </form>

        <a href="<?= BASE_URL ?>/admin/login" class="login-link">Already have an account? Log in</a>

    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
