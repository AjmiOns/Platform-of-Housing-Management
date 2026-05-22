<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Dashboard';

$counts = [
    'properties' => (int) db()->query('SELECT COUNT(*) FROM properties')->fetchColumn(),
    'available'  => (int) db()->query("SELECT COUNT(*) FROM properties WHERE availability_status = 'available'")->fetchColumn(),
    'reserved'   => (int) db()->query("SELECT COUNT(*) FROM properties WHERE availability_status = 'reserved'")->fetchColumn(),
    'rented'     => (int) db()->query("SELECT COUNT(*) FROM properties WHERE availability_status = 'rented'")->fetchColumn(),
    'visits'     => (int) db()->query("SELECT COUNT(*) FROM visit_requests WHERE status = 'new'")->fetchColumn(),
    'messages'   => (int) db()->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn(),
    'owners'     => (int) db()->query('SELECT COUNT(*) FROM owners')->fetchColumn(),
];

// Répartition par catégorie
$byCategory = db()->query(
    'SELECT c.name, COUNT(p.id) AS total
     FROM categories c
     LEFT JOIN properties p ON p.category_id = c.id
     GROUP BY c.id, c.name
     ORDER BY total DESC'
)->fetchAll();

// Répartition par gouvernorat (top 6)
$byGov = db()->query(
    'SELECT governorate, COUNT(*) AS total
     FROM properties
     WHERE published = 1
     GROUP BY governorate
     ORDER BY total DESC
     LIMIT 6'
)->fetchAll();

// Biens ajoutés par mois (6 derniers mois)
$byMonth = db()->query(
    "SELECT DATE_FORMAT(created_at, '%b %Y') AS month,
            DATE_FORMAT(created_at, '%Y-%m') AS month_key,
            COUNT(*) AS total
     FROM properties
     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY month_key, month
     ORDER BY month_key ASC"
)->fetchAll();

// Statuts des visites
$visitStatuses = db()->query(
    "SELECT status, COUNT(*) AS total FROM visit_requests GROUP BY status"
)->fetchAll();

// 5 dernières visites
$latestVisits = db()->query(
    'SELECT v.*, 
            p.title AS property_title,
            c.nom AS client_name,
            c.telephone AS client_phone
     FROM visit_requests v
     JOIN properties p ON p.id = v.property_id
     JOIN clients c ON c.id = v.client_id
     ORDER BY v.created_at DESC
     LIMIT 5'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<style>
.dash-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 60%, #0f4c81 100%);
    border-radius: 16px;
    padding: 2rem 2.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}
.dash-hero::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 220px; height: 220px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
}
.dash-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; left: 30%;
    width: 300px; height: 300px;
    background: rgba(99,179,237,0.07);
    border-radius: 50%;
}
.dash-hero h1 { color: #fff; font-size: 1.6rem; font-weight: 700; margin: 0 0 4px; }
.dash-hero p  { color: rgba(255,255,255,0.55); margin: 0; font-size: 0.9rem; }

.kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
@media(max-width:768px){ .kpi-grid { grid-template-columns: repeat(2,1fr); } }

.kpi-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.4rem 1.5rem;
    border: 1px solid #e8ecf0;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: box-shadow .2s, transform .2s;
}
.kpi-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.08); transform: translateY(-2px); }
.kpi-icon {
    width: 50px; height: 50px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; flex-shrink: 0;
}
.kpi-icon.blue   { background: #dbeafe; }
.kpi-icon.green  { background: #dcfce7; }
.kpi-icon.amber  { background: #fef3c7; }
.kpi-icon.purple { background: #ede9fe; }
.kpi-icon.rose   { background: #ffe4e6; }
.kpi-icon.teal   { background: #ccfbf1; }
.kpi-icon.indigo { background: #e0e7ff; }

.kpi-num { font-size: 1.8rem; font-weight: 700; color: #0f172a; line-height: 1; }
.kpi-lbl { font-size: 0.78rem; color: #64748b; margin-top: 3px; text-transform: uppercase; letter-spacing: .04em; }

.chart-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
@media(max-width:1100px){ .chart-grid { grid-template-columns: repeat(2, 1fr); } }
@media(max-width:600px){ .chart-grid { grid-template-columns: 1fr; } }

.chart-card {
    background: #fff;
    border-radius: 14px;
    padding: 1rem 1.1rem;
    border: 1px solid #e8ecf0;
}
.chart-card-full { grid-column: 1 / -1; }
.chart-title { font-size: .95rem; font-weight: 600; color: #0f172a; margin-bottom: 1.2rem; display: flex; align-items: center; gap: .5rem; }
.chart-title .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

.visits-table { background: #fff; border-radius: 14px; padding: 1.5rem; border: 1px solid #e8ecf0; }
.visits-table table { width: 100%; border-collapse: collapse; font-size: .875rem; }
.visits-table thead th { color: #94a3b8; font-weight: 600; text-transform: uppercase; font-size: .72rem; letter-spacing: .06em; padding: 0 0 .75rem; border-bottom: 1px solid #f1f5f9; }
.visits-table tbody td { padding: .75rem 0; border-bottom: 1px solid #f8fafc; color: #334155; }
.visits-table tbody tr:last-child td { border-bottom: none; }
.status-pill { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: .75rem; font-weight: 600; }
.status-new        { background: #dbeafe; color: #1d4ed8; }
.status-confirmed  { background: #dcfce7; color: #15803d; }
.status-cancelled  { background: #fee2e2; color: #b91c1c; }
.status-done       { background: #f1f5f9; color: #64748b; }

.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.section-header h2 { font-size: .95rem; font-weight: 600; color: #0f172a; margin: 0; }
</style>

<section class="section-padding admin-layout">
<div class="container">
<?php require __DIR__ . '/_menu.php'; ?>
<?php show_flash(); ?>

<!-- Hero -->
<div class="dash-hero">
    <h1>👋 Bienvenue, <?= h(current_user()['name']) ?></h1>
    <p>Tableau de bord — <?= date('l d F Y') ?></p>
</div>

<!-- KPIs -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon blue">🏠</div>
        <div>
            <div class="kpi-num"><?= $counts['properties'] ?></div>
            <div class="kpi-lbl">Total biens</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon green">✅</div>
        <div>
            <div class="kpi-num"><?= $counts['available'] ?></div>
            <div class="kpi-lbl">Disponibles</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon amber">📅</div>
        <div>
            <div class="kpi-num"><?= $counts['visits'] ?></div>
            <div class="kpi-lbl">Visites nouvelles</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon rose">✉️</div>
        <div>
            <div class="kpi-num"><?= $counts['messages'] ?></div>
            <div class="kpi-lbl">Messages nouveaux</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon purple">🔒</div>
        <div>
            <div class="kpi-num"><?= $counts['reserved'] ?></div>
            <div class="kpi-lbl">Réservés</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon teal">🔑</div>
        <div>
            <div class="kpi-num"><?= $counts['rented'] ?></div>
            <div class="kpi-lbl">Loués</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon indigo">👤</div>
        <div>
            <div class="kpi-num"><?= $counts['owners'] ?></div>
            <div class="kpi-lbl">Propriétaires</div>
        </div>
    </div>
</div>

<!-- Charts row -->
<div class="chart-grid">

    <!-- Donut: statut des biens -->
    <div class="chart-card">
        <div class="chart-title"><span class="dot" style="background:#3b82f6"></span>Statut des biens</div>
        <canvas id="chartStatus" height="150"></canvas>
    </div>

    <!-- Donut: statut des visites -->
    <div class="chart-card">
        <div class="chart-title"><span class="dot" style="background:#f59e0b"></span>Statut des visites</div>
        <canvas id="chartVisits" height="150"></canvas>
    </div>

    <!-- Bar: par catégorie -->
    <div class="chart-card">
        <div class="chart-title"><span class="dot" style="background:#8b5cf6"></span>Biens par catégorie</div>
        <canvas id="chartCategory" height="150"></canvas>
    </div>

    <!-- Bar: par gouvernorat -->
    <div class="chart-card">
        <div class="chart-title"><span class="dot" style="background:#10b981"></span>Top gouvernorats</div>
        <canvas id="chartGov" height="150"></canvas>
    </div>

    <?php if (!empty($byMonth)): ?>
    <!-- Line: évolution mensuelle -->
    <div class="chart-card chart-card-full">
        <div class="chart-title"><span class="dot" style="background:#0f4c81"></span>Biens ajoutés — 6 derniers mois</div>
        <canvas id="chartMonthly" height="120"></canvas>
    </div>
    <?php endif; ?>

</div>

<!-- Dernières visites -->
<div class="visits-table">
    <div class="section-header">
        <h2>📋 Dernières demandes de visite</h2>
        <a href="<?= url('admin/visits.php') ?>" class="btn btn-sm btn-primary">Voir tout</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Bien</th>
                    <th>Date souhaitée</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($latestVisits): ?>
                <?php foreach ($latestVisits as $visit): ?>
<tr>

    <td>
        <strong><?= h($visit['client_name']) ?></strong><br>

        <span style="font-size:.85rem;color:#64748b">
            <?= h($visit['client_phone']) ?>
        </span>
    </td>

    <td>
        <?= h($visit['property_title']) ?>
    </td>

    <td>
        <?= h($visit['visit_date']) ?>
    </td>

    <td>
        <?php
        $labels = [
            'new' => 'Nouveau',
            'confirmed' => 'Confirmé',
            'cancelled' => 'Annulé',
            'done' => 'Effectué'
        ];

        $cls = 'status-' . $visit['status'];
        ?>

        <span class="status-pill <?= $cls ?>">
            <?= $labels[$visit['status']] ?? $visit['status'] ?>
        </span>
    </td>

</tr>
<?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" style="color:#94a3b8;text-align:center;padding:2rem 0">Aucune demande de visite.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
Chart.defaults.color = '#64748b';

const palette = {
    blue:   '#3b82f6',
    green:  '#10b981',
    amber:  '#f59e0b',
    purple: '#8b5cf6',
    rose:   '#f43f5e',
    teal:   '#14b8a6',
    indigo: '#6366f1',
    slate:  '#94a3b8',
};

// 1. Statut des biens (Donut)
new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: ['Disponible', 'Réservé', 'Loué'],
        datasets: [{
            data: [<?= $counts['available'] ?>, <?= $counts['reserved'] ?>, <?= $counts['rented'] ?>],
            backgroundColor: [palette.green, palette.amber, palette.slate],
            borderWidth: 2, borderColor: '#fff',
        }]
    },
    options: {
        cutout: '68%',
        plugins: { legend: { position: 'bottom', labels: { padding: 16, boxWidth: 12 } } }
    }
});

// 2. Statut des visites (Donut)
<?php
$vsData   = [];
$vsLabels = [];
$vsColors = ['new'=>'#3b82f6','confirmed'=>'#10b981','cancelled'=>'#f43f5e','done'=>'#94a3b8'];
$vsNames  = ['new'=>'Nouveau','confirmed'=>'Confirmé','cancelled'=>'Annulé','done'=>'Effectué'];
foreach ($visitStatuses as $row) {
    $vsLabels[] = $vsNames[$row['status']] ?? $row['status'];
    $vsData[]   = $row['total'];
    $vsBg[]     = $vsColors[$row['status']] ?? '#cbd5e1';
}
?>
new Chart(document.getElementById('chartVisits'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($vsLabels) ?>,
        datasets: [{
            data: <?= json_encode($vsData) ?>,
            backgroundColor: <?= json_encode($vsBg ?? []) ?>,
            borderWidth: 2, borderColor: '#fff',
        }]
    },
    options: {
        cutout: '68%',
        plugins: { legend: { position: 'bottom', labels: { padding: 16, boxWidth: 12 } } }
    }
});

// 3. Biens par catégorie (Bar horizontal)
<?php
$catLabels = array_column($byCategory, 'name');
$catData   = array_column($byCategory, 'total');
$catColors = ['#3b82f6','#8b5cf6','#10b981','#f59e0b','#f43f5e','#14b8a6'];
?>
new Chart(document.getElementById('chartCategory'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($catLabels) ?>,
        datasets: [{
            label: 'Biens',
            data: <?= json_encode($catData) ?>,
            backgroundColor: <?= json_encode(array_slice($catColors, 0, count($catLabels))) ?>,
            borderRadius: 6, borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: '#f1f5f9' }, ticks: { stepSize: 1 } },
            y: { grid: { display: false } }
        }
    }
});

// 4. Top gouvernorats (Bar)
<?php
$govLabels = array_column($byGov, 'governorate');
$govData   = array_column($byGov, 'total');
?>
new Chart(document.getElementById('chartGov'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($govLabels) ?>,
        datasets: [{
            label: 'Biens',
            data: <?= json_encode($govData) ?>,
            backgroundColor: '#10b981',
            borderRadius: 6, borderSkipped: false,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { grid: { color: '#f1f5f9' }, ticks: { stepSize: 1 } }
        }
    }
});

// 5. Évolution mensuelle (Line)
<?php if (!empty($byMonth)): ?>
<?php
$mLabels = array_column($byMonth, 'month');
$mData   = array_column($byMonth, 'total');
?>
new Chart(document.getElementById('chartMonthly'), {
    type: 'line',
    data: {
        labels: <?= json_encode($mLabels) ?>,
        datasets: [{
            label: 'Biens ajoutés',
            data: <?= json_encode($mData) ?>,
            borderColor: '#0f4c81',
            backgroundColor: 'rgba(15,76,129,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: '#0f4c81',
            pointRadius: 5,
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { grid: { color: '#f1f5f9' }, ticks: { stepSize: 1 } }
        }
    }
});
<?php endif; ?>
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>