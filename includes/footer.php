<?php
// ============================================================
// includes/footer.php
// ============================================================
require_once __DIR__ . '/../config/app.php';
$base = BASE_PATH;
?>
</main>

<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <span class="brand-icon">🌱</span>
            <span class="brand-text">Before I Grow Up</span>
            <p class="footer-tagline">"A place where young dreams find helping hands."</p>
        </div>
        <div class="footer-links">
            <div class="footer-col">
                <h4>Platform</h4>
                <ul>
                    <li><a href="<?= $base ?>/index.php">Home</a></li>
                    <li><a href="<?= $base ?>/supporter/browse_dreams.php">Browse Dreams</a></li>
                    <li><a href="<?= $base ?>/register.php">Register</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>For Guardians</h4>
                <ul>
                    <li><a href="<?= $base ?>/guardian/submit_dream.php">Submit a Dream</a></li>
                    <li><a href="<?= $base ?>/guardian/my_dreams.php">Track Progress</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>For Supporters</h4>
                <ul>
                    <li><a href="<?= $base ?>/supporter/browse_dreams.php">Find a Dream</a></li>
                    <li><a href="<?= $base ?>/supporter/adopt_dream.php">My Support</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Before I Grow Up. Built with care for young dreamers.</p>
    </div>
</footer>
</body>
</html>