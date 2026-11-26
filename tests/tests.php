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
                <td><?= htmlspecialchars($name) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
