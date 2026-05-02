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
/* ── Variables ── */
:root {
    --green:#2e7d32; --green-l:#4caf50; --orange:#f57c00;
    --red:#c62828; --border:#e0e0e0; --err-bg:#ffebee;
    --ai:#6c3fc5; --ai-l:#8b5cf6; --ai-bg:#f5f0ff;
}

/* ── Layout deux colonnes ── */
.recette-layout {
    display: flex;
    gap: 24px;
    align-items: flex-start;
}
.recette-form-col {
    flex: 1;
    min-width: 0;
}
.recette-ai-col {
    width: 360px;
    flex-shrink: 0;
    position: sticky;
    top: 80px;
}

/* ── Carte formulaire ── */
.form-card {
    background:#fff; border-radius:12px;
    box-shadow:0 2px 16px rgba(0,0,0,.08); padding:32px;
}
.form-title {
    font-size:1.4rem; font-weight:700; margin:0 0 26px;
    padding-bottom:14px; border-bottom:3px solid var(--green);
}
.form-grid  { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.full { grid-column:1/-1; }
.form-group { display:flex; flex-direction:column; gap:4px; }
.form-group label { font-size:0.87rem; font-weight:700; color:#333; }
.req { color:var(--red); }
.form-group input,
.form-group select,
.form-group textarea {
    padding:11px 14px; border:2px solid var(--border); border-radius:7px;
    font-size:0.93rem; background:#fafafa; outline:none; font-family:inherit;
    transition:border-color .2s, box-shadow .2s;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color:var(--green);
    box-shadow:0 0 0 3px rgba(46,125,50,.12);
    background:#fff;
}
.form-group textarea { resize:none; min-height:120px; overflow:hidden; }
#description { min-height:120px; width:100%; box-sizing:border-box; }
.is-invalid { border-color:var(--red) !important; background:var(--err-bg) !important; }
.is-valid   { border-color:var(--green) !important; background:#f1f8e9 !important; }
.msg-err { font-size:0.82rem; color:var(--red); font-weight:600; margin-top:2px; }
.msg-ok  { font-size:0.82rem; color:var(--green); font-weight:600; margin-top:2px; }
.err { font-size:0.82rem; color:var(--red); font-weight:600; }
.alert-err {
    background:var(--err-bg); border-left:4px solid var(--red);
    padding:12px 18px; border-radius:6px; margin-bottom:20px;
    color:var(--red); font-weight:600;
}
.img-preview { margin-top:8px; display:flex; align-items:center; gap:12px; }
.img-preview img {
    width:80px; height:80px; object-fit:cover;
    border-radius:8px; border:2px solid var(--border);
}
.img-preview p { font-size:0.78rem; color:#888; }
.form-actions {
    display:flex; gap:14px; margin-top:28px;
    padding-top:20px; border-top:2px solid var(--border);
}
.btn-submit {
    background:var(--green); color:#fff; border:none;
    padding:12px 28px; border-radius:7px; font-size:1rem;
    font-weight:700; cursor:pointer;
}
.btn-submit:hover { background:var(--green-l); }
.btn-back {
    background:#e0e0e0; color:#333; padding:12px 22px;
    border-radius:7px; text-decoration:none; font-weight:600; font-size:1rem;
}
.btn-back:hover { background:#bdbdbd; }

/* ── Badge IA sur description ── */
.desc-wrapper { position:relative; }
.ia-badge {
    display:none; font-size:0.78rem; color:var(--ai);
    font-weight:700; margin-top:4px;
}
.ia-badge.show { display:block; }

/* ── Ingrédients ── */
.ing-section {
    margin-top:28px; padding-top:22px;
    border-top:2px solid var(--border);
}
.ing-title {
    font-size:1rem; font-weight:700; color:var(--green);
    text-transform:uppercase; letter-spacing:.05em; margin-bottom:14px;
}
.ing-table { width:100%; border-collapse:collapse; font-size:0.88rem; margin-bottom:12px; }
.ing-table th {
    background:#f1f8e9; color:var(--green); padding:10px 12px;
    text-align:left; font-weight:700; border-bottom:2px solid #c8e6c9;
}
.ing-table td { padding:7px 6px; vertical-align:top; }
.ing-table input {
    width:100%; padding:8px 10px; border:2px solid var(--border);
    border-radius:6px; font-size:0.87rem; outline:none;
    transition:border-color .2s; font-family:inherit;
}
.ing-table input:focus { border-color:var(--green); }
.ing-table input.is-invalid { border-color:var(--red); background:var(--err-bg); }
.ing-table input.is-valid   { border-color:var(--green); background:#f1f8e9; }
.btn-remove {
    background:var(--red); color:#fff; border:none;
    border-radius:5px; padding:7px 11px; cursor:pointer; font-size:0.85rem;
}
.btn-remove:hover { background:#e53935; }
.msg-ing { font-size:0.75rem; font-weight:600; display:block; margin-top:2px; }
.btn-select-ing {
    background:var(--orange); color:#fff; border:none; border-radius:6px;
    padding:9px 18px; cursor:pointer; font-weight:600; font-size:0.9rem;
    display:inline-flex; align-items:center; gap:6px;
}
.btn-select-ing:hover { background:#ff9800; }

/* ── Modal sélecteur ingrédient ── */
.modal-sel {
    display:none; position:fixed; inset:0;
    background:rgba(0,0,0,.5); z-index:9999;
    align-items:center; justify-content:center;
}
.modal-sel.show { display:flex; }
.modal-sel-box {
    background:#fff; border-radius:12px; padding:24px;
    width:440px; max-width:95%; max-height:80vh;
    display:flex; flex-direction:column;
    box-shadow:0 8px 30px rgba(0,0,0,.2);
}
.modal-sel-box h3 { margin:0 0 14px; font-size:1.1rem; color:var(--green); }
.modal-search {
    padding:9px 12px; border:2px solid var(--border); border-radius:6px;
    font-size:0.9rem; outline:none; margin-bottom:12px;
    width:100%; box-sizing:border-box;
}
.modal-search:focus { border-color:var(--green); }
.ing-list-modal {
    overflow-y:auto; flex:1; border:1px solid var(--border);
    border-radius:6px; list-style:none; padding:0; margin:0;
}
.ing-list-modal li {
    padding:10px 14px; cursor:pointer;
    border-bottom:1px solid #f0f0f0; font-size:0.9rem; transition:background .15s;
}
.ing-list-modal li:hover { background:#f1f8e9; color:var(--green); font-weight:600; }
.modal-close {
    background:#e0e0e0; color:#333; border:none; padding:9px 18px;
    border-radius:6px; cursor:pointer; font-weight:600; margin-top:12px; width:100%;
}

/* ════════════════════════════════════════════════════════
   CHATBOT IA
   ════════════════════════════════════════════════════════ */
.ai-panel {
    background:#fff; border-radius:14px;
    box-shadow:0 4px 24px rgba(108,63,197,.15);
    border:2px solid #e0d4ff;
    display:flex; flex-direction:column;
    height:580px; overflow:hidden;
}

/* En-tête */
.ai-header {
    background:linear-gradient(135deg, var(--ai), var(--ai-l));
    color:#fff; padding:14px 18px;
    display:flex; align-items:center; justify-content:space-between;
    border-radius:12px 12px 0 0;
}
.ai-header-left { display:flex; align-items:center; gap:10px; }
.ai-header-left .ai-icon {
    width:36px; height:36px; background:rgba(255,255,255,.2);
    border-radius:50%; display:flex; align-items:center; justify-content:center;
    font-size:1.2rem;
}
.ai-header-left h3 { margin:0; font-size:1rem; font-weight:700; }
.ai-header-left p  { margin:0; font-size:0.72rem; opacity:.85; }
.ai-status {
    width:9px; height:9px; background:#4ade80;
    border-radius:50%; box-shadow:0 0 6px #4ade80;
    animation: pulse-dot 2s infinite;
}
@keyframes pulse-dot {
    0%,100% { opacity:1; } 50% { opacity:.4; }
}

/* Corps messages */
.ai-messages {
    flex:1; overflow-y:auto; padding:16px;
    display:flex; flex-direction:column; gap:12px;
    background:#faf9ff;
}
.ai-messages::-webkit-scrollbar { width:4px; }
.ai-messages::-webkit-scrollbar-thumb { background:#d4c5f9; border-radius:4px; }

/* Bulles */
.msg-bubble { display:flex; gap:8px; max-width:90%; }
.msg-bubble.user { align-self:flex-end; flex-direction:row-reverse; }
.msg-bubble.bot  { align-self:flex-start; }
.bubble-avatar {
    width:30px; height:30px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:0.9rem;
}
.msg-bubble.bot  .bubble-avatar { background:var(--ai-bg); color:var(--ai); }
.msg-bubble.user .bubble-avatar { background:#e8f5e9; color:var(--green); }
.bubble-content { display:flex; flex-direction:column; gap:3px; }
.bubble-text {
    padding:10px 14px; border-radius:12px;
    font-size:0.87rem; line-height:1.5;
}
.msg-bubble.bot  .bubble-text {
    background:#fff; border:1px solid #e0d4ff; color:#333;
    border-radius:4px 12px 12px 12px;
}
.msg-bubble.user .bubble-text {
    background:var(--ai); color:#fff;
    border-radius:12px 4px 12px 12px;
}
.bubble-time { font-size:0.7rem; color:#aaa; padding:0 4px; }
.msg-bubble.user .bubble-time { text-align:right; }

/* Suggestions d'ingrédients dans le chat */
.sug-list { margin-top:8px; display:flex; flex-direction:column; gap:6px; }
.sug-item {
    background:#f5f0ff; border:1px solid #d4c5f9; border-radius:8px;
    padding:8px 12px; display:flex; align-items:center;
    justify-content:space-between; gap:8px; font-size:0.83rem;
}
.sug-item-info { display:flex; flex-direction:column; gap:1px; }
.sug-item-nom  { font-weight:700; color:#333; }
.sug-item-qte  { color:#777; font-size:0.78rem; }
.sug-btns { display:flex; gap:5px; flex-shrink:0; }
.btn-accept {
    background:#2e7d32; color:#fff; border:none; border-radius:5px;
    padding:4px 10px; cursor:pointer; font-size:0.78rem; font-weight:700;
}
.btn-accept:hover { background:#4caf50; }
.btn-refuse {
    background:#e0e0e0; color:#555; border:none; border-radius:5px;
    padding:4px 10px; cursor:pointer; font-size:0.78rem; font-weight:700;
}
.btn-refuse:hover { background:#bdbdbd; }
.btn-accept:disabled, .btn-refuse:disabled {
    opacity:.5; cursor:default;
}
.sug-accepted { color:#2e7d32; font-size:0.75rem; font-weight:700; }
.sug-refused  { color:#999;    font-size:0.75rem; font-weight:700; }

/* Bouton insérer description */
.btn-insert-desc {
    margin-top:8px; background:var(--ai); color:#fff; border:none;
    border-radius:7px; padding:7px 14px; cursor:pointer;
    font-size:0.82rem; font-weight:700; display:inline-flex;
    align-items:center; gap:5px;
}
.btn-insert-desc:hover { background:var(--ai-l); }
.btn-insert-desc:disabled { opacity:.5; cursor:default; }

/* Bouton Régénérer */
.btn-regenerer {
    margin-top:8px; background:#fff; color:var(--ai);
    border:2px solid var(--ai); border-radius:7px;
    padding:6px 14px; cursor:pointer; font-size:0.82rem;
    font-weight:700; display:inline-flex; align-items:center;
    gap:5px; transition:all .2s;
}
.btn-regenerer:hover { background:var(--ai); color:#fff; }
.btn-regenerer:disabled { opacity:.5; cursor:default; }

/* Typing indicator */
.typing-indicator {
    display:flex; gap:4px; align-items:center; padding:10px 14px;
    background:#fff; border:1px solid #e0d4ff; border-radius:4px 12px 12px 12px;
}
.typing-indicator span {
    width:7px; height:7px; background:#c4b5fd; border-radius:50%;
    animation: bounce 1.2s infinite;
}
.typing-indicator span:nth-child(2) { animation-delay:.2s; }
.typing-indicator span:nth-child(3) { animation-delay:.4s; }
@keyframes bounce {
    0%,60%,100% { transform:translateY(0); }
    30%          { transform:translateY(-6px); }
}

/* Actions rapides */
.ai-quick {
    padding:10px 14px; border-top:1px solid #f0e8ff;
    display:flex; flex-direction:column; gap:6px; background:#faf9ff;
}
.ai-quick-label { font-size:0.72rem; color:#999; font-weight:700; text-transform:uppercase; }
.ai-quick-btns  { display:flex; flex-wrap:wrap; gap:6px; }
.btn-quick {
    background:#fff; border:1.5px solid #d4c5f9; color:var(--ai);
    border-radius:20px; padding:5px 12px; cursor:pointer;
    font-size:0.78rem; font-weight:600; transition:all .2s;
    display:flex; align-items:center; gap:4px;
}
.btn-quick:hover { background:var(--ai); color:#fff; border-color:var(--ai); }

/* Zone de saisie */
.ai-input-area {
    padding:12px 14px; border-top:2px solid #f0e8ff;
    display:flex; gap:8px; background:#fff;
    border-radius:0 0 12px 12px;
}
.ai-input {
    flex:1; padding:10px 14px; border:2px solid #e0d4ff;
    border-radius:22px; font-size:0.88rem; outline:none;
    font-family:inherit; resize:none; height:42px; max-height:80px;
    overflow:hidden; line-height:1.4;
    transition:border-color .2s;
}
.ai-input:focus { border-color:var(--ai); }
.btn-send {
    background:var(--ai); color:#fff; border:none;
    border-radius:22px; padding:10px 18px; cursor:pointer;
    font-weight:700; font-size:0.88rem; flex-shrink:0;
    transition:background .2s;
}
.btn-send:hover    { background:var(--ai-l); }
.btn-send:disabled { opacity:.6; cursor:default; }

/* Responsive */
@media (max-width: 1100px) {
    .recette-layout { flex-direction:column; }
    .recette-ai-col { width:100%; position:static; }
    .ai-panel { height:500px; }
}
</style>

<div class="recette-layout">

<!-- ══════════════════════════════════════════════════════
     COLONNE GAUCHE : Formulaire
     ══════════════════════════════════════════════════════ -->
<div class="recette-form-col">
<div class="form-card">
    <h2 class="form-title"><?= $isEdit ? '✏️ Modifier la Recette' : '➕ Nouvelle Recette' ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="alert-err">⚠️ Veuillez corriger les erreurs ci-dessous.</div>
    <?php endif; ?>

    <form method="POST"
          action="<?= $isEdit ? '/2A35/Admin/recette/update/'.$recette['id'] : '/2A35/Admin/recette/store' ?>"
          enctype="multipart/form-data" id="formRecette" novalidate>

        <div class="form-grid">

            <!-- Nom -->
            <div class="form-group full">
                <label for="nom">Nom de la recette <span class="req">*</span></label>
                <input type="text" id="nom" name="nom" maxlength="150"
                       value="<?= htmlspecialchars($recette['nom'] ?? '') ?>"
                       class="<?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                       placeholder="Ex : Salade méditerranéenne">
                <?php if (isset($errors['nom'])): ?><span class="err">⚠ <?= $errors['nom'] ?></span><?php endif; ?>
            </div>

            <!-- Description -->
            <div class="form-group full">
                <label for="description">Description <span class="req">*</span></label>
                <div class="desc-wrapper">
                    <textarea id="description" name="description"
                              class="<?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                              placeholder="Décrivez brièvement la recette... ou utilisez l'IA ➡️"><?= htmlspecialchars($recette['description'] ?? '') ?></textarea>
                    <span class="ia-badge" id="iaBadge">✨ Générée par IA</span>
                </div>
                <?php if (isset($errors['description'])): ?><span class="err">⚠ <?= $errors['description'] ?></span><?php endif; ?>
            </div>

            <!-- Catégorie -->
            <div class="form-group">
                <label for="categorie">Catégorie <span class="req">*</span></label>
                <select id="categorie" name="categorie" class="<?= isset($errors['categorie']) ? 'is-invalid' : '' ?>">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ([
                        'petit-dejeuner'=>'🌅 Petit-déjeuner','dejeuner'=>'☀️ Déjeuner',
                        'diner'=>'🌙 Dîner','collation'=>'🍎 Collation','dessert'=>'🍰 Dessert',
                        'vegetarien'=>'🥦 Végétarien','regime'=>'⚖️ Régime','sportif'=>'💪 Sportif'
                    ] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($recette['categorie'] ?? '')===$v ? 'selected':'' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['categorie'])): ?><span class="err">⚠ <?= $errors['categorie'] ?></span><?php endif; ?>
            </div>

            <!-- Difficulté -->
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

            <!-- Durée -->
            <div class="form-group">
                <label for="duree">Durée (minutes) <span class="req">*</span></label>
                <input type="number" id="duree" name="duree" min="1" max="1440"
                       value="<?= htmlspecialchars($recette['duree'] ?? '') ?>"
                       class="<?= isset($errors['duree']) ? 'is-invalid' : '' ?>"
                       placeholder="Ex : 30">
                <?php if (isset($errors['duree'])): ?><span class="err">⚠ <?= $errors['duree'] ?></span><?php endif; ?>
            </div>

            <!-- Calories -->
            <div class="form-group">
                <label for="calories">Calories (kcal) <span class="req">*</span></label>
                <input type="number" id="calories" name="calories" min="0" max="99999"
                       value="<?= htmlspecialchars($recette['calories'] ?? '') ?>"
                       class="<?= isset($errors['calories']) ? 'is-invalid' : '' ?>"
                       placeholder="Ex : 350">
                <?php if (isset($errors['calories'])): ?><span class="err">⚠ <?= $errors['calories'] ?></span><?php endif; ?>
            </div>

            <!-- Image -->
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

        </div><!-- /form-grid -->

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
                                <option value="g"  <?= ($ing['unite'] ?? '') === 'g'  ? 'selected' : '' ?>>g</option>
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
</div><!-- /form-card -->
</div><!-- /recette-form-col -->

<!-- ══════════════════════════════════════════════════════
     COLONNE DROITE : Chatbot IA
     ══════════════════════════════════════════════════════ -->
<div class="recette-ai-col">
<div class="ai-panel">

    <!-- En-tête -->
    <div class="ai-header">
        <div class="ai-header-left">
            <div class="ai-icon">🤖</div>
            <div>
                <h3>Assistant IA — Recettes</h3>
                <p>Génération intelligente de contenu</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <button onclick="viderHistorique()" title="Effacer l'historique"
                style="background:rgba(255,255,255,.15);border:none;color:#fff;border-radius:6px;
                       padding:4px 9px;cursor:pointer;font-size:0.75rem;font-weight:600;">
                🗑 Vider
            </button>
            <div class="ai-status" title="IA connectée"></div>
        </div>
    </div>

    <!-- Messages -->
    <div class="ai-messages" id="aiMessages">
        <!-- Message de bienvenue -->
        <div class="msg-bubble bot">
            <div class="bubble-avatar">🤖</div>
            <div class="bubble-content">
                <div class="bubble-text">
                    Bonjour ! Je suis votre assistant IA.<br><br>
                    👉 <strong>Saisissez le nom de la recette</strong> dans le formulaire, puis cliquez sur <strong>"Générer la description"</strong> ou tapez <strong>Envoyer</strong>.<br><br>
                    Je remplirai automatiquement la description et vous proposerai des ingrédients !
                </div>
                <span class="bubble-time"><?= date('H:i') ?></span>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="ai-quick">
        <span class="ai-quick-label">⚡ Actions rapides</span>
        <div class="ai-quick-btns">
            <button class="btn-quick" onclick="actionRapide('description')">✨ Générer la description</button>
            <button class="btn-quick" onclick="actionRapide('ingredients')">🥦 Suggérer les ingrédients</button>
            <button class="btn-quick" onclick="actionRapide('tout')">🪄 Description + ingrédients</button>
        </div>
    </div>

    <!-- Zone de saisie -->
    <div class="ai-input-area">
        <textarea class="ai-input" id="aiInput"
                  placeholder="Votre message..."
                  rows="1"
                  onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();envoyerMessage();}"></textarea>
        <button class="btn-send" id="btnSend" onclick="envoyerMessage()">Envoyer</button>
    </div>

</div><!-- /ai-panel -->
</div><!-- /recette-ai-col -->

</div><!-- /recette-layout -->

<!-- ── Modal sélecteur ingrédient ── -->
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

// ════════════════════════════════════════════════════════
// CHATBOT IA
// ════════════════════════════════════════════════════════

const aiMessages = document.getElementById('aiMessages');
const aiInput    = document.getElementById('aiInput');
const btnSend    = document.getElementById('btnSend');

// ── Clé sessionStorage liée à cette page ─────────────────────────────────────
const CHAT_KEY = 'chatbot_recette_<?= $isEdit ? 'edit_'.($recette['id'] ?? 0) : 'new' ?>';

// ── Sauvegarder un message dans sessionStorage ────────────────────────────────
function sauvegarderMessage(role, html, extras) {
    const historique = JSON.parse(sessionStorage.getItem(CHAT_KEY) || '[]');
    historique.push({ role, html, extras: extras || '', time: new Date().toLocaleTimeString('fr-FR', { hour:'2-digit', minute:'2-digit' }) });
    sessionStorage.setItem(CHAT_KEY, JSON.stringify(historique));
}

// ── Restaurer l'historique au chargement ──────────────────────────────────────
function restaurerHistorique() {
    const historique = JSON.parse(sessionStorage.getItem(CHAT_KEY) || '[]');
    if (historique.length === 0) return;
    // Supprimer le message de bienvenue par défaut
    aiMessages.innerHTML = '';
    historique.forEach(msg => {
        const wrap = document.createElement('div');
        wrap.className = 'msg-bubble ' + msg.role;
        const avatar = msg.role === 'bot' ? '🤖' : '👤';
        wrap.innerHTML = `
            <div class="bubble-avatar">${avatar}</div>
            <div class="bubble-content">
                <div class="bubble-text">${msg.html}</div>
                ${msg.extras}
                <span class="bubble-time">${msg.time}</span>
            </div>`;
        aiMessages.appendChild(wrap);
    });
    aiMessages.scrollTop = aiMessages.scrollHeight;
}

// ── Vider l'historique ────────────────────────────────────────────────────────
function viderHistorique() {
    sessionStorage.removeItem(CHAT_KEY);
    aiMessages.innerHTML = '';
    // Remettre le message de bienvenue
    ajouterBulle('bot', 'Historique effacé. Saisissez le nom de la recette et utilisez les boutons rapides !');
}

/** Récupère le contexte actuel du formulaire */
function getContexte() {
    const ids  = [...document.querySelectorAll('input[name="ing_id[]"]')];
    const noms = [...document.querySelectorAll('input[name="ing_nom[]"]')];
    const ings = ids.map((el, i) => ({ id: el.value, nom: noms[i]?.value || '' }));
    return {
        nom:         document.getElementById('nom').value.trim(),
        categorie:   document.getElementById('categorie').value,
        difficulte:  document.getElementById('difficulte').value,
        duree:       document.getElementById('duree').value,
        ingredients: ings,
    };
}

/** Ajoute une bulle dans le chat et la sauvegarde dans sessionStorage */
function ajouterBulle(role, html, extras) {
    const wrap = document.createElement('div');
    wrap.className = 'msg-bubble ' + role;

    const now = new Date().toLocaleTimeString('fr-FR', { hour:'2-digit', minute:'2-digit' });
    const avatar = role === 'bot' ? '🤖' : '👤';

    // Pour l'historique on ne sauvegarde pas les boutons interactifs (suggestions)
    // car ils ne sont plus fonctionnels après rechargement
    const extrasHtml = extras || '';
    const extrasHistorique = extras
        ? extras.replace(/<button[^>]*>[\s\S]*?<\/button>/g, '')  // retirer les boutons
               .replace(/class="sug-btns"[\s\S]*?<\/div>/g, '')
        : '';

    wrap.innerHTML = `
        <div class="bubble-avatar">${avatar}</div>
        <div class="bubble-content">
            <div class="bubble-text">${html}</div>
            ${extrasHtml}
            <span class="bubble-time">${now}</span>
        </div>`;

    aiMessages.appendChild(wrap);
    aiMessages.scrollTop = aiMessages.scrollHeight;

    // Sauvegarder dans sessionStorage (sans les boutons interactifs)
    sauvegarderMessage(role, html, extrasHistorique);

    return wrap;
}

/** Indicateur de frappe */
function afficherTyping() {
    const wrap = document.createElement('div');
    wrap.className = 'msg-bubble bot';
    wrap.id = 'typingBubble';
    wrap.innerHTML = `
        <div class="bubble-avatar">🤖</div>
        <div class="bubble-content">
            <div class="typing-indicator">
                <span></span><span></span><span></span>
            </div>
        </div>`;
    aiMessages.appendChild(wrap);
    aiMessages.scrollTop = aiMessages.scrollHeight;
}
function supprimerTyping() {
    document.getElementById('typingBubble')?.remove();
}

/** Envoyer — appelle le backend local, pas d'API externe */
async function envoyerMessage(messageForce) {
    const msg = messageForce || aiInput.value.trim();
    if (!msg) return;

    ajouterBulle('user', escHtml(msg));
    aiInput.value = '';
    aiInput.style.height = 'auto';

    btnSend.disabled = true;
    afficherTyping();

    try {
        const resp = await fetch('/2A35/Admin/Ai/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ context: getContexte(), type: 'tout' })
        });

        const data = await resp.json();
        supprimerTyping();
        traiterReponseIA(data, 'tout');
    } catch (e) {
        supprimerTyping();
        ajouterBulle('bot', '❌ Erreur serveur. Réessayez.');
    }

    btnSend.disabled = false;
    aiInput.focus();
}

/** Traite la réponse du backend selon le type demandé */
function traiterReponseIA(data, type) {
    if (data.error) {
        ajouterBulle('bot', '⚠️ ' + escHtml(data.error));
        return;
    }

    // ── Description (type description ou tout) ───────────────────────────────
    if ((type === 'description' || type === 'tout') && data.description) {
        insererDescription(data.description, null);
        let extrasDesc = `
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:10px 12px;margin-top:6px;font-size:0.85rem;color:#166534;">
            <strong>✅ Description insérée automatiquement !</strong><br>
            <em style="color:#555;">${escHtml(data.description)}</em>
        </div>
        <button class="btn-regenerer" onclick="regenererDescription(this)" title="Générer une nouvelle version">
            🔄 Régénérer
        </button>`;
        if (type === 'description') {
            ajouterBulle('bot', 'Description générée et insérée dans le formulaire. Vous pouvez la modifier ou régénérer.', extrasDesc);
            return;
        }
        if (data.suggestions && data.suggestions.length > 0) {
            ajouterBulle('bot', 'Description insérée ! Voici aussi des ingrédients suggérés :', extrasDesc + construireSuggestionsHTML(data.suggestions));
        } else {
            ajouterBulle('bot', 'Description générée et insérée !', extrasDesc);
        }
        return;
    }

    // ── Suggestions seulement (type ingredients) ─────────────────────────────
    if ((type === 'ingredients' || type === 'tout') && data.suggestions && data.suggestions.length > 0) {
        ajouterBulle('bot', 'Voici les ingrédients suggérés pour cette recette :', construireSuggestionsHTML(data.suggestions));
        return;
    }

    ajouterBulle('bot', 'Saisissez le nom de la recette pour que je puisse générer la description et les suggestions.');
}

/** Construit le HTML des suggestions d'ingrédients */
function construireSuggestionsHTML(suggestions) {
    let html = '<div class="sug-list">';
    suggestions.forEach((sug) => {
        const ts = Date.now() + Math.random();
        html += `
            <div class="sug-item" id="sug-${ts}">
                <div class="sug-item-info">
                    <span class="sug-item-nom">🌿 ${escHtml(sug.nom)}</span>
                    <span class="sug-item-qte">${sug.quantite} ${sug.unite}</span>
                </div>
                <div class="sug-btns">
                    <button class="btn-accept"
                        onclick="accepterSuggestion(${sug.id},'${escHtml(sug.nom).replace(/'/g,"\\'")}',${sug.quantite},'${sug.unite}',this)">
                        ✓ Accepter
                    </button>
                    <button class="btn-refuse" onclick="refuserSuggestion(this)">✕ Refuser</button>
                </div>
            </div>`;
    });
    html += '</div>';
    return html;
}

/** Actions rapides */
function actionRapide(type) {
    const nom = document.getElementById('nom').value.trim();
    if (!nom) {
        ajouterBulle('bot', '⚠️ Saisissez d\'abord le <strong>nom de la recette</strong> dans le formulaire.');
        return;
    }
    const labels = {
        'description': '✨ Générer la description',
        'ingredients': '🥦 Suggérer les ingrédients',
        'tout':        '🪄 Description + ingrédients',
    };
    ajouterBulle('user', labels[type] || 'Générer...');
    envoyerMessageAvecType(type);
}

/** Envoyer avec un type d'action spécifique */
async function envoyerMessageAvecType(type) {
    btnSend.disabled = true;
    afficherTyping();

    try {
        const resp = await fetch('/2A35/Admin/Ai/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            // _t varie à chaque appel → suggestions et descriptions différentes
            body: JSON.stringify({ context: { ...getContexte(), _t: Date.now() }, type: type })
        });

        const data = await resp.json();
        supprimerTyping();
        traiterReponseIA(data, type);
    } catch (e) {
        supprimerTyping();
        ajouterBulle('bot', '❌ Erreur serveur. Réessayez.');
    }

    btnSend.disabled = false;
    aiInput.focus();
}

/** Régénérer une nouvelle version de la description */
async function regenererDescription(btn) {
    const nom = document.getElementById('nom').value.trim();
    if (!nom) {
        ajouterBulle('bot', '⚠️ Saisissez d\'abord le nom de la recette.');
        return;
    }
    btn.disabled = true;
    btn.textContent = '⏳ Génération...';
    ajouterBulle('user', '🔄 Régénérer la description');
    afficherTyping();

    try {
        const resp = await fetch('/2A35/Admin/Ai/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            // On ajoute un timestamp pour forcer une variante différente
            body: JSON.stringify({ context: { ...getContexte(), _t: Date.now() }, type: 'description' })
        });
        const data = await resp.json();
        supprimerTyping();
        traiterReponseIA(data, 'description');
    } catch (e) {
        supprimerTyping();
        ajouterBulle('bot', '❌ Erreur. Réessayez.');
    }
    btn.disabled = false;
    btn.textContent = '🔄 Régénérer';
}

/** Insérer la description dans le formulaire (automatique ou manuel) */
function insererDescription(desc, btn) {
    const textarea = document.getElementById('description');
    textarea.value = desc;
    textarea.classList.remove('is-invalid');
    textarea.classList.add('is-valid');
    document.getElementById('iaBadge').classList.add('show');
    // Auto-resize pour afficher tout le texte sans scrollbar
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
    if (btn) {
        btn.textContent = '✅ Insérée !';
        btn.disabled = true;
    }
}

/** Accepter une suggestion d'ingrédient */
function accepterSuggestion(id, nom, quantite, unite, btn) {
    // Vérifier si déjà présent
    const existants = [...document.querySelectorAll('input[name="ing_id[]"]')];
    if (existants.some(el => el.value == id)) {
        btn.closest('.sug-item').querySelector('.sug-btns').innerHTML =
            '<span class="sug-accepted">⚠️ Déjà dans la liste</span>';
        return;
    }

    // Ajouter la ligne dans le tableau
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();

    const tbody = document.getElementById('ingBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <input type="hidden" name="ing_id[]" value="${id}">
            <input type="text" name="ing_nom[]" value="${escHtml(nom)}" readonly
                   style="background:#f9fbe7;cursor:default;border-color:#2e7d32;">
        </td>
        <td>
            <input type="number" name="ing_quantite[]" step="0.01" min="0.01"
                   value="${quantite}" oninput="validerIngQte(this)" class="is-valid">
            <span class="msg-ing msg-ok">✔</span>
        </td>
        <td>
            <select name="ing_unite[]" style="width:100%;padding:8px 10px;border:2px solid var(--border);border-radius:6px;font-size:0.87rem;outline:none;background:#fafafa;">
                <option value="g"  ${unite === 'g'  ? 'selected' : ''}>g</option>
                <option value="ml" ${unite === 'ml' ? 'selected' : ''}>ml</option>
            </select>
        </td>
        <td><button type="button" class="btn-remove" onclick="supprimerLigne(this)">✕</button></td>`;
    tbody.appendChild(tr);
    calculerCalories();

    // Mettre à jour les boutons de la suggestion
    btn.closest('.sug-item').querySelector('.sug-btns').innerHTML =
        '<span class="sug-accepted">✅ Ajouté !</span>';

    // Scroll vers le tableau
    document.getElementById('ingBody').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

/** Refuser une suggestion */
function refuserSuggestion(btn) {
    btn.closest('.sug-item').querySelector('.sug-btns').innerHTML =
        '<span class="sug-refused">✕ Refusé</span>';
}

// ════════════════════════════════════════════════════════
// FORMULAIRE — Fonctions existantes
// ════════════════════════════════════════════════════════

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
function fermerModal() { document.getElementById('modalSel').classList.remove('show'); }
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
                   placeholder="Ex : 200" oninput="validerIngQte(this)">
            <span class="msg-ing"></span>
        </td>
        <td>
            <select name="ing_unite[]" style="width:100%;padding:8px 10px;border:2px solid var(--border);border-radius:6px;font-size:0.87rem;outline:none;background:#fafafa;">
                <option value="g">g</option>
                <option value="ml">ml</option>
            </select>
        </td>
        <td><button type="button" class="btn-remove" onclick="supprimerLigne(this)">✕</button></td>`;
    tbody.appendChild(tr);
    tr.querySelectorAll('input')[1].focus();
    fermerModal();
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
    // Recalcul après suppression
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
    // Recalcul automatique des calories à chaque modification de quantité
    calculerCalories();
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
document.getElementById('description').addEventListener('input', function(){
    validerDescription(this);
    document.getElementById('iaBadge').classList.remove('show');
    // Auto-resize
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});
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
        validerCalories(document.getElementById('calories')),
    ].every(Boolean);

    const ids = document.querySelectorAll('input[name="ing_id[]"]');
    if (ids.length === 0) {
        alert('⚠️ Veuillez sélectionner au moins un ingrédient.');
        ok = false;
    }
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

// ── Utilitaires ───────────────────────────────────────────────────────────────
function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function formatTexte(str) {
    // Convertir les listes markdown en HTML
    return escHtml(str)
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\n- /g, '<br>• ')
        .replace(/\n/g, '<br>');
}

// Auto-resize textarea chatbot
aiInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 80) + 'px';
});

// ── Initialisation au chargement ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    restaurerHistorique();
});
</script>

<?php $content = ob_get_clean(); require_once 'View/back/layout.php'; ?>
