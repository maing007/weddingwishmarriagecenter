<?php
/**
 * Entry point when the web server document root is the /public folder (typical XAMPP).
 */
define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/index-bootstrap.php';
