<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Contact';
$settings = app_settings();
$old = [
    'full_name' => '',
    'phone' => '',
    'email' => '',
    'subject' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $old = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'subject' => trim($_POST['subject'] ?? ''),
        'message' => trim($_POST['message'] ?? ''),
    ];

    if ($old['full_name'] === '' || $old['phone'] === '' || $old['subject'] === '' || $old['message'] === '') {
        flash('danger', 'Veuillez remplir tous les champs obligatoires.');
    } elseif ($old['email'] !== '' && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        flash('danger', 'Veuillez saisir une adresse email valide.');
    } elseif (!preg_match('/^[0-9+\s]{8,20}$/', $old['phone'])) {
        flash('danger', 'Veuillez saisir un numero de telephone tunisien valide.');
    } else {
        $stmt = db()->prepare(
            'INSERT INTO contact_messages (full_name, phone, email, subject, message)
             VALUES (:full_name, :phone, :email, :subject, :message)'
        );

        $stmt->execute([
            'full_name' => $old['full_name'],
            'phone' => $old['phone'],
            'email' => $old['email'] !== '' ? $old['email'] : null,
            'subject' => $old['subject'],
            'message' => $old['message'],
        ]);

        flash('success', 'Votre message a ete envoye avec succes. Notre equipe vous contactera rapidement.');
        redirect('contact.php');
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="contact-hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="contact-badge">Agence immobiliere en Tunisie</span>
                <h1>Contactez <?= h($settings['agency_name']) ?></h1>
                <p>
                    Vous cherchez une maison, un appartement, une villa ou un studio a louer ?
                    Envoyez-nous votre demande et notre equipe vous accompagne selon votre budget et votre ville.
                </p>
            </div>
            <div class="col-lg-5">
                <div class="contact-quick-card">
                    <h5>Besoin d'une reponse rapide ?</h5>
                    <p class="mb-3">Appelez-nous ou contactez-nous sur WhatsApp.</p>
                    <div class="d-flex flex-column gap-2">
                        <a href="tel:<?= h(str_replace(' ', '', $settings['phone'])) ?>" class="btn btn-primary">
                            <i class="fa-solid fa-phone me-2"></i><?= h($settings['phone']) ?>
                        </a>
                        <a href="https://wa.me/<?= h(preg_replace('/[^0-9]/', '', $settings['whatsapp'])) ?>" target="_blank" class="btn btn-success">
                            <i class="fa-brands fa-whatsapp me-2"></i><?= h($settings['whatsapp']) ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="contact-page section-padding">
    <div class="container">
        <?php show_flash(); ?>

        <div class="row g-4 align-items-start">
            <div class="col-lg-5">
                <div class="contact-info-panel">
                    <div class="section-title mb-4">
                        <small>Nos informations</small>
                        <h2><?= h($settings['agency_name']) ?></h2>
                        <p class="text-muted mb-0"><?= h($settings['slogan']) ?></p>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-icon bg-orange">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <h6>Telephone</h6>
                            <p><?= h($settings['phone']) ?></p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-icon bg-green">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div>
                            <h6>WhatsApp</h6>
                            <p><?= h($settings['whatsapp']) ?></p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-icon bg-blue">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <h6>Email</h6>
                            <p><?= h($settings['email']) ?></p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-icon bg-red">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <h6>Adresse</h6>
                            <p>
                                <?= h($settings['address']) ?><br>
                                <?= h($settings['city']) ?>, <?= h($settings['governorate']) ?>, Tunisie
                            </p>
                        </div>
                    </div>

                    <div class="contact-info-item mb-0">
                        <div class="contact-icon bg-dark-custom">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <h6>Horaires</h6>
                            <p><?= h($settings['working_hours']) ?></p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($settings['map_embed_url'])): ?>
                    <div class="contact-map-card mt-4">
                        <h5 class="mb-3">
                            <i class="fa-solid fa-map-location-dot text-warning me-2"></i>
                            Nous trouver
                        </h5>
                        <div class="contact-map">
                            <iframe
                                src="<?= h($settings['map_embed_url']) ?>"
                                loading="lazy"
                                allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-7">
                <div class="contact-form-card">
                    <div class="mb-4">
                        <span class="contact-badge">Envoyer une demande</span>
                        <h3 class="fw-bold mt-2 mb-2">Parlez-nous de votre besoin</h3>
                        <p class="text-muted mb-0">
                            Precisez le type de bien, la ville, votre budget en TND et la periode souhaitee.
                        </p>
                    </div>

                    <form method="post">
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom complet *</label>
                                <input
                                    type="text"
                                    name="full_name"
                                    class="form-control"
                                    value="<?= h($old['full_name']) ?>"
                                    placeholder="Ex: Ahmed Ben Ali"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Telephone *</label>
                                <input
                                    type="tel"
                                    name="phone"
                                    class="form-control"
                                    value="<?= h($old['phone']) ?>"
                                    placeholder="+216 55 123 456"
                                    inputmode="tel"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?= h($old['email']) ?>"
                                    placeholder="exemple@email.com">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Sujet *</label>
                                <input
                                    type="text"
                                    name="subject"
                                    class="form-control"
                                    value="<?= h($old['subject']) ?>"
                                    placeholder="Location appartement S+2"
                                    required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Message *</label>
                                <textarea
                                    name="message"
                                    rows="7"
                                    class="form-control"
                                    placeholder="Ex: Je cherche un appartement S+2 a La Marsa, budget maximum 1500 TND par mois..."
                                    required><?= h($old['message']) ?></textarea>
                            </div>

                            <div class="col-12 d-flex flex-wrap gap-2 align-items-center">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa-solid fa-paper-plane me-2"></i>
                                    Envoyer le message
                                </button>
                                <span class="text-muted small">
                                    Reponse generalement sous 24h.
                                </span>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="contact-note mt-4">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    Vos informations restent confidentielles et sont utilisees uniquement pour traiter votre demande immobiliere.
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>