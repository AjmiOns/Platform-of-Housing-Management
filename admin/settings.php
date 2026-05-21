<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Paramètres agence';
$settings  = app_settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    // Upload logo
    $logo_url = $settings['logo_url'] ?? null;
    try {
        $uploadedLogo = handle_upload('logo_file');
        if ($uploadedLogo) {
            $logo_url = $uploadedLogo;
        }
    } catch (RuntimeException $e) {
        flash('danger', 'Logo : ' . $e->getMessage());
        redirect('admin/settings.php');
    }

    // Upload photo de couverture
    $cover_url = $settings['cover_url'] ?? null;
    try {
        $uploadedCover = handle_upload('cover_file');
        if ($uploadedCover) {
            $cover_url = $uploadedCover;
        }
    } catch (RuntimeException $e) {
        flash('danger', 'Couverture : ' . $e->getMessage());
        redirect('admin/settings.php');
    }

    $data = [
        'agency_name'   => trim($_POST['agency_name']   ?? ''),
        'slogan'        => trim($_POST['slogan']         ?? ''),
        'email'         => trim($_POST['email']          ?? ''),
        'phone'         => trim($_POST['phone']          ?? ''),
        'whatsapp'      => trim($_POST['whatsapp']       ?? ''),
        'address'       => trim($_POST['address']        ?? ''),
        'city'          => trim($_POST['city']           ?? ''),
        'governorate'   => trim($_POST['governorate']    ?? ''),
        'map_embed_url' => trim($_POST['map_embed_url']  ?? ''),
        'facebook'      => trim($_POST['facebook']       ?? ''),
        'instagram'     => trim($_POST['instagram']      ?? ''),
        'working_hours' => trim($_POST['working_hours']  ?? ''),
        'logo_url'      => $logo_url,
        'cover_url'     => $cover_url,
    ];

    db()->prepare(
        'UPDATE agency_settings SET
            agency_name=:agency_name, slogan=:slogan, email=:email,
            phone=:phone, whatsapp=:whatsapp, address=:address,
            city=:city, governorate=:governorate, map_embed_url=:map_embed_url,
            facebook=:facebook, instagram=:instagram, working_hours=:working_hours,
            logo_url=:logo_url, cover_url=:cover_url
         WHERE id = 1'
    )->execute($data);

    flash('success', 'Paramètres modifiés avec succès.');
    redirect('admin/settings.php');
}

require __DIR__ . '/../includes/header.php';
?>

<section class="section-padding admin-layout">
    <div class="container">
        <?php require __DIR__ . '/_menu.php'; ?>
        <?php show_flash(); ?>

        <!-- FICHE ACTUELLE -->
        <div class="admin-card mb-4">
            <h1 class="h4 mb-4">📋 Informations actuelles de l'agence</h1>

            <div class="row g-4">

                <!-- Logo et couverture -->
                <?php if (!empty($settings['logo_url']) || !empty($settings['cover_url'])): ?>
                <div class="col-12">
                    <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Visuels</h6>
                    <div class="d-flex gap-4 align-items-start flex-wrap">
                        <?php if (!empty($settings['logo_url'])): ?>
                        <div>
                            <p class="text-muted small mb-1">Logo</p>
                            <img src="<?= h(property_image($settings['logo_url'])) ?>"
                                 alt="Logo agence" style="max-height:80px;border-radius:6px;border:1px solid #dee2e6;">
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['cover_url'])): ?>
                        <div>
                            <p class="text-muted small mb-1">Photo de couverture</p>
                            <img src="<?= h(property_image($settings['cover_url'])) ?>"
                                 alt="Couverture agence" style="max-height:80px;border-radius:6px;border:1px solid #dee2e6;">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Identité -->
                <div class="col-md-6">
                    <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Identité</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th class="text-muted" style="width:40%">Nom</th>
                            <td><strong><?= h($settings['agency_name']) ?></strong></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Slogan</th>
                            <td><?= h($settings['slogan']) ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Horaires</th>
                            <td><?= h($settings['working_hours']) ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Contact -->
                <div class="col-md-6">
                    <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Contact</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th class="text-muted" style="width:40%">Email</th>
                            <td><a href="mailto:<?= h($settings['email']) ?>"><?= h($settings['email']) ?></a></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Téléphone</th>
                            <td><?= h($settings['phone']) ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">WhatsApp</th>
                            <td><?= h($settings['whatsapp']) ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Localisation -->
                <div class="col-md-6">
                    <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Localisation</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th class="text-muted" style="width:40%">Adresse</th>
                            <td><?= h($settings['address']) ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Ville</th>
                            <td><?= h($settings['city']) ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Gouvernorat</th>
                            <td><?= h($settings['governorate']) ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Réseaux sociaux -->
                <div class="col-md-6">
                    <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Réseaux sociaux</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th class="text-muted" style="width:40%">Facebook</th>
                            <td>
                                <?php if ($settings['facebook'] && $settings['facebook'] !== '#'): ?>
                                    <a href="<?= h($settings['facebook']) ?>" target="_blank">Voir le profil</a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Instagram</th>
                            <td>
                                <?php if ($settings['instagram'] && $settings['instagram'] !== '#'): ?>
                                    <a href="<?= h($settings['instagram']) ?>" target="_blank">Voir le profil</a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Carte Google Maps -->
                <?php if (!empty($settings['map_embed_url'])): ?>
                <div class="col-12">
                    <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Localisation sur la carte</h6>
                    <iframe
                        src="<?= h($settings['map_embed_url']) ?>"
                        width="100%"
                        height="220"
                        style="border:0;border-radius:8px;"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- FORMULAIRE DE MODIFICATION -->
        <div class="admin-card" id="form-settings">
            <h2 class="h4 mb-4">✏️ Modifier les paramètres</h2>

            <form method="post" enctype="multipart/form-data" novalidate>
                <?= csrf_field() ?>

                <!-- Visuels (uploads) -->
                <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Visuels de l'agence</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Logo de l'agence <span class="text-muted">(JPG, PNG, WEBP — max 3 MB)</span></label>
                        <input type="file" name="logo_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <?php if (!empty($settings['logo_url'])): ?>
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <img src="<?= h(property_image($settings['logo_url'])) ?>"
                                     alt="Logo actuel" style="max-height:50px;border-radius:4px;">
                                <span class="text-muted small">Laisser vide pour conserver.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Photo de couverture <span class="text-muted">(JPG, PNG, WEBP — max 3 MB)</span></label>
                        <input type="file" name="cover_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <?php if (!empty($settings['cover_url'])): ?>
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <img src="<?= h(property_image($settings['cover_url'])) ?>"
                                     alt="Couverture actuelle" style="max-height:50px;border-radius:4px;">
                                <span class="text-muted small">Laisser vide pour conserver.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Identité -->
                <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Identité</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nom de l'agence <span class="text-danger">*</span></label>
                        <input type="text" name="agency_name" class="form-control"
                               value="<?= h($settings['agency_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slogan <span class="text-danger">*</span></label>
                        <input type="text" name="slogan" class="form-control"
                               value="<?= h($settings['slogan']) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Horaires <span class="text-danger">*</span></label>
                        <input type="text" name="working_hours" class="form-control"
                               value="<?= h($settings['working_hours']) ?>"
                               placeholder="Ex : Lundi - Samedi : 09:00 - 18:00" required>
                    </div>
                </div>

                <!-- Contact -->
                <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Contact</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                               value="<?= h($settings['email']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control"
                               value="<?= h($settings['phone']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="whatsapp" class="form-control"
                               value="<?= h($settings['whatsapp']) ?>" required>
                    </div>
                </div>

                <!-- Localisation -->
                <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Localisation</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Adresse <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control"
                               value="<?= h($settings['address']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ville <span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control"
                               value="<?= h($settings['city']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gouvernorat <span class="text-danger">*</span></label>
                        <select name="governorate" class="form-select" required>
                            <?php foreach (governorates() as $gov): ?>
                                <option value="<?= h($gov) ?>"
                                    <?= $settings['governorate'] === $gov ? 'selected' : '' ?>>
                                    <?= h($gov) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Google Maps — URL d'intégration</label>
                        <textarea name="map_embed_url" rows="2" class="form-control"><?= h($settings['map_embed_url']) ?></textarea>
                        <div class="form-text">Exemple : https://www.google.com/maps?q=Tunis,Tunisia&amp;output=embed</div>
                    </div>
                </div>

                <!-- Réseaux sociaux -->
                <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Réseaux sociaux</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Facebook</label>
                        <input type="text" name="facebook" class="form-control"
                               value="<?= h($settings['facebook']) ?>"
                               placeholder="https://facebook.com/...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Instagram</label>
                        <input type="text" name="instagram" class="form-control"
                               value="<?= h($settings['instagram']) ?>"
                               placeholder="https://instagram.com/...">
                        </div>
                </div>

                <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
            </form>
        </div>

    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
