<?php
$page_title  = 'Statistiques Recettes';
$active_menu = 'recette';
ob_start();
?>
<style>
.stats-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.stats-header h2 { font-size:1.5rem; font-weight:700; margin:0; animation:fadeInLeft .5s ease forwards; }
.btn-back { background:#e0e0e0; color:#333; padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:600; font-size:0.9rem; }
.btn-back:hover { background:#bdbdbd; }
.charts-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
@media(max-width:900px){ .charts-grid{ grid-template-columns:1fr; } }

@keyframes fadeInUp   { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
@keyframes fadeInLeft { from{opacity:0;transform:translateX(-30px)} to{opacity:1;transform:translateX(0)} }
@keyframes pulse      { 0%,100%{transform:scale(1)} 50%{transform:scale(1.08)} }

.summary-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:14px; margin-bottom:24px; }
.summary-card {
    background:#fff; border-radius:10px; padding:16px 20px;
    box-shadow:0 2px 8px rgba(0,0,0,.06); display:flex; align-items:center; gap:14px;
    opacity:0; animation:fadeInUp .5s ease forwards;
    transition:transform .3s,box-shadow .3s;
}
.summary-card:nth-child(1){animation-delay:.1s}
.summary-card:nth-child(2){animation-delay:.2s}
.summary-card:nth-child(3){animation-delay:.3s}
.summary-card:nth-child(4){animation-delay:.4s}
.summary-card:hover{ transform:translateY(-4px); box-shadow:0 8px 24px rgba(0,0,0,.12); }
.summary-icon { font-size:2rem; animation:pulse 2s infinite; }
.summary-num  { font-size:1.6rem; font-weight:700; color:#2e7d32; }
.summary-lbl  { font-size:0.78rem; color:#777; }

.chart-card {
    background:#fff; border-radius:12px; padding:24px;
    box-shadow:0 2px 12px rgba(0,0,0,.07);
    opacity:0; animation:fadeInUp .6s ease forwards;
    transition:transform .3s,box-shadow .3s;
}
.chart-card:nth-child(1){animation-delay:.5s}
.chart-card:nth-child(2){animation-delay:.7s}
.chart-card:nth-child(3){animation-delay:.9s}
.chart-card:hover{ transform:translateY(-6px); box-shadow:0 12px 30px rgba(0,0,0,.12); }
.chart-card h3 { font-size:1rem; font-weight:700; margin-bottom:18px; color:#1a1a1a; border-bottom:2px solid #f0f0f0; padding-bottom:10px; }
.chart-container { position:relative; height:280px; }
</style>

<div class="stats-header">
    <h2>📊 Statistiques des Recettes</h2>
    <a href="/2A35/Admin/recette" class="btn-back">← Retour à la liste</a>
</div>

<div class="summary-row">
    <div class="summary-card">
        <span class="summary-icon">🍽️</span>
        <div><div class="summary-num"><?= $totalRecettes ?></div><div class="summary-lbl">Total recettes</div></div>
    </div>
    <div class="summary-card">
        <span class="summary-icon">🗂️</span>
        <div><div class="summary-num"><?= count($statsCat) ?></div><div class="summary-lbl">Catégories</div></div>
    </div>
    <div class="summary-card">
        <span class="summary-icon">🔥</span>
        <div><div class="summary-num"><?= (int)($statsCal['moins300'] ?? 0) ?></div><div class="summary-lbl">Recettes &lt; 300 kcal</div></div>
    </div>
    <div class="summary-card">
        <span class="summary-icon">🟢</span>
        <div>
            <div class="summary-num"><?php
                $facile = 0;
                foreach ($statsDiff as $d) { if ($d['difficulte']==='facile') { $facile=$d['total']; break; } }
                echo $facile;
            ?></div>
            <div class="summary-lbl">Recettes faciles</div>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-card">
        <h3>📁 Recettes par catégorie</h3>
        <div class="chart-container"><canvas id="chartCat"></canvas></div>
    </div>
    <div class="chart-card">
        <h3>⚡ Répartition par difficulté</h3>
        <div class="chart-container"><canvas id="chartDiff"></canvas></div>
    </div>
    <div class="chart-card">
        <h3>🔥 Répartition calorique</h3>
        <div class="chart-container"><canvas id="chartCal"></canvas></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const COLORS = { vert:'#2e7d32', orange:'#f57c00', rouge:'#c62828', bleu:'#1565c0', jaune:'#f9a825' };
const animOpts = { animation:{ duration:1200, easing:'easeInOutQuart' } };

// Compteurs animés
document.querySelectorAll('.summary-num').forEach(el => {
    const val = parseInt(el.textContent);
    el.textContent = '0';
    let cur = 0; const pas = val / (1200/16);
    const t = setInterval(() => {
        cur += pas; if (cur >= val) { cur = val; clearInterval(t); }
        el.textContent = Math.round(cur);
    }, 16);
});

new Chart(document.getElementById('chartCat'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($statsCat, 'categorie')) ?>,
        datasets: [{ label:'Recettes', data: <?= json_encode(array_column($statsCat, 'total')) ?>,
            backgroundColor:[COLORS.vert,COLORS.orange,COLORS.bleu,COLORS.rouge,COLORS.jaune,'#7b1fa2','#00838f','#558b2f'],
            borderRadius:6, borderSkipped:false }]
    },
    options:{ ...animOpts, responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true, ticks:{ stepSize:1 } } } }
});

const diffLabels = <?= json_encode(array_column($statsDiff, 'difficulte')) ?>;
const diffColors = diffLabels.map(l => l==='facile' ? COLORS.vert : l==='moyen' ? COLORS.orange : COLORS.rouge);
new Chart(document.getElementById('chartDiff'), {
    type: 'pie',
    data: { labels: diffLabels,
        datasets:[{ data: <?= json_encode(array_column($statsDiff, 'total')) ?>,
            backgroundColor:diffColors, borderWidth:2, borderColor:'#fff' }] },
    options:{ ...animOpts, responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } } }
});

new Chart(document.getElementById('chartCal'), {
    type: 'doughnut',
    data: { labels:['< 300 kcal','300–600 kcal','600–900 kcal','> 900 kcal'],
        datasets:[{ data:[<?= (int)($statsCal['moins300']??0) ?>,<?= (int)($statsCal['entre300_600']??0) ?>,<?= (int)($statsCal['entre600_900']??0) ?>,<?= (int)($statsCal['plus900']??0) ?>],
            backgroundColor:[COLORS.vert,COLORS.jaune,COLORS.orange,COLORS.rouge], borderWidth:2, borderColor:'#fff' }] },
    options:{ ...animOpts, responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } } }
});
</script>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
