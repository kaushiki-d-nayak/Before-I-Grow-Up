<?php
// ============================================================
// includes/auth.php
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function userRole(): string {
    return $_SESSION['role'] ?? '';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        require_once __DIR__ . '/../config/app.php';
        header('Location: ' . BASE_PATH . '/login.php?msg=login_required');
        exit;
    }
}

function requireRole(string $role): void {
    requireLogin();
    if (userRole() !== $role) {
        require_once __DIR__ . '/../config/app.php';
        header('Location: ' . BASE_PATH . '/index.php?msg=access_denied');
        exit;
    }
}

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}