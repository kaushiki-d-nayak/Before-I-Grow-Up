<?php
// config/app.php — Auto-detects base path. Works on WAMP/XAMPP any folder name.
if (!defined('BASE_PATH')) {
    $docRoot     = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
    $projectRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
    $base        = str_replace($docRoot, '', $projectRoot);
    $base        = '/' . ltrim(str_replace('\\', '/', $base), '/');
    define('BASE_PATH', rtrim($base, '/'));
}