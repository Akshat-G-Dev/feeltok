<?php
// FT-02 Welcome page: the first page a visitor sees. Someone who is logged in goes to the first page of his role (FT-07).
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/values.php';

if (is_logged_in()) {
    header('Location: ' . role_home());
    exit;
}

$title = 'Welcome';
require __DIR__ . '/includes/header.php';
?>
<div class="welcome mx-auto text-center">
    <img src="assets/img/logo-mark.png" alt="FeelTok logo" class="img-fluid logo-mark mb-3">
    <h1 class="fw-bold mb-1">FeelTok</h1>
    <p class="tagline mb-3">Feel the need. Knead the feel.</p>
    <p class="lead mb-4">Share short, positive messages built around six values.</p>

    <h2 class="h6 text-uppercase text-secondary mb-3">The six values</h2>
    <div class="row g-2 mb-4">
        <?php foreach (SIX_VALUES as $key => $name): ?>
            <div class="col-6 col-md-4">
                <div class="value-card value-<?= $key ?>"><?= e($name) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
        <a href="register.php" class="btn btn-dark btn-lg px-4">Create account</a>
        <a href="login.php" class="btn btn-outline-dark btn-lg px-4">Log in</a>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
