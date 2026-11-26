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

// saves a new user with a hashed password and the role "user"; returns the new id
function create_user(mysqli $db, string $name, string $email, string $password, string $story): int
{
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $story = $story === '' ? null : $story;
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role, story) VALUES (?, ?, ?, 'user', ?)");
    $stmt->bind_param('ssss', $name, $email, $hash, $story);
    $stmt->execute();
    return $db->insert_id;
}
