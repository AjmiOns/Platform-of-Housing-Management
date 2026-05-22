<?php
/**
 * user/profil.php — Gestion du profil client
 */
$pageTitle  = 'Mon profil';
$activePage = 'profil';
require __DIR__ . '/_layout_top.php';

$clientId = $client['id'];

// Récupérer données fraîches depuis la DB
$stmt = db()->prepare('SELECT * FROM clients WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $clientId]);
$data = $stmt->fetch();

// ── Traitement : mise à jour infos ─────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verify_csrf();

    // ── Infos générales ──────────────────────────────────
    if ($_POST['action'] === 'update_info') {
        $result = validate_profile_update($_POST);

        if (!empty($result['errors'])) {
            foreach ($result['errors'] as $e) flash('danger', $e);
        } else {
            // Vérifier email unicité (exclure soi-même)
            $stmt2 = db()->prepare(
                'SELECT id FROM clients WHERE email = :email AND id != :id LIMIT 1'
            );
            $stmt2->execute(['email' => $result['data']['email'], 'id' => $clientId]);

            if ($stmt2->fetch()) {
                flash('danger', 'Cette adresse e-mail est déjà utilisée par un autre compte.');
            } else {
                $stmt2 = db()->prepare(
                    'UPDATE clients SET nom = :nom, email = :email, telephone = :tel WHERE id = :id'
                );
                $stmt2->execute([
                    'nom'   => $result['data']['nom'],
                    'email' => $result['data']['email'],
                    'tel'   => $result['data']['telephone'],
                    'id'    => $clientId,
                ]);

                // Mettre à jour la session
                $_SESSION['client']['nom']       = $result['data']['nom'];
                $_SESSION['client']['email']     = $result['data']['email'];
                $_SESSION['client']['telephone'] = $result['data']['telephone'] ?? '';

                flash('success', 'Vos informations ont été mises à jour.');
                redirect('user/profil.php');
            }
        }
    }

    // ── Changement de mot de passe ───────────────────────
    if ($_POST['action'] === 'change_password') {
        $currentPw  = $_POST['current_password']  ?? '';
        $newPw      = $_POST['new_password']       ?? '';
        $confirmPw  = $_POST['confirm_password']   ?? '';

        if (!password_verify($currentPw, $data['password_hash'])) {
            flash('danger', 'Mot de passe actuel incorrect.');
        } elseif (mb_strlen($newPw) < 8) {
            flash('danger', 'Le nouveau mot de passe doit contenir au moins 8 caractères.');
        } elseif ($newPw !== $confirmPw) {
            flash('danger', 'Les nouveaux mots de passe ne correspondent pas.');
        } else {
            $hash = password_hash($newPw, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt2 = db()->prepare('UPDATE clients SET password_hash = :hash WHERE id = :id');
            $stmt2->execute(['hash' => $hash, 'id' => $clientId]);
            flash('success', 'Mot de passe modifié avec succès.');
            redirect('user/profil.php');
        }
    }
}
?>

<!-- ── En-tête ─────────────────────────────────────────── -->
<div class="ud-page-header">
    <h1>Mon profil</h1>
    <p>Gérez vos informations personnelles et votre sécurité</p>
</div>

<div class="row g-4">

    <!-- ── Colonne gauche : avatar + résumé ─────────────── -->
    <div class="col-lg-3">
        <div class="ud-card text-center p-4">
            <!-- Avatar large -->
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                 style="width:80px;height:80px;background:var(--primary);border-radius:50%;color:#fff;font-size:2rem;font-weight:800">
                <?= h(mb_strtoupper(mb_substr($data['nom'], 0, 1))) ?>
            </div>
            <h6 class="fw-bold mb-1"><?= h($data['nom']) ?></h6>
            <p class="text-muted small mb-3"><?= h($data['email']) ?></p>
            <?php if ($data['telephone']): ?>
                <p class="text-muted small mb-3">
                    <i class="fa-solid fa-phone me-1"></i><?= h($data['telephone']) ?>
                </p>
            <?php endif; ?>
            <span class="badge" style="background:var(--primary-light);color:var(--primary);font-size:.75rem;padding:5px 12px">
                <i class="fa-solid fa-user me-1"></i>Client
            </span>
            <hr class="my-3">
            <p class="text-muted small mb-0">
                Membre depuis<br>
                <strong><?= date('d/m/Y', strtotime($data['created_at'])) ?></strong>
            </p>
        </div>
    </div>

    <!-- ── Colonne droite : formulaires ─────────────────── -->
    <div class="col-lg-9">

        <!-- Infos générales -->
        <div class="ud-form-section mb-4">
            <h5><i class="fa-solid fa-user-pen"></i>Informations personnelles</h5>

            <form method="post" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_info">

                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label" for="nom">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" id="nom" name="nom" class="form-control"
                               value="<?= h($data['nom']) ?>" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="email">Adresse e-mail <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= h($data['email']) ?>" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="telephone">
                            Téléphone <small class="text-muted fw-normal">(optionnel)</small>
                        </label>
                        <input type="tel" id="telephone" name="telephone" class="form-control"
                               value="<?= h($data['telephone'] ?? '') ?>"
                               placeholder="+216 55 123 456">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-dt">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Changement mot de passe -->
        <div class="ud-form-section">
            <h5><i class="fa-solid fa-shield-halved"></i>Changer le mot de passe</h5>

            <form method="post" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="change_password">

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="current_password">Mot de passe actuel <span class="text-danger">*</span></label>
                        <input type="password" id="current_password" name="current_password"
                               class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="new_password">Nouveau mot de passe <span class="text-danger">*</span></label>
                        <input type="password" id="new_password" name="new_password"
                               class="form-control" placeholder="Minimum 8 caractères" required>
                        <div class="password-strength-bar mt-1">
                            <div class="password-strength-fill" id="pwStrengthFill"></div>
                        </div>
                        <small class="text-muted" id="pwStrengthLabel"></small>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="confirm_password">Confirmer <span class="text-danger">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password"
                               class="form-control" placeholder="Répétez le nouveau mot de passe" required>
                        <div class="invalid-feedback">Les mots de passe ne correspondent pas.</div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-outline-dt">
                        <i class="fa-solid fa-key me-2"></i>Modifier le mot de passe
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
