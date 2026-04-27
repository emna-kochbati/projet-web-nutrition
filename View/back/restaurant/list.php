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
    <span id="search-spinner">⏳ Recherche…</span>
</div>
<div id="result-count"></div>

<!-- Tableau mis à jour dynamiquement -->
<div id="table-container">
    <?php include __DIR__ . '/../../back/restaurant/_table_rows.php'; ?>
</div>

<script>
let searchTimer = null;

document.getElementById('search-input').addEventListener('input', function () {
    const query = this.value.trim();

    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        document.getElementById('search-spinner').style.display = 'inline';

        fetch('/2A35/Admin/restaurant/search?q=' + encodeURIComponent(query), {
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
    }, 300); // délai 300ms après la dernière frappe
});

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

// Afficher le compte initial
document.getElementById('result-count').textContent =
    '<?= count($restaurants) ?> restaurant<?= count($restaurants) > 1 ? "s" : "" ?> trouvé<?= count($restaurants) > 1 ? "s" : "" ?>';
</script>

<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
