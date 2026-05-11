<?php
$page_title  = 'Détails du Plat';
$active_menu = 'meal';
ob_start();
?>
<style>
.detail-card { background:#fff; border-radius:10px; padding:30px; box-shadow:0 2px 15px rgba(0,0,0,.08); max-width:800px; margin:0 auto; }
.detail-header { display:flex; gap:30px; margin-bottom:30px; }
.detail-img { width:250px; height:250px; object-fit:cover; border-radius:12px; }
.btn-back { display:inline-block; margin-bottom:20px; text-decoration:none; color:#666; font-weight:600; }
.badge { display:inline-block; padding:5px 15px; border-radius:20px; font-weight:600; font-size:0.9rem; }
.b-cat { background:#e3f2fd; color:#1565c0; }
.b-dispo { background:#e8f5e9; color:#2e7d32; }
.b-nodispo { background:#ffebee; color:#c62828; }
</style>

<a href="/2A35/Admin/meal" class="btn-back">← Retour à la liste</a>

<div class="detail-card">
    <div class="detail-header">
        <?php if ($meal['image']): ?>
            <img src="/2A35/assets/uploads/meals/<?= htmlspecialchars($meal['image']) ?>" class="detail-img">
        <?php else: ?>
            <div style="width:250px; height:250px; background:#f5f5f5; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:4rem;">🍱</div>
        <?php endif; ?>
        
        <div>
            <h1 style="margin:0 0 10px;"><?= htmlspecialchars($meal['nom']) ?></h1>
            <p style="color:#666; font-size:1.1rem; margin-bottom:15px;">Restaurant: <a href="/2A35/Admin/restaurant/show/<?= $meal['restaurant_id'] ?>" style="color:#2e7d32; font-weight:600; text-decoration:none;"><?= htmlspecialchars($meal['restaurant_nom']) ?></a></p>
            
            <div style="display:flex; gap:10px; margin-bottom:20px;">
                <span class="badge b-cat"><?= htmlspecialchars(str_replace('_', ' ', $meal['categorie'])) ?></span>
                <span class="badge <?= $meal['disponible'] ? 'b-dispo' : 'b-nodispo' ?>">
                    <?= $meal['disponible'] ? 'Disponible' : 'Indisponible' ?>
                </span>
            </div>

            <div style="font-size:1.5rem; font-weight:700; color:#2e7d32;"><?= number_format($meal['prix'], 2) ?> DT</div>
            <?php if ($meal['calories']): ?>
                <div style="color:#666; margin-top:5px;"><?= $meal['calories'] ?> kcal</div>
            <?php endif; ?>
        </div>
    </div>

    <h3>Description</h3>
    <p style="line-height:1.6; color:#444;"><?= nl2br(htmlspecialchars($meal['description'] ?: 'Aucune description.')) ?></p>
    
    <div style="margin-top:40px; display:flex; gap:10px;">
        <a href="/2A35/Admin/meal/edit/<?= $meal['id'] ?>" style="background:#f57c00; color:#fff; padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:600;">✏️ Modifier</a>
        <form action="/2A35/Admin/meal/delete/<?= $meal['id'] ?>" method="POST" onsubmit="return confirm('Supprimer ce plat ?')">
            <button type="submit" style="background:#c62828; color:#fff; border:none; padding:10px 20px; border-radius:6px; cursor:pointer; font-weight:600;">🗑 Supprimer</button>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
