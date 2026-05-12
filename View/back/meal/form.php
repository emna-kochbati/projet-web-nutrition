<?php
$isEdit      = isset($meal['id']) && $meal['id'];
$page_title  = $isEdit ? 'Modifier le Plat' : 'Nouveau Plat';
$active_menu = 'meal';
ob_start();
?>
<style>
:root { --green:#2e7d32; --green-l:#4caf50; --border:#e0e0e0; }
.form-card { background:#fff; border-radius:10px; padding:30px; box-shadow:0 2px 15px rgba(0,0,0,.08); max-width:800px; margin:0 auto; }
.form-group { margin-bottom:20px; }
.form-group label { display:block; margin-bottom:8px; font-weight:600; color:#333; }
.form-group input, .form-group textarea, .form-group select { width:100%; padding:12px; border:2px solid var(--border); border-radius:6px; outline:none; font-size:0.95rem; }
.form-group input:focus { border-color:var(--green); }
.error-msg { color:#c62828; font-size:0.85rem; margin-top:5px; font-weight:600; }
.btn-submit { background:var(--green); color:#fff; border:none; padding:14px 30px; border-radius:6px; cursor:pointer; font-weight:700; width:100%; font-size:1rem; }
.btn-submit:hover { background:var(--green-l); }
</style>

<div class="form-card">
    <h2 style="margin-bottom:25px;"><?= $page_title ?></h2>
    <form action="/2A35/Admin/meal/<?= $isEdit ? 'update/'.$meal['id'] : 'store' ?>" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label>Restaurant</label>
            <select name="restaurant_id">
                <option value="">-- Sélectionner un restaurant --</option>
                <?php foreach ($restaurants as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= (isset($meal['restaurant_id']) && $meal['restaurant_id'] == $r['id']) ? 'selected' : '' ?>><?= htmlspecialchars($r['nom']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['restaurant_id'])): ?><div class="error-msg"><?= $errors['restaurant_id'] ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label>Nom du Plat</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($meal['nom'] ?? '') ?>" placeholder="Ex: Salade César">
            <?php if (isset($errors['nom'])): ?><div class="error-msg"><?= $errors['nom'] ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($meal['description'] ?? '') ?></textarea>
        </div>

        <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
                <label>Prix (DT)</label>
                <input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($meal['prix'] ?? '0.00') ?>">
                <?php if (isset($errors['prix'])): ?><div class="error-msg"><?= $errors['prix'] ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Calories (kcal)</label>
                <input type="number" name="calories" value="<?= htmlspecialchars($meal['calories'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Catégorie</label>
            <select name="categorie">
                <?php 
                $cats = ['entree' => 'Entrée', 'plat_principal' => 'Plat Principal', 'dessert' => 'Dessert', 'boisson' => 'Boisson', 'snack' => 'Snack'];
                foreach ($cats as $val => $lbl): ?>
                    <option value="<?= $val ?>" <?= (isset($meal['categorie']) && $meal['categorie'] === $val) ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                <input type="checkbox" name="disponible" value="1" <?= (!isset($meal['disponible']) || $meal['disponible']) ? 'checked' : '' ?> style="width:auto;">
                Disponible
            </label>
        </div>

        <div class="form-group">
            <label>Image du Plat</label>
            <input type="file" name="image" accept="image/*">
            <?php if (isset($meal['image']) && $meal['image']): ?>
                <div style="margin-top:10px;"><img src="/2A35/assets/uploads/meals/<?= $meal['image'] ?>" width="100" style="border-radius:5px;"></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-submit"><?= $isEdit ? 'Mettre à jour' : 'Ajouter le plat' ?></button>
    </form>
</div>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
