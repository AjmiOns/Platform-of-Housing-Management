<?php
/**
 * user/mes-visites.php — Historique des demandes de visites
 */
$pageTitle  = 'Mes visites';
$activePage = 'visites';
require __DIR__ . '/_layout_top.php';

$clientId = $client['id'];
$visites  = get_client_visits($clientId);

// Séparer visites à venir / passées
$today    = date('Y-m-d');
$avenir   = array_filter($visites, fn($v) => $v['visit_date'] >= $today && in_array($v['status'], ['new','confirmed']));
$historique = array_filter($visites, fn($v) => $v['visit_date'] < $today || in_array($v['status'], ['done','cancelled']));
?>

<!-- ── En-tête ─────────────────────────────────────────── -->
<div class="ud-page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h1>Mes visites</h1>
        <p><?= count($visites) ?> demande<?= count($visites) > 1 ? 's' : '' ?> au total</p>
    </div>
    <a href="<?= $appBase ?>/properties.php" class="btn btn-primary-dt">
        <i class="fa-solid fa-calendar-plus me-2"></i>Planifier une visite
    </a>
</div>

<?php if (empty($visites)): ?>
    <div class="ud-card">
        <div class="ud-empty">
            <i class="fa-solid fa-calendar-xmark"></i>
            <p>Vous n'avez encore demandé aucune visite.<br>
               Consultez les biens disponibles et planifiez une visite.</p>
            <a href="<?= $appBase ?>/properties.php" class="btn btn-primary-dt">
                <i class="fa-solid fa-building me-2"></i>Voir les biens
            </a>
        </div>
    </div>

<?php else: ?>

    <!-- ── Visites à venir ──────────────────────────────── -->
    <?php if (!empty($avenir)): ?>
        <div class="ud-card mb-4">
            <div class="ud-card-header">
                <h5>
                    <i class="fa-solid fa-calendar-check text-success me-2"></i>
                    Visites à venir
                    <span class="badge bg-success ms-2"><?= count($avenir) ?></span>
                </h5>
            </div>
            <div class="ud-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60px"></th>
                                <th>Bien</th>
                                <th>Date & heure</th>
                                <th>Lieu</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($avenir as $v): ?>
                                <tr>
                                    <td>
                                        <img src="<?= h(property_image($v['image_url'])) ?>"
                                             style="width:50px;height:42px;object-fit:cover;border-radius:6px"
                                             alt="">
                                    </td>
                                    <td>
                                        <strong class="d-block"><?= h($v['property_title']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="text-primary fw-semibold">
                                            <?= date('d/m/Y', strtotime($v['visit_date'])) ?>
                                        </span>
                                        <br><small class="text-muted"><?= substr($v['visit_time'], 0, 5) ?></small>
                                    </td>
                                    <td>
                                        <small><?= h($v['city']) ?>, <?= h($v['governorate']) ?></small>
                                    </td>
                                    <td><?= visit_status_badge($v['status']) ?></td>
                                    <td>
                                        <a href="<?= $appBase ?>/property-details.php?id=<?= (int)$v['property_id'] ?>"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ── Historique ─────────────────────────────────────── -->
    <?php if (!empty($historique)): ?>
        <div class="ud-card">
            <div class="ud-card-header">
                <h5>
                    <i class="fa-solid fa-clock-rotate-left text-muted me-2"></i>
                    Historique
                    <span class="badge bg-secondary ms-2"><?= count($historique) ?></span>
                </h5>
            </div>
            <div class="ud-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60px"></th>
                                <th>Bien</th>
                                <th>Date prévue</th>
                                <th>Lieu</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historique as $v): ?>
                                <tr class="text-muted">
                                    <td>
                                        <img src="<?= h(property_image($v['image_url'])) ?>"
                                             style="width:50px;height:42px;object-fit:cover;border-radius:6px;opacity:.7"
                                             alt="">
                                    </td>
                                    <td>
                                        <span class="d-block"><?= h($v['property_title']) ?></span>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($v['visit_date'])) ?>
                                        <br><small><?= substr($v['visit_time'], 0, 5) ?></small>
                                    </td>
                                    <td>
                                        <small><?= h($v['city']) ?></small>
                                    </td>
                                    <td><?= visit_status_badge($v['status']) ?></td>
                                    <td>
                                        <a href="<?= $appBase ?>/property-details.php?id=<?= (int)$v['property_id'] ?>"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
