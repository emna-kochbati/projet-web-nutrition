<?php
$page_title  = 'Détail Restaurant';
$active_menu = 'restaurant';
ob_start();
?>

<style>
.detail-card { background:#fff; border-radius:10px; padding:30px; box-shadow:0 2px 10px rgba(0,0,0,.08); max-width:800px; }
.detail-header { display:flex; gap:24px; align-items:flex-start; margin-bottom:28px; }
.detail-img { width:140px; height:140px; object-fit:cover; border-radius:10px; border:2px solid #ddd; flex-shrink:0; }
.no-img-lg { width:140px; height:140px; background:#f0f0f0; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:3rem; flex-shrink:0; }
.detail-title { font-size:1.5rem; font-weight:700; color:#1a1a1a; margin-bottom:6px; }
.badge-cuisine { display:inline-block; padding:4px 12px; border-radius:12px; background:#e8f5e9; color:#2e7d32; font-size:.82rem; font-weight:600; }
.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:28px; }
.info-item label { font-size:.78rem; color:#888; font-weight:600; text-transform:uppercase; letter-spacing:.05em; }
.info-item p { font-size:.95rem; color:#222; margin-top:3px; }
.section-title { font-size:1.1rem; font-weight:700; color:#2e7d32; margin-bottom:14px; border-bottom:2px solid #e8f5e9; padding-bottom:6px; }
table { width:100%; border-collapse:collapse; }
thead { background:#f5f5f5; }
th, td { padding:10px 14px; text-align:left; font-size:.87rem; border-bottom:1px solid #eee; }
.badge-cat { display:inline-block; padding:2px 9px; border-radius:10px; font-size:.76rem; font-weight:600; background:#e3f2fd; color:#1565c0; }
.badge-dispo { background:#e8f5e9; color:#2e7d32; }
.badge-indispo { background:#ffebee; color:#c62828; }
.btn-edit { background:#1565c0; color:#fff; padding:8px 18px; border-radius:6px; text-decoration:none; font-size:.88rem; font-weight:600; }
.btn-back { background:#eee; color:#333; padding:8px 18px; border-radius:6px; text-decoration:none; font-size:.88rem; font-weight:600; }
.actions-bar { display:flex; gap:10px; margin-top:24px; }
</style>

<div class="detail-card">
    <div class="detail-header">
        <?php if (!empty($restaurant['image'])): ?>
            <img src="/2A35/assets/uploads/restaurants/<?= htmlspecialchars($restaurant['image']) ?>" class="detail-img" alt="">
        <?php else: ?>
            <div class="no-img-lg">🍴</div>
        <?php endif; ?>
        <div>
            <div class="detail-title"><?= htmlspecialchars($restaurant['nom']) ?></div>
            <span class="badge-cuisine"><?= htmlspecialchars($restaurant['type_cuisine']) ?></span>
            <?php if ($restaurant['description']): ?>
                <p style="margin-top:10px;color:#555;font-size:.9rem;"><?= htmlspecialchars($restaurant['description']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <label>📍 Adresse</label>
            <p><?= htmlspecialchars($restaurant['adresse']) ?></p>
        </div>
        <div class="info-item">
            <label>📞 Téléphone</label>
            <p><?= htmlspecialchars($restaurant['telephone'] ?? '—') ?></p>
        </div>
        <div class="info-item">
            <label>📧 Email</label>
            <p><?= htmlspecialchars($restaurant['email'] ?? '—') ?></p>
        </div>
        <div class="info-item">
            <label>🪑 Capacité</label>
            <p><?= $restaurant['capacite'] ? $restaurant['capacite'] . ' places' : '—' ?></p>
        </div>
        <div class="info-item">
            <label>📅 Ajouté le</label>
            <p><?= date('d/m/Y', strtotime($restaurant['created_at'])) ?></p>
        </div>
    </div>

    <!-- Liste des plats -->
    <div class="section-title">🍽️ Plats du restaurant (<?= count($meals) ?>)</div>

    <?php if (empty($meals)): ?>
        <p style="color:#888;text-align:center;padding:20px;">Aucun plat enregistré pour ce restaurant.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Calories</th>
                <th>Disponible</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($meals as $m): ?>
            <tr>
                <td><strong><?= htmlspecialchars($m['nom']) ?></strong></td>
                <td><span class="badge-cat"><?= htmlspecialchars($m['categorie']) ?></span></td>
                <td><?= number_format($m['prix'], 2) ?> DT</td>
                <td><?= $m['calories'] ? $m['calories'] . ' kcal' : '—' ?></td>
                <td>
                    <span class="badge-cat <?= $m['disponible'] ? 'badge-dispo' : 'badge-indispo' ?>">
                        <?= $m['disponible'] ? '✅ Oui' : '❌ Non' ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="actions-bar">
        <a href="/2A35/Admin/restaurant/edit/<?= $restaurant['id'] ?>" class="btn-edit">✏️ Modifier</a>
        <a href="/2A35/Admin/restaurant" class="btn-back">← Retour</a>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
