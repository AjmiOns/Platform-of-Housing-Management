<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Gestion des biens';

$stmt = db()->query(
    'SELECT p.*, c.name AS category_name, o.full_name AS owner_name
     FROM properties p
     JOIN categories c ON c.id = p.category_id
     LEFT JOIN owners o ON o.id = p.owner_id
     ORDER BY p.created_at DESC'
);
$properties = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<section class="section-padding admin-layout">
    <div class="container">
        <?php require __DIR__ . '/_menu.php'; ?>
        <?php show_flash(); ?>
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">Biens immobiliers</h1>
                <a href="<?= url('admin/property-form.php') ?>" class="btn btn-primary">Ajouter un bien</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Bien</th>
                        <th>Localisation</th>
                        <th>Loyer</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($properties as $property): ?>
                        <tr>
                            <td><img class="thumb" src="<?= h(property_image($property['image_url'])) ?>" alt=""></td>
                            <td>
                                <strong><?= h($property['title']) ?></strong><br>
                                <small class="text-muted"><?= h($property['category_name']) ?> | Proprietaire: <?= h($property['owner_name'] ?: '-') ?></small>
                            </td>
                            <td><?= h($property['city']) ?>, <?= h($property['governorate']) ?></td>
                            <td><?= format_tnd((float) $property['rent_price']) ?></td>
                            <td><span class="badge text-bg-<?= status_class($property['availability_status']) ?>"><?= status_label($property['availability_status']) ?></span></td>
                            <td>
                                <a class="btn btn-sm btn-outline-secondary" href="<?= url('property-details.php?id=' . $property['id']) ?>" target="_blank">Voir</a>
                                <a class="btn btn-sm btn-outline-primary" href="<?= url('admin/property-form.php?id=' . $property['id']) ?>">Modifier</a>
                                <a class="btn btn-sm btn-outline-danger" href="<?= url('admin/property-delete.php?id=' . $property['id']) ?>" onclick="return confirm('Supprimer ce bien ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$properties): ?>
                        <tr><td colspan="6" class="text-muted">Aucun bien.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
