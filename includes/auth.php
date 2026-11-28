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

// FT-07 the first page of each role: the admin dashboard for admins and the super admin, the home page for users
function role_home(): string
{
    return has_role('admin', 'super_admin') ? BASE_URL . '/admin/index.php' : BASE_URL . '/home.php';
}

// sends a user without one of these roles back to the first page of his own role
function require_role(string ...$roles): void
{
    require_login();
    if (!has_role(...$roles)) {
        header('Location: ' . role_home());
        exit;
    }
}

// FT-07 the pages of the app are for users; an admin is sent to the admin dashboard
function require_user(): void
{
    require_role('user');
}
