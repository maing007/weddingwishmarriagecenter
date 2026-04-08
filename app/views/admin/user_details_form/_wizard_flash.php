<?php
if (!empty($_SESSION['flash_error'])):
    $msg = (string) $_SESSION['flash_error'];
    unset($_SESSION['flash_error']); ?>
    <div class="alert alert-danger container mt-3" role="alert"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif;

if (!empty($_SESSION['flash_success'])):
    $msg = (string) $_SESSION['flash_success'];
    unset($_SESSION['flash_success']); ?>
    <div class="alert alert-success container mt-3" role="status"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif;
