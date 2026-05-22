<?php $settings = app_settings(); ?>
</main>
<footer class="footer mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <h5><?= h($settings['agency_name']) ?></h5>
                <p><?= h($settings['slogan']) ?>. Service personnalise pour appartements, maisons, villas et studios en Tunisie.</p>
            </div>
            <div class="col-md-3">
                <h6>Contact</h6>
                <p class="mb-1"><i class="fa-solid fa-phone"></i> <?= h($settings['phone']) ?></p>
                <p class="mb-1"><i class="fa-brands fa-whatsapp"></i> <?= h($settings['whatsapp']) ?></p>
                <p class="mb-0"><i class="fa-solid fa-envelope"></i> <?= h($settings['email']) ?></p>
            </div>
            <div class="col-md-4">
                <h6>Adresse</h6>
                <p class="mb-1"><?= h($settings['address']) ?></p>
                <p class="mb-0"><?= h($settings['city']) ?>, <?= h($settings['governorate']) ?></p>
            </div>
        </div>
        <hr>
        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 small">
            <span>© <?= date('Y') ?> <?= h($settings['agency_name']) ?>. Tous droits reserves.</span>
            
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('public/assets/js/app.js') ?>"></script>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= url('public/assets/js/app.js') ?>"></script>
<script src="<?= url('public/assets/js/jquery.custom.js') ?>"></script>
