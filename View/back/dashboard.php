<?php
$page_title  = 'Dashboard';
$active_menu = 'dashboard';
ob_start();

// Préparer les données pour les graphiques
$areaLabels  = array_column($byTypeCuisine, 'type_cuisine');
$areaData    = array_column($byTypeCuisine, 'total');

$pieLabels   = [];
$pieData     = [];
$catLabels   = ['entree'=>'Entrée','plat_principal'=>'Plat principal','dessert'=>'Dessert','boisson'=>'Boisson','snack'=>'Snack'];
foreach ($byCategorie as $row) {
    $pieLabels[] = $catLabels[$row['categorie']] ?? ucfirst($row['categorie']);
    $pieData[]   = (int)$row['total'];
}
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
.stats-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:20px; margin-bottom:32px; }
.stat-card  { background:#fff; border-radius:10px; padding:24px 20px; box-shadow:0 2px 10px rgba(0,0,0,.07); display:flex; align-items:center; gap:16px; }
.stat-icon  { width:56px; height:56px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.6rem; flex-shrink:0; }
.stat-icon.green  { background:#e8f5e9; }
.stat-icon.blue   { background:#e3f2fd; }
.stat-icon.red    { background:#ffebee; }
.stat-value { font-size:1.8rem; font-weight:700; color:#1a1a1a; line-height:1; }
.stat-label { font-size:.82rem; color:#888; margin-top:4px; }
.two-col    { display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-bottom:32px; }
.card-box   { background:#fff; border-radius:10px; padding:24px; box-shadow:0 2px 10px rgba(0,0,0,.07); }
.section-title { font-size:1.05rem; font-weight:700; color:#2e7d32; margin-bottom:16px; padding-bottom:8px; border-bottom:2px solid #e8f5e9; }
.mini-table { width:100%; border-collapse:collapse; font-size:.85rem; }
.mini-table th { background:#f5f5f5; padding:8px 12px; text-align:left; font-weight:600; color:#555; }
.mini-table td { padding:8px 12px; border-bottom:1px solid #f0f0f0; }
.mini-table tr:last-child td { border-bottom:none; }
.mini-table tr:hover td { background:#f9fbe7; }
.badge-cat    { display:inline-block; padding:2px 8px; border-radius:10px; font-size:.75rem; font-weight:600; background:#e8f5e9; color:#2e7d32; }
.badge-dispo  { background:#e8f5e9; color:#2e7d32; padding:2px 8px; border-radius:10px; font-size:.75rem; font-weight:600; }
.badge-indispo{ background:#ffebee; color:#c62828; padding:2px 8px; border-radius:10px; font-size:.75rem; font-weight:600; }
.img-xs   { width:36px; height:36px; object-fit:cover; border-radius:5px; }
.no-img-xs{ width:36px; height:36px; background:#eee; border-radius:5px; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
</style>

<!-- ── Cartes statistiques ──────────────────────────────────────────────── -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green">🍴</div>
        <div>
            <div class="stat-value"><?= $totalRestaurants ?></div>
            <div class="stat-label">Restaurants</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">🍽️</div>
        <div>
            <div class="stat-value"><?= $totalMeals ?></div>
            <div class="stat-label">Plats au total</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div>
            <div class="stat-value"><?= $totalDisponibles ?></div>
            <div class="stat-label">Plats disponibles</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">❌</div>
        <div>
            <div class="stat-value"><?= $totalIndisponibles ?></div>
            <div class="stat-label">Plats indisponibles</div>
        </div>
    </div>
</div>

<!-- ── Graphiques ───────────────────────────────────────────────────────── -->
<div class="two-col">

    <!-- Diagramme en aires — Restaurants par type de cuisine -->
    <div class="card-box">
        <div class="section-title">🍴 Restaurants par type de cuisine</div>
        <?php if (empty($byTypeCuisine)): ?>
            <p style="color:#aaa;text-align:center;padding:40px;">Aucune donnée.</p>
        <?php else: ?>
            <canvas id="areaChart" height="220"></canvas>
        <?php endif; ?>
    </div>

    <!-- Diagramme circulaire — Plats par catégorie -->
    <div class="card-box">
        <div class="section-title">🍽️ Plats par catégorie</div>
        <?php if (empty($byCategorie)): ?>
            <p style="color:#aaa;text-align:center;padding:40px;">Aucune donnée.</p>
        <?php else: ?>
            <canvas id="pieChart" height="220"></canvas>
        <?php endif; ?>
    </div>

</div>

<!-- ── Derniers ajouts ──────────────────────────────────────────────────── -->
<div class="two-col">

    <div class="card-box">
        <div class="section-title">🕐 Derniers restaurants ajoutés</div>
        <?php if (empty($lastRestaurants)): ?>
            <p style="color:#aaa;text-align:center;padding:20px;">Aucun restaurant.</p>
        <?php else: ?>
        <table class="mini-table">
            <thead><tr><th>Image</th><th>Nom</th><th>Cuisine</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($lastRestaurants as $r): ?>
            <tr>
                <td>
                    <?php if (!empty($r['image'])): ?>
                        <img src="/2A35/assets/uploads/restaurants/<?= htmlspecialchars($r['image']) ?>" class="img-xs" alt="">
                    <?php else: ?>
                        <div class="no-img-xs">🍴</div>
                    <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($r['nom']) ?></strong></td>
                <td><span class="badge-cat"><?= htmlspecialchars($r['type_cuisine']) ?></span></td>
                <td><a href="/2A35/Admin/restaurant/edit/<?= $r['id'] ?>" style="color:#2e7d32;">✏️</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <div style="text-align:right;margin-top:12px;">
            <a href="/2A35/Admin/restaurant" style="font-size:.82rem;color:#2e7d32;">Voir tous →</a>
        </div>
    </div>

    <div class="card-box">
        <div class="section-title">🕐 Derniers plats ajoutés</div>
        <?php if (empty($lastMeals)): ?>
            <p style="color:#aaa;text-align:center;padding:20px;">Aucun plat.</p>
        <?php else: ?>
        <table class="mini-table">
            <thead><tr><th>Nom</th><th>Restaurant</th><th>Prix</th><th>Dispo</th></tr></thead>
            <tbody>
            <?php foreach ($lastMeals as $m): ?>
            <tr>
                <td><strong><?= htmlspecialchars($m['nom']) ?></strong></td>
                <td style="color:#666;font-size:.82rem;"><?= htmlspecialchars($m['restaurant_nom']) ?></td>
                <td><?= number_format((float)$m['prix'], 2) ?> DT</td>
                <td>
                    <span class="<?= $m['disponible'] ? 'badge-dispo' : 'badge-indispo' ?>">
                        <?= $m['disponible'] ? '✅' : '❌' ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <div style="text-align:right;margin-top:12px;">
            <a href="/2A35/Admin/meal" style="font-size:.82rem;color:#2e7d32;">Voir tous →</a>
        </div>
    </div>

</div>

<script>
// ── Diagramme en aires — Restaurants par type de cuisine ──────────────────
<?php if (!empty($byTypeCuisine)): ?>
new Chart(document.getElementById('areaChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_map('ucfirst', $areaLabels)) ?>,
        datasets: [{
            label: 'Nombre de restaurants',
            data: <?= json_encode($areaData) ?>,
            fill: true,
            backgroundColor: 'rgba(46,125,50,0.15)',
            borderColor: '#2e7d32',
            borderWidth: 2,
            pointBackgroundColor: '#2e7d32',
            pointRadius: 5,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: {
                label: ctx => ' ' + ctx.parsed.y + ' restaurant' + (ctx.parsed.y > 1 ? 's' : '')
            }}
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
                grid: { color: '#f0f0f0' }
            },
            x: { grid: { display: false } }
        }
    }
});
<?php endif; ?>

// ── Diagramme circulaire — Plats par catégorie ────────────────────────────
<?php if (!empty($byCategorie)): ?>
new Chart(document.getElementById('pieChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($pieLabels) ?>,
        datasets: [{
            data: <?= json_encode($pieData) ?>,
            backgroundColor: [
                '#2e7d32','#1565c0','#e65100','#6a1b9a','#00838f'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 16, font: { size: 12 } }
            },
            tooltip: { callbacks: {
                label: ctx => ' ' + ctx.label + ' : ' + ctx.parsed + ' plat' + (ctx.parsed > 1 ? 's' : '')
            }}
        }
    }
});
<?php endif; ?>
</script>

<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
