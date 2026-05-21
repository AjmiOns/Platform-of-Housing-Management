<?php
/**
 * Controller: Edit Property
 * Handles all business logic for updating an existing property.
 * Included by property-edit.php before any HTML output.
 */

require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Modifier un bien';

// ── 1. Validate ID ────────────────────────────────────────────────────────────
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    flash('danger', 'Identifiant manquant.');
    redirect('admin/properties.php');
}

// ── 2. Load supporting data ───────────────────────────────────────────────────
$categories     = db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$existingOwners = db()->query('SELECT full_name FROM owners ORDER BY full_name')->fetchAll();

// ── 3. Load property ──────────────────────────────────────────────────────────
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

// ── 4. Load existing features ─────────────────────────────────────────────────
$stmt2 = db()->prepare('SELECT feature FROM property_features WHERE property_id = :pid');
$stmt2->execute(['pid' => $id]);
$features = array_column($stmt2->fetchAll(), 'feature');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return; // nothing to process on GET
}

verify_csrf();

// ── 5. Resolve or create owner ────────────────────────────────────────────────
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

// ── 6. Handle image upload — keep old image if no new file uploaded ────────────
$image_url = trim($_POST['image_url'] ?? $property['image_url'] ?? '');
try {
    $uploaded = handle_upload('image_file');
    if ($uploaded) {
        $image_url = $uploaded;
    }
} catch (RuntimeException $e) {
    flash('danger', $e->getMessage());
    redirect('admin/property-edit.php?id=' . $id);
}

// ── 7. Build data payload ─────────────────────────────────────────────────────
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
    'furnished'           => isset($_POST['furnished'])      ? 1 : 0,
    'availability_status' => $_POST['availability_status']           ?? 'available',
    'contract_ready'      => isset($_POST['contract_ready']) ? 1 : 0,
    'payment_method'      => trim($_POST['payment_method']           ?? ''),
    'image_url'           => $image_url,
    'published'           => isset($_POST['published'])      ? 1 : 0,
];

// ── 8. Update property ────────────────────────────────────────────────────────
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

// ── 9. Replace features ───────────────────────────────────────────────────────
db()->prepare('DELETE FROM property_features WHERE property_id = :pid')->execute(['pid' => $id]);
$rawFeatures = array_filter(array_map('trim', explode(',', $_POST['features'] ?? '')));
$stmtF = db()->prepare('INSERT INTO property_features (property_id, feature) VALUES (:pid, :f)');
foreach ($rawFeatures as $f) {
    $stmtF->execute(['pid' => $id, 'f' => $f]);
}

flash('success', 'Bien modifié avec succès.');
redirect('admin/properties.php');