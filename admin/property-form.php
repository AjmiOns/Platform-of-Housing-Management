<?php
require_once __DIR__ . '/../includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$isEdit = $id > 0;
$property = $isEdit ? get_property($id) : null;
if ($isEdit && !$property) {
    flash('warning', 'Bien introuvable.');
    redirect('admin/properties.php');
}

$pageTitle = $isEdit ? 'Modifier un bien' : 'Ajouter un bien';
$errors = [];
$featureText = $isEdit ? implode(', ', get_property_features($id)) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $data = [
        'owner_id' => $_POST['owner_id'] !== '' ? (int) $_POST['owner_id'] : null,
        'category_id' => (int) ($_POST['category_id'] ?? 0),
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'governorate' => trim($_POST['governorate'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'rent_price' => (float) ($_POST['rent_price'] ?? 0),
        'area' => (float) ($_POST['area'] ?? 0),
        'rooms' => (int) ($_POST['rooms'] ?? 1),
        'bedrooms' => (int) ($_POST['bedrooms'] ?? 1),
        'bathrooms' => (int) ($_POST['bathrooms'] ?? 1),
        'floor' => trim($_POST['floor'] ?? ''),
        'parking' => (int) ($_POST['parking'] ?? 0),
        'furnished' => isset($_POST['furnished']) ? 1 : 0,
        'availability_status' => $_POST['availability_status'] ?? 'available',
        'contract_ready' => isset($_POST['contract_ready']) ? 1 : 0,
        'payment_method' => trim($_POST['payment_method'] ?? ''),
        'published' => isset($_POST['published']) ? 1 : 0,
    ];

    if ($data['title'] === '') $errors[] = 'Le titre est obligatoire.';
    if ($data['category_id'] <= 0) $errors[] = 'La categorie est obligatoire.';
    if ($data['description'] === '') $errors[] = 'La description est obligatoire.';
    if ($data['governorate'] === '' || $data['city'] === '' || $data['address'] === '') $errors[] = 'La localisation est obligatoire.';
    if ($data['rent_price'] <= 0) $errors[] = 'Le loyer doit etre superieur a 0.';

    try {
        $uploadedImage = handle_upload('image');
    } catch (RuntimeException $e) {
        $errors[] = $e->getMessage();
        $uploadedImage = null;
    }

    $manualImage = trim($_POST['image_url'] ?? '');
    $data['image_url'] = $uploadedImage ?: ($manualImage ?: ($property['image_url'] ?? null));

    if (!$errors) {
        if ($isEdit) {
            $data['id'] = $id;
            $stmt = db()->prepare(
                'UPDATE properties SET
                    owner_id = :owner_id,
                    category_id = :category_id,
                    title = :title,
                    description = :description,
                    governorate = :governorate,
                    city = :city,
                    address = :address,
                    rent_price = :rent_price,
                    area = :area,
                    rooms = :rooms,
                    bedrooms = :bedrooms,
                    bathrooms = :bathrooms,
                    floor = :floor,
                    parking = :parking,
                    furnished = :furnished,
                    availability_status = :availability_status,
                    contract_ready = :contract_ready,
                    payment_method = :payment_method,
                    image_url = :image_url,
                    published = :published
                 WHERE id = :id'
            );
            $stmt->execute($data);
        } else {
            $stmt = db()->prepare(
                'INSERT INTO properties
                    (owner_id, category_id, title, description, governorate, city, address, rent_price, area, rooms, bedrooms, bathrooms, floor, parking, furnished, availability_status, contract_ready, payment_method, image_url, published)
                 VALUES
                    (:owner_id, :category_id, :title, :description, :governorate, :city, :address, :rent_price, :area, :rooms, :bedrooms, :bathrooms, :floor, :parking, :furnished, :availability_status, :contract_ready, :payment_method, :image_url, :published)'
            );
            $stmt->execute($data);
            $id = (int) db()->lastInsertId();
        }

        db()->prepare('DELETE FROM property_features WHERE property_id = :id')->execute(['id' => $id]);
        $features = array_filter(array_map('trim', explode(',', $_POST['features'] ?? '')));
        $featureStmt = db()->prepare('INSERT INTO property_features (property_id, feature) VALUES (:property_id, :feature)');
        foreach ($features as $feature) {
            $featureStmt->execute(['property_id' => $id, 'feature' => $feature]);
        }

        flash('success', $isEdit ? 'Bien modifie avec succes.' : 'Bien ajoute avec succes.');
        redirect('admin/properties.php');
    }

    $property = array_merge($property ?: [], $data);
    $featureText = $_POST['features'] ?? '';
}

require __DIR__ . '/../includes/header.php';
?>

<section class="section-padding admin-layout">
    <div class="container">
        <?php require __DIR__ . '/_menu.php'; ?>
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h4 mb-0"><?= $isEdit ? 'Modifier le bien' : 'Ajouter un bien' ?></h1>
                <a href="<?= url('admin/properties.php') ?>" class="btn btn-outline-secondary">Retour</a>
            </div>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Titre *</label>
                        <input type="text" name="title" value="<?= h($property['title'] ?? '') ?>" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Categorie *</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Choisir</option>
                            <?php foreach (categories() as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= (int)($property['category_id'] ?? 0) === (int)$category['id'] ? 'selected' : '' ?>><?= h($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Proprietaire</label>
                        <select name="owner_id" class="form-select">
                            <option value="">Aucun</option>
                            <?php foreach (owners() as $owner): ?>
                                <option value="<?= $owner['id'] ?>" <?= (int)($property['owner_id'] ?? 0) === (int)$owner['id'] ? 'selected' : '' ?>><?= h($owner['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gouvernorat *</label>
                        <select name="governorate" class="form-select" required>
                            <option value="">Choisir</option>
                            <?php foreach (governorates() as $gov): ?>
                                <option value="<?= h($gov) ?>" <?= ($property['governorate'] ?? '') === $gov ? 'selected' : '' ?>><?= h($gov) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ville *</label>
                        <input type="text" name="city" value="<?= h($property['city'] ?? '') ?>" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Adresse *</label>
                        <input type="text" name="address" value="<?= h($property['address'] ?? '') ?>" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description *</label>
                        <textarea name="description" rows="5" class="form-control" required><?= h($property['description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Loyer mensuel TND *</label>
                        <input type="number" step="0.01" name="rent_price" value="<?= h((string)($property['rent_price'] ?? '')) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Surface m²</label>
                        <input type="number" step="0.01" name="area" value="<?= h((string)($property['area'] ?? '')) ?>" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Pieces</label>
                        <input type="number" name="rooms" value="<?= h((string)($property['rooms'] ?? 1)) ?>" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Chambres</label>
                        <input type="number" name="bedrooms" value="<?= h((string)($property['bedrooms'] ?? 1)) ?>" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Salles bain</label>
                        <input type="number" name="bathrooms" value="<?= h((string)($property['bathrooms'] ?? 1)) ?>" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Etage</label>
                        <input type="text" name="floor" value="<?= h($property['floor'] ?? '') ?>" class="form-control" placeholder="RDC, 2, RDC + 1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Parking</label>
                        <input type="number" name="parking" value="<?= h((string)($property['parking'] ?? 0)) ?>" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Statut</label>
                        <select name="availability_status" class="form-select">
                            <?php foreach (['available' => 'Disponible', 'reserved' => 'Reserve', 'rented' => 'Loue'] as $key => $label): ?>
                                <option value="<?= $key ?>" <?= ($property['availability_status'] ?? 'available') === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Paiement</label>
                        <input type="text" name="payment_method" value="<?= h($property['payment_method'] ?? 'Virement bancaire ou espece') ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Image locale</label>
                        <input type="file" name="image" class="form-control" accept="image/png,image/jpeg,image/webp">
                        <small class="text-muted">Optionnel. Max 3 MB.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ou URL image</label>
                        <input type="url" name="image_url" value="<?= h($property['image_url'] ?? '') ?>" class="form-control" placeholder="https://...">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Caracteristiques</label>
                        <input type="text" name="features" value="<?= h($featureText) ?>" class="form-control" placeholder="Climatisation, Balcon, Ascenseur">
                        <small class="text-muted">Separez les caracteristiques par des virgules.</small>
                    </div>
                    <div class="col-md-4 form-check ms-2">
                        <input class="form-check-input" type="checkbox" name="furnished" id="furnished" <?= !empty($property['furnished']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="furnished">Meuble</label>
                    </div>
                    <div class="col-md-4 form-check ms-2">
                        <input class="form-check-input" type="checkbox" name="contract_ready" id="contract_ready" <?= !isset($property['contract_ready']) || $property['contract_ready'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="contract_ready">Contrat pret</label>
                    </div>
                    <div class="col-md-4 form-check ms-2">
                        <input class="form-check-input" type="checkbox" name="published" id="published" <?= !isset($property['published']) || $property['published'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="published">Publie</label>
                    </div>
                    <div class="col-12 mt-4">
                        <button class="btn btn-primary">Enregistrer</button>
                        <a class="btn btn-outline-secondary" href="<?= url('admin/properties.php') ?>">Annuler</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
