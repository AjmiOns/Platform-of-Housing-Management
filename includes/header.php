<?php
require_once __DIR__ . '/functions.php';
$settings = app_settings();
$pageTitle = $pageTitle ?? $settings['agency_name'];
$currentPath = str_replace(APP_BASE . '/', '', $_SERVER['REQUEST_URI'] ?? '');
$currentPath = strtok($currentPath, '?') ?: 'index.php';
$isAdminPage = str_starts_with($currentPath, 'admin/');
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?> | <?= h($settings['agency_name']) ?></title>
    <meta name="description" content="<?= h($settings['slogan']) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= url('public/assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="topbar d-none d-lg-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <span><i class="fa-solid fa-envelope"></i> <?= h($settings['email']) ?></span>
            <span class="ms-3"><i class="fa-solid fa-location-dot"></i> <?= h($settings['city']) ?>, <?= h($settings['governorate']) ?></span>
        </div>
        <div>
            <span><?= h($settings['working_hours']) ?></span>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= url('index.php') ?>">
            <span class="brand-mark">DT</span> <?= h($settings['agency_name']) ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item"><a class="nav-link <?= $currentPath === 'index.php' ? 'active' : '' ?>" href="<?= url('index.php') ?>">Accueil</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPath === 'properties.php' ? 'active' : '' ?>" href="<?= url('properties.php') ?>">Biens</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPath === 'contact.php' ? 'active' : '' ?>" href="<?= url('contact.php') ?>">Contact</a></li>
                <?php if (is_admin()): ?>
                    <li class="nav-item"><a class="nav-link <?= $isAdminPage ? 'active' : '' ?>" href="<?= url('admin/dashboard.php') ?>">Admin</a></li>
                    <li class="nav-item"><a class="btn btn-outline-dark btn-sm ms-lg-2" href="<?= url('logout.php') ?>">Deconnexion</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-primary btn-sm ms-lg-2" href="<?= url('login.php') ?>">Espace admin</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main>
