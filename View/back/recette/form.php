<?php
$isEdit      = isset($recette['id']);
$page_title  = $isEdit ? 'Modifier la Recette' : 'Nouvelle Recette';
$active_menu = 'recette';
ob_start();
?>

<style>
.form-card { background:#fff; border-radius:10px; padding:30px; box-shadow:0 2px 10px rgba(0,0,0,.08); max-width:700px; }
.form-group { margin-bottom:18px; }
label { display:block; font-weight:600; font-size:.88rem; color:#333; margin-bottom:6px; }
input[type=text], input[type=number], input[type=file], select, textarea {
    width:100%; padding:10px 14px; border:1px solid #ccc; border-radius:6px; font-size:.9rem; font-family:inherit;
}
input:focus, select:focus, textarea:focus { outline:none; border-color:#2e7d32; box-shadow:0 0 0 3px rgba(46,125,50,.15); }
textarea { resize:vertical; min-height:90px; }
.error-msg { color:#c62828; font-size:.8rem; margin-top:4px; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.btn-submit { background:#2e7d32; color:#fff; padding:11px 28px; border:none; border-radius:6px; font-size:.95rem; font-weight:700; cursor:pointer; }
.btn-submit:hover { background:#1b5e20; }
.btn-cancel { background:#eee; color:#333; padding:11px 20px; border-radius:6px; text-decoration:none; font-size:.9rem; font-weight:600; }
.form-actions { display:flex; gap:12px; margin-top:24px; }
.current-img { margin-top:8px; }
.current-img img { width:80px; height:80px; object-fit:cover; border-radius:8px; border:2px solid #ddd; }
</style>

<div class="form-card">
    <h2 style="margin-bottom:24px;font-size:1.2rem;color:#1a1a1a;">
        <?= $isEdit ? '✏️ Modifier la Recette' : '➕ Nouvelle Recette' ?>
    </h2>

    <form method="POST"
          action="<?= $isEdit ? '/2A35/Admin/recette/update/'.$recette['id'] : '/2A35/Admin/recette/store' ?>"
          enctype="multipart/form-data">

        <div class="form-group">
            <label for="nom">Nom de la recette *</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($recette['nom'] ?? '') ?>" placeholder="Ex: Salade César">
            <?php if (!empty($errors['nom'])): ?><div class="error-msg">⚠ <?= $errors['nom'] ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Décrivez la recette…"><?= htmlspecialchars($recette['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="ingredients">Ingrédients</label>
            <textarea id="ingredients" name="ingredients" placeholder="Liste des ingrédients…"><?= htmlspecialchars($recette['ingredients'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="categorie">Catégorie</label>
                <select id="categorie" name="categorie">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach (['entrée','plat principal','dessert','boisson','snack','autre'] as $c): ?>
                        <option value="<?= $c ?>" <?= ($recette['categorie'] ?? '') === $c ? 'selected' : '' ?>>
                            <?= ucfirst($c) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="temps_preparation">Temps de préparation (min)</label>
                <input type="number" id="temps_preparation" name="temps_preparation" min="1" value="<?= htmlspecialchars($recette['temps_preparation'] ?? '') ?>" placeholder="Ex: 30">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="calories">Calories (kcal)</label>
                <input type="number" id="calories" name="calories" min="0" value="<?= htmlspecialchars($recette['calories'] ?? '') ?>" placeholder="Ex: 350">
            </div>
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                <?php if (!empty($errors['image'])): ?><div class="error-msg">⚠ <?= $errors['image'] ?></div><?php endif; ?>
                <?php if ($isEdit && !empty($recette['image'])): ?>
                    <div class="current-img">
                        <small style="color:#666;">Image actuelle :</small><br>
                        <img src="/2A35/assets/uploads/recettes/<?= htmlspecialchars($recette['image']) ?>" alt="">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <?= $isEdit ? '💾 Enregistrer' : '➕ Ajouter' ?>
            </button>
            <a href="/2A35/Admin/recette" class="btn-cancel">Annuler</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
