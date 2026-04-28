<?php
$page_title  = 'Gestion des Recettes';
$active_menu = 'recette';
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
.stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:14px; margin-bottom:22px; }
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
.rec-img { width:52px; height:52px; object-fit:cover; border-radius:6px; border:2px solid var(--border); }
.no-img  { width:52px; height:52px; background:#f5f5f5; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; border:2px solid var(--border); }
.badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.76rem; font-weight:700; text-transform:capitalize; }
.b-cat  { background:#e8f5e9; color:var(--green); }
.b-easy { background:#e8f5e9; color:var(--green); }
.b-med  { background:#fff3e0; color:var(--orange); }
.b-hard { background:#ffebee; color:var(--red); }
.actions { display:flex; gap:6px; }
.btn-voir { background:#1565c0; color:#fff; padding:5px 11px; border-radius:5px; text-decoration:none; font-size:0.8rem; font-weight:600; }
.btn-edit { background:var(--orange); color:#fff; padding:5px 11px; border-radius:5px; text-decoration:none; font-size:0.8rem; font-weight:600; }
.btn-del  { background:var(--red); color:#fff; padding:5px 11px; border-radius:5px; border:none; cursor:pointer; font-size:0.8rem; font-weight:600; }
.empty { text-align:center; padding:50px; color:#999; }
.empty span { font-size:3rem; display:block; margin-bottom:10px; }
.modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; }
.modal-bg.show { display:flex; }
.modal { background:#fff; border-radius:10px; padding:30px; max-width:400px; width:90%; text-align:center; box-shadow:0 8px 30px rgba(0,0,0,.2); }
.modal h3 { margin:0 0 10px; }
.modal p  { color:#555; margin-bottom:22px; }
.modal-btns { display:flex; gap:12px; justify-content:center; }
.btn-ann  { background:#e0e0e0; color:#333; padding:10px 22px; border:none; border-radius:6px; cursor:pointer; font-weight:600; }
.btn-conf { background:var(--red); color:#fff; padding:10px 22px; border:none; border-radius:6px; cursor:pointer; font-weight:600; }
</style>

<div class="page-header">
    <h2>🍽️ Gestion des Recettes</h2>
    <a href="/2A35/Admin/recette/create" class="btn-green">＋ Nouvelle Recette</a>
</div>

<?php if ($success): ?><div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="stats">
    <div class="stat-card"><span class="stat-icon">🍽️</span><div><div class="stat-num"><?= count($recettes) ?></div><div class="stat-lbl">Total</div></div></div>
    <div class="stat-card"><span class="stat-icon">🟢</span><div><div class="stat-num"><?= count(array_filter($recettes, fn($r) => $r['difficulte']==='facile')) ?></div><div class="stat-lbl">Faciles</div></div></div>
    <div class="stat-card"><span class="stat-icon">🔥</span><div><div class="stat-num"><?= count($recettes)>0 ? round(array_sum(array_column($recettes,'calories'))/count($recettes)) : 0 ?></div><div class="stat-lbl">Moy. kcal</div></div></div>
    <div class="stat-card"><span class="stat-icon">⏱️</span><div><div class="stat-num"><?= count($recettes)>0 ? round(array_sum(array_column($recettes,'duree'))/count($recettes)) : 0 ?> min</div><div class="stat-lbl">Durée moy.</div></div></div>
</div>

<form class="search-form" method="GET" action="/2A35/Admin/recette">
    <input type="text" id="searchInput" name="search" placeholder="Rechercher par nom..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" autocomplete="off">
    <select id="categorieFilter" name="categorie" style="padding:10px 14px; border:2px solid var(--border); border-radius:6px; font-size:0.9rem; outline:none; min-width:160px;">
        <option value="">Toutes catégories</option>
        <?php foreach (['petit-dejeuner'=>'Petit-déjeuner','dejeuner'=>'Déjeuner','diner'=>'Dîner','collation'=>'Collation','dessert'=>'Dessert','vegetarien'=>'Végétarien','regime'=>'Régime','sportif'=>'Sportif'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($_GET['categorie'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
        <?php endforeach; ?>
    </select>
    <select id="difficulteFilter" name="difficulte" style="padding:10px 14px; border:2px solid var(--border); border-radius:6px; font-size:0.9rem; outline:none; min-width:140px;">
        <option value="">Toutes difficultés</option>
        <option value="facile"    <?= ($_GET['difficulte'] ?? '') === 'facile'    ? 'selected' : '' ?>>🟢 Facile</option>
        <option value="moyen"     <?= ($_GET['difficulte'] ?? '') === 'moyen'     ? 'selected' : '' ?>>🟡 Moyen</option>
        <option value="difficile" <?= ($_GET['difficulte'] ?? '') === 'difficile' ? 'selected' : '' ?>>🔴 Difficile</option>
    </select>
    <button type="submit" class="btn-orange">🔍 Rechercher</button>
    <?php if (!empty($_GET['search']) || !empty($_GET['categorie']) || !empty($_GET['difficulte'])): ?>
        <a href="/2A35/Admin/recette" class="btn-clear">✕ Effacer</a>
    <?php endif; ?>
</form>

<!-- Résultats AJAX -->
<div id="ajaxResults"></div>

<div class="table-wrap">
<?php if (empty($recettes)): ?>
    <div class="empty"><span>🍽️</span><p>Aucune recette. <a href="/2A35/Admin/recette/create">Ajouter la première !</a></p></div>
<?php else: ?>
    <table>
        <thead>
            <tr><th>#</th><th>Image</th><th>Nom</th><th>Description</th><th>Catégorie</th><th>Durée</th><th>Difficulté</th><th>Calories</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($recettes as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?php if ($r['image']): ?><img src="/2A35/assets/uploads/recettes/<?= htmlspecialchars($r['image']) ?>" class="rec-img" alt=""><?php else: ?><div class="no-img">🍽️</div><?php endif; ?></td>
                <td><strong><?= htmlspecialchars($r['nom']) ?></strong></td>
                <td style="max-width:180px; color:#555; font-size:0.85rem;">
                    <?php if (!empty($r['description'])): ?>
                        <?= htmlspecialchars(mb_strimwidth($r['description'], 0, 50, '...')) ?>
                    <?php else: ?>
                        <span style="color:#bbb; font-style:italic;">—</span>
                    <?php endif; ?>
                </td>
                <td><span class="badge b-cat"><?= htmlspecialchars($r['categorie']) ?></span></td>
                <td>⏱ <?= $r['duree'] ?> min</td>
                <td><?php $bc=match($r['difficulte']){'facile'=>'b-easy','moyen'=>'b-med','difficile'=>'b-hard',default=>'b-easy'}; ?><span class="badge <?= $bc ?>"><?= $r['difficulte'] ?></span></td>
                <td>🔥 <?= $r['calories'] ?> kcal</td>
                <td>
                    <div class="actions">
                        <a href="/2A35/Admin/recette/show/<?= $r['id'] ?>" class="btn-voir">👁 Voir</a>
                        <a href="/2A35/Admin/recette/edit/<?= $r['id'] ?>" class="btn-edit">✏️ Modifier</a>
                        <button class="btn-del" onclick="confirmer(<?= $r['id'] ?>, '<?= htmlspecialchars(addslashes($r['nom'])) ?>')">🗑 Supprimer</button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<style>
.pagination-wrap { display:flex; justify-content:center; align-items:center; gap:10px; margin-top:28px; flex-wrap:wrap; }
.page-btn {
    width:44px; height:44px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-weight:700; font-size:0.95rem; cursor:pointer;
    text-decoration:none; transition:all .2s;
    border:2px solid #a5d6a7; color:#2e7d32; background:#fff;
}
.page-btn:hover { background:#e8f5e9; border-color:#2e7d32; color:#2e7d32; transform:scale(1.08); }
.page-btn.active { background:#2e7d32; border-color:#2e7d32; color:#fff; box-shadow:0 4px 12px rgba(46,125,50,.35); }
.page-btn.disabled { border-color:#e0e0e0; color:#bbb; cursor:default; pointer-events:none; }
</style>
<div class="pagination-wrap">
    <?php
    $baseUrl = '/2A35/Admin/recette?page=';
    $qs = '';
    if ($search)     $qs .= '&search='.urlencode($search);
    if ($categorie)  $qs .= '&categorie='.urlencode($categorie);
    if ($difficulte) $qs .= '&difficulte='.urlencode($difficulte);
    ?>
    <!-- Précédent -->
    <a href="<?= $baseUrl.($page-1).$qs ?>" class="page-btn <?= $page<=1 ? 'disabled':'' ?>">«</a>

    <?php for ($i=1; $i<=$totalPages; $i++): ?>
        <a href="<?= $baseUrl.$i.$qs ?>" class="page-btn <?= $i===$page ? 'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <!-- Suivant -->
    <a href="<?= $baseUrl.($page+1).$qs ?>" class="page-btn <?= $page>=$totalPages ? 'disabled':'' ?>">»</a>
</div>
<?php endif; ?>

<div class="modal-bg" id="modalDel">
    <div class="modal">
        <h3>⚠️ Confirmer la suppression</h3>
        <p>Supprimer <strong id="nomRec"></strong> ? Cette action est irréversible.</p>
        <div class="modal-btns">
            <button class="btn-ann" onclick="document.getElementById('modalDel').classList.remove('show')">Annuler</button>
            <form id="formDel" method="POST"><button type="submit" class="btn-conf">Supprimer</button></form>
        </div>
    </div>
</div>

<script>
function confirmer(id, nom) {
    document.getElementById('nomRec').textContent = nom;
    document.getElementById('formDel').action = '/2A35/Admin/recette/delete/' + id;
    document.getElementById('modalDel').classList.add('show');
}
document.getElementById('modalDel').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('show');
});

// ── Recherche AJAX dynamique ──────────────────────────────────────────────────
const searchInput     = document.getElementById('searchInput');
const categorieFilter = document.getElementById('categorieFilter');
const difficulteFilter= document.getElementById('difficulteFilter');
const tableWrap       = document.querySelector('.table-wrap');
const ajaxResults     = document.getElementById('ajaxResults');

function rechercheAjax() {
    const search     = searchInput.value.trim();
    const categorie  = categorieFilter.value;
    const difficulte = difficulteFilter.value;

    // Si tout est vide, afficher le tableau normal
    if (!search && !categorie && !difficulte) {
        tableWrap.style.display = '';
        ajaxResults.innerHTML   = '';
        return;
    }

    const url = `/2A35/Admin/recette/ajax?search=${encodeURIComponent(search)}&categorie=${encodeURIComponent(categorie)}&difficulte=${encodeURIComponent(difficulte)}`;

    fetch(url)
        .then(r => r.json())
        .then(recettes => {
            tableWrap.style.display = 'none';

            if (recettes.length === 0) {
                ajaxResults.innerHTML = '<div style="background:#fff;border-radius:10px;padding:40px;text-align:center;color:#999;box-shadow:0 2px 12px rgba(0,0,0,.07);">Aucune recette trouvée.</div>';
                return;
            }

            const diffClass = { facile:'b-easy', moyen:'b-med', difficile:'b-hard' };

            let html = `<div class="table-wrap">
                <table>
                    <thead><tr><th>#</th><th>Image</th><th>Nom</th><th>Description</th><th>Catégorie</th><th>Durée</th><th>Difficulté</th><th>Calories</th><th>Actions</th></tr></thead>
                    <tbody>`;

            recettes.forEach(r => {
                const img = r.image
                    ? `<img src="/2A35/assets/uploads/recettes/${r.image}" class="rec-img" alt="">`
                    : `<div class="no-img">🍽️</div>`;
                const desc = r.description ? r.description.substring(0, 50) + (r.description.length > 50 ? '...' : '') : '<span style="color:#bbb;font-style:italic;">—</span>';
                const dc   = diffClass[r.difficulte] || 'b-easy';

                html += `<tr>
                    <td>${r.id}</td>
                    <td>${img}</td>
                    <td><strong>${r.nom}</strong></td>
                    <td style="max-width:180px;color:#555;font-size:.85rem;">${desc}</td>
                    <td><span class="badge b-cat">${r.categorie}</span></td>
                    <td>⏱ ${r.duree} min</td>
                    <td><span class="badge ${dc}">${r.difficulte}</span></td>
                    <td>🔥 ${r.calories} kcal</td>
                    <td>
                        <div class="actions">
                            <a href="/2A35/Admin/recette/show/${r.id}" class="btn-voir">👁 Voir</a>
                            <a href="/2A35/Admin/recette/edit/${r.id}" class="btn-edit">✏️ Modifier</a>
                            <button class="btn-del" onclick="confirmer(${r.id}, '${r.nom.replace(/'/g,"\\'")}')">🗑 Supprimer</button>
                        </div>
                    </td>
                </tr>`;
            });

            html += `</tbody></table></div>`;
            ajaxResults.innerHTML = html;
        })
        .catch(() => {
            ajaxResults.innerHTML = '<div style="color:red;padding:10px;">Erreur de recherche.</div>';
        });
}

// Déclencher à chaque frappe (avec délai 300ms)
let timer;
searchInput.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(rechercheAjax, 300);
});

// Déclencher aussi au changement des selects
categorieFilter.addEventListener('change',  rechercheAjax);
difficulteFilter.addEventListener('change', rechercheAjax);
</script>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
