<?php
// ============================================================
// admin/dashboard.php — Admin Dashboard with analytics
// Place this file in: /before-i-grow-up/admin/dashboard.php
// ============================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

requireRole('admin');

$pageTitle = 'Admin Dashboard';
$base = BASE_PATH;
$db   = getDB();

// ── Analytics ──────────────────────────────────────────────
$totalUsers     = $db->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();
$totalGuardians = $db->query("SELECT COUNT(*) FROM users WHERE role = 'guardian'")->fetchColumn();
$totalSupporters= $db->query("SELECT COUNT(*) FROM users WHERE role = 'supporter'")->fetchColumn();
$totalDreams    = $db->query("SELECT COUNT(*) FROM dreams")->fetchColumn();
$pendingDreams  = $db->query("SELECT COUNT(*) FROM dreams WHERE status = 'Submitted'")->fetchColumn();
$verifiedDreams = $db->query("SELECT COUNT(*) FROM dreams WHERE status = 'Verified'")->fetchColumn();
$achievedDreams = $db->query("SELECT COUNT(*) FROM dreams WHERE status = 'Dream Achieved'")->fetchColumn();
$totalAdoptions = $db->query("SELECT COUNT(*) FROM dream_support")->fetchColumn();

// Category distribution
$catStats = $db->query("SELECT category, COUNT(*) as cnt FROM dreams GROUP BY category ORDER BY cnt DESC")->fetchAll();

// Recent activity (last 10 dreams)
$recentDreams = $db->query("
    SELECT d.*, s.city, s.age_group, u.name AS guardian_name
    FROM dreams d
    JOIN students s ON d.student_id = s.id
    JOIN users u ON s.guardian_id = u.id
    ORDER BY d.created_at DESC
    LIMIT 8
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-title">Admin Panel</div>
        <nav>
            <a href="<?= $base ?>/admin/dashboard.php" class="sidebar-link active">
                <span class="sidebar-icon">📊</span> Dashboard
            </a>
            <a href="<?= $base ?>/admin/manage_dreams.php" class="sidebar-link">
                <span class="sidebar-icon">🌟</span> Manage Dreams
            </a>
            <a href="<?= $base ?>/admin/manage_users.php" class="sidebar-link">
                <span class="sidebar-icon">👥</span> Manage Users
            </a>
            <a href="<?= $base ?>/supporter/browse_dreams.php" class="sidebar-link">
                <span class="sidebar-icon">🔍</span> View Public
            </a>
            <a href="<?= $base ?>/logout.php" class="sidebar-link" style="margin-top:2rem;border-top:1px solid rgba(255,255,255,.1);padding-top:1rem;">
                <span class="sidebar-icon">🚪</span> Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>👋 Welcome, <?= e($_SESSION['name']) ?></h1>
            <p style="color:var(--muted);margin-top:.25rem;">Here's what's happening on the platform today — <?= date('F j, Y') ?>.</p>
        </div>

        <!-- Stat Cards -->
        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-number" style="color:var(--sage-dark);"><?= $totalDreams ?></div>
                <div class="stat-label">Total Dreams</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:var(--coral);"><?= $pendingDreams ?></div>
                <div class="stat-label">Awaiting Review</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:var(--sky);"><?= $verifiedDreams ?></div>
                <div class="stat-label">Verified</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:var(--amber-dark);"><?= $achievedDreams ?></div>
                <div class="stat-label">Achieved</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:var(--lavender);"><?= $totalGuardians ?></div>
                <div class="stat-label">Guardians</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:var(--sage);"><?= $totalSupporters ?></div>
                <div class="stat-label">Supporters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:var(--amber);"><?= $totalAdoptions ?></div>
                <div class="stat-label">Adoptions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:var(--ink-soft);"><?= $totalUsers ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>

        <?php if ($pendingDreams > 0): ?>
        <div class="flash flash-info" style="margin-bottom:1.5rem;">
            <span>⚠️ <strong><?= $pendingDreams ?> dream<?= $pendingDreams > 1 ? 's' : '' ?></strong> awaiting your review.</span>
            <a href="<?= $base ?>/admin/manage_dreams.php?filter=Submitted" class="btn btn-primary btn-sm">Review Now</a>
        </div>
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;flex-wrap:wrap;">

            <!-- Category Breakdown -->
            <div class="detail-section">
                <h3>📊 Dreams by Category</h3>
                <?php if (empty($catStats)): ?>
                    <p style="color:var(--muted);">No dreams yet.</p>
                <?php else: ?>
                    <div style="display:flex;flex-direction:column;gap:.6rem;">
                    <?php foreach ($catStats as $row):
                        $pct = $totalDreams > 0 ? round(($row['cnt'] / $totalDreams) * 100) : 0;
                    ?>
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:.25rem;font-size:.85rem;">
                                <span><?= e($row['category']) ?></span>
                                <span style="color:var(--muted);"><?= $row['cnt'] ?> (<?= $pct ?>%)</span>
                            </div>
                            <div style="background:var(--border);border-radius:100px;height:8px;overflow:hidden;">
                                <div style="width:<?= $pct ?>%;background:linear-gradient(90deg,var(--sage),var(--amber));height:100%;border-radius:100px;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="detail-section">
                <h3>⚡ Quick Actions</h3>
                <div style="display:flex;flex-direction:column;gap:.75rem;">
                    <a href="<?= $base ?>/admin/manage_dreams.php?filter=Submitted" class="btn btn-amber">
                        🔍 Review Pending Dreams (<?= $pendingDreams ?>)
                    </a>
                    <a href="<?= $base ?>/admin/manage_dreams.php" class="btn btn-outline">
                        🌟 All Dreams Management
                    </a>
                    <a href="<?= $base ?>/admin/manage_users.php" class="btn btn-outline">
                        👥 View All Users
                    </a>
                    <a href="<?= $base ?>/supporter/browse_dreams.php" class="btn btn-outline">
                        🌐 View Public Platform
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Dreams Table -->
        <div class="detail-section" style="margin-top:1.5rem;">
            <h3>🕐 Recent Dream Submissions</h3>
            <?php if (empty($recentDreams)): ?>
                <p style="color:var(--muted);">No dreams submitted yet.</p>
            <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Guardian</th>
                            <th>City</th>
                            <th>Age</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentDreams as $d): ?>
                        <tr>
                            <td><strong><?= e(mb_substr($d['title'], 0, 40)) ?>...</strong></td>
                            <td style="font-size:.8rem;"><?= e($d['category']) ?></td>
                            <td><?= e($d['guardian_name']) ?></td>
                            <td><?= e($d['city']) ?></td>
                            <td><?= e($d['age_group']) ?></td>
                            <td><span class="status-badge status-<?= str_replace(' ', '-', e($d['status'])) ?>"><?= e($d['status']) ?></span></td>
                            <td style="font-size:.8rem;color:var(--muted);"><?= date('M j', strtotime($d['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1rem;">
                <a href="<?= $base ?>/admin/manage_dreams.php" class="btn btn-outline btn-sm">View All Dreams →</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>