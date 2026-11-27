<?php
// FT-06 Admin panel: the first admin page. Only admins and the super admin can open it.
// FT-08 adds the number of users and the newest users here.
require_once __DIR__ . '/../includes/auth.php';
require_role('admin', 'super_admin');

$title = 'Dashboard';
require __DIR__ . '/../includes/admin_header.php';
?>
<h1 class="h3 mb-1">Dashboard</h1>
<p class="text-muted mb-4">Hello, <?= e($_SESSION['name']) ?>. You are logged in to the FeelTok admin panel.</p>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
