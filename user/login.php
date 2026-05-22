<?php
/**
 * user/login.php — Connexion client (utilisateur public)
 * Distinct de login.php à la racine (qui est pour l'admin).
 */
require_once __DIR__ . '/../includes/user_auth.php';

// Déjà connecté → dashboard
if (is_client()) {
    redirect('user/dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse e-mail invalide.';
    } elseif ($password === '') {
        $errors[] = 'Veuillez saisir votre mot de passe.';
    } else {
       $stmt = db()->prepare('SELECT * FROM clients WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if ($client && password_verify($password, $client['password'])) {

    login_client($client);

    $redirect = $_SESSION['redirect_after_login'] ?? '';
    unset($_SESSION['redirect_after_login']);

    header('Location: ' . ($redirect ?: (defined('APP_BASE') ? APP_BASE : '') . '/user/dashboard.php'));
    exit;

} else {
    $errors[] = 'Email ou mot de passe incorrect.';
}
    }
}

$appBase = defined('APP_BASE') ? APP_BASE : '';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion | Espace Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= $appBase ?>/assets/css/user-dashboard.css" rel="stylesheet">
</head>
<body class="ud-body">

<div class="ud-auth-wrapper">
    <div class="ud-auth-box">

        <!-- En-tête -->
        <div class="ud-auth-header">
            <div class="brand">
                <span style="background:rgba(255,255,255,.2);padding:4px 10px;border-radius:8px;margin-right:8px;font-size:1rem">DT</span>
                Dar Tunisie
            </div>
            <p>Connectez-vous à votre espace personnel</p>
        </div>

        <!-- Formulaire -->
        <div class="ud-auth-body">

            <!-- Erreurs -->
            <?php foreach ($errors as $err): ?>
                <div class="ud-alert danger mb-3">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <?= h($err) ?>
                </div>
            <?php endforeach; ?>

            <form method="post" novalidate>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label" for="email">Adresse e-mail</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fa-solid fa-envelope text-muted"></i>
                        </span>
                        <input
                            type="email" id="email" name="email"
                            class="form-control"
                            value="<?= h($_POST['email'] ?? '') ?>"
                            placeholder="votre@email.com"
                            required autofocus
                        >
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="password">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fa-solid fa-lock text-muted"></i>
                        </span>
                        <input
                            type="password" id="password" name="password"
                            class="form-control"
                            placeholder="••••••••"
                            required
                        >
                        <button class="btn btn-outline-secondary" type="button" id="togglePw" tabindex="-1">
                            <i class="fa-solid fa-eye" id="togglePwIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-dt w-100 mb-3">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Se connecter
                </button>
            </form>
        </div>

        <div class="ud-auth-footer">
            Pas encore de compte ?
            <a href="<?= $appBase ?>/user/register.php">Créer un compte</a>
            <span class="mx-2">·</span>
            <a href="<?= $appBase ?>/index.php">
                <i class="fa-solid fa-house"></i> Accueil
            </a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* Toggle show/hide password */
const toggleBtn  = document.getElementById('togglePw');
const toggleIcon = document.getElementById('togglePwIcon');
const pwField    = document.getElementById('password');

if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
        const isText = pwField.type === 'text';
        pwField.type = isText ? 'password' : 'text';
        toggleIcon.className = isText ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
    });
}
</script>
</body>
</html>
