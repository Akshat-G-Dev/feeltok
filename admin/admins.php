<?php
// FT-06 Admins page: the super admin sees the admins and adds a new admin. A normal admin is sent back to the dashboard.
require_once __DIR__ . '/../includes/auth.php';
require_role('admin', 'super_admin');
if (!has_role('super_admin')) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/validation.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $errors = registration_errors($db, $name, $email, $_POST['password'] ?? '', '');
    if (!$errors) {
        create_user($db, $name, $email, $_POST['password'], '', 'admin');
        header('Location: ' . BASE_URL . '/admin/admins.php?added=1');
        exit;
    }
}

$admins = list_admins($db);
$title = 'Admins';
require __DIR__ . '/../includes/admin_header.php';
?>
<h1 class="h3 mb-4">Admins</h1>
<?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success" role="alert">The admin account was created</div>
<?php endif; ?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">Admins and the super admin</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr></thead>
                    <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?= e($admin['name']) ?></td>
                            <td><?= e($admin['email']) ?></td>
                            <td><?= $admin['role'] === 'super_admin' ? 'Super admin' : 'Admin' ?></td>
                            <td><?= e(date('d/m/Y', strtotime($admin['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">Add admin</div>
            <div class="card-body">
                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" maxlength="30" value="<?= e($name) ?>"
                               class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" required>
                        <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= e($errors['name']) ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="<?= e($email) ?>"
                               class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" required>
                        <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">First password</label>
                        <input type="password" id="password" name="password"
                               class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" required>
                        <div class="form-text">At least 8 characters, with a letter and a number.</div>
                        <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?= e($errors['password']) ?></div><?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-dark">Add admin</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
