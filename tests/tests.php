<?php
// Automatic checks. Open http://localhost/feeltok/tests/tests.php
// or run: C:\xampp\php\php.exe tests\tests.php
require __DIR__ . '/../includes/db.php';

$results = [];

function check($name, $ok)
{
    global $results;
    $results[] = [$name, (bool) $ok];
}

function count_rows($db, $sql)
{
    return (int) $db->query($sql)->fetch_row()[0];
}

// FT-01 project set-up
check('FT-01 The database connection works', $db->ping());
foreach (['users', 'posts', 'reactions', 'moods', 'notifications'] as $table) {
    check("FT-01 Table '$table' exists", $db->query("SHOW TABLES LIKE '$table'")->num_rows === 1);
}
check('FT-01 One super admin account exists', count_rows($db, "SELECT COUNT(*) FROM users WHERE role = 'super_admin'") === 1);
check('FT-01 An admin account exists', count_rows($db, "SELECT COUNT(*) FROM users WHERE role = 'admin'") >= 1);
check('FT-01 Four test users exist', count_rows($db, "SELECT COUNT(*) FROM users WHERE role = 'user'") >= 4);
$hash = $db->query("SELECT password FROM users WHERE email = 'sara@feeltok.test'")->fetch_row()[0] ?? '';
check('FT-01 Test passwords are stored as hashes', $hash !== 'Test@1234' && password_verify('Test@1234', $hash));
check('FT-01 The shared header and footer exist', is_file(__DIR__ . '/../includes/header.php') && is_file(__DIR__ . '/../includes/footer.php'));

// FT-02 welcome page: read the HTML that index.php sends to the browser
ob_start();
include __DIR__ . '/../index.php';
$page = ob_get_clean();
check('FT-02 The welcome page shows the logo', str_contains($page, 'alt="FeelTok logo"') && is_file(__DIR__ . '/../assets/img/logo-mark.png'));
check('FT-02 The welcome page shows the tagline', str_contains($page, 'Feel the need. Knead the feel.'));
$all_values = true;
foreach (['Love', 'Gratitude', 'Apology', 'Appreciation', 'Mindfulness', 'Thank you'] as $value) {
    $all_values = $all_values && str_contains($page, ">$value<");
}
check('FT-02 The welcome page shows the six values', $all_values);
check('FT-02 The welcome page has the Create account and Log in buttons',
    str_contains($page, 'href="register.php"') && str_contains($page, 'Create account')
    && str_contains($page, 'href="login.php"') && str_contains($page, 'Log in'));
check('FT-02 The page is set to fit the phone screen width', str_contains($page, 'width=device-width'));

// FT-03 registration: the checks and the saving of a new user
require_once __DIR__ . '/../includes/validation.php';
$errors = registration_errors($db, 'Test User', 'test.user.feeltok.test', 'Test1234', '');
check('FT-03 An email without "@" is refused', ($errors['email'] ?? '') === 'Please enter a valid email');
$errors = registration_errors($db, 'Test User', 'sara@feeltok.test', 'Test1234', '');
check('FT-03 An email that is already registered is refused', ($errors['email'] ?? '') === 'This email is already registered');
$errors = registration_errors($db, 'Test User', 'test.user@feeltok.test', 'abcdefgh', '');
check('FT-03 A password without a number is refused', isset($errors['password']));
check('FT-03 Correct details pass every check', registration_errors($db, 'Test User', 'free.' . time() . '@feeltok.test', 'Test1234', '') === []);
$db->begin_transaction();
$new_id = create_user($db, 'Test User', 'check.' . time() . '@feeltok.test', 'Test1234', '');
$row = $db->query("SELECT password, role FROM users WHERE id = $new_id")->fetch_assoc();
$db->rollback();
check('FT-03 A new user is saved with a hashed password and the role user',
    $row && $row['role'] === 'user' && password_verify('Test1234', $row['password']));

// FT-04 log in: the check of the email and the password
check('FT-04 The right email and password are accepted', (check_login($db, 'sara@feeltok.test', 'Test@1234')['role'] ?? '') === 'user');
check('FT-04 A wrong password is refused', check_login($db, 'sara@feeltok.test', 'Wrong1234') === null);
check('FT-04 An unknown email is refused', check_login($db, 'nobody@feeltok.test', 'Test@1234') === null);

// FT-09 access control: the helpers in auth.php (the real session is kept as it is)
require_once __DIR__ . '/../includes/auth.php';
$saved_session = $_SESSION;
$_SESSION = [];
check('FT-09 A visitor who is not logged in is not let in', !is_logged_in());
$_SESSION = ['user_id' => 2, 'role' => 'admin'];
check('FT-09 An admin has the admin role', has_role('admin', 'super_admin'));
$_SESSION = ['user_id' => 3, 'role' => 'user'];
check('FT-09 A user does not have the admin role', !has_role('admin', 'super_admin'));
$_SESSION = $saved_session;

// FT-09 safe output and safe queries
require_once __DIR__ . '/../includes/security.php';
check('FT-09 "<script>" typed by a user is shown as plain text', e('<script>alert(1)</script>') === '&lt;script&gt;alert(1)&lt;/script&gt;');
check('FT-09 Quotes typed by a user cannot break out of a form field', e('" onfocus="alert(1)') === '&quot; onfocus=&quot;alert(1)');
$plain = 0;
foreach ($db->query('SELECT password FROM users') as $user) {
    $plain += password_get_info($user['password'])['algoName'] === 'unknown' ? 1 : 0;
}
check('FT-09 Every password in the database is stored as a hash', $plain === 0);
$unsafe = [];
foreach (array_merge(glob(__DIR__ . '/../*.php'), glob(__DIR__ . '/../includes/*.php')) as $file) {
    if (preg_match('/->query\(\s*(\$|"[^"]*\$)/', file_get_contents($file))) {
        $unsafe[] = basename($file);
    }
}
check('FT-09 No page puts a value straight into an SQL query (prepared statements are used)', $unsafe === []);

$passed = count(array_filter($results, fn($r) => $r[1]));
$total = count($results);

if (PHP_SAPI === 'cli') {
    foreach ($results as [$name, $ok]) {
        echo ($ok ? 'PASS' : 'FAIL') . "  $name\n";
    }
    echo "$passed of $total checks passed\n";
    exit($passed === $total ? 0 : 1);
}

$title = 'Tests';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h4">FeelTok checks</h1>
<p class="<?= $passed === $total ? 'text-success' : 'text-danger' ?> fw-bold"><?= $passed ?> of <?= $total ?> checks passed</p>
<div class="table-responsive">
    <table class="table table-sm bg-white">
        <thead><tr><th>Result</th><th>Check</th></tr></thead>
        <tbody>
        <?php foreach ($results as [$name, $ok]): ?>
            <tr>
                <td class="fw-bold <?= $ok ? 'text-success' : 'text-danger' ?>"><?= $ok ? 'PASS' : 'FAIL' ?></td>
                <td><?= e($name) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
