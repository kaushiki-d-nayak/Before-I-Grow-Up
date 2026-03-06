<?php
// ============================================================
// config/database.php
// Database connection using PDO
// Place this file in: /before-i-grow-up/config/database.php
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'before_i_grow_up');
define('DB_USER', 'root');       // Change to your MySQL username
define('DB_PASS', '');           // Change to your MySQL password
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a PDO database connection instance.
 * Uses a static variable to avoid creating multiple connections.
 */
function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In production, log this error instead of displaying it
            die('<div style="font-family:sans-serif;color:#c0392b;padding:2rem;">
                <h2>Database Connection Error</h2>
                <p>Could not connect to the database. Please check your config/database.php settings.</p>
                <small>' . htmlspecialchars($e->getMessage()) . '</small>
            </div>');
        }
    }

    return $pdo;
}