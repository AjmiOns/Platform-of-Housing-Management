<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Dashboard';
// Tableau contenant les statistiques du tableau de bord
$counts = [
    'properties' => (int) db()->query('SELECT COUNT(*) FROM properties')->fetchColumn(),
    'available' => (int) db()->query("SELECT COUNT(*) FROM properties WHERE availability_status = 'available'")->fetchColumn(),
    'visits' => (int) db()->query("SELECT COUNT(*) FROM visit_requests WHERE status = 'new'")->fetchColumn(),
    'messages' => (int) db()->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn(),
];
// Récupérer les 5 dernières demandes de visite
$latestVisits = db()->query(
    // Sélection des données des visites + titre du bien
    'SELECT v.*, p.title AS property_title
     FROM visit_requests v
     JOIN properties p ON p.id = v.property_id
     ORDER BY v.created_at DESC
     LIMIT 5'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<!-- Section principale du dashboard administrateur -->
<section class="section-padding admin-layout">
    <div class="container">
        <?php require __DIR__ . '/_menu.php'; ?>
         <!-- Affichage des messages flash -->
        <?php show_flash(); ?>
         <!-- Cartes statistiques -->
        <div class="row g-4 mb-4">
             <!-- Nombre total de biens -->
            <div class="col-md-3"><div class="stats-card"><strong><?= $counts['properties'] ?></strong><span>Biens</span></div></div>
             <!-- Nombre de biens disponibles -->
            <div class="col-md-3"><div class="stats-card"><strong><?= $counts['available'] ?></strong><span>Disponibles</span></div></div>
              <!-- Nombre de nouvelles visites -->
            <div class="col-md-3"><div class="stats-card"><strong><?= $counts['visits'] ?></strong><span>Nouvelles visites</span></div></div>
             <!-- Nombre de nouveaux messages -->
            <div class="col-md-3"><div class="stats-card"><strong><?= $counts['messages'] ?></strong><span>Nouveaux messages</span></div></div>
        </div>
 <!-- Carte affichant les dernières demandes de visite -->
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 mb-0">Dernieres demandes de visite</h3>
                <a href="<?= url('admin/visits.php') ?>" class="btn btn-sm btn-primary">Voir tout</a>
            </div>
             <!-- Tableau responsive -->
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Client</th><th>Bien</th><th>Date souhaitee</th><th>Statut</th></tr></thead>
                    <tbody>
                         <!-- Boucle sur les visites -->
                    <?php foreach ($latestVisits as $visit): ?>
                        <tr>
                             <!-- Informations client -->
                            <td><?= h($visit['full_name']) ?><br><small class="text-muted"><?= h($visit['phone']) ?></small></td>
                            <td><?= h($visit['property_title']) ?></td>
                            <td><?= h($visit['visit_date']) ?> a <?= h(substr($visit['visit_time'], 0, 5)) ?></td>
                            <td><span class="badge text-bg-info"><?= h($visit['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                     <!-- Message si aucune visite -->
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
