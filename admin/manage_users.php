<?php
// ============================================================
// admin/manage_users.php — View and manage all platform users
// Place this file in: /before-i-grow-up/admin/manage_users.php
// ============================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

requireRole('admin');

$pageTitle = 'Manage Users';
$base = BASE_PATH;
$db   = getDB();

$filterRole = $_GET['role']   ?? '';
$search     = trim($_GET['search'] ?? '');

$where  = "WHERE u.role != 'admin'";
$params = [];

if ($filterRole && in_array($filterRole, ['guardian','supporter'])) {
    $where   .= " AND u.role = ?";
    $params[] = $filterRole;
}
if ($search) {
    $where   .= " AND (u.name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$users = $db->prepare("
    SELECT u.*,
           CASE
               WHEN u.role = 'guardian' THEN (SELECT COUNT(*) FROM students s WHERE s.guardian_id = u.id)
               ELSE 0
           END AS student_count,
           CASE
               WHEN u.role = 'supporter' THEN (SELECT COUNT(*) FROM dream_support ds WHERE ds.supporter_id = u.id)
               ELSE 0
           END AS adoption_count,
           sp.profession, sp.interest_area
    FROM users u
    LEFT JOIN supporters sp ON sp.user_id = u.id
    $where
    ORDER BY u.created_at DESC
");
$users->execute($params);
$users = $users->fetchAll();

$totalGuardians  = $db->query("SELECT COUNT(*) FROM users WHERE role='guardian'")->fetchColumn();
$totalSupporters = $db->query("SELECT COUNT(*) FROM users WHERE role='supporter'")->fetchColumn();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-title">Admin Panel</div>
        <nav>
            <a href="<?= $base ?>/admin/dashboard.php"     class="sidebar-link"><span class="sidebar-icon">📊</span> Dashboard</a>
            <a href="<?= $base ?>/admin/manage_dreams.php" class="sidebar-link"><span class="sidebar-icon">🌟</span> Manage Dreams</a>
            <a href="<?= $base ?>/admin/manage_users.php"  class="sidebar-link active"><span class="sidebar-icon">👥</span> Manage Users</a>
            <a href="<?= $base ?>/supporter/browse_dreams.php" class="sidebar-link"><span class="sidebar-icon">🔍</span> View Public</a>
            <a href="<?= $base ?>/logout.php" class="sidebar-link" style="margin-top:2rem;border-top:1px solid rgba(255,255,255,.1);padding-top:1rem;"><span class="sidebar-icon">🚪</span> Logout</a>
        </nav>
    </aside>

    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Manage Users</h1>
            <p style="color:var(--muted);"><?= $totalGuardians ?> Guardians · <?= $totalSupporters ?> Supporters</p>
        </div>

        <!-- Filters -->
        <form method="GET" action="" style="margin-bottom:1.5rem;">
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
                <input type="text" name="search" class="form-control" placeholder="Search by name or email..."
                    style="max-width:260px;" value="<?= e($search) ?>">
                <select name="role" class="form-control" style="max-width:160px;">
                    <option value="">All Roles</option>
                    <option value="guardian"  <?= $filterRole === 'guardian'  ? 'selected' : '' ?>>Guardians</option>
                    <option value="supporter" <?= $filterRole === 'supporter' ? 'selected' : '' ?>>Supporters</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="<?= $base ?>/admin/manage_users.php" class="btn btn-outline btn-sm">Clear</a>
            </div>
        </form>

        <!-- Summary stats -->
        <div class="stat-cards" style="margin-bottom:1.5rem;">
            <div class="stat-card">
                <div class="stat-number" style="color:var(--lavender);"><?= $totalGuardians ?></div>
                <div class="stat-label">Total Guardians</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:var(--sage);"><?= $totalSupporters ?></div>
                <div class="stat-label">Total Supporters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:var(--ink-soft);"><?= count($users) ?></div>
                <div class="stat-label">Showing</div>
            </div>
        </div>

        <?php if (empty($users)): ?>
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <h3>No users found</h3>
                <p>Try adjusting your search or filters.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Details</th>
                            <th>Activity</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td style="color:var(--muted);"><?= (int)$u['id'] ?></td>
                            <td><strong><?= e($u['name']) ?></strong></td>
                            <td style="font-size:.85rem;"><?= e($u['email']) ?></td>
                            <td>
                                <?php if ($u['role'] === 'guardian'): ?>
                                    <span class="status-badge" style="background:#EDE9FE;color:#5B21B6;">👨‍👩‍👧 Guardian</span>
                                <?php else: ?>
                                    <span class="status-badge" style="background:#ECFDF5;color:#065F46;">💛 Supporter</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:.8rem;color:var(--muted);">
                                <?php if ($u['role'] === 'supporter' && $u['profession']): ?>
                                    <div><?= e($u['profession']) ?></div>
                                    <?php if ($u['interest_area']): ?><div><?= e($u['interest_area']) ?></div><?php endif; ?>
                                <?php elseif ($u['role'] === 'guardian'): ?>
                                    <?= (int)$u['student_count'] ?> student(s)
                                <?php endif; ?>
                            </td>
                            <td style="font-size:.8rem;">
                                <?php if ($u['role'] === 'guardian'): ?>
                                    <?= (int)$u['student_count'] ?> dream(s) submitted
                                <?php else: ?>
                                    <?= (int)$u['adoption_count'] ?> adoption(s)
                                <?php endif; ?>
                            </td>
                            <td style="font-size:.8rem;color:var(--muted);"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>