<?php
// includes/header.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/app.php';

$base = BASE_PATH;
$role = userRole();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — Before I Grow Up' : 'Before I Grow Up' ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= $base ?>/favicon.svg">
    <link rel="stylesheet" href="<?= $base ?>/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="<?= $base ?>/index.php" class="nav-brand">
            <span class="brand-icon">🌱</span>
            <span class="brand-text">Before I Grow Up</span>
        </a>
        <input type="checkbox" id="nav-toggle" class="nav-toggle-input">
        <label for="nav-toggle" class="nav-hamburger">
            <span></span><span></span><span></span>
        </label>
        <ul class="nav-links">
            <li><a href="<?= $base ?>/index.php">Home</a></li>
            <li><a href="<?= $base ?>/supporter/browse_dreams.php">Browse Dreams</a></li>
            <?php if (!isLoggedIn()): ?>
                <li><a href="<?= $base ?>/login.php" class="btn-nav">Login</a></li>
                <li><a href="<?= $base ?>/register.php" class="btn-nav btn-nav-primary">Register</a></li>
            <?php elseif ($role === 'admin'): ?>
                <li><a href="<?= $base ?>/admin/dashboard.php">Dashboard</a></li>
                <li><a href="<?= $base ?>/admin/manage_dreams.php">Dreams</a></li>
                <li><a href="<?= $base ?>/admin/manage_users.php">Users</a></li>
                <li><a href="<?= $base ?>/logout.php" class="btn-nav">Logout</a></li>
            <?php elseif ($role === 'guardian'): ?>
                <li><a href="<?= $base ?>/guardian/submit_dream.php">Submit Dream</a></li>
                <li><a href="<?= $base ?>/guardian/my_dreams.php">My Dreams</a></li>
                <li><a href="<?= $base ?>/logout.php" class="btn-nav">Logout</a></li>
            <?php elseif ($role === 'supporter'): ?>
                <li><a href="<?= $base ?>/supporter/browse_dreams.php">Browse</a></li>
                <li><a href="<?= $base ?>/supporter/adopt_dream.php">My Support</a></li>
                <li><a href="<?= $base ?>/logout.php" class="btn-nav">Logout</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<main class="page-main">
<?php $flash = getFlash(); if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>">
        <span><?= e($flash['message']) ?></span>
        <button class="flash-close" onclick="this.parentElement.style.display='none'">×</button>
    </div>
<?php endif; ?>
