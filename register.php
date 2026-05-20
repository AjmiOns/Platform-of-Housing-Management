<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Créer un compte';

// Vérifier si l'administrateur est déjà connecté
if (is_admin()) {
    redirect('admin/dashboard.php');
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF pour sécuriser le formulaire
    verify_csrf();

    $name     = trim($_POST['name']             ?? '');
    $email    = trim($_POST['email']            ?? '');
    $password = $_POST['password']              ?? '';
    $confirm  = $_POST['password_confirm']      ?? '';
    $role     = $_POST['role']                  ?? 'agent';

    // ---- Validations ----
    $errors = [];

    if (mb_strlen($name) < 3) {
        $errors[] = 'Le nom doit contenir au moins 3 caractères.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse e-mail invalide.';
    }

    if (mb_strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }

    if (!in_array($role, ['admin', 'agent'], true)) {
        $role = 'agent';
    }

    // Vérifier si l'e-mail est déjà utilisé
    if (empty($errors)) {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Cette adresse e-mail est déjà utilisée.';
        }
    }

    // ---- Enregistrement ----
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = db()->prepare(
            'INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :hash, :role)'
        );
        $stmt->execute([
            'name'  => $name,
            'email' => $email,
            'hash'  => $hash,
            'role'  => $role,
        ]);

        flash('success', 'Compte créé avec succès. Vous pouvez maintenant vous connecter.');
        redirect('login.php');
    }

    // Afficher les erreurs
    foreach ($errors as $err) {
        flash('danger', $err);
    }
}

require __DIR__ . '/includes/header.php';
?>

<!-- Section d'inscription -->
<section class="section-padding admin-layout">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <?php show_flash(); ?>

                <form class="admin-card" method="post" novalidate>
                    <?= csrf_field() ?>
                    <h1 class="h3 fw-bold mb-1">Créer un compte</h1>
                    <p class="text-muted mb-4">Accès réservé aux membres autorisés de l'agence.</p>

                    <!-- Nom complet -->
                    <div class="mb-3">
                        <label class="form-label" for="name">Nom complet</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                            placeholder="Ex : Ahmed Ben Salah"
                            required
                            autofocus
                        >
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label" for="email">Adresse e-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            placeholder="exemple@dar-tunisie.tn"
                            required
                        >
                    </div>

                    <!-- Rôle -->
                    <div class="mb-3">
                        <label class="form-label" for="role">Rôle</label>
                        <select id="role" name="role" class="form-select">
                            <option value="agent"  <?= (($_POST['role'] ?? 'agent') === 'agent'  ? 'selected' : '') ?>>Agent</option>
                            <option value="admin"  <?= (($_POST['role'] ?? '')        === 'admin'  ? 'selected' : '') ?>>Administrateur</option>
                        </select>
                    </div>

                    <!-- Mot de passe -->
                    <div class="mb-3">
                        <label class="form-label" for="password">Mot de passe</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Minimum 8 caractères"
                            required
                        >
                    </div>

                    <!-- Confirmation mot de passe -->
                    <div class="mb-4">
                        <label class="form-label" for="password_confirm">Confirmer le mot de passe</label>
                        <input
                            type="password"
                            id="password_confirm"
                            name="password_confirm"
                            class="form-control"
                            placeholder="Répétez le mot de passe"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Créer le compte</button>

                    <p class="text-center text-muted mt-3 mb-0 small">
                        Déjà un compte ?
                        <a href="<?= base_url('login.php') ?>">Se connecter</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>