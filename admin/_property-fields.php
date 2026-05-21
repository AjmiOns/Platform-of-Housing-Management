<?php
/**
 * _property-fields.php
 * Inclus dans property-add.php ET property-edit.php
 *
 * Variables attendues dans le contexte appelant :
 *  - $v            : closure fn(key, default) → valeur HTML-escapée
 *  - $categories   : tableau PDO des catégories
 *  - $property     : tableau PDO du bien (null si ajout)
 *  - $features     : tableau de strings (équipements existants)
 *  - $ownerName    : string (nom du propriétaire actuel, '' si ajout)
 *  - $existingOwners : tableau PDO des propriétaires (pour datalist)
 */
?>

<!-- ── Informations principales ── -->
<h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Informations principales</h6>
<div class="row g-3 mb-4">

    <div class="col-md-8">
        <label class="form-label">Titre de l'annonce <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control"
               value="<?= $v('title') ?>" required autofocus>
    </div>

    <div class="col-md-4">
        <label class="form-label">Catégorie <span class="text-danger">*</span></label>
        <select name="category_id" class="form-select" required>
            <option value="">-- Choisir --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                    <?= (($property['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                    <?= h($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Propriétaire</label>
        <input
            type="text"
            name="owner_name"
            class="form-control"
            value="<?= htmlspecialchars($_POST['owner_name'] ?? $ownerName, ENT_QUOTES) ?>"
            placeholder="Nom complet du propriétaire"
            list="owners-list"
            autocomplete="off"
        >
        <datalist id="owners-list">
            <?php foreach ($existingOwners as $o): ?>
                <option value="<?= h($o['full_name']) ?>">
            <?php endforeach; ?>
        </datalist>
        <div class="form-text">Tapez un nom existant ou saisissez un nouveau propriétaire.</div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Statut de disponibilité</label>
        <select name="availability_status" class="form-select">
            <?php foreach (['available' => 'Disponible', 'reserved' => 'Réservé', 'rented' => 'Loué'] as $val => $label): ?>
                <option value="<?= $val ?>"
                    <?= (($property['availability_status'] ?? 'available') === $val) ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Description <span class="text-danger">*</span></label>
        <textarea name="description" class="form-control" rows="4" required><?= $v('description') ?></textarea>
    </div>
</div>

<!-- ── Localisation ── -->
<h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Localisation</h6>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">Gouvernorat <span class="text-danger">*</span></label>
        <input type="text" name="governorate" class="form-control" value="<?= $v('governorate') ?>" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Ville <span class="text-danger">*</span></label>
        <input type="text" name="city" class="form-control" value="<?= $v('city') ?>" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Adresse</label>
        <input type="text" name="address" class="form-control" value="<?= $v('address') ?>">
    </div>
</div>

<!-- ── Caractéristiques ── -->
<h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Caractéristiques</h6>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">Loyer (TND) <span class="text-danger">*</span></label>
        <input type="number" name="rent_price" class="form-control" min="0" step="0.01"
               value="<?= $v('rent_price', 0) ?>" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Surface (m²) <span class="text-danger">*</span></label>
        <input type="number" name="area" class="form-control" min="0" step="0.01"
               value="<?= $v('area', 0) ?>" required>
    </div>
    <div class="col-md-2">
        <label class="form-label">Pièces</label>
        <input type="number" name="rooms" class="form-control" min="1" value="<?= $v('rooms', 1) ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">Chambres</label>
        <input type="number" name="bedrooms" class="form-control" min="0" value="<?= $v('bedrooms', 1) ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">Salles de bain</label>
        <input type="number" name="bathrooms" class="form-control" min="0" value="<?= $v('bathrooms', 1) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Étage</label>
        <input type="text" name="floor" class="form-control"
               value="<?= $v('floor') ?>" placeholder="Ex: 2, RDC, Dernier">
    </div>
    <div class="col-md-3">
        <label class="form-label">Places de parking</label>
        <input type="number" name="parking" class="form-control" min="0" value="<?= $v('parking', 0) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Mode de paiement</label>
        <input type="text" name="payment_method" class="form-control"
               value="<?= $v('payment_method', 'Virement bancaire ou espece') ?>">
    </div>
    <div class="col-md-3 d-flex align-items-end gap-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="furnished" id="furnished"
                <?= ($property['furnished'] ?? 0) ? 'checked' : '' ?>>
            <label class="form-check-label" for="furnished">Meublé</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="contract_ready" id="contract_ready"
                <?= ($property['contract_ready'] ?? 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="contract_ready">Contrat prêt</label>
        </div>
    </div>
</div>

<!-- ── Équipements ── -->
<h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Équipements / Points forts</h6>
<div class="mb-4">
    <label class="form-label">Équipements <span class="text-muted">(séparés par des virgules)</span></label>
    <input type="text" name="features" class="form-control"
           value="<?= htmlspecialchars(implode(', ', $features ?? []), ENT_QUOTES) ?>"
           placeholder="Ex: Climatisation, Balcon, Jardin, Ascenseur">
</div>

<!-- ── Image ── -->
<h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Image</h6>
<div class="mb-4">
    <label class="form-label">Télécharger une image <span class="text-muted">(JPG, PNG, WEBP — max 3 MB)</span></label>
    <input type="file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">

    <?php if (!empty($property['image_url'])): ?>
        <div class="mt-2 d-flex align-items-center gap-3">
            <img src="<?= h(property_image($property['image_url'])) ?>"
                 alt="Image actuelle" style="max-height:100px;border-radius:6px;">
            <span class="text-muted small">Image actuelle — laisser vide pour la conserver.</span>
        </div>
        <input type="hidden" name="image_url" value="<?= h($property['image_url']) ?>">
    <?php endif; ?>
</div>

<!-- ── Publication ── -->
<div class="form-check mb-4">
    <input class="form-check-input" type="checkbox" name="published" id="published"
        <?= ($property['published'] ?? 1) ? 'checked' : '' ?>>
    <label class="form-check-label" for="published">Publier ce bien (visible sur le site)</label>
</div>