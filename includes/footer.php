<?php
// includes/footer.php
if (!isset($base)) {
    require_once __DIR__ . '/../config/app.php';
    $base = BASE_PATH;
}
?>
</main>

<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <a href="<?= $base ?>/index.php" class="nav-brand">
                <span class="brand-icon">🌱</span>
                <span class="brand-text">Before I Grow Up</span>
            </a>
            <p class="footer-tagline" style="color: #b2ac88">
                <strong>A safe platform where children share learning dreams and supporters help make them real.</strong>
            </p>
            <p class="footer-contact" style="color: #b2ac88">
                <strong>Contact us:</strong> <a href="mailto:beforeigrowup@gmail.com"><strong>beforeigrowup@gmail.com</strong></a><br>
                <strong>Phone:</strong> <a href="tel:+919743295253"><strong>+91 9743295253</strong></a><br>
                <strong>Place: TMA pai polytechnic , Manipal</strong>
            </p>
        </div>

        <div class="footer-links">
            <div class="footer-col">
                <h4>Platform</h4>
                <ul>
                    <li><a href="<?= $base ?>/index.php">Home</a></li>
                    <li><a href="<?= $base ?>/supporter/browse_dreams.php">Browse Dreams</a></li>
                    <li><a href="<?= $base ?>/register.php">Register</a></li>
                    <li><a href="<?= $base ?>/login.php">Login</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>For Guardians</h4>
                <ul>
                    <li><a href="<?= $base ?>/guardian/submit_dream.php">Submit Dream</a></li>
                    <li><a href="<?= $base ?>/guardian/my_dreams.php">My Dreams</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>For Supporters</h4>
                <ul>
                    <li><a href="<?= $base ?>/supporter/browse_dreams.php">Find Dreams</a></li>
                    <li><a href="<?= $base ?>/supporter/adopt_dream.php">My Support</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Before I Grow Up. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
