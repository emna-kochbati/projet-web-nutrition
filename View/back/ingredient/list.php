<?php
$page_title  = 'Gestion des Ingrédients';
$active_menu = 'recette';
ob_start();

$types = [
    'legume'         => ['label'=>'Légumes',          'icon'=>'🥕', 'color'=>'#e8f5e9','border'=>'#2e7d32','text'=>'#2e7d32'],
    'fruit'          => ['label'=>'Fruits',            'icon'=>'🍎', 'color'=>'#fff3e0','border'=>'#f57c00','text'=>'#f57c00'],
    'produit-laitier'=> ['label'=>'Produits laitiers', 'icon'=>'🥛', 'color'=>'#e3f2fd','border'=>'#1565c0','text'=>'#1565c0'],
    'epice'          => ['label'=>'Épices',            'icon'=>'🌶️', 'color'=>'#fbe9e7','border'=>'#bf360c','text'=>'#bf360c'],
    'viande'         => ['label'=>'Viandes',           'icon'=>'🥩', 'color'=>'#fce4ec','border'=>'#c62828','text'=>'#c62828'],
    'cereale'        => ['label'=>'Céréales',          'icon'=>'🌾', 'color'=>'#f9fbe7','border'=>'#827717','text'=>'#827717'],
    'autre'          => ['label'=>'Autres',            'icon'=>'🧂', 'color'=>'#f5f5f5','border'=>'#616161','text'=>'#616161'],
];

// Construire les stats
$statsMap = [];
foreach ($stats as $s) $statsMap[$s['type']] = $s['total'];
$total = array_sum(array_column($stats, 'total'));
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

/* Stats */
.stats-row { display:flex; gap:12px; margin-bottom:24px; flex-wrap:wrap; }
.stat-card {
    background:#fff; border-radius:10px; padding:14px 18px;
    display:flex; align-items:center; gap:12px;
    border:2px solid var(--border); flex:1; min-width:130px;
    box-shadow:0 2px 6px rgba(0,0,0,.05);
}
.stat-card.active { border-width:2px; }
.stat-icon-box { font-size:1.8rem; width:44px; height:44px; border-radius:8px; display:flex; align-items:center; justify-content:center; }
.stat-num  { font-size:1.4rem; font-weight:800; }
.stat-lbl  { font-size:0.75rem; color:#777; }

/* Recherche */
.search-form { display:flex; gap:10px; margin-bottom:18px; }
.search-form input { flex:1; padding:10px 14px; border:2px solid var(--border); border-radius:6px; font-size:0.9rem; outline:none; }
.search-form input:focus { border-color:var(--green); }
.btn-orange { background:var(--orange); color:#fff; border:none; padding:10px 18px; border-radius:6px; cursor:pointer; font-weight:600; }
.btn-clear  { background:#e0e0e0; color:#333; padding:10px 14px; border-radius:6px; text-decoration:none; font-weight:600; }

/* Table */
.table-wrap { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,.07); overflow:hidden; }
table { width:100%; border-collapse:collapse; font-size:0.9rem; }
thead { background:var(--green); color:#fff; }
thead th { padding:13px 16px; text-align:left; font-weight:600; }
tbody tr { border-bottom:1px solid var(--border); transition:background .15s; }
tbody tr:hover { background:#f1f8e9; }
tbody td { padding:11px 16px; vertical-align:middle; }
.ing-img { width:52px; height:52px; object-fit:cover; border-radius:6px; border:2px solid var(--border); }
.no-img  { width:52px; height:52px; background:#f5f5f5; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; border:2px solid var(--border); }
.badge-type { display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.76rem; font-weight:700; }
.actions { display:flex; gap:6px; }
.btn-voir { background:#1565c0; color:#fff; padding:5px 11px; border-radius:5px; text-decoration:none; font-size:0.8rem; font-weight:600; }
.btn-edit { background:var(--orange); color:#fff; padding:5px 11px; border-radius:5px; text-decoration:none; font-size:0.8rem; font-weight:600; }
.btn-del  { background:var(--red); color:#fff; padding:5px 11px; border-radius:5px; border:none; cursor:pointer; font-size:0.8rem; font-weight:600; }
.empty { text-align:center; padding:50px; color:#999; }

/* Modal */
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
    <h2>🥦 Gestion des Ingrédients</h2>
    <a href="/2A35/Admin/ingredient/create" class="btn-green">＋ Nouvel Ingrédient</a>
</div>

<?php if ($success): ?><div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div><?php endif; ?>

<!-- Stats -->
<div class="stats-row">
    <div class="stat-card" style="border-color:#9e9e9e;">
        <div class="stat-icon-box" style="background:#f5f5f5;">🧺</div>
        <div><div class="stat-num" style="color:#333;"><?= $total ?></div><div class="stat-lbl">Total ingrédients</div></div>
    </div>
    <?php foreach ($types as $key => $t): ?>
    <div class="stat-card active" style="border-color:<?= $t['border'] ?>; background:<?= $t['color'] ?>;">
        <div class="stat-icon-box" style="background:#fff;"><?= $t['icon'] ?></div>
        <div>
            <div class="stat-num" style="color:<?= $t['text'] ?>;"><?= $statsMap[$key] ?? 0 ?></div>
            <div class="stat-lbl"><?= $t['label'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Recherche -->
<form class="search-form" method="GET" action="/2A35/Admin/ingredient">
    <input type="text" id="searchIngInput" name="search" placeholder="Rechercher par nom..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" autocomplete="off">
    <select id="typeFilter" name="type" style="padding:10px 14px; border:2px solid var(--border); border-radius:6px; font-size:0.9rem; outline:none; min-width:170px;">
        <option value="">Tous les types</option>
        <?php foreach ($types as $key => $t): ?>
            <option value="<?= $key ?>" <?= ($_GET['type'] ?? '') === $key ? 'selected' : '' ?>>
                <?= $t['icon'] ?> <?= $t['label'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn-orange">🔍 Rechercher</button>
    <?php if (!empty($_GET['search']) || !empty($_GET['type'])): ?>
        <a href="/2A35/Admin/ingredient" class="btn-clear">✕ Effacer</a>
    <?php endif; ?>
</form>

<!-- Résultats AJAX -->
<div id="ajaxIngResults"></div>
</form>

<!-- Tableau -->
<div class="table-wrap">
<?php if (empty($ingredients)): ?>
    <div class="empty"><p>Aucun ingrédient. <a href="/2A35/Admin/ingredient/create">Ajouter le premier !</a></p></div>
<?php else: ?>
    <table>
        <thead>
            <tr><th>#</th><th>Image</th><th>Nom</th><th>Type</th><th>Protéines</th><th>Calcium</th><th>Glucides</th><th>Lipides</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($ingredients as $ing): ?>
            <?php $t = $types[$ing['type']] ?? $types['autre']; ?>
            <tr>
                <td><?= $ing['id'] ?></td>
                <td>
                    <?php if ($ing['image']): ?>
                        <img src="/2A35/assets/uploads/ingredients/<?= htmlspecialchars($ing['image']) ?>" class="ing-img" alt="">
                    <?php else: ?>
                        <div class="no-img"><?= $t['icon'] ?></div>
                    <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($ing['nom']) ?></strong></td>
                <td>
                    <span class="badge-type" style="background:<?= $t['color'] ?>;color:<?= $t['text'] ?>;border:1px solid <?= $t['border'] ?>;">
                        <?= $t['icon'] ?> <?= $t['label'] ?>
                    </span>
                </td>
                <td style="color:#2e7d32;font-weight:600;"><?= $ing['proteines'] ?? 0 ?> g</td>
                <td style="color:#1565c0;font-weight:600;"><?= $ing['calcium']   ?? 0 ?> mg</td>
                <td style="color:#f57c00;font-weight:600;"><?= $ing['glucides']  ?? 0 ?> g</td>
                <td style="color:#c62828;font-weight:600;"><?= $ing['lipides']   ?? 0 ?> g</td>
                <td>
                    <div class="actions">
                        <a href="/2A35/Admin/ingredient/show/<?= $ing['id'] ?>" class="btn-voir">👁 Voir</a>
                        <a href="/2A35/Admin/ingredient/edit/<?= $ing['id'] ?>" class="btn-edit">✏️ Modifier</a>
                        <button class="btn-del" onclick="confirmer(<?= $ing['id'] ?>, '<?= htmlspecialchars(addslashes($ing['nom'])) ?>')">🗑 Supprimer</button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>

<!-- Modal suppression -->
<div class="modal-bg" id="modalDel">
    <div class="modal">
        <h3>⚠️ Confirmer la suppression</h3>
        <p>Supprimer <strong id="nomIng"></strong> ?</p>
        <div class="modal-btns">
            <button class="btn-ann" onclick="document.getElementById('modalDel').classList.remove('show')">Annuler</button>
            <form id="formDel" method="POST"><button type="submit" class="btn-conf">Supprimer</button></form>
        </div>
    </div>
</div>

<script>
function confirmer(id, nom) {
    document.getElementById('nomIng').textContent = nom;
    document.getElementById('formDel').action = '/2A35/Admin/ingredient/delete/' + id;
    document.getElementById('modalDel').classList.add('show');
}
document.getElementById('modalDel').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('show');
});

// ── Recherche AJAX dynamique ──────────────────────────────────────────────────
const searchIngInput = document.getElementById('searchIngInput');
const typeFilter     = document.getElementById('typeFilter');
const tableWrapIng   = document.querySelector('.table-wrap');
const ajaxIngResults = document.getElementById('ajaxIngResults');

const typesData = <?= json_encode($types) ?>;

function rechercheIngAjax() {
    const search = searchIngInput.value.trim();
    const type   = typeFilter.value;

    if (!search && !type) {
        tableWrapIng.style.display = '';
        ajaxIngResults.innerHTML   = '';
        return;
    }

    const url = `/2A35/Admin/ingredient/ajax?search=${encodeURIComponent(search)}&type=${encodeURIComponent(type)}`;

    fetch(url)
        .then(r => r.json())
        .then(ingredients => {
            tableWrapIng.style.display = 'none';

            if (ingredients.length === 0) {
                ajaxIngResults.innerHTML = '<div style="background:#fff;border-radius:10px;padding:40px;text-align:center;color:#999;box-shadow:0 2px 12px rgba(0,0,0,.07);">Aucun ingrédient trouvé.</div>';
                return;
            }

            let html = `<div class="table-wrap">
                <table>
                    <thead><tr><th>#</th><th>Image</th><th>Nom</th><th>Type</th><th>Actions</th></tr></thead>
                    <tbody>`;

            ingredients.forEach(ing => {
                const t   = typesData[ing.type] || typesData['autre'];
                const img = ing.image
                    ? `<img src="/2A35/assets/uploads/ingredients/${ing.image}" class="ing-img" alt="">`
                    : `<div class="no-img">${t.icon}</div>`;

                html += `<tr>
                    <td>${ing.id}</td>
                    <td>${img}</td>
                    <td><strong>${ing.nom}</strong></td>
                    <td><span class="badge-type" style="background:${t.color};color:${t.text};border:1px solid ${t.border};">${t.icon} ${t.label}</span></td>
                    <td>
                        <div class="actions">
                            <a href="/2A35/Admin/ingredient/show/${ing.id}" class="btn-voir">👁 Voir</a>
                            <a href="/2A35/Admin/ingredient/edit/${ing.id}" class="btn-edit">✏️ Modifier</a>
                            <button class="btn-del" onclick="confirmer(${ing.id}, '${ing.nom.replace(/'/g,"\\'")}')">🗑 Supprimer</button>
                        </div>
                    </td>
                </tr>`;
            });

            html += `</tbody></table></div>`;
            ajaxIngResults.innerHTML = html;
        })
        .catch(() => {
            ajaxIngResults.innerHTML = '<div style="color:red;padding:10px;">Erreur de recherche.</div>';
        });
}

let timerIng;
searchIngInput.addEventListener('input', () => {
    clearTimeout(timerIng);
    timerIng = setTimeout(rechercheIngAjax, 300);
});

typeFilter.addEventListener('change', rechercheIngAjax);
</script>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
