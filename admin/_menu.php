<?php require_once __DIR__ . '/../includes/functions.php'; ?>
<div class="admin-card mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div>
            <h5 class="mb-0">Back-office</h5>
            <small class="text-muted">Gestion de l'agence immobiliere</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-sm btn-outline-primary" href="<?= url('admin/dashboard.php') ?>">Dashboard</a>
            <a class="btn btn-sm btn-outline-primary" href="<?= url('admin/properties.php') ?>">Biens</a>
            <a class="btn btn-sm btn-outline-primary" href="<?= url('admin/visits.php') ?>">Visites</a>
            <a class="btn btn-sm btn-outline-primary" href="<?= url('admin/messages.php') ?>">Messages</a>
            <a class="btn btn-sm btn-outline-primary" href="<?= url('admin/settings.php') ?>">Parametres</a>
        </div>
    </div>
</div>
