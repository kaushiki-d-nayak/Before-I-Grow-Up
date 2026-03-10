<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/app.php';

// Destroy the session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

// Start new session for flash
session_start();
setFlash('success', 'You have been logged out successfully.');

redirect(BASE_PATH . '/login.php');