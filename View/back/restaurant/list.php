<?php
$page_title  = 'Restaurants';
$active_menu = 'restaurant';
ob_start();
?>

<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.btn-primary { background:#2e7d32; color:#fff; padding:9px 18px; border-radius:6px; text-decoration:none; font-weight:600; font-size:.9rem; }
.btn-primary:hover { background:#1b5e20; }
.btn-danger  { background:#c62828; color:#fff; padding:5px 12px; border-radius:5px; border:none; cursor:pointer; font-size:.82rem; }
.btn-edit    { background:#1565c0; color:#fff; padding:5px 12px; border-radius:5px; text-decoration:none; font-size:.82rem; }
.btn-show    { background:#558b2f; color:#fff; padding:5px 12px; border-radius:5px; text-decoration:none; font-size:.82rem; }
.alert-success { background:#e8f5e9; border-left:4px solid #2e7d32; padding:12px 16px; border-radius:6px; margin-bottom:18px; color:#1b5e20; }
.alert-error   { background:#ffebee; border-left:4px solid #c62828; padding:12px 16px; border-radius:6px; margin-bottom:18px; color:#b71c1c; }
.search-bar { display:flex; gap:10px; margin-bottom:20px; align-items:center; }
.search-bar input { flex:1; padding:9px 14px; border:1px solid #ccc; border-radius:6px; font-size:.9rem; }
#search-spinner { display:none; color:#2e7d32; font-size:.85rem; }
table { width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.07); }
thead { background:#2e7d32; color:#fff; }
th, td { padding:12px 16px; text-align:left; font-size:.88rem; }
tbody tr:nth-child(even) { background:#f9f9f9; }
tbody tr:hover { background:#f1f8e9; }
.badge { display:inline-block; padding:3px 10px; border-radius:12px; font-size:.78rem; font-weight:600; }
.badge-cuisine { background:#e8f5e9; color:#2e7d32; }
.actions { display:flex; gap:6px; }
.img-thumb { width:48px; height:48px; object-fit:cover; border-radius:6px; }
.no-img { width:48px; height:48px; background:#eee; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; }
#result-count { font-size:.85rem; color:#888; margin-bottom:10px; }
</style>

<?php if ($success): ?>
    <div class="alert-success">✅ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert-error">❌ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="page-header">
    <h2 style="font-size:1.3rem;color:#1a1a1a;">🍴 Liste des Restaurants</h2>
    <a href="/2A35/Admin/restaurant/create" class="btn-primary">➕ Nouveau Restaurant</a>
</div>

<!-- Barre de recherche AJAX -->
<div class="search-bar">
    <input type="text" id="search-input" placeholder="Rechercher par nom ou adresse…"
           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" autocomplete="off">
    <select id="type-input" style="padding:9px 14px;border:1px solid #ccc;border-radius:6px;font-size:.9rem;min-width:180px;">
        <option value="">Tous les types</option>
        <?php foreach (['tunisienne','italienne','japonaise','americaine','indienne','mexicaine','française','autre'] as $t): ?>
            <option value="<?= $t ?>"><?= ucfirst($t) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="button" onclick="fetchResults()" style="background:#2e7d32;color:#fff;border:none;padding:9px 18px;border-radius:6px;cursor:pointer;font-weight:600;">
        🔍 Filtrer
    </button>
    <button type="button" onclick="resetSearch()" style="background:#eee;color:#333;border:none;padding:9px 18px;border-radius:6px;cursor:pointer;font-weight:600;">
        ✕ Effacer
    </button>
    <span id="search-spinner">⏳ Recherche…</span>
</div>
<div id="result-count"></div>

<!-- Tableau mis à jour dynamiquement -->
<div id="table-container">
    <?php include __DIR__ . '/../../back/restaurant/_table_rows.php'; ?>
</div>

<!-- ── Pagination circulaire ──────────────────────────────────────────── -->
<?php if ($totalPages > 1): ?>
<div style="display:flex;justify-content:center;align-items:center;gap:8px;margin-top:24px;">
    <button onclick="goToPage(<?= $page - 1 ?>)" <?= $page <= 1 ? 'disabled' : '' ?>
            style="width:40px;height:40px;border-radius:50%;border:2px solid <?= $page<=1?'#ddd':'#a5d6a7' ?>;
                   background:<?= $page<=1?'#f5f5f5':'#fff' ?>;color:<?= $page<=1?'#bbb':'#2e7d32' ?>;
                   font-size:1rem;cursor:<?= $page<=1?'default':'pointer' ?>;font-weight:700;
                   display:flex;align-items:center;justify-content:center;">«</button>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <button onclick="goToPage(<?= $i ?>)"
            style="width:40px;height:40px;border-radius:50%;
                   border:2px solid <?= $i===$page?'#2e7d32':'#a5d6a7' ?>;
                   background:<?= $i===$page?'#2e7d32':'#fff' ?>;
                   color:<?= $i===$page?'#fff':'#2e7d32' ?>;
                   font-size:.9rem;font-weight:700;cursor:pointer;
                   display:flex;align-items:center;justify-content:center;"><?= $i ?></button>
    <?php endfor; ?>
    <button onclick="goToPage(<?= $page + 1 ?>)" <?= $page >= $totalPages ? 'disabled' : '' ?>
            style="width:40px;height:40px;border-radius:50%;border:2px solid <?= $page>=$totalPages?'#ddd':'#a5d6a7' ?>;
                   background:<?= $page>=$totalPages?'#f5f5f5':'#fff' ?>;color:<?= $page>=$totalPages?'#bbb':'#2e7d32' ?>;
                   font-size:1rem;cursor:<?= $page>=$totalPages?'default':'pointer' ?>;font-weight:700;
                   display:flex;align-items:center;justify-content:center;">»</button>
</div>
<?php endif; ?>

<script>
let searchTimer = null;

document.getElementById('search-input').addEventListener('input', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(fetchResults, 300);
});

document.getElementById('search-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); fetchResults(); }
});

function fetchResults() {
    const query = document.getElementById('search-input').value.trim();
    const type  = document.getElementById('type-input').value;
    document.getElementById('search-spinner').style.display = 'inline';

    fetch('/2A35/Admin/restaurant/search?q=' + encodeURIComponent(query) + '&type=' + encodeURIComponent(type), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('search-spinner').style.display = 'none';
        document.getElementById('result-count').textContent =
            data.length + ' restaurant' + (data.length > 1 ? 's' : '') + ' trouvé' + (data.length > 1 ? 's' : '');
        renderTable(data);
    })
    .catch(() => {
        document.getElementById('search-spinner').style.display = 'none';
    });
}

function resetSearch() {
    document.getElementById('search-input').value = '';
    document.getElementById('type-input').value   = '';
    fetchResults();
}

function renderTable(rows) {
    if (rows.length === 0) {
        document.getElementById('table-container').innerHTML =
            '<p style="text-align:center;color:#888;padding:40px;">Aucun restaurant trouvé.</p>';
        return;
    }

    let html = `<table>
        <thead>
            <tr>
                <th>#</th><th>Image</th><th>Nom</th><th>Adresse</th><th>Cuisine</th><th>Téléphone</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>`;

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
