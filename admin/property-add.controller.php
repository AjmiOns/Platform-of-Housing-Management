<?php
/**
 * Controller: Add Property
 * Handles all business logic for creating a new property.
 * Included by property-add.php before any HTML output.
 */

require_once __DIR__ . '/../includes/auth.php';

$pageTitle      = 'Ajouter un bien';
$categories     = db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$existingOwners = db()->query('SELECT full_name FROM owners ORDER BY full_name')->fetchAll();
$property       = null;   // no existing property in add mode
$ownerName      = '';
$features       = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return; // nothing to process on GET
}

verify_csrf();

// ── 1. Resolve or create owner ────────────────────────────────────────────────
$owner_id   = null;
$ownerInput = trim($_POST['owner_name'] ?? '');

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

// ── 2. Handle image upload ────────────────────────────────────────────────────
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

// ── 3. Build data payload ─────────────────────────────────────────────────────
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
    'furnished'           => isset($_POST['furnished'])      ? 1 : 0,
    'availability_status' => $_POST['availability_status']           ?? 'available',
    'contract_ready'      => isset($_POST['contract_ready']) ? 1 : 0,
    'payment_method'      => trim($_POST['payment_method']           ?? ''),
    'image_url'           => $image_url,
    'published'           => isset($_POST['published'])      ? 1 : 0,
];

// ── 4. Insert property ────────────────────────────────────────────────────────
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

// ── 5. Insert features ────────────────────────────────────────────────────────
$rawFeatures = array_filter(array_map('trim', explode(',', $_POST['features'] ?? '')));
$stmtF = db()->prepare('INSERT INTO property_features (property_id, feature) VALUES (:pid, :f)');
foreach ($rawFeatures as $f) {
    $stmtF->execute(['pid' => $propertyId, 'f' => $f]);
}

flash('success', 'Bien ajouté avec succès.');
redirect('admin/properties.php');