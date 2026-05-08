<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Parametres agence';
$settings = app_settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $data = [
        'agency_name' => trim($_POST['agency_name'] ?? ''),
        'slogan' => trim($_POST['slogan'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'whatsapp' => trim($_POST['whatsapp'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'governorate' => trim($_POST['governorate'] ?? ''),
        'map_embed_url' => trim($_POST['map_embed_url'] ?? ''),
        'facebook' => trim($_POST['facebook'] ?? ''),
        'instagram' => trim($_POST['instagram'] ?? ''),
        'working_hours' => trim($_POST['working_hours'] ?? ''),
    ];

    $stmt = db()->prepare(
        'UPDATE agency_settings SET
            agency_name = :agency_name,
            slogan = :slogan,
            email = :email,
            phone = :phone,
            whatsapp = :whatsapp,
            address = :address,
            city = :city,
            governorate = :governorate,
            map_embed_url = :map_embed_url,
            facebook = :facebook,
            instagram = :instagram,
            working_hours = :working_hours
         WHERE id = 1'
    );
    $stmt->execute($data);
    flash('success', 'Parametres modifies.');
    redirect('admin/settings.php');
}

require __DIR__ . '/../includes/header.php';
?>

<section class="section-padding admin-layout">
    <div class="container">
        <?php require __DIR__ . '/_menu.php'; ?>
        <?php show_flash(); ?>
        <form class="admin-card" method="post">
            <?= csrf_field() ?>
            <h1 class="h4 mb-4">Parametres de l'agence</h1>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nom agence</label>
                    <input type="text" name="agency_name" value="<?= h($settings['agency_name']) ?>" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slogan</label>
                    <input type="text" name="slogan" value="<?= h($settings['slogan']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="<?= h($settings['email']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telephone</label>
                    <input type="text" name="phone" value="<?= h($settings['phone']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="whatsapp" value="<?= h($settings['whatsapp']) ?>" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="address" value="<?= h($settings['address']) ?>" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ville</label>
                    <input type="text" name="city" value="<?= h($settings['city']) ?>" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Gouvernorat</label>
                    <select name="governorate" class="form-select" required>
                        <?php foreach (governorates() as $gov): ?>
                            <option value="<?= h($gov) ?>" <?= $settings['governorate'] === $gov ? 'selected' : '' ?>><?= h($gov) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Facebook</label>
                    <input type="text" name="facebook" value="<?= h($settings['facebook']) ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Instagram</label>
                    <input type="text" name="instagram" value="<?= h($settings['instagram']) ?>" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Horaires</label>
                    <input type="text" name="working_hours" value="<?= h($settings['working_hours']) ?>" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Google Maps embed URL</label>
                    <textarea name="map_embed_url" rows="3" class="form-control"><?= h($settings['map_embed_url']) ?></textarea>
                    <small class="text-muted">Exemple: https://www.google.com/maps?q=Tunis,Tunisia&output=embed</small>
                </div>
                <div class="col-12 mt-4">
                    <button class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
