<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Gestion des biens';

// Charger tous les biens
$properties = db()->query(
    'SELECT p.*, c.name AS category_name, o.full_name AS owner_name
     FROM properties p
     JOIN categories c ON c.id = p.category_id
     LEFT JOIN owners o ON o.id = p.owner_id
     ORDER BY p.created_at DESC'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<section class="section-padding admin-layout">
    <div class="container">
        <?php require __DIR__ . '/_menu.php'; ?>
        <?php show_flash(); ?>

        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">
                    Biens immobiliers
                    <span class="badge bg-secondary ms-1" style="font-size:.75rem;"><?= count($properties) ?></span>
                </h1>
                <a href="<?= url('admin/property-add.php') ?>" class="btn btn-primary btn-sm">+ Ajouter un bien</a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Bien</th>
                            <th>Localisation</th>
                            <th>Loyer</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($properties as $p): ?>
                            <tr>
                                <td>
                                    <img
                                        src="<?= h(property_image($p['image_url'])) ?>"
                                        alt=""
                                        style="width:60px;height:45px;object-fit:cover;border-radius:5px;"
                                    >
                                </td>
                                <td>
                                    <strong><?= h($p['title']) ?></strong><br>
                                    <small class="text-muted">
                                        <?= h($p['category_name']) ?>
                                        <?php if ($p['owner_name']): ?>
                                            · <?= h($p['owner_name']) ?>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td class="text-nowrap"><?= h($p['city']) ?>, <?= h($p['governorate']) ?></td>
                                <td class="text-nowrap"><?= format_tnd((float) $p['rent_price']) ?></td>
                                <td>
                                    <span class="badge text-bg-<?= status_class($p['availability_status']) ?>">
                                        <?= status_label($p['availability_status']) ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-secondary"
                                       href="<?= url('property-details.php?id=' . $p['id']) ?>"
                                       target="_blank" title="Voir">👁</a>
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="<?= url('admin/property-edit.php?id=' . $p['id']) ?>"
                                       title="Modifier">✏️</a>
                                    <a class="btn btn-sm btn-outline-danger"
                                       href="<?= url('admin/property-delete.php?id=' . $p['id']) ?>"
                                       onclick="return confirm('Supprimer ce bien définitivement ?')"
                                       title="Supprimer">🗑</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$properties): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Aucun bien enregistré.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>