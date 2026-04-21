<?php
$page_title  = 'Détail — ' . htmlspecialchars($ingredient['nom']);
$active_menu = 'recette';

$types = [
    'legume'          => ['label'=>'Légume',           'icon'=>'🥕', 'color'=>'#e8f5e9','text'=>'#2e7d32'],
    'fruit'           => ['label'=>'Fruit',             'icon'=>'🍎', 'color'=>'#fff3e0','text'=>'#f57c00'],
    'produit-laitier' => ['label'=>'Produit laitier',   'icon'=>'🥛', 'color'=>'#e3f2fd','text'=>'#1565c0'],
    'epice'           => ['label'=>'Épice',             'icon'=>'🌶️', 'color'=>'#fbe9e7','text'=>'#bf360c'],
    'viande'          => ['label'=>'Viande',            'icon'=>'🥩', 'color'=>'#fce4ec','text'=>'#c62828'],
    'cereale'         => ['label'=>'Céréale',           'icon'=>'🌾', 'color'=>'#f9fbe7','text'=>'#827717'],
    'autre'           => ['label'=>'Autre',             'icon'=>'🧂', 'color'=>'#f5f5f5','text'=>'#616161'],
];
$t = $types[$ingredient['type']] ?? $types['autre'];

ob_start();
?>
<style>
:root { --green:#2e7d32; --orange:#f57c00; --red:#c62828; --border:#e0e0e0; }
.show-card { background:#fff; border-radius:12px; box-shadow:0 2px 16px rgba(0,0,0,.08); overflow:hidden; max-width:600px; }
.show-hero { height:200px; display:flex; align-items:center; justify-content:center; position:relative; }
.show-hero img { width:100%; height:100%; object-fit:cover; }
.show-hero .no-img { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:5rem; }
.show-body { padding:26px; }
.show-name { font-size:1.6rem; font-weight:800; margin:0 0 16px; }
.badge-type { display:inline-block; padding:6px 16px; border-radius:20px; font-size:0.9rem; font-weight:700; margin-bottom:20px; }
.meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:24px; }
.meta-item { background:#f9fbe7; border-radius:8px; padding:14px; text-align:center; border:1px solid #dcedc8; }
.meta-icon { font-size:1.5rem; }
.meta-val  { font-size:1rem; font-weight:700; color:var(--green); }
.meta-lbl  { font-size:0.76rem; color:#777; }
.show-actions { display:flex; gap:12px; padding-top:18px; border-top:2px solid var(--border); flex-wrap:wrap; }
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
    <!-- Image -->
    <div class="show-hero" style="background:<?= $t['color'] ?>;">
        <?php if ($ingredient['image']): ?>
            <img src="/2A35/assets/uploads/ingredients/<?= htmlspecialchars($ingredient['image']) ?>" alt="">
        <?php else: ?>
            <div class="no-img"><?= $t['icon'] ?></div>
        <?php endif; ?>
    </div>

    <div class="show-body">
        <h2 class="show-name"><?= htmlspecialchars($ingredient['nom']) ?></h2>

        <span class="badge-type" style="background:<?= $t['color'] ?>;color:<?= $t['text'] ?>;">
            <?= $t['icon'] ?> <?= $t['label'] ?>
        </span>

        <div class="meta-grid">
            <div class="meta-item">
                <div class="meta-icon">#️⃣</div>
                <div class="meta-val"><?= $ingredient['id'] ?></div>
                <div class="meta-lbl">ID</div>
            </div>
            <div class="meta-item">
                <div class="meta-icon">📅</div>
                <div class="meta-val" style="font-size:.85rem"><?= date('d/m/Y', strtotime($ingredient['created_at'])) ?></div>
                <div class="meta-lbl">Date d'ajout</div>
            </div>
        </div>

        <div class="show-actions">
            <a href="/2A35/Admin/ingredient/edit/<?= $ingredient['id'] ?>" class="btn-edit-s">✏️ Modifier</a>
            <button class="btn-del-s" onclick="document.getElementById('modalDel').classList.add('show')">🗑 Supprimer</button>
            <a href="/2A35/Admin/ingredient" class="btn-back-s">← Retour</a>
        </div>
    </div>
</div>

<!-- Modal suppression -->
<div class="modal-bg" id="modalDel">
    <div class="modal">
        <h3>⚠️ Confirmer la suppression</h3>
        <p>Supprimer <strong><?= htmlspecialchars($ingredient['nom']) ?></strong> ?</p>
        <div class="modal-btns">
            <button class="btn-ann" onclick="document.getElementById('modalDel').classList.remove('show')">Annuler</button>
            <form method="POST" action="/2A35/Admin/ingredient/delete/<?= $ingredient['id'] ?>">
                <button type="submit" class="btn-conf">Supprimer</button>
            </form>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
