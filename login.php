<?php
// FT-04 Log in: checks the email and the password, then starts the session.
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/users.php';

if (is_logged_in()) {
    header('Location: ' . role_home());
    exit;
}

$email = trim($_POST['email'] ?? '');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = check_login($db, $email, $_POST['password'] ?? '');
    if ($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        header('Location: ' . role_home()); // FT-07 admins go to the admin dashboard, users to the home page
        exit;
    }
    $error = 'Email or password is not correct';
}

$title = 'Log in';
require __DIR__ . '/includes/header.php';
?>
<div class="form-page mx-auto">
    <h1 class="h3 mb-3">Log in</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= e($email) ?>" required>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-dark btn-lg">Log in</button>
        </div>
    </form>
    <p class="text-center mt-3">New to FeelTok? <a href="register.php">Create account</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
