<?php
// Shared top of every admin page (SB Admin layout). Set $title before including this file.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security.php';
$title = $title ?? 'Admin';
$role_name = ($_SESSION['role'] ?? '') === 'super_admin' ? 'Super admin' : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> - FeelTok Admin</title>
    <link rel="icon" href="<?= BASE_URL ?>/assets/img/icon.png">
    <!-- SB Admin v7.0.7 by Start Bootstrap, MIT licence (includes Bootstrap 5) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/sb-admin/styles.css">
</head>
<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3 d-flex align-items-center gap-2" href="<?= BASE_URL ?>/admin/index.php">
        <img src="<?= BASE_URL ?>/assets/img/icon.png" alt="" width="28" height="28">
        <span>FeelTok Admin</span>
    </a>
    <button class="btn btn-link btn-sm text-white fs-5 order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" type="button" aria-label="Show or hide the menu">&#9776;</button>
    <div class="ms-auto me-3 d-flex align-items-center gap-3">
        <span class="text-white-50 small d-none d-sm-inline"><?= e($_SESSION['name'] ?? '') ?></span>
        <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/logout.php">Log out</a>
    </div>
</nav>
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Admin</div>
                    <a class="nav-link" href="<?= BASE_URL ?>/admin/index.php">Dashboard</a>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                <?= $role_name ?>
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <main class="container-fluid px-4 py-4">
