<?php
$page_title  = 'Plats (Meals)';
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
.search-bar { display:flex; gap:10px; margin-bottom:20px; }
.search-bar input { flex:1; padding:9px 14px; border:1px solid #ccc; border-radius:6px; font-size:.9rem; }
.search-bar button { background:#2e7d32; color:#fff; border:none; padding:9px 18px; border-radius:6px; cursor:pointer; font-weight:600; }
table { width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.07); }
thead { background:#2e7d32; color:#fff; }
th, td { padding:11px 14px; text-align:left; font-size:.87rem; }
tbody tr:nth-child(even) { background:#f9f9f9; }
tbody tr:hover { background:#f1f8e9; }
.badge { display:inline-block; padding:3px 10px; border-radius:12px; font-size:.78rem; font-weight:600; }
.badge-cat { background:#e3f2fd; color:#1565c0; }
.badge-dispo { background:#e8f5e9; color:#2e7d32; }
.badge-indispo { background:#ffebee; color:#c62828; }
.actions { display:flex; gap:6px; }
.img-thumb { width:44px; height:44px; object-fit:cover; border-radius:6px; }
.no-img { width:44px; height:44px; background:#eee; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; }
</style>

<?php if ($success): ?>
    <div class="alert-success">✅ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert-error">❌ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="page-header">
    <h2 style="font-size:1.3rem;color:#1a1a1a;">🍽️ Liste des Plats</h2>
    <a href="/2A35/Admin/meal/create" class="btn-primary">➕ Nouveau Plat</a>
</div>

<form class="search-bar" method="GET" action="/2A35/Admin/meal">
    <input type="text" name="search" placeholder="Rechercher un plat…" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <button type="submit">🔍 Rechercher</button>
    <?php if (!empty($_GET['search'])): ?>
        <a href="/2A35/Admin/meal" style="padding:9px 14px;background:#eee;border-radius:6px;text-decoration:none;color:#333;">✕ Effacer</a>
    <?php endif; ?>
</form>

<?php if (empty($meals)): ?>
    <p style="text-align:center;color:#888;padding:40px;">Aucun plat trouvé.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Nom</th>
            <th>Restaurant</th>
            <th>Catégorie</th>
            <th>Prix</th>
            <th>Calories</th>
            <th>Dispo</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($meals as $m): ?>
        <tr>
            <td><?= $m['id'] ?></td>
            <td>
                <?php if ($m['image']): ?>
                    <img src="/2A35/assets/uploads/meals/<?= htmlspecialchars($m['image']) ?>" class="img-thumb" alt="">
                <?php else: ?>
                    <div class="no-img">🍽️</div>
                <?php endif; ?>
            </td>
            <td><strong><?= htmlspecialchars($m['nom']) ?></strong></td>
            <td><?= htmlspecialchars($m['restaurant_nom']) ?></td>
            <td><span class="badge badge-cat"><?= htmlspecialchars($m['categorie']) ?></span></td>
            <td><?= number_format($m['prix'], 2) ?> DT</td>
            <td><?= $m['calories'] ? $m['calories'] . ' kcal' : '—' ?></td>
            <td>
                <span class="badge <?= $m['disponible'] ? 'badge-dispo' : 'badge-indispo' ?>">
                    <?= $m['disponible'] ? '✅' : '❌' ?>
                </span>
            </td>
            <td>
                <div class="actions">
                    <a href="/2A35/Admin/meal/show/<?= $m['id'] ?>" class="btn-show">👁 Voir</a>
                    <a href="/2A35/Admin/meal/edit/<?= $m['id'] ?>" class="btn-edit">✏️</a>
                    <form method="POST" action="/2A35/Admin/meal/delete/<?= $m['id'] ?>" onsubmit="return confirm('Supprimer ce plat ?')">
                        <button type="submit" class="btn-danger">🗑</button>
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
