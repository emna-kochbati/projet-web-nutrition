<?php
$isEdit      = isset($ingredient['id']);
$page_title  = $isEdit ? 'Modifier l\'Ingrédient' : 'Nouvel Ingrédient';
$active_menu = 'recette';
ob_start();
?>
<style>
:root { --green:#2e7d32; --green-l:#4caf50; --red:#c62828; --border:#e0e0e0; --err-bg:#ffebee; }
.form-card { background:#fff; border-radius:12px; box-shadow:0 2px 16px rgba(0,0,0,.08); padding:32px; max-width:550px; }
.form-title { font-size:1.3rem; font-weight:700; margin:0 0 24px; padding-bottom:12px; border-bottom:3px solid var(--green); }
.form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:20px; }
.form-group label { font-size:0.87rem; font-weight:700; color:#333; }
.req { color:var(--red); }
.form-group input, .form-group select {
    padding:11px 14px; border:2px solid var(--border); border-radius:7px;
    font-size:0.93rem; background:#fafafa; outline:none; font-family:inherit;
    transition:border-color .2s;
}
.form-group input:focus, .form-group select:focus { border-color:var(--green); box-shadow:0 0 0 3px rgba(46,125,50,.12); background:#fff; }
.is-invalid { border-color:var(--red) !important; background:var(--err-bg) !important; }
.is-valid   { border-color:var(--green) !important; background:#f1f8e9 !important; }
.msg-err { font-size:0.82rem; color:var(--red); font-weight:600; }
.msg-ok  { font-size:0.82rem; color:var(--green); font-weight:600; }
.err { font-size:0.82rem; color:var(--red); font-weight:600; }
.alert-err { background:var(--err-bg); border-left:4px solid var(--red); padding:12px 18px; border-radius:6px; margin-bottom:20px; color:var(--red); font-weight:600; }
.img-preview { margin-top:8px; display:flex; align-items:center; gap:12px; }
.img-preview img { width:80px; height:80px; object-fit:cover; border-radius:8px; border:2px solid var(--border); }
.img-preview p { font-size:0.78rem; color:#888; }
.form-actions { display:flex; gap:14px; margin-top:24px; padding-top:18px; border-top:2px solid var(--border); }
.btn-submit { background:var(--green); color:#fff; border:none; padding:12px 28px; border-radius:7px; font-size:1rem; font-weight:700; cursor:pointer; }
.btn-submit:hover { background:var(--green-l); }
.btn-back { background:#e0e0e0; color:#333; padding:12px 22px; border-radius:7px; text-decoration:none; font-weight:600; }
.btn-back:hover { background:#bdbdbd; }
</style>

<div class="form-card">
    <h2 class="form-title"><?= $isEdit ? '✏️ Modifier l\'Ingrédient' : '➕ Nouvel Ingrédient' ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="alert-err">⚠️ Veuillez corriger les erreurs ci-dessous.</div>
    <?php endif; ?>

    <form method="POST"
          action="<?= $isEdit ? '/2A35/Admin/ingredient/update/'.$ingredient['id'] : '/2A35/Admin/ingredient/store' ?>"
          enctype="multipart/form-data"
          id="formIngredient" novalidate>

        <!-- Nom -->
        <div class="form-group">
            <label for="nom">Nom de l'ingrédient <span class="req">*</span></label>
            <div style="display:flex;gap:10px;align-items:flex-start;">
                <div style="flex:1;">
                    <input type="text" id="nom" name="nom" maxlength="150"
                           value="<?= htmlspecialchars($ingredient['nom'] ?? '') ?>"
                           class="<?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                           placeholder="Ex : Tomate, Quinoa, Lait...">
                    <?php if (isset($errors['nom'])): ?><span class="err">⚠ <?= $errors['nom'] ?></span><?php endif; ?>
                </div>
                <button type="button" id="btnAiNutri" onclick="remplirAvecEdamam()"
                    style="background:linear-gradient(135deg,#2e7d32,#4caf50);color:#fff;border:none;
                           border-radius:7px;padding:11px 16px;cursor:pointer;font-weight:700;
                           font-size:0.85rem;white-space:nowrap;display:flex;align-items:center;
                           gap:6px;transition:opacity .2s;flex-shrink:0;">
                    🥗 Valeurs nutritionnelles
                </button>
            </div>
            <!-- Bandeau résultat IA / Open Food Facts -->
            <div id="aiNutriResult" style="display:none;margin-top:10px;padding:10px 14px;
                 background:#f5f0ff;border:1.5px solid #d4c5f9;border-radius:8px;
                 font-size:0.85rem;color:#6c3fc5;font-weight:600;">
            </div>
        </div>

        <!-- Type -->
        <div class="form-group">
            <label for="type">Type <span class="req">*</span></label>
            <select id="type" name="type" class="<?= isset($errors['type']) ? 'is-invalid' : '' ?>">
                <option value="">-- Sélectionner --</option>
                <?php foreach ([
                    'legume'          => '🥕 Légume',
                    'fruit'           => '🍎 Fruit',
                    'produit-laitier' => '🥛 Produit laitier',
                    'epice'           => '🌶️ Épice',
                    'viande'          => '🥩 Viande',
                    'cereale'         => '🌾 Céréale',
                    'autre'           => '🧂 Autre',
                ] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= ($ingredient['type'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['type'])): ?><span class="err">⚠ <?= $errors['type'] ?></span><?php endif; ?>
        </div>

        <!-- Valeurs nutritionnelles -->
        <div style="margin-top:20px;padding-top:16px;border-top:2px solid var(--border);">
            <div style="font-size:0.9rem;font-weight:700;color:var(--green);text-transform:uppercase;letter-spacing:.05em;margin-bottom:14px;">🧪 Valeurs nutritionnelles (pour 100g)</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group">
                    <label for="proteines">💪 Protéines (g)</label>
                    <input type="number" id="proteines" name="proteines" step="0.01" min="0"
                           value="<?= htmlspecialchars($ingredient['proteines'] ?? '0') ?>" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="calcium">🦴 Calcium (mg)</label>
                    <input type="number" id="calcium" name="calcium" step="0.01" min="0"
                           value="<?= htmlspecialchars($ingredient['calcium'] ?? '0') ?>" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="glucides">⚡ Glucides (g)</label>
                    <input type="number" id="glucides" name="glucides" step="0.01" min="0"
                           value="<?= htmlspecialchars($ingredient['glucides'] ?? '0') ?>" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="lipides">🫧 Lipides (g)</label>
                    <input type="number" id="lipides" name="lipides" step="0.01" min="0"
                           value="<?= htmlspecialchars($ingredient['lipides'] ?? '0') ?>" placeholder="0.00">
                </div>
            </div>
        </div>

        <!-- Image -->
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                   class="<?= isset($errors['image']) ? 'is-invalid' : '' ?>">
            <?php if (isset($errors['image'])): ?><span class="err">⚠ <?= $errors['image'] ?></span><?php endif; ?>
            <?php if (!empty($ingredient['image'])): ?>
                <div class="img-preview">
                    <img src="/2A35/assets/uploads/ingredients/<?= htmlspecialchars($ingredient['image']) ?>" alt="">
                    <p>Image actuelle — laisser vide pour la conserver.</p>
                </div>
            <?php endif; ?>

            <!-- Bouton génération image Unsplash -->
            <div style="margin-top:10px;">
                <button type="button" id="btnGenImageIng" onclick="genererImageIngredient()"
                    style="background:linear-gradient(135deg,#e65100,#ff6d00);color:#fff;border:none;
                           border-radius:7px;padding:10px 18px;cursor:pointer;font-weight:700;
                           font-size:0.88rem;display:inline-flex;align-items:center;gap:8px;
                           transition:all .2s;box-shadow:0 2px 8px rgba(230,81,0,.3);">
                    🎨 Générer une image
                </button>
            </div>

            <!-- Aperçu image générée -->
            <div id="imageIngPreview" style="display:none;margin-top:14px;padding:14px;
                 background:#fff8f0;border:2px solid #ffcc80;border-radius:10px;">
                <div style="font-size:0.82rem;font-weight:700;color:#e65100;margin-bottom:10px;">
                    🎨 Image trouvée via Unsplash API
                </div>
                <div style="display:flex;gap:14px;align-items:flex-start;flex-wrap:wrap;">
                    <img id="imageIngImg" src="" alt="Image générée"
                         style="width:120px;height:120px;object-fit:cover;border-radius:8px;
                                border:2px solid #ffcc80;display:none;">
                    <div id="imageIngLoader" style="width:120px;height:120px;background:#f5f5f5;
                         border-radius:8px;display:flex;align-items:center;justify-content:center;
                         flex-direction:column;gap:8px;border:2px solid #e0e0e0;">
                        <div style="display:flex;gap:4px;">
                            <span style="width:8px;height:8px;background:#ff6d00;border-radius:50%;animation:bounce-img .8s infinite;"></span>
                            <span style="width:8px;height:8px;background:#ff6d00;border-radius:50%;animation:bounce-img .8s .2s infinite;"></span>
                            <span style="width:8px;height:8px;background:#ff6d00;border-radius:50%;animation:bounce-img .8s .4s infinite;"></span>
                        </div>
                        <span style="font-size:0.75rem;color:#888;">Recherche...</span>
                    </div>
                    <div style="flex:1;min-width:150px;">
                        <div id="imageIngNom" style="font-weight:700;color:#333;font-size:0.88rem;margin-bottom:8px;"></div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <button type="button" id="btnUtiliserImageIng" onclick="utiliserImageIngredient()"
                                style="background:#2e7d32;color:#fff;border:none;border-radius:6px;
                                       padding:8px 14px;cursor:pointer;font-weight:700;font-size:0.82rem;">
                                ✅ Utiliser cette image
                            </button>
                            <button type="button" onclick="genererImageIngredient()"
                                style="background:#e0e0e0;color:#333;border:none;border-radius:6px;
                                       padding:8px 14px;cursor:pointer;font-weight:600;font-size:0.82rem;">
                                🔄 Régénérer
                            </button>
                        </div>
                        <div id="imageIngStatut" style="margin-top:8px;font-size:0.78rem;color:#888;"></div>
                    </div>
                </div>
            </div>
            <!-- Champ caché pour l'image générée -->
            <input type="hidden" id="imageIngGenereeNom" name="image_generee" value="">
        </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit"><?= $isEdit ? '💾 Enregistrer' : '✅ Ajouter' ?></button>
            <a href="/2A35/Admin/ingredient" class="btn-back">← Retour</a>
        </div>
    </form>
</div>

<script>
// Validation temps réel
const nomEl  = document.getElementById('nom');
const typeEl = document.getElementById('type');

function validerNomIng(el) {
    const v = el.value.trim();
    const s = el.nextElementSibling?.classList.contains('err') ? el.nextElementSibling : null;
    if (!v)             { setMsg(el, 'Le nom est obligatoire.', false); return false; }
    if (v.length < 3)   { setMsg(el, 'Minimum 3 caractères.', false); return false; }
    if (/\d/.test(v))   { setMsg(el, 'Pas de chiffres dans le nom.', false); return false; }
    if (v.length > 150) { setMsg(el, 'Maximum 150 caractères.', false); return false; }
    setMsg(el, '✔ Correct', true); return true;
}

function validerTypeIng(el) {
    if (!el.value) { setMsg(el, 'Sélectionnez un type.', false); return false; }
    setMsg(el, '✔ Correct', true); return true;
}

function setMsg(el, msg, ok) {
    el.classList.toggle('is-invalid', !ok);
    el.classList.toggle('is-valid', ok);
    let s = el.parentNode.querySelector('.msg-dyn');
    if (!s) { s = document.createElement('span'); s.className = 'msg-dyn'; el.parentNode.appendChild(s); }
    s.className = 'msg-dyn ' + (ok ? 'msg-ok' : 'msg-err');
    s.textContent = msg;
}

nomEl.addEventListener('input',   function(){ validerNomIng(this); });
typeEl.addEventListener('change', function(){ validerTypeIng(this); });

// ── Validation valeurs nutritionnelles ────────────────────────────────────────
function validerNutri(el, label) {
    const v = el.value;
    if (v === '') {
        setMsg(el, label + ' est obligatoire (≥ 0).', false);
        return false;
    }
    if (isNaN(v) || parseFloat(v) < 0) {
        setMsg(el, label + ' doit être ≥ 0.', false);
        return false;
    }
    if (parseFloat(v) > 9999) {
        setMsg(el, label + ' semble incorrecte (max 9999).', false);
        return false;
    }
    setMsg(el, '✔ Correct', true);
    return true;
}

['proteines','calcium','glucides','lipides'].forEach(id => {
    const labels = { proteines:'Protéines', calcium:'Calcium', glucides:'Glucides', lipides:'Lipides' };
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', function(){ validerNutri(this, labels[id]); });
});

document.getElementById('formIngredient').addEventListener('submit', function(e) {
    const labels = { proteines:'Protéines', calcium:'Calcium', glucides:'Glucides', lipides:'Lipides' };
    let ok = [validerNomIng(nomEl), validerTypeIng(typeEl)].every(Boolean);
    ['proteines','calcium','glucides','lipides'].forEach(id => {
        const el = document.getElementById(id);
        if (el && !validerNutri(el, labels[id])) ok = false;
    });
    if (!ok) e.preventDefault();
});

// ════════════════════════════════════════════════════════
// EDAMAM FOOD DATABASE API — Valeurs nutritionnelles officielles
// Votre app → Edamam API → valeurs réelles pour 100g
// ════════════════════════════════════════════════════════
async function remplirAvecEdamam() {
    const nom = document.getElementById('nom').value.trim();
    if (!nom || nom.length < 2) {
        afficherResultatIA('⚠️ Saisissez d\'abord le nom de l\'ingrédient.', 'warn');
        return;
    }

    const btn = document.getElementById('btnAiNutri');
    btn.disabled = true;
    btn.innerHTML = '⏳ Recherche...';
    afficherResultatIA('🥗 Recherche dans Edamam pour "' + nom + '"...', 'loading');

    try {
        const resp = await fetch('/2A35/Admin/Ai/edamam', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nom: nom })
        });

        const data = await resp.json();

        if (data.error) {
            afficherResultatIA('❌ ' + data.error, 'error');
        } else {
            // Remplir les champs automatiquement
            const champs = ['proteines', 'calcium', 'glucides', 'lipides'];
            champs.forEach(champ => {
                const el = document.getElementById(champ);
                if (el && data[champ] !== undefined) {
                    el.value = data[champ];
                    el.classList.remove('is-invalid');
                    el.classList.add('is-valid');
                    let s = el.parentNode.querySelector('.msg-dyn');
                    if (!s) { s = document.createElement('span'); s.className = 'msg-dyn'; el.parentNode.appendChild(s); }
                    s.className = 'msg-dyn msg-ok';
                    s.textContent = '✔ Edamam';
                }
            });

            afficherResultatIA(
                '🥗 <strong>Edamam Food Database</strong> — Valeurs officielles pour 100g de <strong>' +
                (data.label || nom) + '</strong> insérées automatiquement ! Vous pouvez les modifier si nécessaire.',
                'success'
            );
        }
    } catch (e) {
        afficherResultatIA('❌ Erreur de connexion à Edamam. Réessayez.', 'error');
    }

    btn.disabled = false;
    btn.innerHTML = '🥗 Valeurs nutritionnelles';
}

function afficherResultatIA(msg, type) {
    const div = document.getElementById('aiNutriResult');
    div.style.display = 'block';
    const styles = {
        success: 'background:#f0fdf4;border-color:#86efac;color:#166534;',
        error:   'background:#ffebee;border-color:#ef9a9a;color:#c62828;',
        warn:    'background:#fff8e1;border-color:#ffe082;color:#f57c00;',
        loading: 'background:#e8f5e9;border-color:#a5d6a7;color:#2e7d32;',
    };
    div.style.cssText = 'display:block;margin-top:10px;padding:10px 14px;border-radius:8px;font-size:0.85rem;font-weight:600;border:1.5px solid;' + (styles[type] || styles.loading);
    div.innerHTML = msg;
}

// ════════════════════════════════════════════════════════
// GÉNÉRATION D'IMAGE INGRÉDIENT — Unsplash API
// ════════════════════════════════════════════════════════
let imageIngGenereeUrl = null;

async function genererImageIngredient() {
    const nom = document.getElementById('nom').value.trim();
    if (!nom || nom.length < 2) {
        alert('⚠️ Saisissez d\'abord le nom de l\'ingrédient.');
        return;
    }

    const btn = document.getElementById('btnGenImageIng');
    btn.disabled = true;
    btn.innerHTML = '⏳ Recherche...';

    document.getElementById('imageIngPreview').style.display = 'block';
    document.getElementById('imageIngLoader').style.display  = 'flex';
    document.getElementById('imageIngImg').style.display     = 'none';
    document.getElementById('imageIngNom').textContent       = '"' + nom + '"';
    document.getElementById('imageIngStatut').textContent    = 'Recherche d\'une photo sur Unsplash...';

    // Réinitialiser le bouton Utiliser
    const btnUtiliser = document.getElementById('btnUtiliserImageIng');
    btnUtiliser.textContent = '✅ Utiliser cette image';
    btnUtiliser.style.background = '#2e7d32';
    btnUtiliser.disabled = false;
    btnUtiliser.style.display = 'none';
    document.getElementById('imageIngGenereeNom').value = '';
    imageIngGenereeUrl = null;

    document.getElementById('imageIngPreview').scrollIntoView({ behavior: 'smooth', block: 'center' });

    try {
        const controller = new AbortController();
        const timeoutId  = setTimeout(() => controller.abort(), 15000);

        const resp = await fetch('/2A35/Admin/Ai/genererImageIngredient', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nom: nom }),
            signal: controller.signal
        });
        clearTimeout(timeoutId);

        const data = await resp.json();

        if (data.error) {
            document.getElementById('imageIngStatut').textContent = '❌ ' + data.error;
            document.getElementById('imageIngLoader').style.display = 'none';
        } else {
            const img = document.getElementById('imageIngImg');
            img.src = '/2A35/assets/uploads/ingredients/' + data.fichier;
            img.style.display = 'block';
            document.getElementById('imageIngLoader').style.display = 'none';
            const source = '📷 Photo Unsplash par ' + (data.auteur || 'Unsplash');
            document.getElementById('imageIngStatut').textContent = '✅ ' + source;
            document.getElementById('btnUtiliserImageIng').style.display = 'inline-block';
            imageIngGenereeUrl = data.fichier;
        }
    } catch (e) {
        document.getElementById('imageIngStatut').textContent = '❌ Erreur. Réessayez.';
        document.getElementById('imageIngLoader').style.display = 'none';
    }

    btn.disabled = false;
    btn.innerHTML = '🎨 Générer une image';
}

function utiliserImageIngredient() {
    if (!imageIngGenereeUrl) return;
    document.getElementById('imageIngGenereeNom').value = imageIngGenereeUrl;
    document.getElementById('imageIngStatut').textContent = '✅ Image sélectionnée !';
    document.getElementById('btnUtiliserImageIng').textContent = '✅ Image sélectionnée';
    document.getElementById('btnUtiliserImageIng').style.background = '#1b5e20';
    document.getElementById('btnUtiliserImageIng').disabled = true;
}

// Animation loader
if (!document.getElementById('ing-anim-style')) {
    const s = document.createElement('style');
    s.id = 'ing-anim-style';
    s.textContent = `@keyframes bounce-img { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-6px)} }`;
    document.head.appendChild(s);
}
</script>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
