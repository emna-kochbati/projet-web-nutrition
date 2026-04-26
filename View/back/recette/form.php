<?php
$isEdit      = isset($recette['id']);
$page_title  = $isEdit ? 'Modifier la Recette' : 'Nouvelle Recette';
$active_menu = 'recette';

require_once 'Model/Ingredient.php';
$ingModel        = new Ingredient();
$tousIngredients = $ingModel->getAll();

ob_start();
?>
<style>
:root { --green:#2e7d32; --green-l:#4caf50; --orange:#f57c00; --red:#c62828; --border:#e0e0e0; --err-bg:#ffebee; }
.form-card { background:#fff; border-radius:12px; box-shadow:0 2px 16px rgba(0,0,0,.08); padding:32px; max-width:750px; }
.form-title { font-size:1.4rem; font-weight:700; margin:0 0 26px; padding-bottom:14px; border-bottom:3px solid var(--green); }
.form-grid  { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.full { grid-column:1/-1; }
.form-group { display:flex; flex-direction:column; gap:4px; }
.form-group label { font-size:0.87rem; font-weight:700; color:#333; }
.req { color:var(--red); }
.form-group input, .form-group select, .form-group textarea {
    padding:11px 14px; border:2px solid var(--border); border-radius:7px;
    font-size:0.93rem; background:#fafafa; outline:none; font-family:inherit;
    transition:border-color .2s, box-shadow .2s;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    border-color:var(--green); box-shadow:0 0 0 3px rgba(46,125,50,.12); background:#fff;
}
.form-group textarea { resize:vertical; min-height:80px; }
.is-invalid { border-color:var(--red) !important; background:var(--err-bg) !important; }
.is-valid   { border-color:var(--green) !important; background:#f1f8e9 !important; }
.msg-err { font-size:0.82rem; color:var(--red); font-weight:600; margin-top:2px; }
.msg-ok  { font-size:0.82rem; color:var(--green); font-weight:600; margin-top:2px; }
.err { font-size:0.82rem; color:var(--red); font-weight:600; }
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
.ing-table input.is-valid   { border-color:var(--green); background:#f1f8e9; }
.btn-remove { background:var(--red); color:#fff; border:none; border-radius:5px; padding:7px 11px; cursor:pointer; font-size:0.85rem; }
.btn-remove:hover { background:#e53935; }
.msg-ing { font-size:0.75rem; font-weight:600; display:block; margin-top:2px; }
/* Bouton sélectionner */
.btn-select-ing { background:var(--orange); color:#fff; border:none; border-radius:6px; padding:9px 18px; cursor:pointer; font-weight:600; font-size:0.9rem; display:inline-flex; align-items:center; gap:6px; }
.btn-select-ing:hover { background:#ff9800; }
/* Modal */
.modal-sel { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; }
.modal-sel.show { display:flex; }
.modal-sel-box { background:#fff; border-radius:12px; padding:24px; width:440px; max-width:95%; max-height:80vh; display:flex; flex-direction:column; box-shadow:0 8px 30px rgba(0,0,0,.2); }
.modal-sel-box h3 { margin:0 0 14px; font-size:1.1rem; color:var(--green); }
.modal-search { padding:9px 12px; border:2px solid var(--border); border-radius:6px; font-size:0.9rem; outline:none; margin-bottom:12px; width:100%; box-sizing:border-box; }
.modal-search:focus { border-color:var(--green); }
.ing-list-modal { overflow-y:auto; flex:1; border:1px solid var(--border); border-radius:6px; list-style:none; padding:0; margin:0; }
.ing-list-modal li { padding:10px 14px; cursor:pointer; border-bottom:1px solid #f0f0f0; font-size:0.9rem; transition:background .15s; }
.ing-list-modal li:hover { background:#f1f8e9; color:var(--green); font-weight:600; }
.modal-close { background:#e0e0e0; color:#333; border:none; padding:9px 18px; border-radius:6px; cursor:pointer; font-weight:600; margin-top:12px; width:100%; }
</style>

<div class="form-card">
    <h2 class="form-title"><?= $isEdit ? '✏️ Modifier la Recette' : '➕ Nouvelle Recette' ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="alert-err">⚠️ Veuillez corriger les erreurs ci-dessous.</div>
    <?php endif; ?>

    <form method="POST"
          action="<?= $isEdit ? '/2A35/Admin/recette/update/'.$recette['id'] : '/2A35/Admin/recette/store' ?>"
          enctype="multipart/form-data" id="formRecette" novalidate>

        <div class="form-grid">

            <div class="form-group full">
                <label for="nom">Nom de la recette <span class="req">*</span></label>
                <input type="text" id="nom" name="nom" maxlength="150"
                       value="<?= htmlspecialchars($recette['nom'] ?? '') ?>"
                       class="<?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                       placeholder="Ex : Salade méditerranéenne">
                <?php if (isset($errors['nom'])): ?><span class="err">⚠ <?= $errors['nom'] ?></span><?php endif; ?>
            </div>

            <div class="form-group full">
                <label for="description">Description <span class="req">*</span></label>
                <textarea id="description" name="description"
                          class="<?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                          placeholder="Décrivez brièvement la recette..."><?= htmlspecialchars($recette['description'] ?? '') ?></textarea>
                <?php if (isset($errors['description'])): ?><span class="err">⚠ <?= $errors['description'] ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="categorie">Catégorie <span class="req">*</span></label>
                <select id="categorie" name="categorie" class="<?= isset($errors['categorie']) ? 'is-invalid' : '' ?>">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach (['petit-dejeuner'=>'🌅 Petit-déjeuner','dejeuner'=>'☀️ Déjeuner','diner'=>'🌙 Dîner','collation'=>'🍎 Collation','dessert'=>'🍰 Dessert','vegetarien'=>'🥦 Végétarien','regime'=>'⚖️ Régime','sportif'=>'💪 Sportif'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($recette['categorie'] ?? '')===$v ? 'selected':'' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['categorie'])): ?><span class="err">⚠ <?= $errors['categorie'] ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="difficulte">Difficulté <span class="req">*</span></label>
                <select id="difficulte" name="difficulte" class="<?= isset($errors['difficulte']) ? 'is-invalid' : '' ?>">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach (['facile'=>'🟢 Facile','moyen'=>'🟡 Moyen','difficile'=>'🔴 Difficile'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($recette['difficulte'] ?? '')===$v ? 'selected':'' ?>><?= $l ?></option>
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
                <input type="number" id="calories" name="calories" min="0" max="99999"
                       value="<?= htmlspecialchars($recette['calories'] ?? '0') ?>"
                       class="<?= isset($errors['calories']) ? 'is-invalid' : '' ?>"
                       placeholder="Calculé automatiquement"
                       readonly
                       style="background:#f1f8e9;cursor:default;border-color:#2e7d32;font-weight:700;color:#2e7d32;">
                <span style="font-size:0.78rem;color:#888;">Calculé automatiquement à partir des ingrédients.</span>
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

        <!-- ── Ingrédients ── -->
        <div class="ing-section">
            <div class="ing-title">🥦 Ingrédients de la recette</div>

            <table class="ing-table">
                <thead>
                    <tr>
                        <th style="width:38%">Ingrédient <span class="req">*</span></th>
                        <th style="width:22%">Quantité <span class="req">*</span></th>
                        <th style="width:25%">Unité <span class="req">*</span></th>
                        <th style="width:15%"></th>
                    </tr>
                </thead>
                <tbody id="ingBody">
                <?php if (!empty($ingredients)): ?>
                    <?php foreach ($ingredients as $ing): ?>
                    <tr>
                        <td>
                            <input type="hidden" name="ing_id[]" value="<?= $ing['ingredient_id'] ?>">
                            <input type="text" name="ing_nom[]" value="<?= htmlspecialchars($ing['nom']) ?>"
                                   readonly style="background:#f9fbe7;cursor:default;border-color:#2e7d32;">
                        </td>
                        <td>
                            <input type="number" name="ing_quantite[]" step="0.01" min="0.01"
                                   value="<?= htmlspecialchars($ing['quantite']) ?>" placeholder="200"
                                   oninput="validerIngQte(this)">
                            <span class="msg-ing"></span>
                        </td>
                        <td>
                            <select name="ing_unite[]" style="width:100%;padding:8px 10px;border:2px solid var(--border);border-radius:6px;font-size:0.87rem;outline:none;background:#fafafa;">
                                <option value="g" <?= ($ing['unite'] ?? '') === 'g'  ? 'selected' : '' ?>>g</option>
                                <option value="ml" <?= ($ing['unite'] ?? '') === 'ml' ? 'selected' : '' ?>>ml</option>
                            </select>
                        </td>
                        <td><button type="button" class="btn-remove" onclick="supprimerLigne(this)">✕</button></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="emptyRow">
                        <td colspan="4" style="text-align:center;color:#999;padding:16px;font-style:italic;">
                            Aucun ingrédient. Cliquez sur "Sélectionner un ingrédient".
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <button type="button" class="btn-select-ing" onclick="ouvrirModal()">
                🔍 Sélectionner un ingrédient
            </button>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit"><?= $isEdit ? '💾 Enregistrer' : '✅ Créer la recette' ?></button>
            <a href="/2A35/Admin/recette" class="btn-back">← Retour</a>
        </div>
    </form>
</div>

<!-- Modal sélecteur -->
<div class="modal-sel" id="modalSel">
    <div class="modal-sel-box">
        <h3>🥦 Sélectionner un ingrédient</h3>
        <input type="text" class="modal-search" id="modalSearch"
               placeholder="Rechercher..." oninput="filtrerIngredients()">
        <ul class="ing-list-modal" id="ingListModal">
            <?php foreach ($tousIngredients as $ti): ?>
                <li onclick="selectionnerIngredient(<?= $ti['id'] ?>, '<?= htmlspecialchars(addslashes($ti['nom'])) ?>')">
                    🌿 <?= htmlspecialchars($ti['nom']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="modal-close" onclick="fermerModal()">✕ Fermer</button>
    </div>
</div>

<script>
const tousIngredients = <?= json_encode($tousIngredients) ?>;

// ── Calcul automatique des calories ──────────────────────────────────────────
function calculerCalories() {
    const ids  = document.querySelectorAll('input[name="ing_id[]"]');
    const qtes = document.querySelectorAll('input[name="ing_quantite[]"]');
    let total  = 0;

    ids.forEach((idEl, i) => {
        const ingId = parseInt(idEl.value);
        const qte   = parseFloat(qtes[i]?.value) || 0;
        if (!ingId || qte <= 0) return;

        const ing = tousIngredients.find(x => x.id == ingId);
        if (!ing) return;

        const p = parseFloat(ing.proteines) || 0;
        const g = parseFloat(ing.glucides)  || 0;
        const l = parseFloat(ing.lipides)   || 0;

        total += ((p * 4) + (g * 4) + (l * 9)) * qte / 100;
    });

    document.getElementById('calories').value = Math.round(total);
}

function ouvrirModal() {
    document.getElementById('modalSearch').value = '';
    filtrerIngredients();
    document.getElementById('modalSel').classList.add('show');
    document.getElementById('modalSearch').focus();
}
function fermerModal() {
    document.getElementById('modalSel').classList.remove('show');
}
document.getElementById('modalSel').addEventListener('click', function(e) {
    if (e.target === this) fermerModal();
});

function filtrerIngredients() {
    const q  = document.getElementById('modalSearch').value.toLowerCase();
    const ul = document.getElementById('ingListModal');
    ul.innerHTML = '';
    const res = tousIngredients.filter(i => i.nom.toLowerCase().includes(q));
    if (!res.length) {
        ul.innerHTML = '<li style="color:#999;cursor:default;padding:12px;">Aucun résultat</li>';
        return;
    }
    res.forEach(i => {
        const li = document.createElement('li');
        li.textContent = '🌿 ' + i.nom;
        li.onclick = () => selectionnerIngredient(i.id, i.nom);
        ul.appendChild(li);
    });
}

function selectionnerIngredient(id, nom) {
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();

    const tbody = document.getElementById('ingBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <input type="hidden" name="ing_id[]" value="${id}">
            <input type="text" name="ing_nom[]" value="${nom}" readonly
                   style="background:#f9fbe7;cursor:default;border-color:#2e7d32;">
        </td>
        <td>
            <input type="number" name="ing_quantite[]" step="0.01" min="0.01"
                   placeholder="Ex : 200" oninput="validerIngQte(this); calculerCalories();">
            <span class="msg-ing"></span>
        </td>
        <td>
            <select name="ing_unite[]" style="width:100%;padding:8px 10px;border:2px solid var(--border);border-radius:6px;font-size:0.87rem;outline:none;background:#fafafa;">
                <option value="g">g</option>
                <option value="ml">ml</option>
            </select>
            <span class="msg-ing"></span>
        </td>
        <td><button type="button" class="btn-remove" onclick="supprimerLigne(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
    tr.querySelectorAll('input')[1].focus();
    fermerModal();
    calculerCalories();
}

function supprimerLigne(btn) {
    const tbody = document.getElementById('ingBody');
    btn.closest('tr').remove();
    if (tbody.rows.length === 0) {
        const tr = document.createElement('tr');
        tr.id = 'emptyRow';
        tr.innerHTML = '<td colspan="4" style="text-align:center;color:#999;padding:16px;font-style:italic;">Aucun ingrédient. Cliquez sur "Sélectionner un ingrédient".</td>';
        tbody.appendChild(tr);
    }
    calculerCalories();
}

// ── Validation temps réel ─────────────────────────────────────────────────────
function setErreur(el, msg) {
    el.classList.remove('is-valid'); el.classList.add('is-invalid');
    let s = el.parentNode.querySelector('.msg-err,.msg-ok');
    if (!s) { s = document.createElement('span'); el.parentNode.appendChild(s); }
    s.className = 'msg-err'; s.textContent = msg;
}
function setOk(el, msg) {
    el.classList.remove('is-invalid'); el.classList.add('is-valid');
    let s = el.parentNode.querySelector('.msg-err,.msg-ok');
    if (!s) { s = document.createElement('span'); el.parentNode.appendChild(s); }
    s.className = 'msg-ok'; s.textContent = msg;
}
function validerNom(el) {
    const v = el.value.trim();
    if (!v)             { setErreur(el,'Le nom est obligatoire.'); return false; }
    if (v.length < 3)   { setErreur(el,'Minimum 3 caractères.'); return false; }
    if (v.length > 150) { setErreur(el,'Maximum 150 caractères.'); return false; }
    if (/\d/.test(v))   { setErreur(el,'Pas de chiffres.'); return false; }
    setOk(el,'✔ Correct'); return true;
}
function validerDescription(el) {
    const v = el.value.trim();
    if (!v)           { setErreur(el,'La description est obligatoire.'); return false; }
    if (v.length < 10){ setErreur(el,'Minimum 10 caractères.'); return false; }
    setOk(el,'✔ Correct'); return true;
}
function validerCategorie(el) {
    if (!el.value) { setErreur(el,'Sélectionnez une catégorie.'); return false; }
    setOk(el,'✔ Correct'); return true;
}
function validerDifficulte(el) {
    if (!el.value) { setErreur(el,'Sélectionnez une difficulté.'); return false; }
    setOk(el,'✔ Correct'); return true;
}
function validerDuree(el) {
    const v = el.value;
    if (!v || !Number.isInteger(+v) || +v < 1) { setErreur(el,'Entier ≥ 1.'); return false; }
    if (+v > 1440) { setErreur(el,'Max 1440 min.'); return false; }
    setOk(el,'✔ Correct'); return true;
}
function validerCalories(el) {
    const v = el.value;
    if (v === '' || +v < 0) { setErreur(el,'Nombre ≥ 0.'); return false; }
    if (+v > 10000) { setErreur(el,'Max 10 000 kcal.'); return false; }
    setOk(el,'✔ Correct'); return true;
}
function validerIngQte(el) {
    const s = el.parentNode.querySelector('.msg-ing');
    if (!el.value || +el.value <= 0) {
        el.classList.add('is-invalid'); el.classList.remove('is-valid');
        if (s) { s.className='msg-ing msg-err'; s.textContent='Quantité > 0.'; }
    } else {
        el.classList.remove('is-invalid'); el.classList.add('is-valid');
        if (s) { s.className='msg-ing msg-ok'; s.textContent='✔'; }
    }
}
function validerIngUnite(el) {
    const s = el.parentNode.querySelector('.msg-ing');
    if (!el.value.trim()) {
        el.classList.add('is-invalid'); el.classList.remove('is-valid');
        if (s) { s.className='msg-ing msg-err'; s.textContent='Unité requise.'; }
    } else {
        el.classList.remove('is-invalid'); el.classList.add('is-valid');
        if (s) { s.className='msg-ing msg-ok'; s.textContent='✔'; }
    }
}

document.getElementById('nom').addEventListener('input',         function(){ validerNom(this); });
document.getElementById('description').addEventListener('input', function(){ validerDescription(this); });
document.getElementById('categorie').addEventListener('change',  function(){ validerCategorie(this); });
document.getElementById('difficulte').addEventListener('change', function(){ validerDifficulte(this); });
document.getElementById('duree').addEventListener('input',       function(){ validerDuree(this); });
document.getElementById('calories').addEventListener('input',    function(){ validerCalories(this); });

document.getElementById('formRecette').addEventListener('submit', function(e) {
    let ok = [
        validerNom(document.getElementById('nom')),
        validerDescription(document.getElementById('description')),
        validerCategorie(document.getElementById('categorie')),
        validerDifficulte(document.getElementById('difficulte')),
        validerDuree(document.getElementById('duree')),
    ].every(Boolean);

    // Au moins un ingrédient
    const ids = document.querySelectorAll('input[name="ing_id[]"]');
    if (ids.length === 0) {
        alert('⚠️ Veuillez sélectionner au moins un ingrédient.');
        ok = false;
    }

    // Valider quantités et unités
    document.querySelectorAll('input[name="ing_quantite[]"]').forEach(el => {
        if (!el.value || +el.value <= 0) { validerIngQte(el); ok = false; }
    });
    document.querySelectorAll('input[name="ing_unite[]"]').forEach(el => {
        if (!el.value.trim()) { validerIngUnite(el); ok = false; }
    });

    if (!ok) {
        e.preventDefault();
        document.querySelector('.is-invalid')?.scrollIntoView({ behavior:'smooth', block:'center' });
    }
});
</script>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
