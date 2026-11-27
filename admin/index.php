<?php
// FT-08 Admin dashboard: the number of registered users and the 10 newest users.
// Only admins and the super admin can open it.
require_once __DIR__ . '/../includes/auth.php';
require_role('admin', 'super_admin');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/users.php';

$user_count = count_users($db);
$newest = newest_users($db, 10);

$title = 'Dashboard';
require __DIR__ . '/../includes/admin_header.php';
?>
<h1 class="h3 mb-1">Dashboard</h1>
<p class="text-muted mb-4">Hello, <?= e($_SESSION['name']) ?>. You are logged in to the FeelTok admin panel.</p>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Registered users</div>
                <div class="display-5 fw-bold" id="user-count"><?= $user_count ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Newest users</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Name</th><th>Email</th><th>Joined</th></tr></thead>
                    <tbody>
                    <?php foreach ($newest as $user): ?>
                        <tr>
                            <td><?= e($user['name']) ?></td>
                            <td><?= e($user['email']) ?></td>
                            <td><?= e(date('d/m/Y', strtotime($user['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
