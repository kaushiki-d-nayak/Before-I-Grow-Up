<?php
// ============================================================
// config/app.php — Auto-detects base path at runtime.
// Works on WAMP, XAMPP, any folder name, any subfolder depth.
// ============================================================

if (!defined('BASE_PATH')) {
    // Normalize slashes for Windows (WAMP uses backslashes)
    $docRoot     = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
    $projectRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');

    // Strip document root from project path to get URL base
    $base = str_replace($docRoot, '', $projectRoot);
    $base = '/' . ltrim(str_replace('\\', '/', $base), '/');
    $base = rtrim($base, '/');

    define('BASE_PATH', $base);
}
