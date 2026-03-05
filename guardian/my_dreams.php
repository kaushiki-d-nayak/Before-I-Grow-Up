<?php
// ============================================================
// guardian/my_dreams.php — Guardian's submitted dreams tracker
// Place this file in: /before-i-grow-up/guardian/my_dreams.php
// ============================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

requireRole('guardian');

$pageTitle = 'My Dreams';
$base = BASE_PATH;
$db  = getDB();

// Fetch all dreams submitted by this guardian
$stmt = $db->prepare("
    SELECT d.*, s.age_group, s.city,
           (SELECT COUNT(*) FROM dream_support ds WHERE ds.dream_id = d.id) AS support_count
    FROM dreams d
    JOIN students s ON d.student_id = s.id
    WHERE s.guardian_id = ?
    ORDER BY d.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$dreams = $stmt->fetchAll();

$statusSteps = ['Submitted', 'Verified', 'Matched', 'In Progress', 'Dream Achieved'];

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1>My Submitted Dreams</h1>
            <p>Track the progress of every dream you've submitted on behalf of your students.</p>
        </div>
        <a href="<?= $base ?>/guardian/submit_dream.php" class="btn btn-primary">+ Submit New Dream</a>
    </div>
</section>

<div class="section-sm">
    <div class="container">

        <?php if (empty($dreams)): ?>
            <div class="empty-state">
                <div class="empty-icon">🌱</div>
                <h3>No dreams submitted yet</h3>
                <p>Start by submitting a learning dream for a student in your care.</p>
                <a href="<?= $base ?>/guardian/submit_dream.php" class="btn btn-primary" style="margin-top:1.25rem;">Submit First Dream</a>
            </div>
        <?php else: ?>

            <div style="display:flex;flex-direction:column;gap:1.5rem;">
            <?php foreach ($dreams as $dream):
                $currentStep = array_search($dream['status'], $statusSteps);
            ?>
                <div class="card" style="padding:2rem;">
                    <div style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;">
                        <div>
                            <span class="dream-category"><?= e($dream['category']) ?></span>
                            <h3 style="margin-top:.5rem;"><?= e($dream['title']) ?></h3>
                            <div style="display:flex;flex-wrap:wrap;gap:.75rem;margin-top:.5rem;">
                                <span style="font-size:.85rem;color:var(--muted);">📍 <?= e($dream['city']) ?></span>
                                <span style="font-size:.85rem;color:var(--muted);">🎂 Age <?= e($dream['age_group']) ?></span>
                                <span style="font-size:.85rem;color:var(--muted);">💰 <?= e($dream['budget_range']) ?></span>
                                <span style="font-size:.85rem;color:var(--muted);">💛 <?= $dream['support_count'] ?> supporter(s)</span>
                            </div>
                        </div>
                        <span class="status-badge status-<?= str_replace(' ', '-', e($dream['status'])) ?>">
                            <?= e($dream['status']) ?>
                        </span>
                    </div>

                    <p style="font-size:.9rem;margin-bottom:1.5rem;"><?= e(mb_substr($dream['description'], 0, 200)) ?>...</p>

                    <!-- Progress Steps -->
                    <div class="progress-steps">
                        <?php foreach ($statusSteps as $i => $step):
                            $cls = '';
                            if ($i < $currentStep)  $cls = 'done';
                            if ($i === $currentStep) $cls = 'active';
                        ?>
                        <div class="step <?= $cls ?>">
                            <div class="step-dot"><?= $i < $currentStep ? '✓' : ($i + 1) ?></div>
                            <div class="step-name"><?= e($step) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="margin-top:1rem;font-size:.8rem;color:var(--muted);">
                        Submitted on <?= date('F j, Y', strtotime($dream['created_at'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>