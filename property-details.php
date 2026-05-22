<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/user_auth.php';

$id       = (int) ($_GET['id'] ?? 0);
$property = get_property($id);
if (!$property || (int) $property['published'] !== 1) {
    flash('warning', 'Bien introuvable.');
    redirect('properties.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $client = current_client();

    if (!$client) {
        flash('danger', 'Vous devez être connecté.');
        redirect('user/login.php');
    }

    $clientId = (int) $client['id'];
    $propertyId = (int) $id;

    $fullName  = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $visitDate = $_POST['visit_date'] ?? '';
    $visitTime = $_POST['visit_time'] ?? '';
    $message   = trim($_POST['message'] ?? '');

    if ($fullName === '' || $phone === '' || $visitDate === '' || $visitTime === '') {
        flash('danger', 'Veuillez remplir les champs obligatoires.');
    } else {

        $stmt = db()->prepare(
            "INSERT INTO visit_requests 
            (client_id, property_id, visit_date, message, status, created_at)
            VALUES (:client_id, :property_id, :visit_date, :message, 'new', NOW())"
        );

        $stmt->execute([
            'client_id'   => $clientId,
            'property_id' => $propertyId,
            'visit_date'  => $visitDate,
            'message'     => $message
        ]);

        flash('success', 'Votre demande de visite a été envoyée.');
        redirect('property-details.php?id=' . $id);
    }
}

$features  = get_property_features((int) $property['id']);
$pageTitle = $property['title'];

// Favori : vérifier si le client connecté a mis ce bien en favori
$isFav = is_client() ? is_favorite(current_client()['id'], (int) $property['id']) : false;

require __DIR__ . '/includes/header.php';
?>

<style>
/* ── Page hero détail ───────────────────────────────── */
.detail-hero {
    background: linear-gradient(90deg, rgba(23,33,43,.92), rgba(23,33,43,.45)),
                url('<?= h(property_image($property['image_url'])) ?>') center/cover no-repeat;
    padding: 70px 0 60px;
    color: #fff;
    position: relative;
}
.detail-hero .badge-category {
    background: rgba(255,255,255,.15);
    color: #fff;
    font-size: .8rem;
    letter-spacing: .04em;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,.25);
}
.detail-hero .price {
    color: #ffcc80;
    font-size: 2.2rem;
    font-weight: 800;
}
.detail-hero .status-pill {
    font-size: .78rem;
    padding: 4px 12px;
    border-radius: 99px;
    font-weight: 700;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,.2);
}
.detail-hero h1 { font-size: clamp(1.6rem, 4vw, 2.6rem); font-weight: 800; }

/* ── Image principale ───────────────────────────────── */
.detail-image {
    width: 100%;
    height: 420px;
    object-fit: cover;
    border-radius: 20px;
    box-shadow: 0 16px 40px rgba(23,33,43,.14);
}
@media(max-width:768px){ .detail-image { height: 260px; } }

/* ── Blocs de contenu ───────────────────────────────── */
.detail-card {
    background: #fff;
    border-radius: 20px;
    padding: 1.75rem;
    box-shadow: 0 8px 28px rgba(23,33,43,.07);
    border: 1px solid rgba(23,33,43,.06);
    margin-bottom: 1.5rem;
}
.detail-card h3 {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 1rem;
    padding-bottom: .6rem;
    border-bottom: 2px solid var(--light);
}

/* ── Grille de stats ────────────────────────────────── */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}
@media(max-width:576px){ .stat-grid { grid-template-columns: repeat(2,1fr); } }

.stat-box {
    background: var(--light);
    border-radius: 14px;
    padding: 1rem .75rem;
    text-align: center;
}
.stat-box strong {
    display: block;
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--primary);
}
.stat-box span {
    font-size: .78rem;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .04em;
}

/* ── Équipements ────────────────────────────────────── */
.feature-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: .5rem;
}
.feature-chip {
    background: #fff3e8;
    color: var(--primary-dark);
    border-radius: 99px;
    padding: 5px 14px;
    font-size: .82rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* ── Fiche rapide ───────────────────────────────────── */
.info-list { list-style: none; padding: 0; margin: 0; }
.info-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .65rem 0;
    border-bottom: 1px solid var(--light);
    font-size: .9rem;
}
.info-list li:last-child { border-bottom: none; }
.info-list li span:first-child { color: var(--muted); }
.info-list li strong { color: var(--dark); }

/* ── Formulaire visite ──────────────────────────────── */
.visit-form-card {
    background: #fff;
    border-radius: 20px;
    padding: 1.75rem;
    box-shadow: 0 8px 28px rgba(23,33,43,.07);
    border: 1px solid rgba(23,33,43,.06);
    position: sticky;
    top: 90px;
}
.visit-form-card h4 {
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 1.25rem;
    padding-bottom: .6rem;
    border-bottom: 2px solid var(--light);
}
.visit-form-card .form-control,
.visit-form-card .form-select {
    border-radius: 10px;
    min-height: 44px;
}

/* ── Breadcrumb ─────────────────────────────────────── */
.detail-breadcrumb {
    font-size: .84rem;
    color: rgba(255,255,255,.7);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 6px;
}
.detail-breadcrumb a { color: rgba(255,255,255,.8); text-decoration: none; }
.detail-breadcrumb a:hover { color: #fff; }

/* ── Bouton favori ──────────────────────────────────── */
.btn-fav {
    border-radius: 10px;
    font-size: .88rem;
    padding: 8px 18px;
    font-weight: 600;
    transition: all .2s;
}
.btn-fav.active {
    background: #e53935;
    border-color: #e53935;
    color: #fff;
}
</style>

<!-- ── Hero avec image en fond ────────────────────── -->
<section class="detail-hero">
    <div class="container">
        <div class="detail-breadcrumb">
            <a href="<?= url('index.php') ?>">Accueil</a>
            <span>›</span>
            <a href="<?= url('properties.php') ?>">Biens</a>
            <span>›</span>
            <span><?= h($property['title']) ?></span>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge badge-category"><?= h($property['category_name']) ?></span>
                    <?php
                    $statusColors = [
                        'available' => 'rgba(23,138,76,.8)',
                        'reserved'  => 'rgba(230,81,0,.8)',
                        'rented'    => 'rgba(100,100,100,.7)',
                    ];
                    $sc = $statusColors[$property['availability_status']] ?? 'rgba(100,100,100,.7)';
                    ?>
                    <span class="status-pill" style="background:<?= $sc ?>">
                        <?= status_label($property['availability_status']) ?>
                    </span>
                </div>
                <h1 class="mb-2"><?= h($property['title']) ?></h1>
                <p class="mb-0" style="color:rgba(255,255,255,.75)">
                    <i class="fa-solid fa-location-dot me-1"></i>
                    <?= h($property['address']) ?>, <?= h($property['city']) ?>, <?= h($property['governorate']) ?>
                </p>
            </div>
            <div class="text-lg-end">
                <div class="price"><?= format_tnd((float) $property['rent_price']) ?></div>
                <span style="color:rgba(255,255,255,.6);font-size:.9rem">par mois</span>
            </div>
        </div>
    </div>
</section>

<!-- ── Contenu principal ────────────────────────────── -->
<section class="section-padding" style="padding-top:48px">
    <div class="container">
        <?php show_flash(); ?>
        <div class="row g-5">

            <!-- Colonne gauche -->
            <div class="col-lg-8">

                <!-- Image + bouton favori -->
                <div class="position-relative mb-4">
                    <img
                        class="detail-image"
                        src="<?= h(property_image($property['image_url'])) ?>"
                        alt="<?= h($property['title']) ?>"
                    >
                    <!-- Bouton favori (coin haut droit de l'image) -->
                    <div style="position:absolute;top:16px;right:16px">
                        <?php if (is_client()): ?>
                            <form action="<?= url('user/toggle-favori.php') ?>" method="post" id="favForm">
                                <?= csrf_field() ?>
                                <input type="hidden" name="property_id" value="<?= (int)$property['id'] ?>">
                                <input type="hidden" name="redirect_to"
                                       value="property-details.php?id=<?= (int)$property['id'] ?>">
                                <button type="submit" id="favBtn"
                                        class="btn btn-fav <?= $isFav ? 'active' : 'btn-light' ?>">
                                    <i class="fa-<?= $isFav ? 'solid' : 'regular' ?> fa-heart me-1"></i>
                                    <span id="favLabel"><?= $isFav ? 'Sauvegardé' : 'Sauvegarder' ?></span>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="<?= url('user/login.php') ?>" class="btn btn-fav btn-light"
                               title="Connectez-vous pour sauvegarder ce bien">
                                <i class="fa-regular fa-heart me-1"></i>Sauvegarder
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Stats -->
                <div class="detail-card">
                    <h3><i class="fa-solid fa-chart-simple me-2" style="color:var(--primary)"></i>Caractéristiques</h3>
                    <div class="stat-grid">
                        <div class="stat-box">
                            <strong><?= h($property['area']) ?></strong>
                            <span>m² surface</span>
                        </div>
                        <div class="stat-box">
                            <strong><?= (int) $property['rooms'] ?></strong>
                            <span>Pièces</span>
                        </div>
                        <div class="stat-box">
                            <strong><?= (int) $property['bedrooms'] ?></strong>
                            <span>Chambres</span>
                        </div>
                        <div class="stat-box">
                            <strong><?= (int) $property['bathrooms'] ?></strong>
                            <span>Salles de bain</span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="detail-card">
                    <h3><i class="fa-solid fa-align-left me-2" style="color:var(--primary)"></i>Description</h3>
                    <p class="text-muted mb-0" style="line-height:1.8"><?= nl2br(h($property['description'])) ?></p>
                </div>

                <!-- Équipements -->
                <?php if ($features): ?>
                <div class="detail-card">
                    <h3><i class="fa-solid fa-star me-2" style="color:var(--primary)"></i>Équipements & Points forts</h3>
                    <div class="feature-chips">
                        <?php foreach ($features as $feature): ?>
                            <span class="feature-chip">
                                <i class="fa-solid fa-check" style="font-size:.7rem"></i>
                                <?= h($feature) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Colonne droite -->
            <div class="col-lg-4">

                <!-- Fiche rapide -->
                <div class="detail-card">
                    <h3><i class="fa-solid fa-circle-info me-2" style="color:var(--primary)"></i>Informations</h3>
                    <ul class="info-list">
                        <li>
                            <span>Statut</span>
                            <strong>
                                <span class="badge text-bg-<?= status_class($property['availability_status']) ?>">
                                    <?= status_label($property['availability_status']) ?>
                                </span>
                            </strong>
                        </li>
                        <li><span>Étage</span><strong><?= h($property['floor'] ?: '—') ?></strong></li>
                        <li><span>Parking</span><strong><?= (int) $property['parking'] ?> place(s)</strong></li>
                        <li><span>Meublé</span><strong><?= $property['furnished'] ? '✅ Oui' : '❌ Non' ?></strong></li>
                        <li><span>Contrat</span><strong><?= $property['contract_ready'] ? '✅ Prêt' : 'À vérifier' ?></strong></li>
                        <li><span>Paiement</span><strong><?= h($property['payment_method']) ?></strong></li>
                        <?php if ($property['owner_name']): ?>
                        <li><span>Propriétaire</span><strong><?= h($property['owner_name']) ?></strong></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Formulaire visite -->
                <div class="visit-form-card">
                    <h4><i class="fa-solid fa-calendar-check me-2" style="color:var(--primary)"></i>Demander une visite</h4>

                    <?php if (is_client()): ?>
                        <!-- Client connecté : pré-remplir les champs -->
                        <?php $cl = current_client(); ?>
                        <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded"
                             style="background:#f8f5f1;font-size:.82rem">
                            <i class="fa-solid fa-user-check" style="color:var(--primary)"></i>
                            <span>Connecté en tant que <strong><?= h($cl['nom']) ?></strong></span>
                        </div>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nom complet <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control"
                                   placeholder="Votre nom"
                                   value="<?= is_client() ? h(current_client()['nom']) : '' ?>"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Téléphone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control"
                                   placeholder="+216 xx xxx xxx"
                                   value="<?= is_client() ? h(current_client()['telephone'] ?? '') : '' ?>"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="optionnel"
                                   value="<?= is_client() ? h(current_client()['email']) : '' ?>">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" name="visit_date" class="form-control"
                                       min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Heure <span class="text-danger">*</span></label>
                                <input type="time" name="visit_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Message</label>
                            <textarea name="message" class="form-control" rows="3"
                                      placeholder="Précisez votre disponibilité..."></textarea>
                        </div>
                        <button class="btn btn-primary w-100" style="min-height:46px;font-weight:700">
                            <i class="fa-solid fa-paper-plane me-2"></i>Envoyer la demande
                        </button>
                    </form>

                    <?php if (!is_client()): ?>
                        <p class="text-center mt-3 mb-0" style="font-size:.82rem;color:var(--muted)">
                            <a href="<?= url('user/login.php') ?>" style="color:var(--primary);font-weight:600">
                                Connectez-vous
                            </a>
                            pour suivre vos visites depuis votre espace client.
                        </p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>

<?php if (is_client()): ?>
<script>
/* Toggle favori en AJAX (fonctionne aussi sans JS via form POST) */
document.getElementById('favForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn   = document.getElementById('favBtn');
    const label = document.getElementById('favLabel');
    const icon  = btn.querySelector('i');

    try {
        const res  = await fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();

        if (json.favorite) {
            btn.classList.add('active');
            btn.classList.remove('btn-light');
            icon.className = 'fa-solid fa-heart me-1';
            label.textContent = 'Sauvegardé';
        } else {
            btn.classList.remove('active');
            btn.classList.add('btn-light');
            icon.className = 'fa-regular fa-heart me-1';
            label.textContent = 'Sauvegarder';
        }
    } catch(err) {
        this.submit(); // fallback sans JS
    }
});
</script>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
