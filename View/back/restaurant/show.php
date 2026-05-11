<?php
$page_title  = 'Détails du Restaurant';
$active_menu = 'restaurant';
ob_start();
?>
<style>
.detail-card { background:#fff; border-radius:10px; padding:30px; box-shadow:0 2px 15px rgba(0,0,0,.08); max-width:900px; margin:0 auto; }
.detail-header { display:flex; gap:30px; margin-bottom:40px; border-bottom:1px solid #eee; padding-bottom:30px; }
.detail-img { width:300px; height:200px; object-fit:cover; border-radius:12px; }
.meal-list { margin-top:30px; }
.meal-item { display:flex; justify-content:space-between; padding:15px; border-bottom:1px solid #eee; }
.btn-back { display:inline-block; margin-bottom:20px; text-decoration:none; color:#666; font-weight:600; }
</style>

<a href="/2A35/Admin/restaurant" class="btn-back">← Retour à la liste</a>

<div class="detail-card">
    <div class="detail-header">
        <?php if ($restaurant['image']): ?>
            <img src="/2A35/assets/uploads/restaurants/<?= htmlspecialchars($restaurant['image']) ?>" class="detail-img">
        <?php else: ?>
            <div style="width:300px; height:200px; background:#f5f5f5; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:3rem;">🍴</div>
        <?php endif; ?>
        
        <div>
            <h1 style="margin:0 0 10px;"><?= htmlspecialchars($restaurant['nom']) ?></h1>
            <p style="color:#666; font-size:1.1rem;"><?= htmlspecialchars($restaurant['adresse']) ?></p>
            <span class="badge" style="background:#e3f2fd; color:#1565c0; padding:5px 15px; border-radius:20px;"><?= htmlspecialchars($restaurant['type_cuisine']) ?></span>
            <div style="margin-top:20px;">
                <strong>Contact:</strong> <?= htmlspecialchars($restaurant['email']) ?> | <?= htmlspecialchars($restaurant['telephone']) ?>
            </div>
        </div>
    </div>

    <h3>Description</h3>
    <p><?= nl2br(htmlspecialchars($restaurant['description'])) ?></p>

    <div class="meal-list">
        <h3>🍽️ Menu / Plats</h3>
        <?php if (empty($meals)): ?>
            <p class="text-muted">Aucun plat enregistré pour ce restaurant.</p>
        <?php else: ?>
            <?php foreach ($meals as $m): ?>
                <div class="meal-item">
                    <div>
                        <strong><?= htmlspecialchars($m['nom']) ?></strong>
                        <div style="font-size:0.85rem; color:#888;"><?= htmlspecialchars($m['description']) ?></div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:bold; color:#2e7d32;"><?= number_format($m['prix'], 2) ?> DT</div>
                        <div style="font-size:0.8rem;"><?= $m['calories'] ?> kcal</div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
