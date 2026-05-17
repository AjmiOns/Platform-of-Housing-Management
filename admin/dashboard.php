<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Dashboard';

$counts = [
    'properties' => (int) db()->query('SELECT COUNT(*) FROM properties')->fetchColumn(),
    'available' => (int) db()->query("SELECT COUNT(*) FROM properties WHERE availability_status = 'available'")->fetchColumn(),
    'visits' => (int) db()->query("SELECT COUNT(*) FROM visit_requests WHERE status = 'new'")->fetchColumn(),
    'messages' => (int) db()->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn(),
];

$latestVisits = db()->query(
    'SELECT v.*, p.title AS property_title
     FROM visit_requests v
     JOIN properties p ON p.id = v.property_id
     ORDER BY v.created_at DESC
     LIMIT 5'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<section class="section-padding admin-layout">
    <div class="container">
        <?php require __DIR__ . '/_menu.php'; ?>
        <?php show_flash(); ?>
        <div class="row g-4 mb-4">
            <div class="col-md-3"><div class="stats-card"><strong><?= $counts['properties'] ?></strong><span>Biens</span></div></div>
            <div class="col-md-3"><div class="stats-card"><strong><?= $counts['available'] ?></strong><span>Disponibles</span></div></div>
            <div class="col-md-3"><div class="stats-card"><strong><?= $counts['visits'] ?></strong><span>Nouvelles visites</span></div></div>
            <div class="col-md-3"><div class="stats-card"><strong><?= $counts['messages'] ?></strong><span>Nouveaux messages</span></div></div>
        </div>

        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 mb-0">Dernieres demandes de visite</h3>
                <a href="<?= url('admin/visits.php') ?>" class="btn btn-sm btn-primary">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Client</th><th>Bien</th><th>Date souhaitee</th><th>Statut</th></tr></thead>
                    <tbody>
                    <?php foreach ($latestVisits as $visit): ?>
                        <tr>
                            <td><?= h($visit['full_name']) ?><br><small class="text-muted"><?= h($visit['phone']) ?></small></td>
                            <td><?= h($visit['property_title']) ?></td>
                            <td><?= h($visit['visit_date']) ?> a <?= h(substr($visit['visit_time'], 0, 5)) ?></td>
                            <td><span class="badge text-bg-info"><?= h($visit['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$latestVisits): ?>
                        <tr><td colspan="4" class="text-muted">Aucune demande.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
