<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Modifier un bien';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    flash('danger', 'Identifiant manquant.');
    redirect('admin/properties.php');
}

$categories = db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();

// Charger le bien
$stmt = db()->prepare(
    'SELECT p.*, o.full_name AS owner_name
     FROM properties p
     LEFT JOIN owners o ON o.id = p.owner_id
     WHERE p.id = :id LIMIT 1'
);
$stmt->execute(['id' => $id]);
$property = $stmt->fetch();

if (!$property) {
    flash('danger', 'Bien introuvable.');
    redirect('admin/properties.php');
}

$ownerName = $property['owner_name'] ?? '';

// Équipements existants
$stmt2 = db()->prepare('SELECT feature FROM property_features WHERE property_id = :pid');
$stmt2->execute(['pid' => $id]);
$features = array_column($stmt2->fetchAll(), 'feature');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    // Propriétaire : cherche ou crée
    $ownerInput = trim($_POST['owner_name'] ?? '');
    $owner_id   = null;

    if ($ownerInput !== '') {
        $stmt = db()->prepare('SELECT id FROM owners WHERE full_name = :n LIMIT 1');
        $stmt->execute(['n' => $ownerInput]);
        $existing = $stmt->fetch();

        if ($existing) {
            $owner_id = $existing['id'];
        } else {
            db()->prepare('INSERT INTO owners (full_name, phone) VALUES (:name, :phone)')
               ->execute(['name' => $ownerInput, 'phone' => '']);
            $owner_id = db()->lastInsertId();
        }
    }

    $data = [
        'id'                  => $id,
        'owner_id'            => $owner_id,
        'category_id'         => (int)   ($_POST['category_id']         ?? 0),
        'title'               => trim($_POST['title']                    ?? ''),
        'description'         => trim($_POST['description']              ?? ''),
        'governorate'         => trim($_POST['governorate']              ?? ''),
        'city'                => trim($_POST['city']                     ?? ''),
        'address'             => trim($_POST['address']                  ?? ''),
        'rent_price'          => (float) ($_POST['rent_price']           ?? 0),
        'area'                => (float) ($_POST['area']                 ?? 0),
        'rooms'               => (int)   ($_POST['rooms']                ?? 1),
        'bedrooms'            => (int)   ($_POST['bedrooms']             ?? 1),
        'bathrooms'           => (int)   ($_POST['bathrooms']            ?? 1),
        'floor'               => trim($_POST['floor']                    ?? ''),
        'parking'             => (int)   ($_POST['parking']              ?? 0),
        'furnished'           => isset($_POST['furnished'])     ? 1 : 0,
        'availability_status' => $_POST['availability_status']           ?? 'available',
        'contract_ready'      => isset($_POST['contract_ready']) ? 1 : 0,
        'payment_method'      => trim($_POST['payment_method']           ?? ''),
        'image_url'           => trim($_POST['image_url']                ?? ''),
        'published'           => isset($_POST['published'])     ? 1 : 0,
    ];

    db()->prepare(
        'UPDATE properties SET
            owner_id=:owner_id, category_id=:category_id, title=:title,
            description=:description, governorate=:governorate, city=:city,
            address=:address, rent_price=:rent_price, area=:area,
            rooms=:rooms, bedrooms=:bedrooms, bathrooms=:bathrooms,
            floor=:floor, parking=:parking, furnished=:furnished,
            availability_status=:availability_status, contract_ready=:contract_ready,
            payment_method=:payment_method, image_url=:image_url, published=:published
         WHERE id=:id'
    )->execute($data);

    // Équipements
    db()->prepare('DELETE FROM property_features WHERE property_id = :pid')->execute(['pid' => $id]);
    $rawFeatures = array_filter(array_map('trim', explode(',', $_POST['features'] ?? '')));
    $stmtF = db()->prepare('INSERT INTO property_features (property_id, feature) VALUES (:pid, :f)');
    foreach ($rawFeatures as $f) {
        $stmtF->execute(['pid' => $id, 'f' => $f]);
    }

    flash('success', 'Bien modifié avec succès.');
    redirect('admin/properties.php');
}

$existingOwners = db()->query('SELECT full_name FROM owners ORDER BY full_name')->fetchAll();

// $v : priorité POST → DB
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

            <form method="post" novalidate>
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