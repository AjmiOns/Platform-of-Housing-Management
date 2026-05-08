<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Demandes de visite';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'confirmed';
    if ($id > 0 && in_array($status, ['new', 'confirmed', 'cancelled', 'done'], true)) {
        $stmt = db()->prepare('UPDATE visit_requests SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
        flash('success', 'Statut de la visite modifie.');
    }
    redirect('admin/visits.php');
}

$visits = db()->query(
    'SELECT v.*, p.title AS property_title, p.city, p.governorate
     FROM visit_requests v
     JOIN properties p ON p.id = v.property_id
     ORDER BY v.created_at DESC'
)->fetchAll();
require __DIR__ . '/../includes/header.php';
?>

<section class="section-padding admin-layout">
    <div class="container">
        <?php require __DIR__ . '/_menu.php'; ?>
        <?php show_flash(); ?>
        <div class="admin-card">
            <h1 class="h4 mb-4">Demandes de visite</h1>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Client</th><th>Bien</th><th>Date souhaitee</th><th>Message</th><th>Statut</th></tr></thead>
                    <tbody>
                    <?php foreach ($visits as $visit): ?>
                        <tr>
                            <td>
                                <strong><?= h($visit['full_name']) ?></strong><br>
                                <small><?= h($visit['phone']) ?></small><br>
                                <small><?= h($visit['email'] ?: '-') ?></small>
                            </td>
                            <td>
                                <?= h($visit['property_title']) ?><br>
                                <small class="text-muted"><?= h($visit['city']) ?>, <?= h($visit['governorate']) ?></small>
                            </td>
                            <td><?= h($visit['visit_date']) ?> a <?= h(substr($visit['visit_time'], 0, 5)) ?></td>
                            <td style="max-width:300px"><?= nl2br(h($visit['message'] ?: '-')) ?></td>
                            <td>
                                <form method="post" class="d-flex gap-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $visit['id'] ?>">
                                    <select name="status" class="form-select form-select-sm">
                                        <?php foreach (['new' => 'Nouveau', 'confirmed' => 'Confirme', 'cancelled' => 'Annule', 'done' => 'Effectue'] as $key => $label): ?>
                                            <option value="<?= $key ?>" <?= $visit['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-sm btn-primary">OK</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$visits): ?>
                        <tr><td colspan="5" class="text-muted">Aucune demande.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
