<?php
$page_title  = 'Gestion des Restaurants';
$active_menu = 'restaurant';
ob_start();
?>
<style>
:root { --green:#2e7d32; --green-l:#4caf50; --orange:#f57c00; --red:#c62828; --border:#e0e0e0; }
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:22px; }
.page-header h2 { font-size:1.5rem; font-weight:700; margin:0; }
.btn-green { background:var(--green); color:#fff; padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:600; font-size:0.9rem; }
.btn-green:hover { background:var(--green-l); color:#fff; }
.alert { padding:12px 18px; border-radius:6px; margin-bottom:16px; font-weight:600; }
.alert-success { background:#e8f5e9; color:var(--green); border-left:4px solid var(--green); }
.alert-error   { background:#ffebee; color:var(--red);   border-left:4px solid var(--red); }
.stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:14px; margin-bottom:22px; }
.stat-card { background:#fff; border-radius:10px; padding:16px 18px; box-shadow:0 2px 8px rgba(0,0,0,.06); display:flex; align-items:center; gap:12px; }
.stat-icon { font-size:1.8rem; }
.stat-num  { font-size:1.5rem; font-weight:700; color:var(--green); }
.stat-lbl  { font-size:0.78rem; color:#777; }
.search-form { display:flex; gap:10px; margin-bottom:18px; }
.search-form input { flex:1; padding:10px 14px; border:2px solid var(--border); border-radius:6px; font-size:0.9rem; outline:none; }
.search-form input:focus { border-color:var(--green); }
.btn-orange { background:var(--orange); color:#fff; border:none; padding:10px 18px; border-radius:6px; cursor:pointer; font-weight:600; }
.btn-clear  { background:#e0e0e0; color:#333; padding:10px 14px; border-radius:6px; text-decoration:none; font-weight:600; }
.table-wrap { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,.07); overflow:hidden; }
table { width:100%; border-collapse:collapse; font-size:0.9rem; }
thead { background:var(--green); color:#fff; }
thead th { padding:13px 16px; text-align:left; font-weight:600; }
tbody tr { border-bottom:1px solid var(--border); transition:background .15s; }
tbody tr:hover { background:#f1f8e9; }
tbody td { padding:11px 16px; vertical-align:middle; }
.res-img { width:52px; height:52px; object-fit:cover; border-radius:6px; border:2px solid var(--border); }
.no-img  { width:52px; height:52px; background:#f5f5f5; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; border:2px solid var(--border); }
.badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.76rem; font-weight:700; text-transform:capitalize; }
.b-type  { background:#e3f2fd; color:#1565c0; }
.actions { display:flex; gap:6px; }
.btn-voir { background:#1565c0; color:#fff; padding:5px 11px; border-radius:5px; text-decoration:none; font-size:0.8rem; font-weight:600; }
.btn-edit { background:var(--orange); color:#fff; padding:5px 11px; border-radius:5px; text-decoration:none; font-size:0.8rem; font-weight:600; }
.btn-del  { background:var(--red); color:#fff; padding:5px 11px; border-radius:5px; border:none; cursor:pointer; font-size:0.8rem; font-weight:600; }
.empty { text-align:center; padding:50px; color:#999; }
</style>

<div class="page-header">
    <h2>🍴 Gestion des Restaurants</h2>
    <a href="/2A35/Admin/restaurant/create" class="btn-green">＋ Nouveau Restaurant</a>
</div>

<?php if ($success): ?><div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="stats">
    <div class="stat-card"><span class="stat-icon">🏪</span><div><div class="stat-num"><?= count($restaurants) ?></div><div class="stat-lbl">Total Restaurants</div></div></div>
    <div class="stat-card"><span class="stat-icon">🏆</span><div><div class="stat-num"><?= count($classement) > 0 ? $classement[0]['nom'] : '-' ?></div><div class="stat-lbl">Top Healthy</div></div></div>
</div>

<form class="search-form" method="GET" action="/2A35/Admin/restaurant">
    <input type="text" name="search" placeholder="Rechercher un restaurant..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <button type="submit" class="btn-orange">🔍 Rechercher</button>
    <?php if (!empty($_GET['search'])): ?>
        <a href="/2A35/Admin/restaurant" class="btn-clear">✕ Effacer</a>
    <?php endif; ?>
</form>

<div class="table-wrap">
<?php if (empty($restaurants)): ?>
    <div class="empty"><span>🍴</span><p>Aucun restaurant. <a href="/2A35/Admin/restaurant/create">Ajouter le premier !</a></p></div>
<?php else: ?>
    <table>
        <thead>
            <tr><th>#</th><th>Image</th><th>Nom</th><th>Type</th><th>Adresse</th><th>Healthy Score</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($restaurants as $r): ?>
            <?php 
                $c = array_values(array_filter($classement, fn($item) => $item['id'] == $r['id']))[0] ?? null;
            ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?php if ($r['image']): ?><img src="/2A35/assets/uploads/restaurants/<?= htmlspecialchars($r['image']) ?>" class="res-img" alt=""><?php else: ?><div class="no-img">🍴</div><?php endif; ?></td>
                <td><strong><?= htmlspecialchars($r['nom']) ?></strong></td>
                <td><span class="badge b-type"><?= htmlspecialchars($r['type_cuisine']) ?></span></td>
                <td><?= htmlspecialchars($r['adresse']) ?></td>
                <td>
                    <?php if ($c): ?>
                        <span style="color: <?= $c['color'] ?>; font-weight: bold;"><?= $c['score'] ?>% (<?= $c['label'] ?>)</span>
                    <?php else: ?>
                        <span class="text-muted">N/A</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="actions">
                        <a href="/2A35/Admin/restaurant/show/<?= $r['id'] ?>" class="btn-voir">👁 Voir</a>
                        <a href="/2A35/Admin/restaurant/edit/<?= $r['id'] ?>" class="btn-edit">✏️ Modifier</a>
                        <form action="/2A35/Admin/restaurant/delete/<?= $r['id'] ?>" method="POST" onsubmit="return confirm('Supprimer ce restaurant ?')">
                            <button type="submit" class="btn-del">🗑 Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>

    rows.forEach(r => {
        const img = r.image
            ? `<img src="/2A35/assets/uploads/restaurants/${r.image}" class="img-thumb" alt="">`
            : `<div class="no-img">🍴</div>`;

        html += `<tr>
            <td>${r.id}</td>
            <td>${img}</td>
            <td><strong>${escHtml(r.nom)}</strong></td>
            <td>${escHtml(r.adresse)}</td>
            <td><span class="badge badge-cuisine">${escHtml(r.type_cuisine)}</span></td>
            <td>${escHtml(r.telephone || '—')}</td>
            <td>
                <div class="actions">
                    <a href="/2A35/Admin/restaurant/show/${r.id}" class="btn-show">👁 Voir</a>
                    <a href="/2A35/Admin/restaurant/edit/${r.id}" class="btn-edit">✏️ Modifier</a>
                    <form method="POST" action="/2A35/Admin/restaurant/delete/${r.id}" onsubmit="return confirm('Supprimer ce restaurant ?')">
                        <button type="submit" class="btn-danger">🗑 Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>`;
    });

    html += '</tbody></table>';
    document.getElementById('table-container').innerHTML = html;
}

function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}

function goToPage(p) {
    const total = <?= $totalPages ?>;
    if (p < 1 || p > total) return;
    const q    = document.getElementById('search-input').value.trim();
    const type = document.getElementById('type-input').value;
    let url = '/2A35/Admin/restaurant?page=' + p;
    if (q)    url += '&search=' + encodeURIComponent(q);
    if (type) url += '&type='   + encodeURIComponent(type);
    window.location.href = url;
}

// Afficher le compte initial
document.getElementById('result-count').textContent =
    '<?= $total ?> restaurant<?= $total > 1 ? "s" : "" ?> trouvé<?= $total > 1 ? "s" : "" ?>';
</script>

<!-- ══════════════════════════════════════════════════════════════════════ -->
<!-- ── Classement Healthy ─────────────────────────────────────────────── -->
<!-- ══════════════════════════════════════════════════════════════════════ -->
<div style="margin-top:40px;">

<style>
.rank-card { background:#fff; border-radius:10px; padding:18px 20px; box-shadow:0 2px 10px rgba(0,0,0,.07); margin-bottom:12px; display:flex; align-items:center; gap:18px; transition:transform .2s; }
.rank-card:hover { transform:translateY(-2px); box-shadow:0 4px 16px rgba(0,0,0,.1); }
.rank-number { font-size:1.6rem; font-weight:900; min-width:44px; text-align:center; }
.rank-img-cl { width:56px; height:56px; border-radius:8px; object-fit:cover; flex-shrink:0; }
.rank-no-img-cl { width:56px; height:56px; border-radius:8px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0; }
.rank-info-cl { flex:1; }
.rank-name-cl { font-size:.98rem; font-weight:700; color:#1a1a1a; margin-bottom:3px; }
.rank-cuisine-cl { font-size:.78rem; color:#888; margin-bottom:6px; }
.rank-stats-cl { display:flex; gap:10px; flex-wrap:wrap; }
.stat-pill-cl { font-size:.76rem; color:#555; background:#f5f5f5; padding:2px 9px; border-radius:20px; }
.rank-progress { background:#f0f0f0; border-radius:20px; height:7px; margin-top:8px; max-width:280px; overflow:hidden; }
.rank-progress-fill { height:100%; border-radius:20px; }
.rank-score-cl { text-align:center; min-width:90px; }
.rank-score-val { font-size:1.5rem; font-weight:800; }
.rank-score-lbl { font-size:.72rem; font-weight:600; margin-top:2px; }
.cl-legend { display:flex; flex-wrap:wrap; gap:14px; margin-bottom:16px; font-size:.82rem; }
.cl-legend-dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:5px; }
</style>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid #e8f5e9;">
    <h3 style="font-size:1.05rem;font-weight:700;color:#2e7d32;margin:0;">🏆 Classement Healthy des Restaurants</h3>
</div>

<!-- Légende -->
<div class="cl-legend">
    <span><span class="cl-legend-dot" style="background:#2e7d32;"></span>Très healthy (≥120)</span>
    <span><span class="cl-legend-dot" style="background:#558b2f;"></span>Healthy (≥80)</span>
    <span><span class="cl-legend-dot" style="background:#f57f17;"></span>Modéré (≥50)</span>
    <span><span class="cl-legend-dot" style="background:#c62828;"></span>Calorique (&lt;50)</span>
    <span><span class="cl-legend-dot" style="background:#9e9e9e;"></span>Non évalué</span>
    <span style="color:#888;font-size:.78rem;">Score = 100 − (moy.cal ÷ 10) + % plats &lt;500 kcal</span>
</div>

<?php if (empty($classement)): ?>
    <p style="color:#aaa;text-align:center;padding:30px;">Aucun restaurant.</p>
<?php else: ?>
<?php foreach ($classement as $r): ?>
<div class="rank-card">
    <div class="rank-number">
        <?= $r['rang'] === 1 ? '🥇' : ($r['rang'] === 2 ? '🥈' : ($r['rang'] === 3 ? '🥉' : '#'.$r['rang'])) ?>
    </div>
    <?php if (!empty($r['image'])): ?>
        <img src="/2A35/assets/uploads/restaurants/<?= htmlspecialchars($r['image']) ?>" class="rank-img-cl" alt="">
    <?php else: ?>
        <div class="rank-no-img-cl">🍴</div>
    <?php endif; ?>
    <div class="rank-info-cl">
        <div class="rank-name-cl"><?= htmlspecialchars($r['nom']) ?></div>
        <div class="rank-cuisine-cl"><?= ucfirst(htmlspecialchars($r['type_cuisine'])) ?></div>
        <div class="rank-stats-cl">
            <span class="stat-pill-cl">🍽️ <?= $r['total_meals'] ?> plat<?= $r['total_meals'] > 1 ? 's' : '' ?></span>
            <?php if ($r['avg_calories'] !== null): ?>
            <span class="stat-pill-cl">🔥 <?= $r['avg_calories'] ?> kcal moy.</span>
            <?php endif; ?>
            <span class="stat-pill-cl" style="color:#2e7d32;">🥗 <?= $r['nb_healthy'] ?> healthy (<?= $r['pct_healthy'] ?>%)</span>
        </div>
        <?php if ($r['total_meals'] > 0): ?>
        <div class="rank-progress">
            <div class="rank-progress-fill" style="width:<?= $r['pct_healthy'] ?>%;background:<?= $r['color'] ?>;"></div>
        </div>
        <?php endif; ?>
    </div>
    <div class="rank-score-cl">
        <div class="rank-score-val" style="color:<?= $r['color'] ?>;"><?= $r['total_meals'] > 0 ? $r['score'] : '—' ?></div>
        <div class="rank-score-lbl" style="color:<?= $r['color'] ?>;"><?= $r['label'] ?></div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

</div>

<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
