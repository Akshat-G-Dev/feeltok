<?php
// FT-04 Log out: ends the session and returns to the welcome page.
require __DIR__ . '/includes/auth.php';

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $cookie = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
}
session_destroy();

header('Location: index.php');
exit;
