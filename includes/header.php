<?php
// Shared top of every page. Set $title before including this file.
require_once __DIR__ . '/config.php';
$title = $title ?? 'FeelTok';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?> - FeelTok</title>
    <link rel="icon" href="<?= BASE_URL ?>/assets/img/icon.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/index.php">
            <img src="<?= BASE_URL ?>/assets/img/icon.png" alt="" width="36" height="36">
            <span class="fw-bold">FeelTok</span>
        </a>
    </div>
</nav>
<main class="container py-4">
