<?php
$page_title  = 'Détail — ' . htmlspecialchars($recette['nom']);
$active_menu = 'recette';
ob_start();
?>
<style>
:root { --green:#2e7d32; --orange:#f57c00; --red:#c62828; --border:#e0e0e0; }
.show-card { background:#fff; border-radius:12px; box-shadow:0 2px 16px rgba(0,0,0,.08); overflow:hidden; max-width:800px; }
.show-hero { position:relative; height:220px; background:linear-gradient(135deg,#2e7d32,#1b5e20); display:flex; align-items:flex-end; padding:22px; }
.show-hero img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.35; }
.hero-content { position:relative; z-index:1; color:#fff; }
.hero-content h2 { font-size:1.7rem; font-weight:800; margin:0 0 8px; }
.hero-badges { display:flex; gap:8px; flex-wrap:wrap; }
.hbadge { background:rgba(255,255,255,.2); color:#fff; padding:4px 12px; border-radius:20px; font-size:0.8rem; font-weight:700; }
.show-body { padding:26px; }
.meta-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(120px,1fr)); gap:12px; margin-bottom:24px; }
.meta-item { background:#f9fbe7; border-radius:8px; padding:14px; text-align:center; border:1px solid #dcedc8; }
.meta-icon { font-size:1.5rem; }
.meta-val  { font-size:1.1rem; font-weight:700; color:var(--green); }
.meta-lbl  { font-size:0.76rem; color:#777; }
.section-title { font-size:.95rem; font-weight:700; color:var(--green); text-transform:uppercase; letter-spacing:.05em; margin-bottom:12px; padding-bottom:6px; border-bottom:2px solid #e8f5e9; }
.ing-table { width:100%; border-collapse:collapse; font-size:.9rem; margin-bottom:20px; }
.ing-table th { background:#f1f8e9; color:var(--green); padding:9px 12px; text-align:left; font-weight:700; border-bottom:2px solid #c8e6c9; }
.ing-table td { padding:9px 12px; border-bottom:1px solid var(--border); }
.show-actions { display:flex; gap:12px; margin-top:20px; padding-top:18px; border-top:2px solid var(--border); flex-wrap:wrap; }
.btn-edit-s { background:var(--orange); color:#fff; padding:11px 22px; border-radius:7px; text-decoration:none; font-weight:700; }
.btn-del-s  { background:var(--red); color:#fff; padding:11px 22px; border-radius:7px; border:none; cursor:pointer; font-weight:700; }
.btn-back-s { background:#e0e0e0; color:#333; padding:11px 22px; border-radius:7px; text-decoration:none; font-weight:600; }
.modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; }
.modal-bg.show { display:flex; }
.modal { background:#fff; border-radius:10px; padding:30px; max-width:400px; width:90%; text-align:center; }
.modal h3 { margin:0 0 10px; }
.modal p  { color:#555; margin-bottom:22px; }
.modal-btns { display:flex; gap:12px; justify-content:center; }
.btn-ann  { background:#e0e0e0; color:#333; padding:10px 22px; border:none; border-radius:6px; cursor:pointer; font-weight:600; }
.btn-conf { background:var(--red); color:#fff; padding:10px 22px; border:none; border-radius:6px; cursor:pointer; font-weight:600; }
</style>

<div class="show-card">
    <!-- Hero -->
    <div class="show-hero">
        <?php if ($recette['image']): ?>
            <img src="/2A35/assets/uploads/recettes/<?= htmlspecialchars($recette['image']) ?>" alt="">
        <?php endif; ?>
        <div class="hero-content">
            <h2><?= htmlspecialchars($recette['nom']) ?></h2>
            <div class="hero-badges">
                <span class="hbadge">🗂 <?= htmlspecialchars($recette['categorie']) ?></span>
                <span class="hbadge">⚡ <?= htmlspecialchars($recette['difficulte']) ?></span>
                <span class="hbadge">📅 <?= date('d/m/Y', strtotime($recette['created_at'])) ?></span>
            </div>
        </div>
    </div>

    <div class="show-body">
        <!-- Stats -->
        <div class="meta-grid">
            <div class="meta-item"><div class="meta-icon">⏱️</div><div class="meta-val"><?= $recette['duree'] ?> min</div><div class="meta-lbl">Durée</div></div>
            <div class="meta-item"><div class="meta-icon">🔥</div><div class="meta-val"><?= $recette['calories'] ?> kcal</div><div class="meta-lbl">Calories</div></div>
            <div class="meta-item"><div class="meta-icon">📊</div><div class="meta-val"><?= htmlspecialchars($recette['difficulte']) ?></div><div class="meta-lbl">Difficulté</div></div>
            <div class="meta-item"><div class="meta-icon">🗂</div><div class="meta-val" style="font-size:.85rem"><?= htmlspecialchars($recette['categorie']) ?></div><div class="meta-lbl">Catégorie</div></div>
            <div class="meta-item"><div class="meta-icon">🥦</div><div class="meta-val"><?= count($ingredients) ?></div><div class="meta-lbl">Ingrédients</div></div>
        </div>

        <!-- Ingrédients -->
        <?php if (!empty($ingredients)): ?>
        <div class="section-title">🥦 Ingrédients</div>
        <table class="ing-table">
            <thead>
                <tr><th>Nom</th><th>Quantité</th><th>Unité</th></tr>
            </thead>
            <tbody>
            <?php foreach ($ingredients as $ing): ?>
                <tr>
                    <td>🌿 <?= htmlspecialchars($ing['nom']) ?></td>
                    <td style="font-weight:700;color:var(--orange)"><?= $ing['quantite'] ?></td>
                    <td><?= htmlspecialchars($ing['unite']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="color:#999;font-style:italic;margin-bottom:20px;">Aucun ingrédient enregistré.</p>
        <?php endif; ?>

        <!-- Valeurs nutritionnelles calculées par jointure -->
        <?php if (!empty($valeursNutri) && ($valeursNutri['proteines'] + $valeursNutri['calcium'] + $valeursNutri['glucides'] + $valeursNutri['lipides']) > 0): ?>
        <div class="section-title">🧪 Valeurs nutritionnelles</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:10px;margin-bottom:20px;">
            <div style="background:#e8f5e9;border-radius:8px;padding:14px;text-align:center;border:1px solid #c8e6c9;">
                <div style="font-size:1.4rem;">💪</div>
                <div style="font-size:1.1rem;font-weight:700;color:#2e7d32;"><?= $valeursNutri['proteines'] ?> g</div>
                <div style="font-size:0.76rem;color:#777;">Protéines</div>
            </div>
            <div style="background:#e3f2fd;border-radius:8px;padding:14px;text-align:center;border:1px solid #bbdefb;">
                <div style="font-size:1.4rem;">🦴</div>
                <div style="font-size:1.1rem;font-weight:700;color:#1565c0;"><?= $valeursNutri['calcium'] ?> mg</div>
                <div style="font-size:0.76rem;color:#777;">Calcium</div>
            </div>
            <div style="background:#fff3e0;border-radius:8px;padding:14px;text-align:center;border:1px solid #ffe0b2;">
                <div style="font-size:1.4rem;">⚡</div>
                <div style="font-size:1.1rem;font-weight:700;color:#f57c00;"><?= $valeursNutri['glucides'] ?> g</div>
                <div style="font-size:0.76rem;color:#777;">Glucides</div>
            </div>
            <div style="background:#fce4ec;border-radius:8px;padding:14px;text-align:center;border:1px solid #f8bbd0;">
                <div style="font-size:1.4rem;">🫧</div>
                <div style="font-size:1.1rem;font-weight:700;color:#c62828;"><?= $valeursNutri['lipides'] ?> g</div>
                <div style="font-size:0.76rem;color:#777;">Lipides</div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Nutri-Score -->
        <?php if (isset($nutriScore) && $nutriScore['lettre'] !== '?'): ?>
        <div class="section-title">🏅 Nutri-Score</div>
        <div style="background:#fff;border-radius:12px;border:2px solid #e0e0e0;padding:20px;margin-bottom:20px;">

            <!-- Barre des 5 lettres -->
            <div style="display:flex;gap:6px;align-items:center;margin-bottom:16px;">
                <?php foreach (['A','B','C','D','E'] as $l):
                    $configs = [
                        'A' => ['bg'=>'#1a7a1a','size'=>'2rem'],
                        'B' => ['bg'=>'#5aab1f','size'=>'1.8rem'],
                        'C' => ['bg'=>'#f5c800','size'=>'1.6rem'],
                        'D' => ['bg'=>'#e07800','size'=>'1.6rem'],
                        'E' => ['bg'=>'#d32f2f','size'=>'1.6rem'],
                    ];
                    $isActive = ($l === $nutriScore['lettre']);
                    $cfg = $configs[$l];
                ?>
                <div style="
                    background:<?= $cfg['bg'] ?>;
                    color:#fff;
                    width:<?= $isActive ? '52px' : '38px' ?>;
                    height:<?= $isActive ? '52px' : '38px' ?>;
                    border-radius:8px;
                    display:flex;align-items:center;justify-content:center;
                    font-size:<?= $isActive ? '1.5rem' : '1rem' ?>;
                    font-weight:900;
                    opacity:<?= $isActive ? '1' : '0.35' ?>;
                    transition:all .2s;
                    <?= $isActive ? 'box-shadow:0 4px 12px rgba(0,0,0,.25);transform:scale(1.1);' : '' ?>
                "><?= $l ?></div>
                <?php endforeach; ?>

                <!-- Label -->
                <div style="margin-left:14px;">
                    <div style="font-size:1.1rem;font-weight:800;color:<?= $nutriScore['bg'] ?>;">
                        <?= $nutriScore['lettre'] ?> — <?= $nutriScore['label'] ?>
                    </div>
                    <div style="font-size:0.78rem;color:#888;margin-top:2px;">
                        Score calculé : <?= $nutriScore['score'] ?> pts
                        (<?= $nutriScore['details']['ptsNeg'] ?> négatifs − <?= $nutriScore['details']['ptsPos'] ?> positifs)
                    </div>
                </div>
            </div>

            <!-- Détail du calcul -->
            <div style="background:#f9f9f9;border-radius:8px;padding:12px;font-size:0.82rem;color:#555;">
                <strong style="color:#333;">Calcul basé sur 100g de recette :</strong>
                <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:8px;">
                    <span>💪 Protéines : <strong><?= $nutriScore['details']['proteines'] ?>g</strong></span>
                    <span>🦴 Calcium : <strong><?= $nutriScore['details']['calcium'] ?>mg</strong></span>
                    <span>⚡ Glucides : <strong><?= $nutriScore['details']['glucides'] ?>g</strong></span>
                    <span>🫧 Lipides : <strong><?= $nutriScore['details']['lipides'] ?>g</strong></span>
                    <span>🔥 Calories : <strong><?= $nutriScore['details']['calories'] ?> kcal</strong></span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="show-actions">
            <a href="/2A35/Admin/recette/edit/<?= $recette['id'] ?>" class="btn-edit-s">✏️ Modifier</a>
            <button class="btn-del-s" onclick="document.getElementById('modalDel').classList.add('show')">🗑 Supprimer</button>
            <a href="/2A35/Admin/recette" class="btn-back-s">← Retour à la liste</a>
        </div>
    </div>
</div>

<!-- Modal suppression -->
<div class="modal-bg" id="modalDel">
    <div class="modal">
        <h3>⚠️ Confirmer la suppression</h3>
        <p>Supprimer <strong><?= htmlspecialchars($recette['nom']) ?></strong> ?</p>
        <div class="modal-btns">
            <button class="btn-ann" onclick="document.getElementById('modalDel').classList.remove('show')">Annuler</button>
            <form method="POST" action="/2A35/Admin/recette/delete/<?= $recette['id'] ?>">
                <button type="submit" class="btn-conf">Supprimer</button>
            </form>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
