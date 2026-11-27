<?php
// FT-03 Saving users: every query uses a prepared statement.

// true when the email is already registered
function email_taken(mysqli $db, string $email): bool
{
    $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

// saves a new account with a hashed password; the role is "user" unless the super admin adds an admin (FT-06)
function create_user(mysqli $db, string $name, string $email, string $password, string $story, string $role = 'user'): int
{
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $story = $story === '' ? null : $story;
    $stmt = $db->prepare('INSERT INTO users (name, email, password, role, story) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssss', $name, $email, $hash, $role, $story);
    $stmt->execute();
    return $db->insert_id;
}

// FT-04 returns the user when the email and the password are right, otherwise null
function check_login(mysqli $db, string $email, string $password): ?array
{
    $stmt = $db->prepare('SELECT id, name, password, role FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    return $user && password_verify($password, $user['password']) ? $user : null;
}

// FT-06 the admins and the super admin, oldest first
function list_admins(mysqli $db): array
{
    $stmt = $db->prepare("SELECT name, email, role, created_at FROM users WHERE role IN ('admin', 'super_admin') ORDER BY created_at, id");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
