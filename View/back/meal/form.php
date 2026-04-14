<?php
$isEdit      = isset($meal['id']);
$page_title  = $isEdit ? 'Modifier le Plat' : 'Nouveau Plat';
$active_menu = 'restaurant';
ob_start();
?>

<style>
.form-card { background:#fff; border-radius:10px; padding:30px; box-shadow:0 2px 10px rgba(0,0,0,.08); max-width:680px; }
.form-group { margin-bottom:18px; }
label { display:block; font-weight:600; font-size:.88rem; color:#333; margin-bottom:6px; }
input[type=text], input[type=number], input[type=file], select, textarea {
    width:100%; padding:10px 14px; border:1px solid #ccc; border-radius:6px; font-size:.9rem; font-family:inherit;
}
input:focus, select:focus, textarea:focus { outline:none; border-color:#2e7d32; box-shadow:0 0 0 3px rgba(46,125,50,.15); }
textarea { resize:vertical; min-height:80px; }
.error-msg { color:#c62828; font-size:.8rem; margin-top:4px; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.btn-submit { background:#2e7d32; color:#fff; padding:11px 28px; border:none; border-radius:6px; font-size:.95rem; font-weight:700; cursor:pointer; }
.btn-submit:hover { background:#1b5e20; }
.btn-cancel { background:#eee; color:#333; padding:11px 20px; border-radius:6px; text-decoration:none; font-size:.9rem; font-weight:600; }
.form-actions { display:flex; gap:12px; margin-top:24px; }
.checkbox-group { display:flex; align-items:center; gap:10px; }
.checkbox-group input { width:auto; }
.current-img img { width:70px; height:70px; object-fit:cover; border-radius:8px; border:2px solid #ddd; margin-top:6px; }
</style>

<div class="form-card">
    <h2 style="margin-bottom:24px;font-size:1.2rem;color:#1a1a1a;">
        <?= $isEdit ? '✏️ Modifier le Plat' : '➕ Nouveau Plat' ?>
    </h2>

    <form method="POST"
          action="<?= $isEdit ? '/2A35/Admin/meal/update/'.$meal['id'] : '/2A35/Admin/meal/store' ?>"
          enctype="multipart/form-data">

        <div class="form-group">
            <label for="restaurant_id">Restaurant *</label>
            <select id="restaurant_id" name="restaurant_id">
                <option value="">-- Sélectionner un restaurant --</option>
                <?php foreach ($restaurants as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= ($meal['restaurant_id'] ?? '') == $r['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['restaurant_id'])): ?><div class="error-msg">⚠ <?= $errors['restaurant_id'] ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="nom">Nom du plat *</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($meal['nom'] ?? '') ?>" placeholder="Ex: Couscous Agneau">
            <?php if (!empty($errors['nom'])): ?><div class="error-msg">⚠ <?= $errors['nom'] ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Décrivez le plat…"><?= htmlspecialchars($meal['description'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="categorie">Catégorie *</label>
                <select id="categorie" name="categorie">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach (['entree' => 'Entrée', 'plat_principal' => 'Plat principal', 'dessert' => 'Dessert', 'boisson' => 'Boisson', 'snack' => 'Snack'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= ($meal['categorie'] ?? '') === $val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['categorie'])): ?><div class="error-msg">⚠ <?= $errors['categorie'] ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="prix">Prix (DT) *</label>
                <input type="number" id="prix" name="prix" step="0.01" min="0" value="<?= htmlspecialchars($meal['prix'] ?? '') ?>" placeholder="Ex: 12.50">
                <?php if (!empty($errors['prix'])): ?><div class="error-msg">⚠ <?= $errors['prix'] ?></div><?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="calories">Calories (kcal)</label>
                <input type="number" id="calories" name="calories" min="0" value="<?= htmlspecialchars($meal['calories'] ?? '') ?>" placeholder="Ex: 450">
                <?php if (!empty($errors['calories'])): ?><div class="error-msg">⚠ <?= $errors['calories'] ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Disponibilité</label>
                <div class="checkbox-group" style="margin-top:10px;">
                    <input type="checkbox" id="disponible" name="disponible" value="1"
                        <?= (!isset($meal['disponible']) || $meal['disponible']) ? 'checked' : '' ?>>
                    <label for="disponible" style="margin:0;font-weight:400;">Plat disponible</label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
            <?php if (!empty($errors['image'])): ?><div class="error-msg">⚠ <?= $errors['image'] ?></div><?php endif; ?>
            <?php if ($isEdit && !empty($meal['image'])): ?>
                <div class="current-img">
                    <small style="color:#666;">Image actuelle :</small><br>
                    <img src="/2A35/assets/uploads/meals/<?= htmlspecialchars($meal['image']) ?>" alt="">
                </div>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <?= $isEdit ? '💾 Enregistrer' : '➕ Ajouter' ?>
            </button>
            <a href="/2A35/Admin/meal" class="btn-cancel">Annuler</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
