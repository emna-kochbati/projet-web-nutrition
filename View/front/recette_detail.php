<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header -->
<div class="container-fluid page-header py-5 mb-5 wow fadeIn"
     style="background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url('/2A35/assets/img/carousel-2.jpg') center/cover no-repeat;">
    <div class="container py-5">
        <h1 class="display-5 text-white fw-bold mb-3"><?= htmlspecialchars($recette['nom']) ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/2A35/Home" class="text-white">Accueil</a></li>
                <li class="breadcrumb-item"><a href="/2A35/RecetteFront" class="text-white">Recettes</a></li>
                <li class="breadcrumb-item text-secondary active"><?= htmlspecialchars($recette['nom']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-5">

        <!-- Image + infos -->
        <div class="col-lg-5 wow fadeInLeft" data-wow-delay="0.1s">
            <?php if ($recette['image']): ?>
                <img src="/2A35/assets/uploads/recettes/<?= htmlspecialchars($recette['image']) ?>"
                     class="img-fluid rounded-3 shadow w-100"
                     style="max-height:380px; object-fit:cover;"
                     alt="<?= htmlspecialchars($recette['nom']) ?>">
            <?php else: ?>
                <div class="rounded-3 d-flex align-items-center justify-content-center shadow"
                     style="height:380px; background:linear-gradient(135deg,#2e7d32,#4caf50);">
                    <i class="fa fa-utensils fa-5x text-white opacity-75"></i>
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="row g-3 mt-3">
                <div class="col-6">
                    <div class="bg-light rounded-3 p-3 text-center">
                        <i class="fa fa-clock fa-2x text-primary mb-2"></i>
                        <div class="fw-bold fs-5"><?= $recette['duree'] ?> min</div>
                        <small class="text-muted">Durée</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-light rounded-3 p-3 text-center">
                        <i class="fa fa-fire fa-2x text-danger mb-2"></i>
                        <div class="fw-bold fs-5"><?= $recette['calories'] ?> kcal</div>
                        <small class="text-muted">Calories</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-light rounded-3 p-3 text-center">
                        <?php $color = match($recette['difficulte']) {'facile'=>'success','moyen'=>'warning','difficile'=>'danger',default=>'secondary'}; ?>
                        <i class="fa fa-signal fa-2x text-<?= $color ?> mb-2"></i>
                        <div class="fw-bold fs-5"><?= htmlspecialchars($recette['difficulte']) ?></div>
                        <small class="text-muted">Difficulté</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-light rounded-3 p-3 text-center">
                        <i class="fa fa-tag fa-2x text-secondary mb-2"></i>
                        <div class="fw-bold" style="font-size:0.95rem"><?= htmlspecialchars($recette['categorie']) ?></div>
                        <small class="text-muted">Catégorie</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails -->
        <div class="col-lg-7 wow fadeInRight" data-wow-delay="0.1s">
            <h2 class="fw-bold mb-3" style="color:#2e7d32;"><?= htmlspecialchars($recette['nom']) ?></h2>

            <!-- Description -->
            <?php if (!empty($recette['description'])): ?>
            <div class="mb-4">
                <h6 class="fw-bold mb-2" style="color:#2e7d32;">📝 Description</h6>
                <p style="color:#555; line-height:1.8; font-size:0.97rem; background:#f9fbe7; border-left:4px solid #2e7d32; padding:14px 18px; border-radius:6px; margin:0;">
                    <?= nl2br(htmlspecialchars($recette['description'])) ?>
                </p>
            </div>
            <?php endif; ?>
            <!-- Ingrédients -->
            <?php if (!empty($ingredients)): ?>
            <h5 class="fw-bold mb-3"><i class="fa fa-leaf me-2 text-success"></i>Ingrédients</h5>
            <ul class="list-group list-group-flush mb-4">
                <?php foreach ($ingredients as $ing): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="fa fa-circle text-success me-2" style="font-size:0.5rem;"></i><?= htmlspecialchars($ing['nom']) ?></span>
                        <span class="badge" style="background:#2e7d32;"><?= $ing['quantite'] ?> <?= htmlspecialchars($ing['unite']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <!-- Valeurs nutritionnelles -->
            <?php if (!empty($valeursNutri) && ($valeursNutri['proteines'] + $valeursNutri['glucides'] + $valeursNutri['lipides']) > 0): ?>
            <div class="mb-4">
                <h5 class="fw-bold mb-3"><i class="fa fa-flask me-2 text-success"></i>Valeurs nutritionnelles</h5>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div style="background:#e8f5e9;border-radius:10px;padding:14px;text-align:center;border:1px solid #c8e6c9;">
                            <div style="font-size:1.5rem;">💪</div>
                            <div style="font-size:1.1rem;font-weight:800;color:#2e7d32;"><?= $valeursNutri['proteines'] ?> g</div>
                            <div style="font-size:0.75rem;color:#777;">Protéines</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="background:#e3f2fd;border-radius:10px;padding:14px;text-align:center;border:1px solid #bbdefb;">
                            <div style="font-size:1.5rem;">🦴</div>
                            <div style="font-size:1.1rem;font-weight:800;color:#1565c0;"><?= $valeursNutri['calcium'] ?> mg</div>
                            <div style="font-size:0.75rem;color:#777;">Calcium</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="background:#fff3e0;border-radius:10px;padding:14px;text-align:center;border:1px solid #ffe0b2;">
                            <div style="font-size:1.5rem;">⚡</div>
                            <div style="font-size:1.1rem;font-weight:800;color:#f57c00;"><?= $valeursNutri['glucides'] ?> g</div>
                            <div style="font-size:0.75rem;color:#777;">Glucides</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="background:#fce4ec;border-radius:10px;padding:14px;text-align:center;border:1px solid #f8bbd0;">
                            <div style="font-size:1.5rem;">🫧</div>
                            <div style="font-size:1.1rem;font-weight:800;color:#c62828;"><?= $valeursNutri['lipides'] ?> g</div>
                            <div style="font-size:0.75rem;color:#777;">Lipides</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Nutri-Score badge -->
            <?php if (isset($nutriScore) && $nutriScore['lettre'] !== '?'): ?>
            <?php
            $nsConfig = [
                'A' => ['bg' => '#1a7a1a', 'label' => 'Excellent'],
                'B' => ['bg' => '#5aab1f', 'label' => 'Bon'],
                'C' => ['bg' => '#f5c800', 'label' => 'Moyen'],
                'D' => ['bg' => '#e07800', 'label' => 'Médiocre'],
                'E' => ['bg' => '#d32f2f', 'label' => 'Mauvais'],
            ];
            $nsCfg = $nsConfig[$nutriScore['lettre']] ?? $nsConfig['C'];
            ?>
            <div class="mb-4">
                <h5 class="fw-bold mb-3">🏅 Nutri-Score</h5>
                <div style="display:flex;align-items:center;gap:14px;background:#f9f9f9;
                     border-radius:12px;padding:14px 18px;border:1px solid #e0e0e0;">
                    <!-- Lettre active avec clignotement -->
                    <div style="width:56px;height:56px;background:<?= $nsCfg['bg'] ?>;
                         color:#fff;border-radius:10px;display:flex;align-items:center;
                         justify-content:center;font-size:1.8rem;font-weight:900;
                         box-shadow:0 4px 12px rgba(0,0,0,.2);
                         animation:ns-pulse 1.5s ease-in-out infinite;">
                        <?= $nutriScore['lettre'] ?>
                    </div>
                    <!-- Barre A B C D E -->
                    <div style="display:flex;gap:5px;align-items:center;">
                        <?php foreach (['A'=>'#1a7a1a','B'=>'#5aab1f','C'=>'#f5c800','D'=>'#e07800','E'=>'#d32f2f'] as $l=>$c): ?>
                        <div style="width:30px;height:30px;background:<?= $c ?>;border-radius:6px;
                             display:flex;align-items:center;justify-content:center;
                             color:#fff;font-size:0.85rem;font-weight:800;
                             opacity:<?= $l === $nutriScore['lettre'] ? '1' : '0.3' ?>;">
                            <?= $l ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Label -->
                    <div>
                        <div style="font-weight:800;font-size:1rem;color:<?= $nsCfg['bg'] ?>;">
                            <?= $nutriScore['lettre'] ?> — <?= $nsCfg['label'] ?> pour la santé
                        </div>
                        <div style="font-size:0.78rem;color:#888;margin-top:2px;">
                            <?= $nutriScore['label'] ?>
                        </div>
                    </div>
                </div>
            </div>
            <style>
            @keyframes ns-pulse {
                0%,100% { box-shadow:0 4px 12px rgba(0,0,0,.2); }
                50%      { box-shadow:0 4px 20px <?= $nsCfg['bg'] ?>88; }
            }
            </style>
            <?php endif; ?>

            <div class="d-flex gap-3 mt-4 flex-wrap">
                <a href="/2A35/RecetteFront" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Retour aux recettes
                </a>
            </div>

            <!-- Panneau d'analyse IA -->
            <div id="panneauIA" style="display:none; margin-top:24px; border-radius:12px;
                 border:2px solid #e0d4ff; background:#faf8ff; padding:22px;
                 box-shadow:0 4px 20px rgba(108,63,197,.1);">

                <!-- En-tête -->
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;
                            padding-bottom:14px;border-bottom:2px solid #e0d4ff;">
                    <div style="width:38px;height:38px;background:linear-gradient(135deg,#6c3fc5,#8b5cf6);
                                border-radius:50%;display:flex;align-items:center;justify-content:center;
                                font-size:1.2rem;">🤖</div>
                    <div>
                        <div style="font-weight:800;color:#6c3fc5;font-size:1rem;">Analyse IA — <?= htmlspecialchars($recette['nom']) ?></div>
                        <div style="font-size:0.75rem;color:#999;" id="sourceLabel"></div>
                    </div>
                </div>

                <!-- Indicateur de chargement -->
                <div id="iaLoading" style="text-align:center;padding:20px;">
                    <div style="display:inline-flex;gap:6px;align-items:center;">
                        <span style="width:10px;height:10px;background:#8b5cf6;border-radius:50%;
                                     animation:bounce-ia .8s infinite;"></span>
                        <span style="width:10px;height:10px;background:#8b5cf6;border-radius:50%;
                                     animation:bounce-ia .8s .2s infinite;"></span>
                        <span style="width:10px;height:10px;background:#8b5cf6;border-radius:50%;
                                     animation:bounce-ia .8s .4s infinite;"></span>
                    </div>
                    <div style="color:#8b5cf6;font-weight:600;margin-top:8px;font-size:0.9rem;">
                        Gemini analyse la recette...
                    </div>
                </div>

                <!-- Résultats -->
                <div id="iaResultats" style="display:none;">

                    <!-- Badges profils de santé -->
                    <div style="margin-bottom:18px;">
                        <div style="font-weight:700;color:#333;font-size:0.9rem;margin-bottom:10px;
                                    text-transform:uppercase;letter-spacing:.05em;">
                            👤 Profils de santé
                        </div>
                        <div id="profilsBadges" style="display:flex;flex-wrap:wrap;gap:8px;"></div>
                    </div>

                    <!-- Analyse textuelle Gemini -->
                    <div style="background:#fff;border-radius:8px;padding:16px;
                                border-left:4px solid #8b5cf6;">
                        <div style="font-weight:700;color:#6c3fc5;font-size:0.85rem;
                                    margin-bottom:8px;">💬 Analyse nutritionnelle</div>
                        <div id="analyseTexte" style="color:#444;font-size:0.9rem;line-height:1.7;"></div>
                    </div>
                </div>

                <!-- Erreur -->
                <div id="iaErreur" style="display:none;color:#c62828;font-weight:600;
                     background:#ffebee;padding:12px;border-radius:8px;font-size:0.88rem;"></div>
            </div>
        </div>

    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     CHATBOT FLOTTANT — Assistant Nutrition
     ══════════════════════════════════════════════════════ -->

<!-- Icône flottante -->
<button id="chatbotToggle" onclick="toggleChatbot()"
    style="position:fixed;bottom:28px;right:28px;width:60px;height:60px;
           background:linear-gradient(135deg,#6c3fc5,#8b5cf6);color:#fff;
           border:none;border-radius:50%;cursor:pointer;font-size:1.6rem;
           box-shadow:0 6px 20px rgba(108,63,197,.45);z-index:9999;
           transition:all .3s;display:flex;align-items:center;justify-content:center;">
    🤖
</button>

<!-- Bulle indicateur -->
<div id="chatbotBulle" style="position:fixed;bottom:88px;right:28px;
     background:#6c3fc5;color:#fff;border-radius:20px;padding:6px 14px;
     font-size:0.78rem;font-weight:700;z-index:9998;
     box-shadow:0 4px 12px rgba(108,63,197,.4);white-space:nowrap;">
    💬 Posez vos questions sur cette recette !
</div>

<!-- Fenêtre chatbot -->
<div id="chatbotWindow" style="display:none;position:fixed;bottom:100px;right:28px;
     width:360px;height:520px;background:#fff;border-radius:16px;
     box-shadow:0 8px 40px rgba(108,63,197,.25);border:2px solid #e0d4ff;
     z-index:9997;display:none;flex-direction:column;overflow:hidden;">

    <!-- En-tête -->
    <div style="background:linear-gradient(135deg,#6c3fc5,#8b5cf6);padding:14px 18px;
         display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;background:rgba(255,255,255,.2);border-radius:50%;
                 display:flex;align-items:center;justify-content:center;font-size:1.2rem;">🤖</div>
            <div>
                <div style="color:#fff;font-weight:800;font-size:0.95rem;">Assistant Nutrition</div>
                <div style="color:rgba(255,255,255,.8);font-size:0.72rem;"><?= htmlspecialchars($recette['nom']) ?></div>
            </div>
        </div>
        <button onclick="toggleChatbot()"
            style="background:rgba(255,255,255,.2);border:none;color:#fff;
                   border-radius:50%;width:30px;height:30px;cursor:pointer;font-size:1rem;">✕</button>
    </div>

    <!-- Messages -->
    <div id="chatMessages" style="flex:1;overflow-y:auto;padding:14px;
         display:flex;flex-direction:column;gap:10px;background:#faf8ff;">
        <!-- Message de bienvenue -->
        <div style="display:flex;gap:8px;align-items:flex-start;">
            <div style="width:28px;height:28px;background:#e0d4ff;border-radius:50%;
                 display:flex;align-items:center;justify-content:center;font-size:0.85rem;flex-shrink:0;">🤖</div>
            <div style="background:#fff;border:1px solid #e0d4ff;border-radius:4px 12px 12px 12px;
                 padding:10px 14px;font-size:0.85rem;color:#333;line-height:1.5;max-width:85%;">
                Bonjour ! Je suis votre assistant nutrition pour <strong><?= htmlspecialchars($recette['nom']) ?></strong>.<br><br>
                Posez-moi vos questions, par exemple :<br>
                • <em>Est-ce adapté aux diabétiques ?</em><br>
                • <em>Combien de calories pour 2 portions ?</em><br>
                • <em>Est-ce bon pour perdre du poids ?</em>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div style="padding:8px 12px;border-top:1px solid #f0e8ff;background:#faf8ff;
         display:flex;flex-wrap:wrap;gap:6px;">
        <button onclick="envoyerQuestion('Est-ce adapté aux diabétiques ?')"
            style="background:#fff;border:1.5px solid #d4c5f9;color:#6c3fc5;border-radius:16px;
                   padding:4px 10px;cursor:pointer;font-size:0.75rem;font-weight:600;">🩺 Diabétiques</button>
        <button onclick="envoyerQuestion('Est-ce bon pour perdre du poids ?')"
            style="background:#fff;border:1.5px solid #d4c5f9;color:#6c3fc5;border-radius:16px;
                   padding:4px 10px;cursor:pointer;font-size:0.75rem;font-weight:600;">⚖️ Perte de poids</button>
        <button onclick="envoyerQuestion('Quels sont les bienfaits nutritionnels de cette recette ?')"
            style="background:#fff;border:1.5px solid #d4c5f9;color:#6c3fc5;border-radius:16px;
                   padding:4px 10px;cursor:pointer;font-size:0.75rem;font-weight:600;">💪 Bienfaits</button>
        <button onclick="envoyerQuestion('Comment améliorer cette recette pour la rendre plus saine ?')"
            style="background:#fff;border:1.5px solid #d4c5f9;color:#6c3fc5;border-radius:16px;
                   padding:4px 10px;cursor:pointer;font-size:0.75rem;font-weight:600;">🔧 Améliorer</button>
    </div>

    <!-- Zone de saisie -->
    <div style="padding:10px 12px;border-top:2px solid #f0e8ff;background:#fff;
         display:flex;gap:8px;align-items:center;">
        <input type="text" id="chatInput" placeholder="Votre question..."
            style="flex:1;padding:9px 14px;border:2px solid #e0d4ff;border-radius:20px;
                   font-size:0.85rem;outline:none;font-family:inherit;"
            onkeydown="if(event.key==='Enter') envoyerQuestion()">
        <button onclick="envoyerQuestion()" id="chatSendBtn"
            style="width:38px;height:38px;background:#6c3fc5;color:#fff;border:none;
                   border-radius:50%;cursor:pointer;font-size:1rem;flex-shrink:0;
                   display:flex;align-items:center;justify-content:center;">➤</button>
    </div>
</div>

<?php include 'View/front/partials/footer.php'; ?>

<style>
@keyframes bounce-ia {
    0%,60%,100% { transform:translateY(0); }
    30%          { transform:translateY(-8px); }
}
@keyframes clignote-vert   { 0%,100%{box-shadow:0 0 0 0 rgba(46,125,50,0);}   50%{box-shadow:0 0 10px 4px rgba(46,125,50,.4);} }
@keyframes clignote-orange { 0%,100%{box-shadow:0 0 0 0 rgba(245,124,0,0);}   50%{box-shadow:0 0 10px 4px rgba(245,124,0,.4);} }
@keyframes clignote-rouge  { 0%,100%{box-shadow:0 0 0 0 rgba(198,40,40,0);}   50%{box-shadow:0 0 10px 4px rgba(198,40,40,.4);} }
#chatMessages::-webkit-scrollbar { width:4px; }
#chatMessages::-webkit-scrollbar-thumb { background:#d4c5f9; border-radius:4px; }
</style>

<script>
// Données de la recette
const recetteData = {
    id:          <?= (int)$recette['id'] ?>,
    nom:         <?= json_encode($recette['nom']) ?>,
    calories:    <?= (int)$recette['calories'] ?>,
    ingredients: <?= json_encode(array_map(fn($i) => $i['nom'], $ingredients ?? [])) ?>,
};
const userProfil  = JSON.parse(sessionStorage.getItem('userProfil') || '{}');
const profilActif = sessionStorage.getItem('profilActif') === 'true';

// ── Toggle chatbot ────────────────────────────────────────────────────────────
function toggleChatbot() {
    const win   = document.getElementById('chatbotWindow');
    const bulle = document.getElementById('chatbotBulle');
    const isOpen = win.style.display === 'flex';
    win.style.display   = isOpen ? 'none' : 'flex';
    bulle.style.display = isOpen ? 'block' : 'none';
    if (!isOpen) document.getElementById('chatInput').focus();
}

// Masquer la bulle après 4 secondes
setTimeout(() => {
    const b = document.getElementById('chatbotBulle');
    if (b) b.style.opacity = '0';
    setTimeout(() => { if (b) b.style.display = 'none'; }, 500);
}, 4000);

// ── Ajouter une bulle dans le chat ────────────────────────────────────────────
function ajouterMessage(role, html) {
    const msgs = document.getElementById('chatMessages');
    const div  = document.createElement('div');
    div.style.cssText = 'display:flex;gap:8px;align-items:flex-start;' + (role === 'user' ? 'flex-direction:row-reverse;' : '');

    const avatar = role === 'user'
        ? '<div style="width:28px;height:28px;background:#e8f5e9;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.85rem;flex-shrink:0;">👤</div>'
        : '<div style="width:28px;height:28px;background:#e0d4ff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.85rem;flex-shrink:0;">🤖</div>';

    const bubble = role === 'user'
        ? `<div style="background:#6c3fc5;color:#fff;border-radius:12px 4px 12px 12px;padding:10px 14px;font-size:0.85rem;line-height:1.5;max-width:85%;">${html}</div>`
        : `<div style="background:#fff;border:1px solid #e0d4ff;border-radius:4px 12px 12px 12px;padding:10px 14px;font-size:0.85rem;color:#333;line-height:1.5;max-width:85%;">${html}</div>`;

    div.innerHTML = avatar + bubble;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

// ── Indicateur de frappe ──────────────────────────────────────────────────────
function afficherTyping() {
    const msgs = document.getElementById('chatMessages');
    const div  = document.createElement('div');
    div.id = 'typingMsg';
    div.style.cssText = 'display:flex;gap:8px;align-items:flex-start;';
    div.innerHTML = `
        <div style="width:28px;height:28px;background:#e0d4ff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.85rem;flex-shrink:0;">🤖</div>
        <div style="background:#fff;border:1px solid #e0d4ff;border-radius:4px 12px 12px 12px;padding:10px 14px;">
            <div style="display:flex;gap:4px;">
                <span style="width:7px;height:7px;background:#c4b5fd;border-radius:50%;animation:bounce-ia .8s infinite;"></span>
                <span style="width:7px;height:7px;background:#c4b5fd;border-radius:50%;animation:bounce-ia .8s .2s infinite;"></span>
                <span style="width:7px;height:7px;background:#c4b5fd;border-radius:50%;animation:bounce-ia .8s .4s infinite;"></span>
            </div>
        </div>`;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}
function supprimerTyping() { document.getElementById('typingMsg')?.remove(); }

// ── Envoyer une question ──────────────────────────────────────────────────────
async function envoyerQuestion(questionForce) {
    const input = document.getElementById('chatInput');
    const question = questionForce || input.value.trim();
    if (!question) return;

    ajouterMessage('user', escHtml(question));
    input.value = '';
    document.getElementById('chatSendBtn').disabled = true;
    afficherTyping();

    try {
        const resp = await fetch('/2A35/Admin/Ai/chatRecette', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                question: question,
                recette:  recetteData,
                profil:   userProfil
            })
        });
        const data = await resp.json();
        supprimerTyping();

        if (data.error) {
            ajouterMessage('bot', '❌ ' + escHtml(data.error));
        } else {
            ajouterMessage('bot', data.reponse.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>'));
        }
    } catch (e) {
        supprimerTyping();
        ajouterMessage('bot', '❌ Erreur de connexion. Réessayez.');
    }

    document.getElementById('chatSendBtn').disabled = false;
    input.focus();
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>
