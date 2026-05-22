/**
 * jquery.custom.js — Améliorations jQuery pour Dar Tunisie
 *
 * IMPORTANT : Ce fichier nécessite jQuery 3.x
 * Charger APRÈS Bootstrap et APRÈS app.js dans footer.php :
 *
 *   <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
 *   <script src="<?= url('public/assets/js/jquery.custom.js') ?>"></script>
 *
 * Sections :
 *  1. Formulaires — validation live + feedback visuel
 *  2. Admin table — filtre jQuery + tri colonnes
 *  3. Image upload — drag & drop sur les champs file
 *  4. Smooth scroll — ancres internes
 *  5. Navbar — scroll spy léger
 *  6. Stats cards — animation countUp jQuery
 *  7. Toast notifications — helper global showToast()
 */

'use strict';

$(function () {

    /* ═══════════════════════════════════════════════════════
       1. FORMULAIRES — validation live avec feedback Bootstrap
    ═══════════════════════════════════════════════════════ */

    // Marquer un champ comme valide/invalide en temps réel
    $('input[required], select[required], textarea[required]').on('blur input', function () {
        var $el = $(this);
        if ($el.val().trim() === '') {
            $el.addClass('is-invalid').removeClass('is-valid');
        } else {
            $el.addClass('is-valid').removeClass('is-invalid');
        }
    });

    // Empêcher la soumission si des champs requis sont vides
    $('form').on('submit', function (e) {
        var $form = $(this);
        var valid = true;

        $form.find('[required]').each(function () {
            if ($(this).val().trim() === '') {
                $(this).addClass('is-invalid');
                valid = false;
            }
        });

        if (!valid) {
            e.preventDefault();
            // Scroll vers le premier champ invalide
            var $first = $form.find('.is-invalid').first();
            if ($first.length) {
                $('html, body').animate({
                    scrollTop: $first.offset().top - 120
                }, 400);
                $first.focus();
            }
        }
    });


    /* ═══════════════════════════════════════════════════════
       2A. ADMIN TABLE — filtre jQuery sur les tableaux admin
           Usage : ajouter id="jquery-search" sur l'input
                   et class="jquery-filterable" sur le <tbody>
    ═══════════════════════════════════════════════════════ */
    $('#jquery-search').on('input', function () {
        var q = $(this).val().toLowerCase().trim();
        $('.jquery-filterable tr').each(function () {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(q));
        });

        // Afficher un message si aucun résultat
        var $tbody = $('.jquery-filterable');
        if (!$tbody.find('tr:visible').length) {
            if (!$tbody.find('.no-results-jquery').length) {
                var cols = $tbody.closest('table').find('thead th').length || 5;
                $tbody.append(
                    '<tr class="no-results-jquery">'
                    + '<td colspan="' + cols + '" class="text-center text-muted py-3">Aucun résultat pour "' + $('<span>').text($(this).val()).html() + '"</td>'
                    + '</tr>'
                );
            }
        } else {
            $tbody.find('.no-results-jquery').remove();
        }
    });


    /* ═══════════════════════════════════════════════════════
       2B. ADMIN TABLE — tri par colonne au clic sur <th>
           Ajouter class="sortable-table" sur le <table>
    ═══════════════════════════════════════════════════════ */
    $('.sortable-table thead th').css('cursor', 'pointer').on('click', function () {
        var $th    = $(this);
        var table  = $th.closest('table');
        var col    = $th.index();
        var asc    = $th.data('sort-asc') !== true;

        $th.data('sort-asc', asc);
        table.find('thead th').find('.sort-icon').remove();
        $th.append('<span class="sort-icon ms-1">' + (asc ? '↑' : '↓') + '</span>');

        var rows = table.find('tbody tr').toArray();
        rows.sort(function (a, b) {
            var valA = $(a).find('td').eq(col).text().trim().toLowerCase();
            var valB = $(b).find('td').eq(col).text().trim().toLowerCase();
            var numA = parseFloat(valA);
            var numB = parseFloat(valB);

            if (!isNaN(numA) && !isNaN(numB)) {
                return asc ? numA - numB : numB - numA;
            }
            return asc ? valA.localeCompare(valB, 'fr') : valB.localeCompare(valA, 'fr');
        });

        table.find('tbody').append(rows);
    });


    /* ═══════════════════════════════════════════════════════
       3. IMAGE UPLOAD — drag & drop visuel
          Fonctionne sur tous les input[type=file]
    ═══════════════════════════════════════════════════════ */
    $('input[type="file"]').each(function () {
        var $input = $(this);
        var $wrap  = $input.closest('.mb-4, .mb-3, div').first();

        $wrap.on('dragover dragenter', function (e) {
            e.preventDefault();
            $wrap.css('outline', '2px dashed var(--primary)');
        });

        $wrap.on('dragleave drop', function (e) {
            e.preventDefault();
            $wrap.css('outline', '');
            if (e.type === 'drop') {
                var dt = e.originalEvent.dataTransfer;
                if (dt && dt.files.length) {
                    $input[0].files = dt.files;
                    $input.trigger('change'); // déclenche la prévisualisation de app.js
                }
            }
        });
    });


    /* ═══════════════════════════════════════════════════════
       4. SMOOTH SCROLL — ancres internes (#section)
    ═══════════════════════════════════════════════════════ */
    $('a[href^="#"]').not('[data-bs-toggle]').on('click', function (e) {
        var target = $(this.hash);
        if (!target.length) return;
        e.preventDefault();
        $('html, body').animate({
            scrollTop: target.offset().top - 80
        }, 600, 'swing');
    });


    /* ═══════════════════════════════════════════════════════
       5. NAVBAR — fond opaque progressif au scroll
    ═══════════════════════════════════════════════════════ */
    var $navbar = $('.navbar');
    if ($navbar.length) {
        $(window).on('scroll.navbar', function () {
            var scrolled = $(this).scrollTop();
            // Augmenter progressivement l'ombre
            var alpha = Math.min(scrolled / 150, 0.15);
            $navbar.css('box-shadow', '0 2px 16px rgba(23,33,43,' + alpha + ')');
        });
    }


    /* ═══════════════════════════════════════════════════════
       6. STATS CARDS — animation countUp jQuery
          Ajouter class="count-up" data-target="42"
          sur les éléments <strong> des stats cards
    ═══════════════════════════════════════════════════════ */
    function animateCountUp($el) {
        var target   = parseInt($el.data('target'), 10) || 0;
        var duration = 1200;
        var start    = 0;
        var startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            // Easing : ease-out cubique
            var eased = 1 - Math.pow(1 - progress, 3);
            $el.text(Math.round(eased * target));
            if (progress < 1) requestAnimationFrame(step);
        }

        requestAnimationFrame(step);
    }

    // Observer : déclenche l'animation quand l'élément est visible
    if ('IntersectionObserver' in window) {
        var countObs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCountUp($(entry.target));
                    countObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        $('.count-up').each(function () { countObs.observe(this); });
    } else {
        // Fallback sans observer : affiche directement la valeur finale
        $('.count-up').each(function () {
            $(this).text($(this).data('target'));
        });
    }


    /* ═══════════════════════════════════════════════════════
       7. TOAST NOTIFICATIONS — helper global
          Usage depuis n'importe quel script :
          showToast('Copié !', 'success')
          showToast('Erreur réseau', 'danger')
    ═══════════════════════════════════════════════════════ */
    if (!$('#toast-container').length) {
        $('body').append(
            '<div id="toast-container" style="'
            + 'position:fixed;bottom:20px;right:20px;z-index:9999;display:flex;'
            + 'flex-direction:column;gap:8px"></div>'
        );
    }

    window.showToast = function (message, type) {
        type = type || 'info';
        var bgMap = {
            success : '#178a4c',
            danger  : '#dc3545',
            warning : '#e65100',
            info    : '#17212b'
        };
        var $toast = $('<div>')
            .text(message)
            .css({
                background    : bgMap[type] || bgMap.info,
                color         : '#fff',
                padding       : '10px 18px',
                borderRadius  : '10px',
                fontSize      : '14px',
                boxShadow     : '0 4px 16px rgba(0,0,0,.2)',
                opacity       : 0,
                transition    : 'opacity .3s ease'
            });

        $('#toast-container').append($toast);
        // Fade in
        setTimeout(function () { $toast.css('opacity', 1); }, 10);
        // Fade out et suppression
        setTimeout(function () {
            $toast.css('opacity', 0);
            setTimeout(function () { $toast.remove(); }, 300);
        }, 3500);
    };

});