<?php
// FT-05 Home page after log in: the greeting, the six values and the messages.
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/values.php';

// Messages are posted from FT-10 (Sprint 2); until then there are none.
$message_count = (int) $db->query("SELECT COUNT(*) FROM posts WHERE status = 'active'")->fetch_row()[0];

$title = 'Home';
require __DIR__ . '/includes/header.php';
?>
<div class="home mx-auto">
    <h1 class="h3 mb-1">Hello, <?= e($_SESSION['name']) ?></h1>
    <p class="text-secondary mb-4">Welcome to FeelTok.</p>

    <h2 class="h6 text-uppercase text-secondary mb-3">The six values</h2>
    <div class="row g-2 mb-4">
        <?php foreach (SIX_VALUES as $key => $name): ?>
            <div class="col-6 col-md-4">
                <div class="value-card value-<?= $key ?>"><?= e($name) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 class="h6 text-uppercase text-secondary mb-3">Messages</h2>
    <div class="card border-0">
        <div class="card-body text-center py-5">
            <?php if ($message_count === 0): ?>
                <p class="mb-0">No messages yet - share the first Hi Six!</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
