<?php
session_start();

// Generate 6 digit numeric captcha
$code = rand(100000, 999999);

// Store in session for verification
$_SESSION['captcha_code'] = $code;

// Create image
header("Content-Type: image/png");
$image = imagecreate(120, 40);
$bg     = imagecolorallocate($image, 240, 240, 240);
$text   = imagecolorallocate($image, 50, 50, 50);

imagestring($image, 5, 30, 10, $code, $text);
imagepng($image);
imagedestroy($image);
?>
