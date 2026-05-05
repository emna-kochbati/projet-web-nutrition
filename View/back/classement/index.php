<?php
$page_title  = 'Classement des Restaurants';
$active_menu = 'classement';
ob_start();
?>

<style>
.rank-card { background:#fff; border-radius:10px; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,.07); margin-bottom:14px; display:flex; align-items:center; gap:20px; transition:transform .2s; }
.rank-card:hover { transform:translateY(-2px); box-shadow:0 4px 16px rgba(0,0,0,.1); }
.rank-number { font-size:1.8rem; font-weight:900; color:#ccc; min-width:48px; text-align:center; }
.rank-number.gold   { color:#f9a825; }
.rank-number.silver { color:#90a4ae; }
.rank-number.bronze { color:#a1887f; }
.rank-img { width:64px; height:64px; border-radius:10px; object-fit:cover; flex-shrink:0; }
.rank-no-img { width:64px; height:64px; border-radius:10px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; font-size:1.8rem; flex-shrink:0; }
.rank-info { flex:1; }
.rank-name { font-size:1.05rem; font-weight:700; color:#1a1a1a; margin-bottom:4px; }
.rank-cuisine { font-size:.8rem; color:#888; margin-bottom:8px; }
.rank-stats { display:flex; gap:16px; flex-wrap:wrap; }
.stat-pill { display:flex; align-items:center; gap:5px; font-size:.8rem; color:#555; background:#f5f5f5; padding:3px 10px; border-radius:20px; }
.rank-score-block { text-align:center; min-width:110px; }
.score-value { font-size:1.6rem; font-weight:800; }
.score-label { font-size:.75rem; font-weight:600; margin-top:2px; }
.progress-bar-wrap { background:#f0f0f0; border-radius:20px; height:8px; margin-top:8px; overflow:hidden; }
.progress-bar-fill { height:100%; border-radius:20px; transition:width .6s; }
.legend-box { background:#fff; border-radius:10px; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,.07); margin-bottom:24px; }
.legend-item { display:inline-flex; align-items:center; gap:8px; margin-right:20px; font-size:.85rem; }
.legend-dot { width:12px; height:12px; border-radius:50%; }
.section-title { font-size:1.1rem; font-weight:700; color:#2e7d32; margin-bottom:16px; padding-bottom:8px; border-bottom:2px solid #e8f5e9; }
.empty-state { text-align:center; padding:60px; color:#aaa; }
</style>

<!-- Légende -->
<div class="legend-box">
    <div class="section-title">📊 Classement Healthy — Logique de calcul</div>
    <p style="font-size:.85rem;color:#555;margin-bottom:12px;">
        Le score est calculé ainsi : <strong>Score = 100 − (moyenne calories ÷ 10) + % plats healthy</strong><br>
        Un plat est considéré <strong>healthy</strong> si ses calories sont inférieures à <strong>500 kcal</strong>.
        Plus le score est élevé, plus le restaurant est sain.
    </p>
    <div>
        <span class="legend-item"><span class="legend-dot" style="background:#2e7d32;"></span> Très healthy (score ≥ 120)</span>
        <span class="legend-item"><span class="legend-dot" style="background:#558b2f;"></span> Healthy (score ≥ 80)</span>
        <span class="legend-item"><span class="legend-dot" style="background:#f57f17;"></span> Modéré (score ≥ 50)</span>
        <span class="legend-item"><span class="legend-dot" style="background:#c62828;"></span> Calorique (score &lt; 50)</span>
        <span class="legend-item"><span class="legend-dot" style="background:#9e9e9e;"></span> Non évalué (aucun plat)</span>
    </div>
</div>

<?php if (empty($classement)): ?>
    <div class="empty-state">
        <div style="font-size:3rem;">🍴</div>
        <p>Aucun restaurant trouvé.</p>
    </div>
<?php else: ?>

<?php foreach ($classement as $r): ?>
<div class="rank-card">

    <!-- Rang -->
    <div class="rank-number <?= $r['rang'] === 1 ? 'gold' : ($r['rang'] === 2 ? 'silver' : ($r['rang'] === 3 ? 'bronze' : '')) ?>">
        <?= $r['rang'] === 1 ? '🥇' : ($r['rang'] === 2 ? '🥈' : ($r['rang'] === 3 ? '🥉' : '#'.$r['rang'])) ?>
    </div>

    <!-- Image -->
    <?php if (!empty($r['image'])): ?>
        <img src="/2A35/assets/uploads/restaurants/<?= htmlspecialchars($r['image']) ?>" class="rank-img" alt="">
    <?php else: ?>
        <div class="rank-no-img">🍴</div>
    <?php endif; ?>

    <!-- Infos -->
    <div class="rank-info">
        <div class="rank-name"><?= htmlspecialchars($r['nom']) ?></div>
        <div class="rank-cuisine"><?= ucfirst(htmlspecialchars($r['type_cuisine'])) ?></div>
        <div class="rank-stats">
            <span class="stat-pill">🍽️ <?= $r['total_meals'] ?> plat<?= $r['total_meals'] > 1 ? 's' : '' ?></span>
            <?php if ($r['avg_calories'] !== null): ?>
            <span class="stat-pill">🔥 <?= $r['avg_calories'] ?> kcal moy.</span>
            <?php else: ?>
            <span class="stat-pill" style="color:#aaa;">🔥 Calories non renseignées</span>
            <?php endif; ?>
            <span class="stat-pill" style="color:#2e7d32;">🥗 <?= $r['nb_healthy'] ?> plat<?= $r['nb_healthy'] > 1 ? 's' : '' ?> healthy (<?= $r['pct_healthy'] ?>%)</span>
        </div>
        <!-- Barre de progression healthy -->
        <?php if ($r['total_meals'] > 0): ?>
        <div class="progress-bar-wrap" style="margin-top:10px;max-width:300px;">
            <div class="progress-bar-fill" style="width:<?= $r['pct_healthy'] ?>%;background:<?= $r['color'] ?>;"></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Score -->
    <div class="rank-score-block">
        <div class="score-value" style="color:<?= $r['color'] ?>;"><?= $r['total_meals'] > 0 ? $r['score'] : '—' ?></div>
        <div class="score-label" style="color:<?= $r['color'] ?>;"><?= $r['label'] ?></div>
    </div>

</div>
<?php endforeach; ?>

<?php endif; ?>

<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
