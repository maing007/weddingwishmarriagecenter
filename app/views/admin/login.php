<?php
$title = "Admin Login";
if (session_status() === PHP_SESSION_NONE) session_start();

// Generate random captcha
$_SESSION['captcha'] = rand(1000, 9999);

// If already logged in
if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/dashboard');
    exit;
}

// require __DIR__ . '/partials/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
body {
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, #f5f7fa, #e4e8eb);
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.login-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-card {
    background: #fff;
    width: 400px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,.08);
    border-radius: 10px;
    text-align: center;
}

.login-logo {
    width: 120px;
    margin-bottom: 20px;
}

.login-title {
    font-size: 26px;
    font-weight: 600;
    color: #333;
    margin-bottom: 25px;
}

.form-control {
    height: 50px;
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-bottom: 18px;
    box-shadow: none;
}

.form-control:focus {
    border-color: #2196f3;
    box-shadow: none;
}

.captcha-box {
    background: #f5f5f5;
    padding: 12px;
    font-size: 24px;
    font-weight: bold;
    letter-spacing: 4px;
    margin-bottom: 15px;
    border-radius: 6px;
    user-select: none;
}

.btn-login {
    width: 100%;
    height: 50px;
    background: #2196f3;
    border: none;
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    border-radius: 6px;
}

.btn-login:hover {
    background: #1976d2;
}

.error-text {
    color: red;
    font-size: 13px;
    text-align: left;
    display: none;
    margin-top: -12px;
    margin-bottom: 12px;
}
</style>

<div class="login-wrapper">

    <div class="login-card">

        <img src="<?= BASE_URL ?>/assets/images/logo.png" class="login-logo">

        <div class="login-title">Admin Login</div>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" method="post" action="<?= BASE_URL ?>/admin/login">

            <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>

            <div id="emailError" class="error-text">
                Invalid email
            </div>

            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>

            <div id="passwordError" class="error-text">
                Password minimum 6 characters
            </div>

            <div class="captcha-box">
                <?= $_SESSION['captcha'] ?>
            </div>

            <input type="text"
                   name="captcha"
                   id="captcha"
                   class="form-control"
                   placeholder="Enter captcha"
                   required>

            <div id="captchaError" class="error-text">
                Captcha does not match
            </div>

            <button type="submit" class="btn-login">
                Log In
            </button>

        </form>

    </div>

</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e){

    let email = document.getElementById('email').value.trim();
    let password = document.getElementById('password').value.trim();
    let captcha = document.getElementById('captcha').value.trim();

    let emailError = document.getElementById('emailError');
    let passwordError = document.getElementById('passwordError');
    let captchaError = document.getElementById('captchaError');

    let valid = true;

    emailError.style.display = 'none';
    passwordError.style.display = 'none';
    captchaError.style.display = 'none';

    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

    if (!email.match(emailPattern)) {
        emailError.style.display = 'block';
        valid = false;
    }

    if (password.length < 6) {
        passwordError.style.display = 'block';
        valid = false;
    }

    if (captcha != "<?= $_SESSION['captcha'] ?>") {
        captchaError.style.display = 'block';
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
    }

});
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>