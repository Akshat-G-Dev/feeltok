<?php
// Home page for a logged-in user. A simple greeting for now; FT-05 builds the full home page.
require __DIR__ . '/includes/auth.php';
require_login();

$title = 'Home';
require __DIR__ . '/includes/header.php';
?>
<div class="text-center">
    <h1 class="h3">Hello, <?= e($_SESSION['name']) ?></h1>
    <p class="lead">Welcome to FeelTok.</p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
