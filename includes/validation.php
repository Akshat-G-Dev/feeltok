<?php
// FT-03 Registration checks: returns one message for each wrong field.
require_once __DIR__ . '/users.php';

function registration_errors(mysqli $db, string $name, string $email, string $password, string $story): array
{
    $errors = [];
    $length = mb_strlen($name);
    if ($length < 2 || $length > 30) {
        $errors['name'] = 'Please enter a name of 2 to 30 characters';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email';
    } elseif (email_taken($db, $email)) {
        $errors['email'] = 'This email is already registered';
    }
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'The password needs at least 8 characters, with a letter and a number';
    }
    if (mb_strlen($story) > 300) {
        $errors['story'] = 'The story can have up to 300 characters';
    }
    return $errors;
}
