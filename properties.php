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
    $where[] = '(p.title LIKE :q OR p.description LIKE :q OR p.address LIKE :q)';
    $params['q'] = '%' . $_GET['q'] . '%';
}
//
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
        <form class="admin-card mb-5" method="get">
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

<?php require __DIR__ . '/includes/footer.php'; ?>
