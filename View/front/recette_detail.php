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
                <!-- Bouton Analyse IA -->
                <button type="button" id="btnAnalyseIA" onclick="analyserRecette()"
                    style="background:linear-gradient(135deg,#6c3fc5,#8b5cf6);color:#fff;border:none;
                           border-radius:8px;padding:10px 22px;cursor:pointer;font-weight:700;
                           font-size:0.95rem;display:flex;align-items:center;gap:8px;
                           box-shadow:0 4px 14px rgba(108,63,197,.3);transition:all .2s;">
                    🤖 Analyser avec l'IA
                </button>
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

<?php include 'View/front/partials/footer.php'; ?>

<style>
@keyframes bounce-ia {
    0%,60%,100% { transform:translateY(0); }
    30%          { transform:translateY(-8px); }
}
</style>

<script>
// Données de la recette passées au JS
const recetteData = {
    id:          <?= (int)$recette['id'] ?>,
    nom:         <?= json_encode($recette['nom']) ?>,
    calories:    <?= (int)$recette['calories'] ?>,
    ingredients: <?= json_encode(array_map(fn($i) => $i['nom'], $ingredients ?? [])) ?>,
};

// Récupérer le profil sauvegardé depuis la page liste
const userProfil    = JSON.parse(sessionStorage.getItem('userProfil')    || '{}');
const profilActif   = sessionStorage.getItem('profilActif') === 'true';

async function analyserRecette() {
    const btn = document.getElementById('btnAnalyseIA');
    const panneau = document.getElementById('panneauIA');

    panneau.style.display = 'block';
    document.getElementById('iaLoading').style.display  = 'block';
    document.getElementById('iaResultats').style.display = 'none';
    document.getElementById('iaErreur').style.display   = 'none';

    btn.disabled = true;
    btn.innerHTML = '⏳ Analyse en cours...';
    panneau.scrollIntoView({ behavior: 'smooth', block: 'start' });

    try {
        const resp = await fetch('/2A35/Admin/Ai/analyser', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            // Envoyer la recette + le profil utilisateur si disponible
            body: JSON.stringify({ recette: recetteData, profil: userProfil })
        });

        const data = await resp.json();

        document.getElementById('iaLoading').style.display = 'none';

        if (data.error) {
            document.getElementById('iaErreur').style.display = 'block';
            document.getElementById('iaErreur').textContent   = '❌ ' + data.error;
        } else {
            document.getElementById('iaResultats').style.display = 'block';

            // ── Source + badge profil utilisateur ────────────────────────────
            const p = userProfil;
            const labelsP = {
                objectif: {'perte-poids':'Perte de poids','prise-masse':'Prise de masse','maintien':'Maintien du poids'},
                regime:   {'diabetique':'Diabétique','vegetarien':'Végétarien','sportif':'Sportif','normal':'Normal'},
                activite: {'sedentaire':'Sédentaire','modere':'Modéré','sportif':'Sportif intensif'},
            };
            const srcLabel = data.source === 'gemini'
                ? '🤖 <strong style="color:#6c3fc5;">Analyse par Google Gemini IA</strong>'
                : '⚙️ Analyse locale';
            let profilBadge = '';
            if ((p.objectif || p.regime || p.activite) && profilActif) {
                profilBadge = `<div style="margin-bottom:8px;padding:6px 12px;background:#f0fdf4;
                    border-radius:8px;border:1px solid #a5d6a7;font-size:0.78rem;color:#2e7d32;font-weight:600;">
                    🎯 Analyse selon votre profil :
                    ${p.objectif ? ' ⚖️ '+(labelsP.objectif[p.objectif]||p.objectif) : ''}
                    ${p.regime && p.regime!=='normal' ? ' · 🩺 '+(labelsP.regime[p.regime]||p.regime) : ''}
                    ${p.activite ? ' · 🏃 '+(labelsP.activite[p.activite]||p.activite) : ''}
                </div>`;
            }
            document.getElementById('sourceLabel').innerHTML = profilBadge + srcLabel;

            // ── Badges : profil utilisateur OU profils génériques ────────────
            const statutStyles = {
                adapte: { bg:'#e8f5e9', color:'#2e7d32', border:'#a5d6a7', txt:'✅ Adapté',       anim:'clignote-vert' },
                modere: { bg:'#fff8e1', color:'#f57c00', border:'#ffe082', txt:'⚠️ Modéré',       anim:'clignote-orange' },
                non:    { bg:'#ffebee', color:'#c62828', border:'#ef9a9a', txt:'❌ Déconseillé',  anim:'clignote-rouge' },
            };

            // Injecter les animations CSS
            if (!document.getElementById('badge-anim-style')) {
                const s = document.createElement('style');
                s.id = 'badge-anim-style';
                s.textContent = `
                    @keyframes clignote-vert   { 0%,100%{box-shadow:0 0 0 0 rgba(46,125,50,0);}   50%{box-shadow:0 0 10px 4px rgba(46,125,50,.4);} }
                    @keyframes clignote-orange { 0%,100%{box-shadow:0 0 0 0 rgba(245,124,0,0);}   50%{box-shadow:0 0 10px 4px rgba(245,124,0,.4);} }
                    @keyframes clignote-rouge  { 0%,100%{box-shadow:0 0 0 0 rgba(198,40,40,0);}   50%{box-shadow:0 0 10px 4px rgba(198,40,40,.4);} }
                `;
                document.head.appendChild(s);
            }

            const container = document.getElementById('profilsBadges');
            container.innerHTML = '';

            function creerBadge(icon, label, statut) {
                const st = statutStyles[statut] || statutStyles.modere;
                return `<div style="background:${st.bg};border:2px solid ${st.border};
                     border-radius:10px;padding:12px 18px;display:flex;align-items:center;gap:10px;
                     min-width:150px;animation:${st.anim} 1.5s ease-in-out infinite;">
                    <span style="font-size:1.4rem;">${icon}</span>
                    <div>
                        <div style="font-weight:700;color:#333;font-size:0.85rem;">${label}</div>
                        <div style="font-weight:800;color:${st.color};font-size:0.88rem;">${st.txt}</div>
                    </div>
                </div>`;
            }

            if (p.regime && p.regime !== 'normal' && profilActif) {
                // ── CAS 1 : Profil avec régime → afficher SEULEMENT le régime ─
                const regimeIcons = {
                    'diabetique':'🩺','vegetarien':'🥦','sportif':'💪','normal':'🍽️'
                };
                const regimeLabels = {
                    'diabetique':'Diabétique','vegetarien':'Végétarien',
                    'sportif':'Sportif','normal':'Normal'
                };
                // Calculer le statut pour ce régime
                let statut = 'modere';
                if (p.regime === 'diabetique' && data.profils.diabetique)   statut = data.profils.diabetique.statut;
                else if (p.regime === 'vegetarien' && data.profils.vegetarien) statut = data.profils.vegetarien.statut;
                else if (p.regime === 'sportif' && data.profils.sportif)    statut = data.profils.sportif.statut;
                else if (p.regime === 'normal' && data.profils.normal)      statut = data.profils.normal.statut;

                container.innerHTML = creerBadge(
                    regimeIcons[p.regime] || '🩺',
                    regimeLabels[p.regime] || p.regime,
                    statut
                );            } else {
                // ── CAS 2 : Pas de profil → afficher TOUS les types avec clignotement
                const tousTypes = [
                    { key:'diabetique', icon:'🩺', label:'Diabétique' },
                    { key:'sportif',    icon:'💪', label:'Sportif' },
                    { key:'vegetarien', icon:'🥦', label:'Végétarien' },
                    { key:'normal',     icon:'🍽️', label:'Normal' },
                ];
                tousTypes.forEach(t => {
                    const statut = data.profils[t.key]?.statut || 'modere';
                    container.innerHTML += creerBadge(t.icon, t.label, statut);
                });
            }

            // ── Analyse structurée en points ──────────────────────────────────
            document.getElementById('analyseTexte').innerHTML =
                data.analyse
                    .replace(/✅ COMPATIBILITÉ AVEC VOTRE PROFIL/gi,
                        '<div style="font-weight:800;color:#2e7d32;font-size:0.9rem;margin:12px 0 6px;">✅ Compatibilité avec votre profil</div>')
                    .replace(/💡 CONSEILS PERSONNALISÉS/gi,
                        '<div style="font-weight:800;color:#f57c00;font-size:0.9rem;margin:12px 0 6px;">💡 Conseils personnalisés</div>')
                    .replace(/🔧 AMÉLIORATIONS SUGGÉRÉES/gi,
                        '<div style="font-weight:800;color:#6c3fc5;font-size:0.9rem;margin:12px 0 6px;">🔧 Améliorations suggérées</div>')
                    .replace(/\n• /g, '</p><p style="margin:4px 0;padding-left:12px;border-left:3px solid #e0d4ff;">• ')
                    .replace(/^• /,   '<p style="margin:4px 0;padding-left:12px;border-left:3px solid #e0d4ff;">• ')
                    .replace(/\n/g, '<br>') + '</p>';
        }
    } catch (e) {
        document.getElementById('iaLoading').style.display = 'none';
        document.getElementById('iaErreur').style.display  = 'block';
        document.getElementById('iaErreur').textContent    = '❌ Erreur de connexion. Réessayez.';
    }

    btn.disabled = false;
    btn.innerHTML = '🤖 Analyser avec l\'IA';
}
</script>
