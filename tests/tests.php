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

// FT-02 welcome page: read the HTML that index.php sends to a visitor (the real session is kept as it is)
require_once __DIR__ . '/../includes/auth.php';
$saved_session = $_SESSION;
$_SESSION = [];
ob_start();
include __DIR__ . '/../index.php';
$page = ob_get_clean();
$_SESSION = $saved_session;
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

// FT-05 home page: read the HTML that home.php sends to Sara after log in
$saved_session = $_SESSION;
$_SESSION = ['user_id' => 3, 'name' => 'Sara', 'role' => 'user'];
ob_start();
include __DIR__ . '/../home.php';
$page = ob_get_clean();
$_SESSION = $saved_session;
check('FT-05 The home page greets the user by name', str_contains($page, 'Hello, Sara'));
$all_values = true;
foreach (['Love', 'Gratitude', 'Apology', 'Appreciation', 'Mindfulness', 'Thank you'] as $value) {
    $all_values = $all_values && str_contains($page, ">$value<");
}
check('FT-05 The home page shows the six values', $all_values);
$no_messages = count_rows($db, "SELECT COUNT(*) FROM posts WHERE status = 'active'") === 0;
check('FT-05 With no messages the home page invites the user to share the first one',
    !$no_messages || str_contains($page, 'No messages yet - share the first Hi Six!'));
check('FT-05 The menu has Home and Log out and folds under a button on a phone',
    str_contains($page, 'navbar-toggler') && str_contains($page, '/home.php">Home</a>') && str_contains($page, '/logout.php">Log out</a>'));

// FT-06 admin panel: the SB Admin layout, read as an admin sees it
check('FT-06 SB Admin is stored in the project with its MIT licence',
    is_file(__DIR__ . '/../assets/vendor/sb-admin/styles.css') && is_file(__DIR__ . '/../assets/vendor/sb-admin/scripts.js')
    && str_contains((string) @file_get_contents(__DIR__ . '/../assets/vendor/sb-admin/LICENSE'), 'MIT'));
$saved_session = $_SESSION;
$_SESSION = ['user_id' => 2, 'name' => 'Admin', 'role' => 'admin'];
ob_start();
include __DIR__ . '/../admin/index.php';
$page = ob_get_clean();
$_SESSION = $saved_session;
check('FT-06 The admin panel has the SB Admin top bar and side menu',
    str_contains($page, 'sb-topnav') && str_contains($page, 'id="layoutSidenav_nav"') && str_contains($page, '/admin/index.php">Dashboard</a>'));

// FT-06 the super admin adds an admin
$db->begin_transaction();
$admin_id = create_user($db, 'Check Admin', 'check.admin.' . time() . '@feeltok.test', 'Admin1234', '', 'admin');
$row = $db->query("SELECT password, role FROM users WHERE id = $admin_id")->fetch_assoc();
$db->rollback();
check('FT-06 The super admin can add an admin (role admin, hashed password)',
    $row && $row['role'] === 'admin' && password_verify('Admin1234', $row['password']));
$saved_session = $_SESSION;
$_SESSION = ['user_id' => 1, 'name' => 'Super Admin', 'role' => 'super_admin'];
ob_start();
include __DIR__ . '/../admin/admins.php';
$page = ob_get_clean();
check('FT-06 The Admins page lists the admins and has the Add admin form',
    str_contains($page, 'superadmin@feeltok.test') && str_contains($page, 'admin@feeltok.test') && str_contains($page, '>Add admin</button>'));
$super_menu = str_contains($page, '/admin/admins.php">Admins</a>');
$_SESSION = ['user_id' => 2, 'name' => 'Admin', 'role' => 'admin'];
ob_start();
include __DIR__ . '/../admin/index.php';
$page = ob_get_clean();
$_SESSION = $saved_session;
check('FT-06 Only the super admin sees Admins in the side menu', $super_menu && !str_contains($page, '/admin/admins.php">Admins</a>'));

// FT-07 access by role (the real session is kept as it is)
$saved_session = $_SESSION;
$homes = [];
foreach (['user', 'admin', 'super_admin'] as $role) {
    $_SESSION = ['user_id' => 3, 'name' => 'Check', 'role' => $role];
    $homes[$role] = role_home();
}
$_SESSION = ['user_id' => 3, 'name' => 'Sara', 'role' => 'user'];
$user_ok = has_role('user') && !has_role('admin', 'super_admin');
$_SESSION = ['user_id' => 2, 'name' => 'Admin', 'role' => 'admin'];
$admin_ok = !has_role('user') && has_role('admin', 'super_admin');
$_SESSION = $saved_session;
check('FT-07 After log in a user goes to the home page, an admin and the super admin to the admin dashboard',
    $homes['user'] === BASE_URL . '/home.php' && $homes['admin'] === BASE_URL . '/admin/index.php' && $homes['super_admin'] === BASE_URL . '/admin/index.php');
check('FT-07 Only users can open the pages of the app, and only admins the admin pages', $user_ok && $admin_ok);

// FT-08 admin dashboard, read as an admin sees it
$saved_session = $_SESSION;
$_SESSION = ['user_id' => 2, 'name' => 'Admin', 'role' => 'admin'];
ob_start();
include __DIR__ . '/../admin/index.php';
$page = ob_get_clean();
$_SESSION = $saved_session;
$users_now = count_rows($db, "SELECT COUNT(*) FROM users WHERE role = 'user'");
check('FT-08 The dashboard shows the number of registered users', str_contains($page, 'id="user-count">' . $users_now . '<'));
$newest = newest_users($db, 10);
$dates = array_column($newest, 'created_at');
$sorted = $dates;
rsort($sorted);
$saved_session = $_SESSION;
$_SESSION = ['user_id' => 2, 'name' => 'Admin', 'role' => 'admin'];
ob_start();
include __DIR__ . '/../admin/users.php';
$page = ob_get_clean();
$_SESSION = $saved_session;
$all_listed = true;
foreach ($db->query('SELECT email FROM users') as $account) {
    $all_listed = $all_listed && str_contains($page, '>' . $account['email'] . '<');
}
check('FT-08 The Users page lists every account with its name, email, role and join date',
    $all_listed && str_contains($page, '<th>Role</th>') && str_contains($page, '<th>Joined</th>'));
check('FT-08 The dashboard lists at most 10 users, newest first', count($newest) <= 10 && count($newest) === min(10, $users_now) && $dates === $sorted);

// FT-09 access control: the helpers in auth.php (the real session is kept as it is)
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
