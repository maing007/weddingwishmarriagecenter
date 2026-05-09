<?php
/**
 * Entry point when the web server document root is the project folder (typical shared hosting).
 * CSS/JS and uploads are served from /public via root .htaccess rewrites.
 */
define('APP_ROOT', __DIR__);
require APP_ROOT . '/index-bootstrap.php';
