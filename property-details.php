<?php
require_once __DIR__ . '/includes/functions.php';

$id = (int) ($_GET['id'] ?? 0);
$property = get_property($id);
if (!$property || (int) $property['published'] !== 1) {
    flash('warning', 'Bien introuvable.');
    redirect('properties.php');
}
//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $visitDate = $_POST['visit_date'] ?? '';
    $visitTime = $_POST['visit_time'] ?? '';
    $message = trim($_POST['message'] ?? '');

    if ($fullName === '' || $phone === '' || $visitDate === '' || $visitTime === '') {
        flash('danger', 'Veuillez remplir les champs obligatoires.');
    } else {
        $stmt = db()->prepare(
            'INSERT INTO visit_requests (property_id, full_name, phone, email, visit_date, visit_time, message)
             VALUES (:property_id, :full_name, :phone, :email, :visit_date, :visit_time, :message)'
        );
        $stmt->execute([
            'property_id' => $property['id'],
            'full_name' => $fullName,
            'phone' => $phone,
            'email' => $email ?: null,
            'visit_date' => $visitDate,
            'visit_time' => $visitTime,
            'message' => $message ?: null,
        ]);
        flash('success', 'Votre demande de visite a ete envoyee. Un agent vous contactera.');
        redirect('property-details.php?id=' . $property['id']);
    }
}

$features = get_property_features((int) $property['id']);
$pageTitle = $property['title'];
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div>
                <span class="badge badge-category mb-2"><?= h($property['category_name']) ?></span>
                <h1 class="fw-bold mb-1"><?= h($property['title']) ?></h1>
                <p class="lead text-muted mb-0"><i class="fa-solid fa-location-dot"></i> <?= h($property['address']) ?>, <?= h($property['city']) ?>, <?= h($property['governorate']) ?></p>
            </div>
            <div class="text-lg-end">
                <div class="price fs-2"><?= format_tnd((float) $property['rent_price']) ?></div>
                <span class="text-muted">par mois</span>
            </div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <?php show_flash(); ?>
        <div class="row g-5">
            <div class="col-lg-8">
                <img class="detail-image mb-4" src="<?= h(property_image($property['image_url'])) ?>" alt="<?= h($property['title']) ?>">
                <div class="admin-card mb-4">
                    <h3>Description</h3>
                    <p class="text-muted mb-0"><?= nl2br(h($property['description'])) ?></p>
                </div>
                <div class="admin-card">
                    <h3>Caracteristiques</h3>
                    <div class="row g-3 mt-1">
                        <div class="col-md-3"><div class="info-box p-3"><strong><?= h($property['area']) ?> m²</strong><br><span>Surface</span></div></div>
                        <div class="col-md-3"><div class="info-box p-3"><strong><?= (int) $property['rooms'] ?></strong><br><span>Pieces</span></div></div>
                        <div class="col-md-3"><div class="info-box p-3"><strong><?= (int) $property['bedrooms'] ?></strong><br><span>Chambres</span></div></div>
                        <div class="col-md-3"><div class="info-box p-3"><strong><?= (int) $property['bathrooms'] ?></strong><br><span>Salles bain</span></div></div>
                    </div>
                    <?php if ($features): ?>
                        <ul class="feature-list mt-4">
                            <?php foreach ($features as $feature): ?>
                                <li><i class="fa-solid fa-check text-success"></i> <?= h($feature) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="admin-card mb-4">
                    <h4>Informations rapides</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0"><span>Statut</span><strong><?= status_label($property['availability_status']) ?></strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>Etage</span><strong><?= h($property['floor'] ?: '-') ?></strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>Parking</span><strong><?= (int) $property['parking'] ?></strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>Meuble</span><strong><?= $property['furnished'] ? 'Oui' : 'Non' ?></strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>Contrat</span><strong><?= $property['contract_ready'] ? 'Pret' : 'A verifier' ?></strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>Paiement</span><strong><?= h($property['payment_method']) ?></strong></li>
                    </ul>
                </div>

                <form class="admin-card" method="post">
                    <?= csrf_field() ?>
                    <h4>Demander une visite</h4>
                    <div class="mb-3">
                        <label class="form-label">Nom complet *</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telephone *</label>
                        <input type="tel" name="phone" class="form-control" placeholder="+216 xx xxx xxx" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Date *</label>
                            <input type="date" name="visit_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Heure *</label>
                            <input type="time" name="visit_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="3"></textarea>
                    </div>
                    <button class="btn btn-primary w-100">Envoyer la demande</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
