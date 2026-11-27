<?php
// FT-09 Access control: starts the session and checks the log in and the role.
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function has_role(string ...$roles): bool
{
    return is_logged_in() && in_array($_SESSION['role'] ?? '', $roles, true);
}

// sends a visitor who is not logged in to the log in page
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/login.php'); // full path, so it also works from the admin folder
        exit;
    }
}

// sends a user without one of these roles back to his home page
function require_role(string ...$roles): void
{
    require_login();
    if (!has_role(...$roles)) {
        header('Location: ' . BASE_URL . '/home.php');
        exit;
    }
}
