<?php
$page_title  = 'Statistiques Ingrédients';
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
    opacity:0; animation:fadeInUp .5s ease forwards; transition:transform .3s,box-shadow .3s;
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
    opacity:0; animation:fadeInUp .6s ease forwards; transition:transform .3s,box-shadow .3s;
}
.chart-card:nth-child(1){animation-delay:.5s}
.chart-card:nth-child(2){animation-delay:.7s}
.chart-card:nth-child(3){animation-delay:.9s}
.chart-card:hover{ transform:translateY(-6px); box-shadow:0 12px 30px rgba(0,0,0,.12); }
.chart-card h3 { font-size:1rem; font-weight:700; margin-bottom:18px; color:#1a1a1a; border-bottom:2px solid #f0f0f0; padding-bottom:10px; }
.chart-container { position:relative; height:280px; }
</style>

<div class="stats-header">
    <h2>📊 Statistiques des Ingrédients</h2>
    <a href="/2A35/Admin/ingredient" class="btn-back">← Retour à la liste</a>
</div>

<div class="summary-row">
    <div class="summary-card">
        <span class="summary-icon">🥦</span>
        <div><div class="summary-num"><?= $totalIngredients ?></div><div class="summary-lbl">Total ingrédients</div></div>
    </div>
    <div class="summary-card">
        <span class="summary-icon">💪</span>
        <div><div class="summary-num"><?= round($moyennes['moy_prot']??0,1) ?>g</div><div class="summary-lbl">Moy. Protéines</div></div>
    </div>
    <div class="summary-card">
        <span class="summary-icon">⚡</span>
        <div><div class="summary-num"><?= round($moyennes['moy_gluc']??0,1) ?>g</div><div class="summary-lbl">Moy. Glucides</div></div>
    </div>
    <div class="summary-card">
        <span class="summary-icon">🫧</span>
        <div><div class="summary-num"><?= round($moyennes['moy_lip']??0,1) ?>g</div><div class="summary-lbl">Moy. Lipides</div></div>
    </div>
</div>

<div class="charts-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <div class="chart-card">
        <h3>🗂 Ingrédients par type</h3>
        <div class="chart-container"><canvas id="chartType"></canvas></div>
    </div>
    <div class="chart-card">
        <h3>🧪 Moyennes nutritionnelles (g/100g)</h3>
        <div class="chart-container"><canvas id="chartNutri"></canvas></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const COLORS = { vert:'#2e7d32', orange:'#f57c00', rouge:'#c62828', bleu:'#1565c0', jaune:'#f9a825' };
const animOpts = { animation:{ duration:1200, easing:'easeInOutQuart' } };
const typeColors = ['#2e7d32','#f57c00','#1565c0','#bf360c','#c62828','#827717','#616161'];

document.querySelectorAll('.summary-num').forEach(el => {
    const txt = el.textContent;
    const val = parseFloat(txt);
    if (isNaN(val)) return;
    const suffix = txt.replace(String(val),'');
    el.textContent = '0'+suffix;
    let cur=0; const pas=val/(1200/16);
    const t=setInterval(()=>{ cur+=pas; if(cur>=val){cur=val;clearInterval(t);}
        el.textContent=(Number.isInteger(val)?Math.round(cur):cur.toFixed(1))+suffix; },16);
});

const typeLabels = <?= json_encode(array_column($statsType, 'type')) ?>;
const typeData   = <?= json_encode(array_column($statsType, 'total')) ?>;

new Chart(document.getElementById('chartType'), {
    type:'bar', data:{ labels:typeLabels,
        datasets:[{ label:'Ingrédients', data:typeData, backgroundColor:typeColors, borderRadius:6, borderSkipped:false }] },
    options:{ ...animOpts, responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true, ticks:{ stepSize:1 } } } }
});

new Chart(document.getElementById('chartNutri'), {
    type:'bar',
    data:{ labels:['Protéines','Calcium','Glucides','Lipides'],
        datasets:[{ label:'Moyenne', data:[
            <?= round($moyennes['moy_prot']??0,1) ?>,
            <?= round($moyennes['moy_cal']??0,1) ?>,
            <?= round($moyennes['moy_gluc']??0,1) ?>,
            <?= round($moyennes['moy_lip']??0,1) ?>],
            backgroundColor:[COLORS.vert,COLORS.bleu,COLORS.orange,COLORS.rouge], borderRadius:6, borderSkipped:false }] },
    options:{ ...animOpts, indexAxis:'y', responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false } }, scales:{ x:{ beginAtZero:true } } }
});
</script>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
