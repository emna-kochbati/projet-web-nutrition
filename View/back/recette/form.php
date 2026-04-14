<?php
$isEdit      = isset($recette['id']);
$page_title  = $isEdit ? 'Modifier la Recette' : 'Nouvelle Recette';
$active_menu = 'recette';
ob_start();
?>
<style>
:root { --green:#2e7d32; --green-l:#4caf50; --orange:#f57c00; --red:#c62828; --border:#e0e0e0; --err-bg:#ffebee; }
.form-card { background:#fff; border-radius:12px; box-shadow:0 2px 16px rgba(0,0,0,.08); padding:32px; max-width:700px; }
.form-title { font-size:1.4rem; font-weight:700; margin:0 0 26px; padding-bottom:14px; border-bottom:3px solid var(--green); }
.form-grid  { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.full { grid-column:1/-1; }
.form-group { display:flex; flex-direction:column; gap:4px; }
.form-group label { font-size:0.87rem; font-weight:700; color:#333; }
.req { color:var(--red); }
.form-group input, .form-group select {
    padding:11px 14px; border:2px solid var(--border); border-radius:7px;
    font-size:0.93rem; background:#fafafa; outline:none; font-family:inherit;
    transition:border-color .2s, box-shadow .2s;
}
.form-group input:focus, .form-group select:focus {
    border-color:var(--green); box-shadow:0 0 0 3px rgba(46,125,50,.12); background:#fff;
}
/* Champ invalide */
.form-group input.is-invalid,
.form-group select.is-invalid { border-color:var(--red) !important; background:var(--err-bg) !important; }
/* Champ valide */
.form-group input.is-valid,
.form-group select.is-valid { border-color:var(--green) !important; background:#f1f8e9 !important; }
/* Messages */
.msg-err   { font-size:0.82rem; color:var(--red);   font-weight:600; margin-top:2px; }
.msg-ok    { font-size:0.82rem; color:var(--green);  font-weight:600; margin-top:2px; }
.err       { font-size:0.82rem; color:var(--red);   font-weight:600; }
.alert-err { background:var(--err-bg); border-left:4px solid var(--red); padding:12px 18px; border-radius:6px; margin-bottom:20px; color:var(--red); font-weight:600; }
.img-preview { margin-top:8px; display:flex; align-items:center; gap:12px; }
.img-preview img { width:80px; height:80px; object-fit:cover; border-radius:8px; border:2px solid var(--border); }
.img-preview p { font-size:0.78rem; color:#888; }
.form-actions { display:flex; gap:14px; margin-top:28px; padding-top:20px; border-top:2px solid var(--border); }
.btn-submit { background:var(--green); color:#fff; border:none; padding:12px 28px; border-radius:7px; font-size:1rem; font-weight:700; cursor:pointer; }
.btn-submit:hover { background:var(--green-l); }
.btn-back { background:#e0e0e0; color:#333; padding:12px 22px; border-radius:7px; text-decoration:none; font-weight:600; font-size:1rem; }
.btn-back:hover { background:#bdbdbd; }
/* Ingrédients */
.ing-section { margin-top:28px; padding-top:22px; border-top:2px solid var(--border); }
.ing-title { font-size:1rem; font-weight:700; color:var(--green); text-transform:uppercase; letter-spacing:.05em; margin-bottom:14px; }
.ing-table { width:100%; border-collapse:collapse; font-size:0.88rem; margin-bottom:12px; }
.ing-table th { background:#f1f8e9; color:var(--green); padding:10px 12px; text-align:left; font-weight:700; border-bottom:2px solid #c8e6c9; }
.ing-table td { padding:7px 6px; vertical-align:top; }
.ing-table input { width:100%; padding:8px 10px; border:2px solid var(--border); border-radius:6px; font-size:0.87rem; outline:none; transition:border-color .2s; font-family:inherit; }
.ing-table input:focus { border-color:var(--green); }
.ing-table input.is-invalid { border-color:var(--red); background:var(--err-bg); }
.btn-remove { background:var(--red); color:#fff; border:none; border-radius:5px; padding:7px 11px; cursor:pointer; font-size:0.85rem; }
.btn-remove:hover { background:#e53935; }
.btn-add-ing { background:var(--green); color:#fff; border:none; border-radius:6px; padding:9px 18px; cursor:pointer; font-weight:600; font-size:0.9rem; display:inline-flex; align-items:center; gap:6px; }
.btn-add-ing:hover { background:var(--green-l); }
</style>

<div class="form-card">
    <h2 class="form-title"><?= $isEdit ? '✏️ Modifier la Recette' : '➕ Nouvelle Recette' ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="alert-err">⚠️ Veuillez corriger les erreurs ci-dessous.</div>
    <?php endif; ?>

    <form method="POST"
          action="<?= $isEdit ? '/2A35/Admin/recette/update/'.$recette['id'] : '/2A35/Admin/recette/store' ?>"
          enctype="multipart/form-data"
          id="formRecette"
          novalidate>

        <div class="form-grid">

            <div class="form-group full">
                <label for="nom">Nom de la recette <span class="req">*</span></label>
                <input type="text" id="nom" name="nom" maxlength="150"
                       value="<?= htmlspecialchars($recette['nom'] ?? '') ?>"
                       class="<?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                       placeholder="Ex : Salade méditerranéenne">
                <?php if (isset($errors['nom'])): ?><span class="err">⚠ <?= $errors['nom'] ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="categorie">Catégorie <span class="req">*</span></label>
                <select id="categorie" name="categorie" class="<?= isset($errors['categorie']) ? 'is-invalid' : '' ?>">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach (['petit-dejeuner'=>'🌅 Petit-déjeuner','dejeuner'=>'☀️ Déjeuner','diner'=>'🌙 Dîner','collation'=>'🍎 Collation','dessert'=>'🍰 Dessert','vegetarien'=>'🥦 Végétarien','regime'=>'⚖️ Régime','sportif'=>'💪 Sportif'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($recette['categorie'] ?? '')===$v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['categorie'])): ?><span class="err">⚠ <?= $errors['categorie'] ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="difficulte">Difficulté <span class="req">*</span></label>
                <select id="difficulte" name="difficulte" class="<?= isset($errors['difficulte']) ? 'is-invalid' : '' ?>">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach (['facile'=>'🟢 Facile','moyen'=>'🟡 Moyen','difficile'=>'🔴 Difficile'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($recette['difficulte'] ?? '')===$v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['difficulte'])): ?><span class="err">⚠ <?= $errors['difficulte'] ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="duree">Durée (minutes) <span class="req">*</span></label>
                <input type="number" id="duree" name="duree" min="1" max="1440"
                       value="<?= htmlspecialchars($recette['duree'] ?? '') ?>"
                       class="<?= isset($errors['duree']) ? 'is-invalid' : '' ?>"
                       placeholder="Ex : 30">
                <?php if (isset($errors['duree'])): ?><span class="err">⚠ <?= $errors['duree'] ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="calories">Calories (kcal) <span class="req">*</span></label>
                <input type="number" id="calories" name="calories" min="0" max="10000"
                       value="<?= htmlspecialchars($recette['calories'] ?? '') ?>"
                       class="<?= isset($errors['calories']) ? 'is-invalid' : '' ?>"
                       placeholder="Ex : 350">
                <?php if (isset($errors['calories'])): ?><span class="err">⚠ <?= $errors['calories'] ?></span><?php endif; ?>
            </div>

            <div class="form-group full">
                <label for="image">Image de la recette</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                       class="<?= isset($errors['image']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['image'])): ?><span class="err">⚠ <?= $errors['image'] ?></span><?php endif; ?>
                <?php if (!empty($recette['image'])): ?>
                    <div class="img-preview">
                        <img src="/2A35/assets/uploads/recettes/<?= htmlspecialchars($recette['image']) ?>" alt="">
                        <p>Image actuelle — laisser vide pour la conserver.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- ── Section Ingrédients ── -->
        <div class="ing-section">
            <div class="ing-title">🥦 Ingrédients</div>

            <table class="ing-table" id="ingTable">
                <thead>
                    <tr>
                        <th>Nom <span class="req">*</span></th>
                        <th>Quantité <span class="req">*</span></th>
                        <th>Unité <span class="req">*</span></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="ingBody">
                <?php if (!empty($ingredients)): ?>
                    <?php foreach ($ingredients as $i => $ing): ?>
                    <tr>
                        <td><input type="text" name="ing_nom[]" value="<?= htmlspecialchars($ing['nom']) ?>" placeholder="Ex : Tomate" class="<?= isset($errors["ing_nom_$i"]) ? 'is-invalid' : '' ?>">
                            <?php if (isset($errors["ing_nom_$i"])): ?><span class="err"><?= $errors["ing_nom_$i"] ?></span><?php endif; ?>
                        </td>
                        <td><input type="number" name="ing_quantite[]" step="0.01" min="0.01" value="<?= htmlspecialchars($ing['quantite']) ?>" placeholder="200" class="<?= isset($errors["ing_quantite_$i"]) ? 'is-invalid' : '' ?>">
                            <?php if (isset($errors["ing_quantite_$i"])): ?><span class="err"><?= $errors["ing_quantite_$i"] ?></span><?php endif; ?>
                        </td>
                        <td><input type="text" name="ing_unite[]" value="<?= htmlspecialchars($ing['unite']) ?>" placeholder="g, ml, pièce..." class="<?= isset($errors["ing_unite_$i"]) ? 'is-invalid' : '' ?>">
                            <?php if (isset($errors["ing_unite_$i"])): ?><span class="err"><?= $errors["ing_unite_$i"] ?></span><?php endif; ?>
                        </td>
                        <td><button type="button" class="btn-remove" onclick="supprimerLigne(this)">✕</button></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td><input type="text" name="ing_nom[]" placeholder="Ex : Tomate"></td>
                        <td><input type="number" name="ing_quantite[]" step="0.01" min="0.01" placeholder="200"></td>
                        <td><input type="text" name="ing_unite[]" placeholder="g, ml, pièce..."></td>
                        <td><button type="button" class="btn-remove" onclick="supprimerLigne(this)">✕</button></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <button type="button" class="btn-add-ing" onclick="ajouterIngredient()">＋ Ajouter un ingrédient</button>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit"><?= $isEdit ? '💾 Enregistrer' : '✅ Créer la recette' ?></button>
            <a href="/2A35/Admin/recette" class="btn-back">← Retour</a>
        </div>
    </form>
</div>

<script>
// ── Helpers ───────────────────────────────────────────────────────────────────
function setErreur(el, msg) {
    el.classList.remove('is-valid');
    el.classList.add('is-invalid');
    let span = el.parentNode.querySelector('.msg-err, .msg-ok');
    if (!span) { span = document.createElement('span'); el.parentNode.appendChild(span); }
    span.className = 'msg-err';
    span.textContent = msg;
}

function setOk(el, msg) {
    el.classList.remove('is-invalid');
    el.classList.add('is-valid');
    let span = el.parentNode.querySelector('.msg-err, .msg-ok');
    if (!span) { span = document.createElement('span'); el.parentNode.appendChild(span); }
    span.className = 'msg-ok';
    span.textContent = msg;
}

function clearMsg(el) {
    el.classList.remove('is-invalid','is-valid');
    const span = el.parentNode.querySelector('.msg-err, .msg-ok');
    if (span) span.remove();
}

// ── Règles de validation ──────────────────────────────────────────────────────
function validerNom(el) {
    const v = el.value.trim();
    if (v === '')           { setErreur(el, 'Le nom de la recette est obligatoire.'); return false; }
    if (v.length < 3)       { setErreur(el, 'Le nom doit contenir au moins 3 caractères.'); return false; }
    if (v.length > 150)     { setErreur(el, 'Le nom ne peut pas dépasser 150 caractères.'); return false; }
    if (/\d/.test(v))       { setErreur(el, 'Le nom ne doit pas contenir de chiffres.'); return false; }
    setOk(el, '✔ Correct'); return true;
}

function validerCategorie(el) {
    if (!el.value) { setErreur(el, 'Veuillez sélectionner une catégorie.'); return false; }
    setOk(el, '✔ Correct'); return true;
}

function validerDifficulte(el) {
    if (!el.value) { setErreur(el, 'Veuillez sélectionner une difficulté.'); return false; }
    setOk(el, '✔ Correct'); return true;
}

function validerDuree(el) {
    const v = el.value;
    if (v === '' || isNaN(v))       { setErreur(el, 'La durée est obligatoire.'); return false; }
    if (!Number.isInteger(+v) || +v < 1) { setErreur(el, 'La durée doit être un entier ≥ 1 minute.'); return false; }
    if (+v > 1440)                  { setErreur(el, 'La durée ne peut pas dépasser 1440 minutes.'); return false; }
    setOk(el, '✔ Correct'); return true;
}

function validerCalories(el) {
    const v = el.value;
    if (v === '')       { setErreur(el, 'Les calories sont obligatoires.'); return false; }
    if (isNaN(v) || +v < 0) { setErreur(el, 'Les calories doivent être un nombre ≥ 0.'); return false; }
    if (+v > 10000)     { setErreur(el, 'Valeur calorique incorrecte (max 10 000 kcal).'); return false; }
    setOk(el, '✔ Correct'); return true;
}

// ── Écoute en temps réel ──────────────────────────────────────────────────────
document.getElementById('nom').addEventListener('input',        function(){ validerNom(this); });
document.getElementById('categorie').addEventListener('change', function(){ validerCategorie(this); });
document.getElementById('difficulte').addEventListener('change',function(){ validerDifficulte(this); });
document.getElementById('duree').addEventListener('input',      function(){ validerDuree(this); });
document.getElementById('calories').addEventListener('input',   function(){ validerCalories(this); });

// ── Validation à la soumission ────────────────────────────────────────────────
document.getElementById('formRecette').addEventListener('submit', function(e) {
    const ok = [
        validerNom(document.getElementById('nom')),
        validerCategorie(document.getElementById('categorie')),
        validerDifficulte(document.getElementById('difficulte')),
        validerDuree(document.getElementById('duree')),
        validerCalories(document.getElementById('calories')),
    ].every(Boolean);

    if (!ok) {
        e.preventDefault();
        // Scroll vers le premier champ invalide
        const first = document.querySelector('.is-invalid');
        if (first) first.scrollIntoView({ behavior:'smooth', block:'center' });
    }
});

// ── Ingrédients dynamiques ────────────────────────────────────────────────────
function ajouterIngredient() {
    const tbody = document.getElementById('ingBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" name="ing_nom[]" placeholder="Ex : Tomate"></td>
        <td><input type="number" name="ing_quantite[]" step="0.01" min="0.01" placeholder="200"></td>
        <td><input type="text" name="ing_unite[]" placeholder="g, ml, pièce..."></td>
        <td><button type="button" class="btn-remove" onclick="supprimerLigne(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
    tr.querySelector('input').focus();
}

function supprimerLigne(btn) {
    const tbody = document.getElementById('ingBody');
    if (tbody.rows.length <= 1) {
        btn.closest('tr').querySelectorAll('input').forEach(i => i.value = '');
        return;
    }
    btn.closest('tr').remove();
}
</script>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
