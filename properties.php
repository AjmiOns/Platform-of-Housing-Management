<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Biens a louer';

$where = ['p.published = 1'];
$params = [];

if (!empty($_GET['category'])) {
    $where[] = 'c.slug = :category';
    $params['category'] = $_GET['category'];
}
if (!empty($_GET['governorate'])) {
    $where[] = 'p.governorate = :governorate';
    $params['governorate'] = $_GET['governorate'];
}
if (!empty($_GET['city'])) {
    $where[] = 'p.city LIKE :city';
    $params['city'] = '%' . $_GET['city'] . '%';
}
if (!empty($_GET['max_price'])) {
    $where[] = 'p.rent_price <= :max_price';
    $params['max_price'] = (float) $_GET['max_price'];
}
if (!empty($_GET['q'])) {
    $where[] = '(p.title LIKE :q1 OR p.description LIKE :q2 OR p.address LIKE :q3)';
    $qVal = '%' . $_GET['q'] . '%';
    $params['q1'] = $qVal;
    $params['q2'] = $qVal;
    $params['q3'] = $qVal;
}

$sql = 'SELECT p.*, c.name AS category_name
        FROM properties p
        JOIN categories c ON c.id = p.category_id
        WHERE ' . implode(' AND ', $where) . '
        ORDER BY p.availability_status ASC, p.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1 class="fw-bold">Biens a louer</h1>
        <p class="lead text-muted mb-0">Recherchez un appartement, une maison, une villa ou un studio en Tunisie.</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <?php show_flash(); ?>

        <!-- ══════════════════════════════════════════════
             FILTRE CLASSIQUE (PHP — rechargement de page)
        ══════════════════════════════════════════════ -->
        <form class="admin-card mb-4" method="get">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Mot-cle</label>
                    <input type="text" name="q" value="<?= h($_GET['q'] ?? '') ?>" class="form-control" placeholder="La Marsa, S+2...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="category" class="form-select">
                        <option value="">Tous</option>
                        <?php foreach (categories() as $category): ?>
                            <option value="<?= h($category['slug']) ?>" <?= ($_GET['category'] ?? '') === $category['slug'] ? 'selected' : '' ?>><?= h($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Gouvernorat</label>
                    <select name="governorate" class="form-select">
                        <option value="">Tous</option>
                        <?php foreach (governorates() as $gov): ?>
                            <option value="<?= h($gov) ?>" <?= ($_GET['governorate'] ?? '') === $gov ? 'selected' : '' ?>><?= h($gov) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ville</label>
                    <input type="text" name="city" value="<?= h($_GET['city'] ?? '') ?>" class="form-control" placeholder="Tunis">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Budget max</label>
                    <input type="number" name="max_price" value="<?= h($_GET['max_price'] ?? '') ?>" class="form-control" placeholder="TND">
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-primary">OK</button>
                </div>
            </div>
        </form>

        <!-- ══════════════════════════════════════════════
             RECHERCHE INSTANTANÉE via fetch() → API REST
             Consomme : api/properties.php
        ══════════════════════════════════════════════ -->
        <div class="admin-card mb-5" id="live-search-box">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span style="font-size:1.1rem">⚡</span>
                <h6 class="mb-0 fw-semibold">Recherche instantanée</h6>
                <span class="badge bg-primary ms-1" style="font-size:.7rem">API</span>
            </div>
            <div class="row g-2 align-items-center">
                <div class="col-md-8">
                    <input
                        id="live-q"
                        type="text"
                        class="form-control"
                        placeholder="Tapez un mot-clé, ville, adresse… les résultats s'affichent en temps réel"
                    >
                </div>
                <div class="col-md-4">
                    <span id="live-count" class="text-muted small"></span>
                </div>
            </div>
            <div id="live-results" class="row g-3 mt-2"></div>
        </div>

        <!-- ══════════════════════════════════════════════
             RÉSULTATS PHP (filtre classique)
        ══════════════════════════════════════════════ -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0"><?= count($properties) ?> resultat(s)</h2>
            <a href="<?= url('properties.php') ?>" class="btn btn-sm btn-outline-secondary">Reinitialiser</a>
        </div>

        <?php if (!$properties): ?>
            <div class="alert alert-info">Aucun bien ne correspond a votre recherche.</div>
        <?php endif; ?>

        <div class="row g-4">
            <?php foreach ($properties as $property): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card property-card">
                        <img src="<?= h(property_image($property['image_url'])) ?>" class="property-image" alt="<?= h($property['title']) ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge badge-category"><?= h($property['category_name']) ?></span>
                                <span class="badge text-bg-<?= status_class($property['availability_status']) ?>"><?= status_label($property['availability_status']) ?></span>
                            </div>
                            <h5><a href="<?= url('property-details.php?id=' . $property['id']) ?>" class="text-dark text-decoration-none"><?= h($property['title']) ?></a></h5>
                            <p class="text-muted"><i class="fa-solid fa-location-dot"></i> <?= h($property['address']) ?></p>
                            <div class="price mb-3"><?= format_tnd((float) $property['rent_price']) ?> / mois</div>
                            <ul class="feature-list">
                                <li><?= (int) $property['bedrooms'] ?> chambres</li>
                                <li><?= (int) $property['bathrooms'] ?> bain</li>
                                <li><?= h($property['area']) ?> m²</li>
                                <li><?= (int) $property['parking'] ?> parking</li>
                            </ul>
                            <a href="<?= url('property-details.php?id=' . $property['id']) ?>" class="btn btn-primary w-100 mt-3">Voir details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════
     SCRIPT fetch() — consomme api/properties.php
     Placé avant footer pour que le DOM soit prêt
══════════════════════════════════════════════ -->
<script>
(function () {
    'use strict';

    const input    = document.getElementById('live-q');
    const results  = document.getElementById('live-results');
    const countEl  = document.getElementById('live-count');
    let   timer    = null;

    // URL de l'API construite depuis la base du site
    const API_URL = '<?= url('api/properties.php') ?>';

    /**
     * Appel fetch() vers api/properties.php
     */
    async function searchAPI(q) {
        const url = new URL(API_URL);
        url.searchParams.set('q', q);

        try {
            const response = await fetch(url.href);

            if (!response.ok) {
                throw new Error('Erreur réseau : ' + response.status);
            }

            const json = await response.json();
            renderResults(json.data, json.count);

        } catch (error) {
            results.innerHTML = '<div class="col-12"><div class="alert alert-danger">Impossible de contacter l\'API.</div></div>';
            countEl.textContent = '';
        }
    }

    /**
     * Affiche les cartes retournées par l'API
     */
    function renderResults(properties, count) {
        countEl.textContent = count + ' résultat(s) via API';

        if (!properties || properties.length === 0) {
            results.innerHTML = '<div class="col-12"><p class="text-muted">Aucun bien trouvé.</p></div>';
            return;
        }

        results.innerHTML = properties.map(function (p) {
            var statusClass = { available: 'success', reserved: 'warning', rented: 'secondary' }[p.availability_status] || 'secondary';
            var statusLabel = { available: 'Disponible', reserved: 'Réservé', rented: 'Loué' }[p.availability_status] || p.availability_status;
            var price       = parseInt(p.rent_price).toLocaleString('fr-TN') + ' TND';
            var image       = p.image_url && p.image_url.startsWith('http') ? p.image_url : '<?= url('public/assets/images/property-placeholder.svg') ?>';
            var detailUrl   = '<?= url('property-details.php') ?>?id=' + p.id;

            return '<div class="col-md-6 col-lg-4">'
                + '<div class="card property-card">'
                + '<img src="' + image + '" class="property-image" alt="' + escHtml(p.title) + '">'
                + '<div class="card-body">'
                + '<div class="d-flex justify-content-between align-items-center mb-2">'
                + '<span class="badge badge-category">' + escHtml(p.category_name || '') + '</span>'
                + '<span class="badge text-bg-' + statusClass + '">' + statusLabel + '</span>'
                + '</div>'
                + '<h5><a href="' + detailUrl + '" class="text-dark text-decoration-none">' + escHtml(p.title) + '</a></h5>'
                + '<p class="text-muted"><i class="fa-solid fa-location-dot"></i> ' + escHtml(p.address || '') + '</p>'
                + '<div class="price mb-3">' + price + ' / mois</div>'
                + '<a href="' + detailUrl + '" class="btn btn-primary w-100 mt-2">Voir détails</a>'
                + '</div></div></div>';
        }).join('');
    }

    /**
     * Échappe les caractères HTML pour éviter XSS
     */
    function escHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    /**
     * Écoute la frappe avec debounce 400ms
     */
    input.addEventListener('input', function () {
        clearTimeout(timer);
        results.innerHTML = '';
        countEl.textContent = '';

        var q = input.value.trim();

        if (q.length < 2) return;

        countEl.textContent = 'Recherche…';

        timer = setTimeout(function () {
            searchAPI(q);
        }, 400);
    });

})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>