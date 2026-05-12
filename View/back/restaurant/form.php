<?php
$page_title  = isset($restaurant['id']) ? 'Modifier le Restaurant' : 'Nouveau Restaurant';
$active_menu = 'restaurant';
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
    <form action="/2A35/Admin/restaurant/<?= isset($restaurant['id']) ? 'update/'.$restaurant['id'] : 'store' ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nom du Restaurant</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($restaurant['nom'] ?? '') ?>" placeholder="Ex: Green Food Tunis">
            <?php if (isset($errors['nom'])): ?><div class="error-msg"><?= $errors['nom'] ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($restaurant['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Adresse</label>
            <input type="text" name="adresse" value="<?= htmlspecialchars($restaurant['adresse'] ?? '') ?>">
            <?php if (isset($errors['adresse'])): ?><div class="error-msg"><?= $errors['adresse'] ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label>Type de Cuisine</label>
            <select name="type_cuisine">
                <?php 
                $types = ['tunisienne','italienne','japonaise','americaine','indienne','mexicaine','française','autre'];
                foreach ($types as $t): ?>
                    <option value="<?= $t ?>" <?= (isset($restaurant['type_cuisine']) && $restaurant['type_cuisine'] === $t) ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Email de contact</label>
            <input type="email" name="email" value="<?= htmlspecialchars($restaurant['email'] ?? '') ?>">
            <?php if (isset($errors['email'])): ?><div class="error-msg"><?= $errors['email'] ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label>Téléphone</label>
            <input type="text" name="telephone" value="<?= htmlspecialchars($restaurant['telephone'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Image du Restaurant</label>
            <input type="file" name="image" accept="image/*">
            <?php if (isset($restaurant['image'])): ?>
                <div style="margin-top:10px;"><img src="/2A35/assets/uploads/restaurants/<?= $restaurant['image'] ?>" width="100" style="border-radius:5px;"></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-submit"><?= isset($restaurant['id']) ? 'Mettre à jour' : 'Ajouter le restaurant' ?></button>
    </form>
</div>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
