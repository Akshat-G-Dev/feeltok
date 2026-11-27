<?php
// Shared top of every page. Set $title before including this file.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security.php';
$title = $title ?? 'FeelTok';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> - FeelTok</title>
    <link rel="icon" href="<?= BASE_URL ?>/assets/img/icon.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-md bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/index.php">
            <img src="<?= BASE_URL ?>/assets/img/icon.png" alt="" width="36" height="36">
            <span class="fw-bold">FeelTok</span>
        </a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- FT-05 menu: on a phone it folds under the button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-menu"
                    aria-controls="main-menu" aria-expanded="false" aria-label="Open the menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="main-menu">
                <ul class="navbar-nav ms-auto align-items-md-center gap-2 py-2 py-md-0">
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/home.php">Home</a></li>
                    <li class="nav-item"><a class="btn btn-outline-dark btn-sm d-inline-flex align-items-center" href="<?= BASE_URL ?>/logout.php">Log out</a></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</nav>
<main class="container py-4">
