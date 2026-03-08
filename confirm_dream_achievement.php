<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/dream_achievement.php';

$pageTitle = 'Confirm Dream Completion';
$base = BASE_PATH;
$db = getDB();
ensureDreamAchievementSchema($db);

$token = trim((string)($_GET['token'] ?? $_POST['token'] ?? ''));
$error = '';
$success = '';
$request = null;

if ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    $error = 'This confirmation link is invalid.';
} else {
    $stmt = $db->prepare("
        SELECT dac.id, dac.dream_id, dac.expires_at, dac.confirmed_at,
               d.title, d.status AS dream_status
        FROM dream_achievement_confirmations dac
        JOIN dreams d ON d.id = dac.dream_id
        WHERE dac.token = ?
        LIMIT 1
    ");
    $stmt->execute([$token]);
    $request = $stmt->fetch();

    if (!$request) {
        $error = 'This confirmation request was not found.';
    } elseif (!empty($request['confirmed_at'])) {
        $success = 'This dream has already been confirmed. Thank you.';
    } elseif (strtotime($request['expires_at']) < time()) {
        $error = 'This confirmation link has expired. Please request a new one from the admin.';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $up = $db->prepare("
            UPDATE dream_achievement_confirmations
            SET confirmed_at = NOW(), confirmed_ip = ?
            WHERE id = ? AND confirmed_at IS NULL AND expires_at >= NOW()
        ");
        $up->execute([$ip, (int)$request['id']]);
        if ($up->rowCount() > 0) {
            $success = 'Thank you. Your completion confirmation has been recorded. Admin can now mark this dream as achieved.';
        } else {
            $error = 'This confirmation could not be recorded. Please try again or ask the admin to resend the email.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="section-sm">
  <div class="container" style="max-width:680px;">
    <div class="card" style="padding:1.5rem;">
      <h1 style="margin-top:0;">Confirm Dream Completion</h1>

      <?php if ($success): ?>
        <div class="flash flash-success"><span><?= e($success) ?></span></div>
      <?php elseif ($error): ?>
        <div class="flash flash-error"><span><?= e($error) ?></span></div>
      <?php else: ?>
        <p>Please confirm that this dream has been completed:</p>
        <p><strong><?= e($request['title']) ?></strong></p>
        <p style="color:#6B7280;">Current status: <?= e($request['dream_status']) ?></p>
        <form method="POST">
          <input type="hidden" name="token" value="<?= e($token) ?>">
          <button type="submit" class="btn btn-primary">Yes, this dream is completed</button>
        </form>
      <?php endif; ?>

      <p style="margin-top:1rem;"><a href="<?= $base ?>/index.php">Back to home</a></p>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

