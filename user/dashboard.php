<?php
/**
 * user/dashboard.php — Tableau de bord client
 */
$pageTitle  = 'Tableau de bord';
$activePage = 'dashboard';
require __DIR__ . '/_layout_top.php';

// ── Statistiques ─────────────────────────────────────────
$clientId = $client['id'];

$stmt = db()->prepare('SELECT COUNT(*) FROM favorites WHERE client_id = :id');
$stmt->execute(['id' => $clientId]);
$totalFavoris = (int) $stmt->fetchColumn();

$stmt = db()->prepare('SELECT COUNT(*) FROM visit_requests WHERE client_id = :id');
$stmt->execute(['id' => $clientId]);
$totalVisites = (int) $stmt->fetchColumn();

$stmt = db()->prepare("SELECT COUNT(*) FROM visit_requests WHERE client_id = :id AND status = 'confirmed'");
$stmt->execute(['id' => $clientId]);
$visitesConfirmees = (int) $stmt->fetchColumn();

$stmt = db()->prepare("SELECT COUNT(*) FROM visit_requests WHERE client_id = :id AND status = 'new'");
$stmt->execute(['id' => $clientId]);
$visitesEnAttente = (int) $stmt->fetchColumn();

// Derniers favoris (3 max)
$recentFavoris = get_favorites($clientId);
$recentFavoris = array_slice($recentFavoris, 0, 3);

// Dernières visites (5 max)
$recentVisites = get_client_visits($clientId);
$recentVisites = array_slice($recentVisites, 0, 5);
?>

<!-- ── En-tête de page ───────────────────────────────── -->
<div class="ud-page-header">
    <h1>Bienvenue, <?= h($client['nom']) ?> 👋</h1>
    <p>Voici un aperçu de votre activité sur Dar Tunisie</p>
</div>

<!-- ── Statistiques ─────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="ud-stat-card">
            <div class="ud-stat-icon orange">
                <i class="fa-solid fa-heart"></i>
            </div>
            <div class="ud-stat-info">
                <div class="value"><?= $totalFavoris ?></div>
                <div class="label">Biens favoris</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="ud-stat-card">
            <div class="ud-stat-icon blue">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="ud-stat-info">
                <div class="value"><?= $totalVisites ?></div>
                <div class="label">Visites demandées</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="ud-stat-card">
            <div class="ud-stat-icon green">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="ud-stat-info">
                <div class="value"><?= $visitesConfirmees ?></div>
                <div class="label">Visites confirmées</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="ud-stat-card">
            <div class="ud-stat-icon purple">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="ud-stat-info">
                <div class="value"><?= $visitesEnAttente ?></div>
                <div class="label">En attente</div>
            </div>
        </div>
    </div>
</div>

<!-- ── Grille principale ─────────────────────────────── -->
<div class="row g-4">

    <!-- Dernières visites -->
    <div class="col-lg-7">
        <div class="ud-card h-100">
            <div class="ud-card-header">
                <h5><i class="fa-solid fa-calendar-check text-primary me-2"></i>Mes dernières visites</h5>
                <a href="mes-visites.php" class="btn btn-sm btn-outline-dt">Tout voir</a>
            </div>
            <div class="ud-card-body">
                <?php if (empty($recentVisites)): ?>
                    <div class="ud-empty">
                        <i class="fa-solid fa-calendar-xmark"></i>
                        <p>Aucune visite demandée pour l'instant.</p>
                        <a href="<?= $appBase ?>/properties.php" class="btn btn-primary-dt btn-sm">
                            <i class="fa-solid fa-search me-1"></i> Parcourir les biens
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentVisites as $v): ?>
                        <div class="ud-visit-item">
                            <img
                                src="<?= h(property_image($v['image_url'])) ?>"
                                alt="<?= h($v['property_title']) ?>"
                                class="ud-visit-thumb">
                            <div class="ud-visit-info">
                                <div class="title"><?= h($v['property_title']) ?></div>
                                <div class="meta">
                                    <i class="fa-solid fa-calendar-day me-1"></i>
                                    <?= h(date('d/m/Y', strtotime($v['visit_date']))) ?>
                                
                                    &nbsp;·&nbsp;
                                    <i class="fa-solid fa-location-dot me-1"></i>
                                    <?= h($v['city']) ?>
                                </div>
                            </div>
                            <div class="ms-auto ps-2">
                                <?= visit_status_badge($v['status']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Favoris récents -->
    <div class="col-lg-5">
        <div class="ud-card h-100">
            <div class="ud-card-header">
                <h5><i class="fa-solid fa-heart text-danger me-2"></i>Mes favoris récents</h5>
                <a href="favoris.php" class="btn btn-sm btn-outline-dt">Tout voir</a>
            </div>
            <div class="ud-card-body">
                <?php if (empty($recentFavoris)): ?>
                    <div class="ud-empty">
                        <i class="fa-solid fa-heart-crack"></i>
                        <p>Aucun bien favori. Ajoutez des biens qui vous intéressent !</p>
                        <a href="<?= $appBase ?>/properties.php" class="btn btn-primary-dt btn-sm">
                            Découvrir les biens
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentFavoris as $fav): ?>
                        <div class="ud-visit-item">
                            <img
                                src="<?= h(property_image($fav['image_url'])) ?>"
                                alt="<?= h($fav['title']) ?>"
                                class="ud-visit-thumb">
                            <div class="ud-visit-info">
                                <div class="title"><?= h($fav['title']) ?></div>
                                <div class="meta">
                                    <?= h($fav['city']) ?>, <?= h($fav['governorate']) ?>
                                </div>
                            </div>
                            <div class="ms-auto ps-2 text-end">
                                <strong class="d-block" style="color:var(--primary);font-size:.85rem">
                                    <?= format_tnd((float)$fav['rent_price']) ?>
                                </strong>
                                <a href="<?= $appBase ?>/property-details.php?id=<?= $fav['id'] ?>"
                                   class="btn btn-xs btn-outline-secondary mt-1" style="font-size:.72rem;padding:2px 8px">
                                    Voir
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div><!-- /row -->

<!-- CTA Explorer -->
<div class="ud-card mt-4" style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);border:none">
    <div class="ud-card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="text-white">
            <h6 class="fw-bold mb-1 fs-5">Trouvez votre prochain bien</h6>
            <p class="mb-0 opacity-75 small">Parcourez notre catalogue de biens disponibles à la location en Tunisie.</p>
        </div>
        <a href="<?= $appBase ?>/properties.php" class="btn btn-light fw-bold">
            <i class="fa-solid fa-magnifying-glass me-2"></i>Voir tous les biens
        </a>
    </div>
</div>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
