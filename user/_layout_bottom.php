        </div><!-- /ud-content -->
    </div><!-- /ud-main -->
</div><!-- /ud-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* Sidebar mobile toggle */
const sidebar  = document.getElementById('udSidebar');
const overlay  = document.getElementById('udOverlay');
const toggle   = document.getElementById('udMenuToggle');

function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('show'); }
function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }

if (toggle)  toggle.addEventListener('click', openSidebar);
if (overlay) overlay.addEventListener('click', closeSidebar);

/* Password strength meter */
const pwInput = document.getElementById('new_password');
const fill    = document.getElementById('pwStrengthFill');
const label   = document.getElementById('pwStrengthLabel');

if (pwInput && fill) {
    pwInput.addEventListener('input', () => {
        const v = pwInput.value;
        let score = 0;
        if (v.length >= 8)  score++;
        if (/[A-Z]/.test(v)) score++;
        if (/[0-9]/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;

        const pct   = score * 25;
        const color = ['#e0e0e0','#f44336','#ff9800','#2196f3','#4caf50'][score];
        const text  = ['','Faible','Moyen','Bon','Fort'][score];

        fill.style.width = pct + '%';
        fill.style.background = color;
        if (label) label.textContent = text;
    });
}

/* Confirm password match */
const confirmPw = document.getElementById('confirm_password');
if (confirmPw && pwInput) {
    confirmPw.addEventListener('input', () => {
        if (confirmPw.value && confirmPw.value !== pwInput.value) {
            confirmPw.classList.add('is-invalid');
        } else {
            confirmPw.classList.remove('is-invalid');
        }
    });
}

/* Auto-dismiss alerts after 5s */
document.querySelectorAll('.ud-alert').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity .5s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
    }, 5000);
});
</script>
</body>
</html>
