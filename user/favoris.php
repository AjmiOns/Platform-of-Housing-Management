<?php
/**
 * user/favoris.php — Mes biens favoris
 */
$pageTitle  = 'Mes favoris';
$activePage = 'favoris';
require __DIR__ . '/_layout_top.php';

$clientId = $client['id'];

// Supprimer un favori (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_fav'])) {
    verify_csrf();
    $propId = (int) ($_POST['property_id'] ?? 0);
    if ($propId > 0) {
        remove_favorite($clientId, $propId);
        flash('success', 'Bien retiré de vos favoris.');
        redirect('user/favoris.php');
    }
}

$favoris = get_favorites($clientId);
?>

<!-- ── En-tête ─────────────────────────────────────────── -->
<div class="ud-page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h1>Mes favoris</h1>
        <p><?= count($favoris) ?> bien<?= count($favoris) > 1 ? 's' : '' ?> sauvegardé<?= count($favoris) > 1 ? 's' : '' ?></p>
    </div>
    <a href="<?= $appBase ?>/properties.php" class="btn btn-primary-dt">
        <i class="fa-solid fa-plus me-2"></i>Ajouter des biens
    </a>
</div>

<?php if (empty($favoris)): ?>
    <!-- État vide -->
    <div class="ud-card">
        <div class="ud-empty">
            <i class="fa-solid fa-heart-crack"></i>
            <p>Vous n'avez pas encore de bien en favori.<br>Parcourez notre catalogue et ajoutez des biens qui vous intéressent.</p>
            <a href="<?= $appBase ?>/properties.php" class="btn btn-primary-dt">
                <i class="fa-solid fa-magnifying-glass me-2"></i>Découvrir les biens
            </a>
        </div>
    </div>

<?php else: ?>
    <div class="row g-3">
        <?php foreach ($favoris as $fav): ?>
            <div class="col-sm-6 col-xl-4">
                <div class="ud-property-card">

                    <!-- Image -->
                    <div class="ud-property-img">
                        <img src="<?= h(property_image($fav['image_url'])) ?>"
                             alt="<?= h($fav['title']) ?>"
                             loading="lazy">

                        <!-- Statut -->
                        <span class="badge-status badge bg-<?= status_class($fav['availability_status']) ?>">
                            <?= status_label($fav['availability_status']) ?>
                        </span>

                        <!-- Bouton retirer -->
                        <form method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="property_id" value="<?= (int)$fav['id'] ?>">
                            <button type="submit" name="remove_fav" class="btn-remove-fav"
                                    title="Retirer des favoris"
                                    onclick="return confirm('Retirer ce bien de vos favoris ?')">
                                <i class="fa-solid fa-heart-crack"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Détails -->
                    <div class="ud-property-body">
                        <span class="badge text-bg-light mb-1" style="font-size:.7rem">
                            <?= h($fav['category_name']) ?>
                        </span>
                        <h6><?= h($fav['title']) ?></h6>
                        <p class="ud-property-location">
                            <i class="fa-solid fa-location-dot me-1"></i>
                            <?= h($fav['city']) ?>, <?= h($fav['governorate']) ?>
                        </p>

                        <div class="ud-property-price">
                            <?= format_tnd((float)$fav['rent_price']) ?>
                            <small class="text-muted fw-normal" style="font-size:.7rem">/mois</small>
                        </div>

                        <div class="ud-property-meta">
                            <span><i class="fa-solid fa-bed me-1"></i><?= (int)$fav['bedrooms'] ?> ch.</span>
                            <span><i class="fa-solid fa-bath me-1"></i><?= (int)$fav['bathrooms'] ?> sdb.</span>
                            <span><i class="fa-solid fa-maximize me-1"></i><?= (int)$fav['area'] ?> m²</span>
                        </div>

                        <a href="<?= $appBase ?>/property-details.php?id=<?= (int)$fav['id'] ?>"
                           class="btn btn-primary-dt w-100 mt-3" style="font-size:.83rem;padding:8px">
                            <i class="fa-solid fa-eye me-1"></i>Voir le bien
                        </a>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
