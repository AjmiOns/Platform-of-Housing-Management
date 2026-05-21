<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Ajouter un bien';

$categories   = db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$property     = null;   // aucun bien existant en mode ajout
$ownerName    = '';     // champ propriétaire vide
$features     = [];     // aucun équipement par défaut

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

    // Gestion de l'upload image
    $image_url = null;
    try {
        $uploaded = handle_upload('image_file');
        if ($uploaded) {
            $image_url = $uploaded;
        }
    } catch (RuntimeException $e) {
        flash('danger', $e->getMessage());
        redirect('admin/property-add.php');
    }

    $data = [
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
        'image_url'           => $image_url,
        'published'           => isset($_POST['published'])     ? 1 : 0,
    ];

    db()->prepare(
        'INSERT INTO properties
            (owner_id, category_id, title, description, governorate, city, address,
             rent_price, area, rooms, bedrooms, bathrooms, floor, parking, furnished,
             availability_status, contract_ready, payment_method, image_url, published)
         VALUES
            (:owner_id, :category_id, :title, :description, :governorate, :city, :address,
             :rent_price, :area, :rooms, :bedrooms, :bathrooms, :floor, :parking, :furnished,
             :availability_status, :contract_ready, :payment_method, :image_url, :published)'
    )->execute($data);

    $propertyId = db()->lastInsertId();

    // Équipements
    $rawFeatures = array_filter(array_map('trim', explode(',', $_POST['features'] ?? '')));
    $stmtF = db()->prepare('INSERT INTO property_features (property_id, feature) VALUES (:pid, :f)');
    foreach ($rawFeatures as $f) {
        $stmtF->execute(['pid' => $propertyId, 'f' => $f]);
    }

    flash('success', 'Bien ajouté avec succès.');
    redirect('admin/properties.php');
}

$existingOwners = db()->query('SELECT full_name FROM owners ORDER BY full_name')->fetchAll();

$v = fn(string $key, $default = '') => htmlspecialchars($_POST[$key] ?? $default, ENT_QUOTES);

require __DIR__ . '/../includes/header.php';
?>

<section class="section-padding admin-layout">
    <div class="container">
        <?php require __DIR__ . '/_menu.php'; ?>
        <?php show_flash(); ?>

        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h4 mb-0">➕ Ajouter un bien</h1>
                <a href="<?= url('admin/properties.php') ?>" class="btn btn-outline-secondary btn-sm">← Retour</a>
            </div>

            <form method="post" enctype="multipart/form-data" novalidate>
                <?= csrf_field() ?>

                <?php require __DIR__ . '/_property-fields.php'; ?>

                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary">➕ Ajouter le bien</button>
                    <a href="<?= url('admin/properties.php') ?>" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
