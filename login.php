<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Connexion admin';
// Vérifier si l'administrateur est déjà connecté
if (is_admin()) {
    redirect('admin/dashboard.php');
}
// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF pour sécuriser le formulaire
    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    // Exécuter la requête avec l'email saisi 
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
 // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($user && password_verify($password, $user['password_hash'])) {
         // Stocker les informations utilisateur dans la session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
         // Redirection vers le dashboard admin
        redirect('admin/dashboard.php');
    }
// Message d'erreur si identifiants incorrects
    flash('danger', 'Email ou mot de passe incorrect.');
}

require __DIR__ . '/includes/header.php';
?>
<!-- Section de connexion administrateur -->
<section class="section-padding admin-layout">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <?php show_flash(); ?>
                <form class="admin-card" method="post">
                    <?= csrf_field() ?>
                    <h1 class="h3 fw-bold mb-3">Connexion admin</h1>
                    <p class="text-muted">Compte par defaut: admin@dar-tunisie.tn / admin123</p>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Se connecter</button>
                    <p class="text-center text-muted mt-3 mb-0 small">
                        Vous n'avez pas de compte ?
                        <a href="register.php">S'inscrire</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
