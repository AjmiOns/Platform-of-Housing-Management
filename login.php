<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Connexion admin';

if (is_admin()) {
    redirect('admin/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        redirect('admin/dashboard.php');
    }

    flash('danger', 'Email ou mot de passe incorrect.');
}

require __DIR__ . '/includes/header.php';
?>

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
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
