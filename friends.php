<?php
// FT-33 Friends screen: four hard-coded friends for the first release.
// FT-34 to FT-36 (release v2.0) replace them with real friends from the database.
require_once __DIR__ . '/includes/auth.php';
require_user();

$friends = ['Maya', 'Yusuf', 'Hana', 'Karim'];

$title = 'Friends';
require __DIR__ . '/includes/header.php';
?>
<div class="home mx-auto">
    <h1 class="h3 mb-4">Friends</h1>
    <ul class="list-group mb-3">
        <?php foreach ($friends as $friend): ?>
            <li class="list-group-item d-flex align-items-center gap-3 py-3">
                <span class="friend-initial"><?= e(substr($friend, 0, 1)) ?></span>
                <span><?= e($friend) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
    <p class="text-secondary small mb-0">Finding and adding friends comes in the next release.</p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
