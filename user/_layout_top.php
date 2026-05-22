<?php
/**
 * user/_layout_top.php
 * En-tête du dashboard utilisateur : doctype, CSS, sidebar, topbar.
 * Usage : inclure EN DÉBUT de chaque page user/ APRÈS avoir défini $pageTitle.
 *
 * Variables attendues :
 *   $pageTitle  (string)  — titre affiché dans l'onglet et la topbar
 *   $activePage (string)  — 'dashboard' | 'favoris' | 'visites' | 'profil'
 */

require_once __DIR__ . '/../includes/user_auth.php';
require_client(); // redirige si non connecté

$client   = current_client();
$settings = app_settings();
$initials = mb_strtoupper(mb_substr($client['nom'], 0, 1));

// Compter les favoris pour le badge
$nbFavoris = 0;
$nbVisites = 0;
try {
    $s = db()->prepare('SELECT COUNT(*) FROM favorites WHERE client_id = :id');
    $s->execute(['id' => $client['id']]);
    $nbFavoris = (int) $s->fetchColumn();

    $s2 = db()->prepare("SELECT COUNT(*) FROM visit_requests WHERE client_id = :id AND status IN ('new','confirmed')");
    $s2->execute(['id' => $client['id']]);
    $nbVisites = (int) $s2->fetchColumn();
} catch (Exception) {}

$activePage = $activePage ?? '';
$appBase    = defined('APP_BASE') ? APP_BASE : '';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?> | <?= h($settings['agency_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= $appBase ?>/assets/css/user-dashboard.css" rel="stylesheet">
</head>
<body class="ud-body">

<!-- Overlay mobile -->
<div class="ud-overlay" id="udOverlay"></div>

<div class="ud-wrapper">

    <!-- ── SIDEBAR ─────────────────────────────────────────── -->
    <aside class="ud-sidebar" id="udSidebar">

        <!-- Logo -->
        <a class="ud-sidebar-brand" href="<?= $appBase ?>/index.php">
            <div class="brand-mark">DT</div>
            <span><?= h($settings['agency_name']) ?></span>
        </a>

        <!-- Mini profil -->
        <div class="ud-sidebar-user d-flex align-items-center gap-2">
            <div class="ud-avatar"><?= h($initials) ?></div>
            <div class="ud-user-info">
                <strong><?= h($client['nom']) ?></strong>
                <small><?= h($client['email']) ?></small>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="ud-nav">
            <div class="ud-nav-section">Menu</div>

            <a class="ud-nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>"
               href="<?= $appBase ?>/user/dashboard.php">
                <i class="fa-solid fa-chart-pie"></i> Tableau de bord
            </a>

            <a class="ud-nav-link <?= $activePage === 'favoris' ? 'active' : '' ?>"
               href="<?= $appBase ?>/user/favoris.php">
                <i class="fa-solid fa-heart"></i> Mes favoris
                <?php if ($nbFavoris): ?>
                    <span class="ud-nav-badge"><?= $nbFavoris ?></span>
                <?php endif; ?>
            </a>

            <a class="ud-nav-link <?= $activePage === 'visites' ? 'active' : '' ?>"
               href="<?= $appBase ?>/user/mes-visites.php">
                <i class="fa-solid fa-calendar-check"></i> Mes visites
                <?php if ($nbVisites): ?>
                    <span class="ud-nav-badge"><?= $nbVisites ?></span>
                <?php endif; ?>
            </a>

            <div class="ud-nav-section mt-2">Compte</div>

            <a class="ud-nav-link <?= $activePage === 'profil' ? 'active' : '' ?>"
               href="<?= $appBase ?>/user/profil.php">
                <i class="fa-solid fa-user-circle"></i> Mon profil
            </a>

            <a class="ud-nav-link" href="<?= $appBase ?>/properties.php">
                <i class="fa-solid fa-building"></i> Voir les biens
            </a>
        </nav>

        <!-- Déconnexion -->
        <div class="ud-sidebar-footer">
            <a class="ud-nav-link logout-link"
               href="<?= $appBase ?>/user/logout.php">
                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
        </div>
    </aside>

    <!-- ── MAIN ────────────────────────────────────────────── -->
    <div class="ud-main">

        <!-- Topbar -->
        <header class="ud-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="ud-menu-toggle" id="udMenuToggle" aria-label="Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <span class="ud-topbar-title"><?= h($pageTitle) ?></span>
            </div>
            <div class="ud-topbar-right">
                <a href="<?= $appBase ?>/properties.php"
                   class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex align-items-center gap-1">
                    <i class="fa-solid fa-search"></i> Rechercher
                </a>
                <a href="<?= $appBase ?>/user/profil.php" class="ud-avatar-sm text-decoration-none" title="Mon profil">
                    <?= h($initials) ?>
                </a>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="ud-content pb-0">
            <?php
            if (!empty($_SESSION['flash'])) {
                $flash = $_SESSION['flash'];
                unset($_SESSION['flash']);
                $icon = match($flash['type']) {
                    'success' => 'fa-circle-check',
                    'danger'  => 'fa-circle-xmark',
                    'warning' => 'fa-triangle-exclamation',
                    default   => 'fa-circle-info',
                };
                echo '<div class="ud-alert ' . h($flash['type']) . ' mb-0">'
                   . '<i class="fa-solid ' . $icon . '"></i>'
                   . h($flash['message'])
                   . '</div>';
            }
            ?>
        </div>

        <!-- Contenu de la page -->
        <div class="ud-content pt-3">
