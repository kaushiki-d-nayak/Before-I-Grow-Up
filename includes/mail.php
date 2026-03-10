<?php
require_once __DIR__ . '/../config/app.php';

$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
} else {
    $phpMailerSrc = __DIR__ . '/../PHPMailer/src/';
    $phpMailerFiles = ['Exception.php', 'PHPMailer.php', 'SMTP.php'];
    foreach ($phpMailerFiles as $file) {
        $full = $phpMailerSrc . $file;
        if (file_exists($full)) {
            require_once $full;
        }
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!defined('APP_NAME')) {
    define('APP_NAME', 'Before I Grow Up');
}
if (!defined('APP_FROM_EMAIL')) {
    define('APP_FROM_EMAIL', 'beforeigrowup1@gmail.com');
}

if (!defined('APP_SMTP_HOST')) {
    define('APP_SMTP_HOST', 'smtp.gmail.com');
}
if (!defined('APP_SMTP_USER')) {
    define('APP_SMTP_USER', APP_FROM_EMAIL);
}
if (!defined('APP_SMTP_PASS')) {
    define('APP_SMTP_PASS', 'mbtsmpvjvclyjhiw');
}
if (!defined('APP_SMTP_PORT')) {
    define('APP_SMTP_PORT', 587); 
}
if (!defined('APP_SMTP_SECURE')) {
    define('APP_SMTP_SECURE', 'tls');
}

function appUrl(string $path): string {
    $base   = defined('BASE_PATH') ? BASE_PATH : '';
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . $base . '/' . ltrim($path, '/');
}

function sendEmail(string $to, string $subject, string $htmlBody): bool {
    if (!class_exists(PHPMailer::class)) {
        error_log('PHPMailer not available. Missing vendor autoload and/or PHPMailer/src files.');
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = APP_SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = APP_SMTP_USER;
        $mail->Password   = APP_SMTP_PASS;
        $mail->SMTPSecure = APP_SMTP_SECURE;
        $mail->Port       = APP_SMTP_PORT;

        // Recipients
        $mail->setFrom(APP_FROM_EMAIL, APP_NAME);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email sending failed: ' . $e->getMessage());
        return false;
    }
}
