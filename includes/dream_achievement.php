<?php
function ensureDreamAchievementSchema(PDO $db): void {
    static $ensured = false;
    if ($ensured) {
        return;
    }

    $colStmt = $db->query("SHOW COLUMNS FROM students LIKE 'student_email'");
    if (!$colStmt->fetch()) {
        $db->exec("
            ALTER TABLE students
            ADD COLUMN student_email VARCHAR(255) NULL AFTER city
        ");
    }

    $db->exec("
        CREATE TABLE IF NOT EXISTS dream_achievement_confirmations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            dream_id INT UNSIGNED NOT NULL,
            token VARCHAR(64) NOT NULL,
            recipient_email VARCHAR(255) NOT NULL,
            requested_by_admin_id INT UNSIGNED NOT NULL,
            requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL,
            confirmed_at DATETIME NULL DEFAULT NULL,
            confirmed_ip VARCHAR(45) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_dream (dream_id),
            UNIQUE KEY uniq_token (token),
            INDEX idx_expires (expires_at),
            INDEX idx_confirmed (confirmed_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $ensured = true;
}

function getDreamAchievementConfirmation(PDO $db, int $dreamId): ?array {
    $stmt = $db->prepare("
        SELECT *
        FROM dream_achievement_confirmations
        WHERE dream_id = ?
        LIMIT 1
    ");
    $stmt->execute([$dreamId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function saveDreamAchievementRequest(PDO $db, int $dreamId, int $adminId, string $recipientEmail, string $token, string $expiresAt): void {
    $stmt = $db->prepare("
        INSERT INTO dream_achievement_confirmations
            (dream_id, token, recipient_email, requested_by_admin_id, expires_at, confirmed_at, confirmed_ip)
        VALUES
            (?, ?, ?, ?, ?, NULL, NULL)
        ON DUPLICATE KEY UPDATE
            token = VALUES(token),
            recipient_email = VALUES(recipient_email),
            requested_by_admin_id = VALUES(requested_by_admin_id),
            requested_at = CURRENT_TIMESTAMP,
            expires_at = VALUES(expires_at),
            confirmed_at = NULL,
            confirmed_ip = NULL
    ");
    $stmt->execute([$dreamId, $token, $recipientEmail, $adminId, $expiresAt]);
}

