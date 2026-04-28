<?php
$page_title  = 'Dashboard';
$active_menu = 'dashboard';
ob_start();

$facile = 0;
foreach ($statsDiff as $d) { if ($d['difficulte']==='facile') { $facile=$d['total']; break; } }
?>
<style>
@keyframes fadeInUp   { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
@keyframes fadeInLeft { from{opacity:0;transform:translateX(-30px)} to{opacity:1;transform:translateX(0)} }
@keyframes pulse      { 0%,100%{transform:scale(1)} 50%{transform:scale(1.08)} }

.dash-title { font-size:1.5rem; font-weight:700; margin-bottom:24px; animation:fadeInLeft .5s ease forwards; display:flex; align-items:center; gap:10px; }

.kpi-row { display:grid; grid-template-columns:repeat(6,1fr); gap:14px; margin-bottom:24px; }
@media(max-width:1100px){ .kpi-row{ grid-template-columns:repeat(3,1fr); } }
@media(max-width:600px) { .kpi-row{ grid-template-columns:repeat(2,1fr); } }

.kpi-card {
    background:#fff; border-radius:12px; padding:16px 18px;
    box-shadow:0 2px 8px rgba(0,0,0,.06); display:flex; align-items:center; gap:12px;
    opacity:0; animation:fadeInUp .5s ease forwards; transition:transform .25s,box-shadow .25s;
}
.kpi-card:hover { transform:translateY(-4px); box-shadow:0 8px 22px rgba(0,0,0,.1); }
.kpi-card:nth-child(1){animation-delay:.05s} .kpi-card:nth-child(2){animation-delay:.1s}
.kpi-card:nth-child(3){animation-delay:.15s} .kpi-card:nth-child(4){animation-delay:.2s}
.kpi-card:nth-child(5){animation-delay:.25s} .kpi-card:nth-child(6){animation-delay:.3s}
.kpi-icon { font-size:2rem; animation:pulse 2.5s infinite; }
.kpi-num  { font-size:1.6rem; font-weight:800; color:#2e7d32; }
.kpi-lbl  { font-size:0.75rem; color:#888; }

.section-hdr { display:flex; align-items:center; gap:8px; font-size:1rem; font-weight:700; color:#1a1a1a; margin:24px 0 14px; padding-bottom:8px; border-bottom:2px solid #f0f0f0; }

.charts-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:16px; }
.charts-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media(max-width:900px){ .charts-3,.charts-2{ grid-template-columns:1fr; } }

.chart-card {
    background:#fff; border-radius:14px; padding:22px;
    box-shadow:0 2px 10px rgba(0,0,0,.07);
    opacity:0; animation:fadeInUp .6s ease forwards; transition:transform .25s,box-shadow .25s;
}
.chart-card:hover { transform:translateY(-4px); box-shadow:0 10px 26px rgba(0,0,0,.1); }
.chart-card h3 { font-size:0.88rem; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:.05em; margin-bottom:16px; display:flex; align-items:center; gap:6px; }
.chart-container { position:relative; height:250px; }

.charts-3 .chart-card:nth-child(1){animation-delay:.2s}
.charts-3 .chart-card:nth-child(2){animation-delay:.35s}
.charts-3 .chart-card:nth-child(3){animation-delay:.5s}
.charts-2 .chart-card:nth-child(1){animation-delay:.2s}
.charts-2 .chart-card:nth-child(2){animation-delay:.35s}
</style>

<div class="dash-title"><span>📊</span> Dashboard — Statistiques</div>

<!-- KPI -->
<div class="kpi-row">
    <div class="kpi-card">
        <span class="kpi-icon">🍽️</span>
        <div><div class="kpi-num" data-val="<?= $totalRecettes ?>"><?= $totalRecettes ?></div><div class="kpi-lbl">Total Recettes</div></div>
    </div>
    <div class="kpi-card">
        <span class="kpi-icon">🗂️</span>
        <div><div class="kpi-num" data-val="<?= count($statsCat) ?>"><?= count($statsCat) ?></div><div class="kpi-lbl">Catégories</div></div>
    </div>
    <div class="kpi-card">
        <span class="kpi-icon">🟢</span>
        <div><div class="kpi-num" data-val="<?= $facile ?>"><?= $facile ?></div><div class="kpi-lbl">Recettes faciles</div></div>
    </div>
    <div class="kpi-card">
        <span class="kpi-icon">🥦</span>
        <div><div class="kpi-num" data-val="<?= $totalIngredients ?>"><?= $totalIngredients ?></div><div class="kpi-lbl">Total Ingrédients</div></div>
    </div>
    <div class="kpi-card">
        <span class="kpi-icon">💪</span>
        <div><div class="kpi-num" data-val="<?= round($moyennes['moy_prot']??0,1) ?>"><?= round($moyennes['moy_prot']??0,1) ?>g</div><div class="kpi-lbl">Moy. Protéines</div></div>
    </div>
    <div class="kpi-card">
        <span class="kpi-icon">🔥</span>
        <div><div class="kpi-num" data-val="<?= (int)($statsCal['moins300']??0) ?>"><?= (int)($statsCal['moins300']??0) ?></div><div class="kpi-lbl">Recettes &lt;300 kcal</div></div>
    </div>
</div>

<!-- Recettes -->
<div class="section-hdr"><span>🍽️</span> Statistiques des Recettes</div>
<div class="charts-3">
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

<!-- Ingrédients -->
<div class="section-hdr"><span>🥦</span> Statistiques des Ingrédients</div>
<div class="charts-2">
    <div class="chart-card">
        <h3>🗂 Ingrédients par type</h3>
        <div class="chart-container"><canvas id="chartType"></canvas></div>
    </div>
    <div class="chart-card">
        <h3>🧪 Moyennes nutritionnelles (g/100g)</h3>
        <div class="chart-container"><canvas id="chartNutri"></canvas></div>
    </div>
</div>

<!-- Dernières recettes + derniers ingrédients -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:24px;margin-bottom:8px;">

    <!-- Dernières recettes -->
    <div class="chart-card" style="animation-delay:.2s;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;padding-bottom:12px;border-bottom:2px solid #f0f0f0;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:1.2rem;">⏱️</span>
                <span style="font-size:1rem;font-weight:700;color:#1a1a1a;">Dernières recettes ajoutées</span>
            </div>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:0.88rem;">
            <thead>
                <tr style="background:#f9f9f9;">
                    <th style="padding:8px 10px;text-align:left;color:#aaa;font-weight:600;font-size:0.75rem;text-transform:uppercase;">Image</th>
                    <th style="padding:8px 10px;text-align:left;color:#aaa;font-weight:600;font-size:0.75rem;text-transform:uppercase;">Recette</th>
                    <th style="padding:8px 10px;text-align:left;color:#aaa;font-weight:600;font-size:0.75rem;text-transform:uppercase;">Catégorie</th>
                    <th style="padding:8px 10px;text-align:left;color:#aaa;font-weight:600;font-size:0.75rem;text-transform:uppercase;">Durée</th>
                    <th style="padding:8px 10px;"></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($dernieresRecettes as $r): ?>
            <tr style="border-bottom:1px solid #f5f5f5;transition:background .15s;" onmouseover="this.style.background='#f9fbe7'" onmouseout="this.style.background=''">
                <td style="padding:10px;">
                    <?php if ($r['image']): ?>
                        <img src="/2A35/assets/uploads/recettes/<?= htmlspecialchars($r['image']) ?>"
                             style="width:48px;height:48px;object-fit:cover;border-radius:10px;border:1px solid #eee;" alt="">
                    <?php else: ?>
                        <div style="width:48px;height:48px;background:#f1f8e9;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;border:1px solid #eee;">🍽️</div>
                    <?php endif; ?>
                </td>
                <td style="padding:10px;font-weight:600;color:#1a1a1a;"><?= htmlspecialchars($r['nom']) ?></td>
                <td style="padding:10px;">
                    <span style="background:#e8f5e9;color:#2e7d32;padding:3px 10px;border-radius:20px;font-size:0.78rem;font-weight:600;">
                        <?= htmlspecialchars($r['categorie']) ?>
                    </span>
                </td>
                <td style="padding:10px;color:#555;">⏱ <?= $r['duree'] ?> min</td>
                <td style="padding:10px;">
                    <a href="/2A35/Admin/recette/edit/<?= $r['id'] ?>" style="color:#bbb;font-size:1rem;text-decoration:none;" title="Modifier">✏️</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div style="text-align:right;margin-top:14px;">
            <a href="/2A35/Admin/recette" style="color:#2e7d32;font-size:0.85rem;font-weight:600;text-decoration:none;">
                Voir toutes les recettes →
            </a>
        </div>
    </div>

    <!-- Derniers ingrédients -->
    <div class="chart-card" style="animation-delay:.35s;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;padding-bottom:12px;border-bottom:2px solid #f0f0f0;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:1.2rem;">⏱️</span>
                <span style="font-size:1rem;font-weight:700;color:#1a1a1a;">Derniers ingrédients ajoutés</span>
            </div>
        </div>
        <?php
        $typeColors = [
            'legume'=>['bg'=>'#e8f5e9','text'=>'#2e7d32','icon'=>'🥕'],
            'fruit'=>['bg'=>'#fff3e0','text'=>'#f57c00','icon'=>'🍎'],
            'produit-laitier'=>['bg'=>'#e3f2fd','text'=>'#1565c0','icon'=>'🥛'],
            'epice'=>['bg'=>'#fbe9e7','text'=>'#bf360c','icon'=>'🌶️'],
            'viande'=>['bg'=>'#fce4ec','text'=>'#c62828','icon'=>'🥩'],
            'cereale'=>['bg'=>'#f9fbe7','text'=>'#827717','icon'=>'🌾'],
            'autre'=>['bg'=>'#f5f5f5','text'=>'#616161','icon'=>'🧂'],
        ];
        ?>
        <table style="width:100%;border-collapse:collapse;font-size:0.88rem;">
            <thead>
                <tr style="background:#f9f9f9;">
                    <th style="padding:8px 10px;text-align:left;color:#aaa;font-weight:600;font-size:0.75rem;text-transform:uppercase;">Image</th>
                    <th style="padding:8px 10px;text-align:left;color:#aaa;font-weight:600;font-size:0.75rem;text-transform:uppercase;">Nom</th>
                    <th style="padding:8px 10px;text-align:left;color:#aaa;font-weight:600;font-size:0.75rem;text-transform:uppercase;">Type</th>
                    <th style="padding:8px 10px;"></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($derniersIngredients as $ing):
                $tc = $typeColors[$ing['type']] ?? $typeColors['autre'];
            ?>
            <tr style="border-bottom:1px solid #f5f5f5;transition:background .15s;" onmouseover="this.style.background='#f9fbe7'" onmouseout="this.style.background=''">
                <td style="padding:10px;">
                    <?php if ($ing['image']): ?>
                        <img src="/2A35/assets/uploads/ingredients/<?= htmlspecialchars($ing['image']) ?>"
                             style="width:48px;height:48px;object-fit:cover;border-radius:10px;border:1px solid #eee;" alt="">
                    <?php else: ?>
                        <div style="width:48px;height:48px;background:<?= $tc['bg'] ?>;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;border:1px solid #eee;"><?= $tc['icon'] ?></div>
                    <?php endif; ?>
                </td>
                <td style="padding:10px;font-weight:600;color:#1a1a1a;"><?= htmlspecialchars($ing['nom']) ?></td>
                <td style="padding:10px;">
                    <span style="background:<?= $tc['bg'] ?>;color:<?= $tc['text'] ?>;padding:3px 10px;border-radius:20px;font-size:0.78rem;font-weight:600;">
                        <?= htmlspecialchars($ing['type']) ?>
                    </span>
                </td>
                <td style="padding:10px;">
                    <a href="/2A35/Admin/ingredient/edit/<?= $ing['id'] ?>" style="color:#bbb;font-size:1rem;text-decoration:none;" title="Modifier">✏️</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div style="text-align:right;margin-top:14px;">
            <a href="/2A35/Admin/ingredient" style="color:#2e7d32;font-size:0.85rem;font-weight:600;text-decoration:none;">
                Voir tous les ingrédients →
            </a>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script><script>
const C = { g:'#2e7d32', gl:'#4caf50', o:'#f57c00', r:'#c62828', b:'#1565c0', y:'#f9a825' };
const anim = { animation:{ duration:1200, easing:'easeInOutQuart' } };

// Compteurs animés
document.querySelectorAll('.kpi-num').forEach(el => {
    const raw = el.getAttribute('data-val');
    if (!raw) return;
    const val = parseFloat(raw);
    const suffix = el.textContent.replace(String(val), '');
    el.textContent = '0' + suffix;
    let cur = 0; const pas = val / (1000/16);
    const t = setInterval(() => {
        cur += pas; if (cur >= val) { cur = val; clearInterval(t); }
        el.textContent = (Number.isInteger(val) ? Math.round(cur) : cur.toFixed(1)) + suffix;
    }, 16);
});

// Catégories
new Chart(document.getElementById('chartCat'), {
    type:'bar',
    data:{ labels: <?= json_encode(array_column($statsCat,'categorie')) ?>,
        datasets:[{ label:'Recettes', data: <?= json_encode(array_column($statsCat,'total')) ?>,
            backgroundColor:[C.g,C.o,C.b,C.r,C.y,'#7b1fa2','#00838f','#558b2f'],
            borderRadius: 10,
            borderSkipped: false,
            barPercentage: 0.75,
            categoryPercentage: 0.85
        }] },
    options:{ ...anim, responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false } },
        scales:{
            y:{ beginAtZero:true, ticks:{ stepSize:1, color:'#aaa', font:{size:11} },
                grid:{ color:'#f0f0f0' }, border:{ display:false } },
            x:{ ticks:{ color:'#555', font:{size:11} },
                grid:{ color:'#f0f0f0' }, border:{ display:false } }
        }
    }
});

// Difficulté
const diffL = <?= json_encode(array_column($statsDiff,'difficulte')) ?>;
new Chart(document.getElementById('chartDiff'), {
    type:'pie',
    data:{ labels:diffL, datasets:[{ data: <?= json_encode(array_column($statsDiff,'total')) ?>,
        backgroundColor:diffL.map(l=>l==='facile'?C.g:l==='moyen'?C.o:C.r),
        borderWidth:3, borderColor:'#fff' }] },
    options:{ ...anim, responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ position:'bottom', labels:{ padding:14, font:{size:12} } } } }
});

// Calories
new Chart(document.getElementById('chartCal'), {
    type:'doughnut',
    data:{ labels:['< 300 kcal','300–600 kcal','600–900 kcal','> 900 kcal'],
        datasets:[{ data:[<?= (int)($statsCal['moins300']??0) ?>,<?= (int)($statsCal['entre300_600']??0) ?>,<?= (int)($statsCal['entre600_900']??0) ?>,<?= (int)($statsCal['plus900']??0) ?>],
            backgroundColor:[C.g,C.y,C.o,C.r], borderWidth:3, borderColor:'#fff' }] },
    options:{ ...anim, responsive:true, maintainAspectRatio:false, cutout:'60%',
        plugins:{ legend:{ position:'bottom', labels:{ padding:12, font:{size:12} } } } }
});

// Types ingrédients
const tc = [C.g,C.o,C.b,'#bf360c',C.r,'#827717','#616161'];
const ctxType = document.getElementById('chartType').getContext('2d');
const gradType = ctxType.createLinearGradient(0, 0, 0, 260);
gradType.addColorStop(0, 'rgba(76,175,80,0.45)');
gradType.addColorStop(1, 'rgba(76,175,80,0.02)');

new Chart(ctxType, {
    type:'line',
    data:{
        labels: <?= json_encode(array_column($statsType,'type')) ?>,
        datasets:[{
            label:'Ingrédients',
            data: <?= json_encode(array_column($statsType,'total')) ?>,
            borderColor: '#2e7d32',
            backgroundColor: gradType,
            borderWidth: 3,
            tension: 0.45,
            fill: true,
            pointRadius: 6,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#2e7d32',
            pointBorderWidth: 2.5,
            pointHoverRadius: 9,
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: '#2e7d32',
        }]
    },
    options:{
        ...anim, responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false } },
        scales:{
            y:{ beginAtZero:true, ticks:{ stepSize:1, color:'#aaa', font:{size:11} },
                grid:{ color:'rgba(0,0,0,0.06)', borderDash:[4,4] }, border:{ display:false } },
            x:{ ticks:{ color:'#555', font:{size:11} },
                grid:{ color:'rgba(0,0,0,0.04)', borderDash:[4,4] }, border:{ display:false } }
        }
    }
});

// Moyennes nutritionnelles
new Chart(document.getElementById('chartNutri'), {
    type:'bar',
    data:{ labels:['Protéines','Calcium','Glucides','Lipides'],
        datasets:[{ label:'Moyenne', data:[
            <?= round($moyennes['moy_prot']??0,1) ?>,
            <?= round($moyennes['moy_cal']??0,1) ?>,
            <?= round($moyennes['moy_gluc']??0,1) ?>,
            <?= round($moyennes['moy_lip']??0,1) ?>],
            backgroundColor:[C.g,C.b,C.o,C.r], borderRadius:8, borderSkipped:false }] },
    options:{ ...anim, indexAxis:'y', responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false } },
        scales:{ x:{ beginAtZero:true, ticks:{ color:'#aaa', font:{size:11} },
                     grid:{ color:'#f5f5f5' }, border:{ display:false } },
                 y:{ ticks:{ color:'#555', font:{size:11} }, grid:{ display:false }, border:{ display:false } } } }
});
</script>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
