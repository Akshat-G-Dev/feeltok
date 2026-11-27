<?php
// FT-03 Registration: a new user creates an account and is logged in.
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/validation.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$story = trim($_POST['story'] ?? '');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = registration_errors($db, $name, $email, $password, $story);
    if (!$errors) {
        $id = create_user($db, $name, $email, $password, $story);
        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        $_SESSION['name'] = $name;
        $_SESSION['role'] = 'user';
        header('Location: home.php');
        exit;
    }
}

// Bootstrap classes and message for one field
function field_state(array $errors, string $field): array
{
    return isset($errors[$field]) ? [' is-invalid', '<div class="invalid-feedback">' . e($errors[$field]) . '</div>'] : ['', ''];
}

$title = 'Create account';
require __DIR__ . '/includes/header.php';
[$nameClass, $nameError] = field_state($errors, 'name');
[$emailClass, $emailError] = field_state($errors, 'email');
[$passwordClass, $passwordError] = field_state($errors, 'password');
[$storyClass, $storyError] = field_state($errors, 'story');
?>
<div class="form-page mx-auto">
    <h1 class="h3 mb-3">Create account</h1>
    <form method="post" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-control<?= $nameClass ?>" maxlength="30"
                   value="<?= e($name) ?>" required>
            <?= $nameError ?>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control<?= $emailClass ?>"
                   value="<?= e($email) ?>" required>
            <?= $emailError ?>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control<?= $passwordClass ?>" required>
            <div class="form-text">At least 8 characters, with a letter and a number.</div>
            <?= $passwordError ?>
        </div>
        <div class="mb-4">
            <label for="story" class="form-label">Your story (optional)</label>
            <textarea id="story" name="story" class="form-control<?= $storyClass ?>" rows="3" maxlength="300"><?= e($story) ?></textarea>
            <?= $storyError ?>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-dark btn-lg">Create account</button>
        </div>
    </form>
    <p class="text-center mt-3">Already have an account? <a href="login.php">Log in</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
