<?php
$page_title  = 'Recettes';
$active_menu = 'recette';
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
.search-bar { display:flex; gap:10px; margin-bottom:20px; }
.search-bar input { flex:1; padding:9px 14px; border:1px solid #ccc; border-radius:6px; font-size:.9rem; }
.search-bar button { background:#2e7d32; color:#fff; border:none; padding:9px 18px; border-radius:6px; cursor:pointer; font-weight:600; }
table { width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.07); }
thead { background:#2e7d32; color:#fff; }
th, td { padding:12px 16px; text-align:left; font-size:.88rem; }
tbody tr:nth-child(even) { background:#f9f9f9; }
tbody tr:hover { background:#f1f8e9; }
.badge { display:inline-block; padding:3px 10px; border-radius:12px; font-size:.78rem; font-weight:600; }
.badge-cat { background:#e8f5e9; color:#2e7d32; }
.actions { display:flex; gap:6px; }
.img-thumb { width:48px; height:48px; object-fit:cover; border-radius:6px; }
.no-img { width:48px; height:48px; background:#eee; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; }
</style>

<?php if (!empty($success)): ?>
    <div class="alert-success">✅ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert-error">❌ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="page-header">
    <h2 style="font-size:1.3rem;color:#1a1a1a;">🥘 Liste des Recettes</h2>
    <a href="/2A35/Admin/recette/create" class="btn-primary">➕ Nouvelle Recette</a>
</div>

<form class="search-bar" method="GET" action="/2A35/Admin/recette">
    <input type="text" name="search" placeholder="Rechercher par nom…" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <button type="submit">🔍 Rechercher</button>
    <?php if (!empty($_GET['search'])): ?>
        <a href="/2A35/Admin/recette" style="padding:9px 14px;background:#eee;border-radius:6px;text-decoration:none;color:#333;">✕ Effacer</a>
    <?php endif; ?>
</form>

<?php if (empty($recettes)): ?>
    <p style="text-align:center;color:#888;padding:40px;">Aucune recette trouvée.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Nom</th>
            <th>Catégorie</th>
            <th>Temps (min)</th>
            <th>Calories</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($recettes as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td>
                <?php if (!empty($r['image'])): ?>
                    <img src="/2A35/assets/uploads/recettes/<?= htmlspecialchars($r['image']) ?>" class="img-thumb" alt="">
                <?php else: ?>
                    <div class="no-img">🥘</div>
                <?php endif; ?>
            </td>
            <td><strong><?= htmlspecialchars($r['nom']) ?></strong></td>
            <td><span class="badge badge-cat"><?= htmlspecialchars($r['categorie'] ?? '—') ?></span></td>
            <td><?= $r['temps_preparation'] ? $r['temps_preparation'] . ' min' : '—' ?></td>
            <td><?= $r['calories'] ? $r['calories'] . ' kcal' : '—' ?></td>
            <td>
                <div class="actions">
                    <a href="/2A35/Admin/recette/edit/<?= $r['id'] ?>" class="btn-edit">✏️ Modifier</a>
                    <form method="POST" action="/2A35/Admin/recette/delete/<?= $r['id'] ?>" onsubmit="return confirm('Supprimer cette recette ?')">
                        <button type="submit" class="btn-danger">🗑 Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
