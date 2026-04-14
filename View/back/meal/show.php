<?php
$page_title  = 'Détail Plat';
$active_menu = 'restaurant';
ob_start();
?>

<style>
.detail-card { background:#fff; border-radius:10px; padding:30px; box-shadow:0 2px 10px rgba(0,0,0,.08); max-width:600px; }
.detail-header { display:flex; gap:24px; align-items:flex-start; margin-bottom:28px; }
.detail-img { width:130px; height:130px; object-fit:cover; border-radius:10px; border:2px solid #ddd; flex-shrink:0; }
.no-img-lg { width:130px; height:130px; background:#f0f0f0; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:3rem; flex-shrink:0; }
.detail-title { font-size:1.4rem; font-weight:700; color:#1a1a1a; margin-bottom:6px; }
.badge { display:inline-block; padding:4px 12px; border-radius:12px; font-size:.82rem; font-weight:600; }
.badge-cat { background:#e3f2fd; color:#1565c0; }
.badge-dispo { background:#e8f5e9; color:#2e7d32; }
.badge-indispo { background:#ffebee; color:#c62828; }
.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.info-item label { font-size:.78rem; color:#888; font-weight:600; text-transform:uppercase; letter-spacing:.05em; }
.info-item p { font-size:.95rem; color:#222; margin-top:3px; }
.btn-edit { background:#1565c0; color:#fff; padding:8px 18px; border-radius:6px; text-decoration:none; font-size:.88rem; font-weight:600; }
.btn-back { background:#eee; color:#333; padding:8px 18px; border-radius:6px; text-decoration:none; font-size:.88rem; font-weight:600; }
.actions-bar { display:flex; gap:10px; margin-top:24px; }
</style>

<div class="detail-card">
    <div class="detail-header">
        <?php if (!empty($meal['image'])): ?>
            <img src="/2A35/assets/uploads/meals/<?= htmlspecialchars($meal['image']) ?>" class="detail-img" alt="">
        <?php else: ?>
            <div class="no-img-lg">🍽️</div>
        <?php endif; ?>
        <div>
            <div class="detail-title"><?= htmlspecialchars($meal['nom']) ?></div>
            <span class="badge badge-cat"><?= htmlspecialchars($meal['categorie']) ?></span>
            &nbsp;
            <span class="badge <?= $meal['disponible'] ? 'badge-dispo' : 'badge-indispo' ?>">
                <?= $meal['disponible'] ? '✅ Disponible' : '❌ Indisponible' ?>
            </span>
            <?php if ($meal['description']): ?>
                <p style="margin-top:10px;color:#555;font-size:.9rem;"><?= htmlspecialchars($meal['description']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <label>🍴 Restaurant</label>
            <p><?= htmlspecialchars($meal['restaurant_nom']) ?></p>
        </div>
        <div class="info-item">
            <label>💰 Prix</label>
            <p><?= number_format($meal['prix'], 2) ?> DT</p>
        </div>
        <div class="info-item">
            <label>🔥 Calories</label>
            <p><?= $meal['calories'] ? $meal['calories'] . ' kcal' : '—' ?></p>
        </div>
        <div class="info-item">
            <label>📅 Ajouté le</label>
            <p><?= date('d/m/Y', strtotime($meal['created_at'])) ?></p>
        </div>
    </div>

    <div class="actions-bar">
        <a href="/2A35/Admin/meal/edit/<?= $meal['id'] ?>" class="btn-edit">✏️ Modifier</a>
        <a href="/2A35/Admin/meal" class="btn-back">← Retour</a>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
