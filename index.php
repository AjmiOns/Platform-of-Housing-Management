<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Accueil';
$settings = app_settings();

$latestStmt = db()->query(
    'SELECT p.*, c.name AS category_name
     FROM properties p
     JOIN categories c ON c.id = p.category_id
     WHERE p.published = 1
     ORDER BY p.created_at DESC
     LIMIT 6'
);
$latestProperties = $latestStmt->fetchAll();

$stats = [
    'properties' => (int) db()->query('SELECT COUNT(*) FROM properties WHERE published = 1')->fetchColumn(),
    'available' => (int) db()->query("SELECT COUNT(*) FROM properties WHERE published = 1 AND availability_status = 'available'")->fetchColumn(),
    'governorates' => (int) db()->query('SELECT COUNT(DISTINCT governorate) FROM properties WHERE published = 1')->fetchColumn(),
];
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <p class="text-uppercase fw-bold text-warning mb-2">Agence immobiliere en Tunisie</p>
                <h1>Trouvez votre maison ou appartement a louer en TND.</h1>
                <p class="lead mt-3 mb-4"><?= h($settings['slogan']) ?>. Biens verifies, visites organisees et suivi administratif clair.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?= url('properties.php') ?>" class="btn btn-primary btn-lg">Voir les biens</a>
                    <a href="https://wa.me/<?= preg_replace('/\D+/', '', $settings['whatsapp']) ?>" class="btn btn-light btn-lg" target="_blank">WhatsApp</a>
                </div>
            </div>
            <div class="col-lg-5">
                <form class="hero-card" action="<?= url('properties.php') ?>" method="get">
                    <h4 class="mb-3">Recherche rapide</h4>
                    <div class="mb-3">
                        <label class="form-label">Type de bien</label>
                        <select name="category" class="form-select">
                            <option value="">Tous les types</option>
                            <?php foreach (categories() as $category): ?>
                                <option value="<?= h($category['slug']) ?>"><?= h($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Gouvernorat</label>
                            <select name="governorate" class="form-select">
                                <option value="">Tous</option>
                                <?php foreach (governorates() as $gov): ?>
                                    <option value="<?= h($gov) ?>"><?= h($gov) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Budget max</label>
                            <input type="number" name="max_price" class="form-control" placeholder="ex: 1500">
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 mt-4">Rechercher</button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4"><div class="stats-card"><strong><?= $stats['properties'] ?></strong><span>Biens publies</span></div></div>
            <div class="col-md-4"><div class="stats-card"><strong><?= $stats['available'] ?></strong><span>Disponibles</span></div></div>
            <div class="col-md-4"><div class="stats-card"><strong><?= $stats['governorates'] ?></strong><span>Gouvernorats couverts</span></div></div>
        </div>
    </div>
</section>

<section class="section-padding bg-light">
    <div class="container">
        <div class="row align-items-end mb-4">
            <div class="col-lg-8 section-title">
                <small>Nos derniers biens</small>
                <h2>Locations recentes en Tunisie</h2>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= url('properties.php') ?>" class="btn btn-outline-primary">Tous les biens</a>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($latestProperties as $property): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card property-card">
                        <img src="<?= h(property_image($property['image_url'])) ?>" class="property-image" alt="<?= h($property['title']) ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge badge-category"><?= h($property['category_name']) ?></span>
                                <span class="badge text-bg-<?= status_class($property['availability_status']) ?>"><?= status_label($property['availability_status']) ?></span>
                            </div>
                            <h5><a class="text-decoration-none text-dark" href="<?= url('property-details.php?id=' . $property['id']) ?>"><?= h($property['title']) ?></a></h5>
                            <p class="text-muted mb-2"><i class="fa-solid fa-location-dot"></i> <?= h($property['city']) ?>, <?= h($property['governorate']) ?></p>
                            <div class="price mb-3"><?= format_tnd((float) $property['rent_price']) ?> / mois</div>
                            <ul class="feature-list">
                                <li><?= (int) $property['bedrooms'] ?> chambres</li>
                                <li><?= (int) $property['bathrooms'] ?> bain</li>
                                <li><?= h($property['area']) ?> m²</li>
                                <li><?= (int) $property['parking'] ?> parking</li>
                            </ul>
                            <a class="btn btn-primary w-100 mt-3" href="<?= url('property-details.php?id=' . $property['id']) ?>">Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6 section-title">
                <small>Pourquoi nous choisir</small>
                <h2>Un service adapte au marche tunisien</h2>
                <p class="text-muted">Prix en dinar tunisien, quartiers locaux, contrats de location, suivi des visites et communication rapide avec les clients.</p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-md-6"><div class="info-box"><h5>Biens verifies</h5><p>Les informations essentielles sont structurees: surface, loyer, etat, parking et paiement.</p></div></div>
                    <div class="col-md-6"><div class="info-box"><h5>Visites organisees</h5><p>Le client demande une date et l'agence confirme depuis l'espace admin.</p></div></div>
                    <div class="col-md-6"><div class="info-box"><h5>Contact direct</h5><p>Telephone, email et WhatsApp sont personnalisables.</p></div></div>
                    <div class="col-md-6"><div class="info-box"><h5>Back-office</h5><p>Ajout, modification et suppression des biens depuis une interface admin.</p></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
