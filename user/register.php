<?php
/**
 * user/register.php — Inscription client public
 */
require_once __DIR__ . '/../includes/user_auth.php';

if (is_client()) {
    redirect('user/dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $nom      = trim($_POST['nom']              ?? '');
    $email    = trim($_POST['email']            ?? '');
    $tel      = trim($_POST['telephone']        ?? '');
    $password = $_POST['password']              ?? '';
    $confirm  = $_POST['password_confirm']      ?? '';

    // ── Validations ──────────────────────────────────────
    if (mb_strlen($nom) < 2) {
        $errors[] = 'Le nom doit contenir au moins 2 caractères.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse e-mail invalide.';
    }
    if ($tel !== '' && !preg_match('/^[+\d\s\-]{7,20}$/', $tel)) {
        $errors[] = 'Numéro de téléphone invalide (ex: +216 55 123 456).';
    }
    if (mb_strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }

    // Email déjà utilisé ?
    if (empty($errors)) {
        $stmt = db()->prepare('SELECT id FROM clients WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Cette adresse e-mail est déjà enregistrée.';
        }
    }

    // ── Enregistrement ───────────────────────────────────
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

      $stmt = db()->prepare(
    'INSERT INTO clients (nom, email, telephone, password)
     VALUES (:nom, :email, :tel, :password)'
);

$stmt->execute([
    'nom'      => $nom,
    'email'    => $email,
    'tel'      => $tel ?: null,
    'password' => $hash,
]);

        flash('success', 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.');
        redirect('user/login.php');
    }
}

$appBase = defined('APP_BASE') ? APP_BASE : '';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Créer un compte | Espace Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= $appBase ?>/assets/css/user-dashboard.css" rel="stylesheet">
</head>
<body class="ud-body">

<div class="ud-auth-wrapper">
    <div class="ud-auth-box" style="max-width:480px">

        <div class="ud-auth-header">
            <div class="brand">
                <span style="background:rgba(255,255,255,.2);padding:4px 10px;border-radius:8px;margin-right:8px;font-size:1rem">DT</span>
                Dar Tunisie
            </div>
            <p>Créez votre espace client gratuit</p>
        </div>

        <div class="ud-auth-body">

            <?php foreach ($errors as $err): ?>
                <div class="ud-alert danger mb-3">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <?= h($err) ?>
                </div>
            <?php endforeach; ?>

            <form method="post" novalidate>
                <?= csrf_field() ?>

                <!-- Nom -->
                <div class="mb-3">
                    <label class="form-label" for="nom">Nom complet <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-user text-muted"></i></span>
                        <input type="text" id="nom" name="nom" class="form-control"
                               value="<?= h($_POST['nom'] ?? '') ?>"
                               placeholder="Ahmed Ben Salah" required autofocus>
                    </div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label" for="email">Adresse e-mail <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-envelope text-muted"></i></span>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= h($_POST['email'] ?? '') ?>"
                               placeholder="exemple@email.com" required>
                    </div>
                </div>

                <!-- Téléphone -->
                <div class="mb-3">
                    <label class="form-label" for="telephone">
                        Téléphone <small class="text-muted fw-normal">(optionnel)</small>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-phone text-muted"></i></span>
                        <input type="tel" id="telephone" name="telephone" class="form-control"
                               value="<?= h($_POST['telephone'] ?? '') ?>"
                               placeholder="+216 55 123 456">
                    </div>
                </div>

                <!-- Mot de passe -->
                <div class="mb-2">
                    <label class="form-label" for="new_password">Mot de passe <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" id="new_password" name="password" class="form-control"
                               placeholder="Minimum 8 caractères" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePw" tabindex="-1">
                            <i class="fa-solid fa-eye" id="togglePwIcon"></i>
                        </button>
                    </div>
                    <div class="password-strength-bar mt-1">
                        <div class="password-strength-fill" id="pwStrengthFill"></div>
                    </div>
                    <small class="text-muted" id="pwStrengthLabel"></small>
                </div>

                <!-- Confirmation -->
                <div class="mb-4">
                    <label class="form-label" for="confirm_password">Confirmer le mot de passe <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-lock-open text-muted"></i></span>
                        <input type="password" id="confirm_password" name="password_confirm"
                               class="form-control" placeholder="Répétez le mot de passe" required>
                        <div class="invalid-feedback">Les mots de passe ne correspondent pas.</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-dt w-100 mb-3">
                    <i class="fa-solid fa-user-plus me-2"></i> Créer mon compte
                </button>
            </form>
        </div>

        <div class="ud-auth-footer">
            Déjà un compte ? <a href="<?= $appBase ?>/user/login.php">Se connecter</a>
            <span class="mx-2">·</span>
            <a href="<?= $appBase ?>/index.php"><i class="fa-solid fa-house"></i> Accueil</a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* Toggle password visibility */
const toggleBtn  = document.getElementById('togglePw');
const toggleIcon = document.getElementById('togglePwIcon');
const pwField    = document.getElementById('new_password');
if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
        const isText = pwField.type === 'text';
        pwField.type = isText ? 'password' : 'text';
        toggleIcon.className = isText ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
    });
}

/* Password strength */
const fill  = document.getElementById('pwStrengthFill');
const label = document.getElementById('pwStrengthLabel');
if (pwField && fill) {
    pwField.addEventListener('input', () => {
        const v = pwField.value;
        let score = 0;
        if (v.length >= 8)            score++;
        if (/[A-Z]/.test(v))          score++;
        if (/[0-9]/.test(v))          score++;
        if (/[^A-Za-z0-9]/.test(v))   score++;
        fill.style.width = (score * 25) + '%';
        fill.style.background = ['#e0e0e0','#f44336','#ff9800','#2196f3','#4caf50'][score];
        if (label) label.textContent = ['','Faible','Moyen','Bon','Fort'][score];
    });
}

/* Confirm match */
const confirmPw = document.getElementById('confirm_password');
if (confirmPw && pwField) {
    confirmPw.addEventListener('input', () => {
        confirmPw.classList.toggle('is-invalid', confirmPw.value !== pwField.value);
    });
}
</script>
</body>
</html>
