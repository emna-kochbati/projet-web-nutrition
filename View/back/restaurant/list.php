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

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
