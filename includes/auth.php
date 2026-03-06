<?php
// ============================================================
// includes/auth.php
// Session management and role-based access control helpers
// Place this file in: /before-i-grow-up/includes/auth.php
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if the user is logged in.
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get the current user's role.
 */
function userRole(): string {
    return $_SESSION['role'] ?? '';
}

/**
 * Redirect to login if not logged in.
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /before-i-grow-up/login.php?msg=login_required');
        exit;
    }
}

/**
 * Restrict page to a specific role. Redirect with error if mismatch.
 */
function requireRole(string $role): void {
    requireLogin();
    if (userRole() !== $role) {
        header('Location: /before-i-grow-up/index.php?msg=access_denied');
        exit;
    }
}

/**
 * Sanitize output to prevent XSS.
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect helper.
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Flash message: set in session, display once.
 */
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