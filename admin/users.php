<?php
// FT-08 Users page: every account with its name, email, role and join date. Only admins and the super admin can open it.
require_once __DIR__ . '/../includes/auth.php';
require_role('admin', 'super_admin');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/users.php';

$users = list_users($db);
$roles = ['user' => 'User', 'admin' => 'Admin', 'super_admin' => 'Super admin'];

$title = 'Users';
require __DIR__ . '/../includes/admin_header.php';
?>
<h1 class="h3 mb-1">Users</h1>
<p class="text-muted mb-4"><?= count($users) ?> accounts, newest first.</p>
<div class="card">
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr></thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e($user['name']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= $roles[$user['role']] ?? e($user['role']) ?></td>
                    <td><?= e(date('d/m/Y', strtotime($user['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
