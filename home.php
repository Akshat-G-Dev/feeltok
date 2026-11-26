<?php
// Home page for a logged-in user. A simple greeting for now; FT-05 builds the full home page.
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$title = 'Home';
require __DIR__ . '/includes/header.php';
?>
<div class="text-center">
    <h1 class="h3">Hello, <?= htmlspecialchars($_SESSION['name']) ?></h1>
    <p class="lead">Welcome to FeelTok.</p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
