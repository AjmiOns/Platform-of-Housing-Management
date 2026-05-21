<?php
/**
 * View: Edit Property
 * Loads the controller first, then renders the edit form.
 */

require __DIR__ . '/property-edit.controller.php';

// Helper: priority POST → DB value
$v = fn(string $key, $default = '') => htmlspecialchars(
    $_POST[$key] ?? $property[$key] ?? $default,
    ENT_QUOTES
);

require __DIR__ . '/../includes/header.php';
?>

<section class="section-padding admin-layout">
    <div class="container">
        <?php require __DIR__ . '/_menu.php'; ?>
        <?php show_flash(); ?>

        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h4 mb-0">✏️ Modifier : <?= h($property['title']) ?></h1>
                <a href="<?= url('admin/properties.php') ?>" class="btn btn-outline-secondary btn-sm">← Retour</a>
            </div>

            <form method="post" enctype="multipart/form-data" novalidate>
                <?= csrf_field() ?>

                <?php require __DIR__ . '/_property-fields.php'; ?>

                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
                    <a href="<?= url('admin/properties.php') ?>" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>