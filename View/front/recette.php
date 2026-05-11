<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header -->
<div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <h1 class="display-4 text-white fw-bold animated slideInDown">Nos Recettes</h1>
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a class="text-white" href="/2A35/Home">Accueil</a></li>
                <li class="breadcrumb-item text-primary active">Recettes</li>
            </ol>
        </nav>
    </div>
</div>


<!-- Filtres -->
<div class="container-xxl py-3">
    <div class="container">
        <form method="GET" action="/2A35/RecetteFront" class="row g-3 align-items-end bg-light rounded p-4 shadow-sm wow fadeInUp" data-wow-delay="0.1s">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted text-uppercase">🔍 Rechercher</label>
                <input type="text" id="searchFront" name="search" class="form-control" autocomplete="off"
                       placeholder="Nom de la recette..."
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted text-uppercase">🗂 Catégorie</label>
                <select id="selectCategorie" name="categorie" class="form-select">
                    <option value="">Toutes</option>
                    <?php foreach ([
                        'petit-dejeuner'=>'Petit-déjeuner','dejeuner'=>'Déjeuner',
                        'diner'=>'Dîner','collation'=>'Collation','dessert'=>'Dessert',
                        'vegetarien'=>'Végétarien','regime'=>'Régime','sportif'=>'Sportif'
                    ] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($_GET['categorie'] ?? '')===$v ? 'selected':'' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted text-uppercase">⚡ Difficulté</label>
                <select id="selectDifficulte" name="difficulte" class="form-select">
                    <option value="">Toutes</option>
                    <?php foreach (['facile'=>'Facile','moyen'=>'Moyen','difficile'=>'Difficile'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($_GET['difficulte'] ?? '')===$v ? 'selected':'' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                <!-- Bouton microphone à côté de Filtrer -->
                <button type="button" id="btnMic" onclick="demarrerVoix()" title="Recherche vocale"
                    style="flex-shrink:0;width:44px;height:44px;border-radius:50%;
                           background:#2e7d32;color:#fff;border:none;cursor:pointer;
                           display:flex;align-items:center;justify-content:center;
                           font-size:1.1rem;box-shadow:0 2px 8px rgba(46,125,50,.35);
                           transition:all .2s;">
                    🎤
                </button>
                <?php if (!empty($_GET['search']) || !empty($_GET['categorie']) || !empty($_GET['difficulte'])): ?>
                    <a href="/2A35/RecetteFront" class="btn btn-outline-secondary">✕</a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Barre d'écoute vocale — apparaît quand le micro est actif -->
        <div id="barreEcoute" style="display:none;margin-top:14px;background:#fff;
             border-radius:12px;padding:16px 20px;box-shadow:0 4px 16px rgba(46,125,50,.15);
             border:2px solid #a5d6a7;">
            <div style="display:flex;align-items:center;gap:14px;">
                <!-- Ondes sonores animées -->
                <div style="display:flex;gap:3px;align-items:center;height:32px;">
                    <?php for($i=0;$i<7;$i++): ?>
                    <div class="onde" style="width:4px;background:#2e7d32;border-radius:4px;
                         animation:onde-anim <?= 0.4 + $i*0.1 ?>s ease-in-out infinite alternate;
                         height:<?= 8 + $i*4 ?>px;"></div>
                    <?php endfor; ?>
                </div>
                <!-- Texte en temps réel -->
                <div style="flex:1;">
                    <div style="font-size:0.75rem;color:#888;font-weight:600;text-transform:uppercase;
                                letter-spacing:.05em;margin-bottom:3px;">🎤 Je vous écoute...</div>
                    <div id="texteVoix" style="font-size:1rem;font-weight:700;color:#1b5e20;
                         min-height:24px;font-style:italic;">&nbsp;</div>
                </div>
                <!-- Bouton stop -->
                <button onclick="recognition && recognition.stop()"
                    style="background:#c62828;color:#fff;border:none;border-radius:8px;
                           padding:8px 14px;cursor:pointer;font-weight:700;font-size:0.85rem;">
                    ⏹ Stop
                </button>
            </div>
            <!-- Filtres détectés -->
            <div id="filtresDetectes" style="display:none;margin-top:10px;padding-top:10px;
                 border-top:1px solid #e8f5e9;font-size:0.82rem;color:#555;"></div>
        </div>

        <style>
        @keyframes onde-anim {
            from { transform:scaleY(0.4); opacity:.5; }
            to   { transform:scaleY(1.4); opacity:1; }
        }
        @keyframes pulse-mic {
            0%,100% { box-shadow:0 0 0 0 rgba(198,40,40,.5); }
            50%      { box-shadow:0 0 0 10px rgba(198,40,40,0); }
        }
        </style>
    </div>
</div>

<!-- Liste recettes -->
<div class="container-xxl py-5">
    <div class="container">

        <?php if (empty($recettes)): ?>
            <div class="text-center py-5 wow fadeIn">
                <i class="fa fa-utensils fa-4x text-primary mb-3 d-block"></i>
                <h4 class="text-muted">Aucune recette trouvée.</h4>
                <a href="/2A35/RecetteFront" class="btn btn-primary mt-3">Voir toutes les recettes</a>
            </div>
        <?php else: ?>

            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width:500px;">
                <div class="btn btn-sm border rounded-pill text-primary px-3 mb-3">Nos Recettes</div>
                <h1><?= count($recettes) ?> recette<?= count($recettes) > 1 ? 's' : '' ?> disponible<?= count($recettes) > 1 ? 's' : '' ?></h1>
            </div>

            <div class="row g-4" id="recettesGrid">
            <?php foreach ($recettes as $i => $r): ?>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="<?= ($i % 3) * 0.2 ?>s">
                    <div class="rounded overflow-hidden shadow-sm h-100 bg-white">

                        <!-- Image -->
                        <div style="position:relative; height:230px; overflow:hidden;">
                            <?php if ($r['image']): ?>
                                <img src="/2A35/assets/uploads/recettes/<?= htmlspecialchars($r['image']) ?>"
                                     class="w-100 h-100" style="object-fit:cover; transition:transform .4s;"
                                     onmouseover="this.style.transform='scale(1.05)'"
                                     onmouseout="this.style.transform='scale(1)'"
                                     alt="<?= htmlspecialchars($r['nom']) ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center"
                                     style="background:linear-gradient(135deg,#2e7d32,#66bb6a);">
                                    <i class="fa fa-utensils fa-4x text-white opacity-75"></i>
                                </div>
                            <?php endif; ?>
                            <!-- Badge catégorie -->
                            <span class="position-absolute top-0 start-0 m-3 badge rounded-pill"
                                  style="background:#2e7d32; font-size:0.78rem; padding:6px 12px;">
                                <?= htmlspecialchars($r['categorie']) ?>
                            </span>
                            <!-- Badge difficulté -->
                            <?php $dc = match($r['difficulte']){'facile'=>'success','moyen'=>'warning','difficile'=>'danger',default=>'secondary'}; ?>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-<?= $dc ?> rounded-pill"
                                  style="font-size:0.78rem; padding:6px 12px;">
                                <?= htmlspecialchars($r['difficulte']) ?>
                            </span>
                        </div>

                        <!-- Contenu -->
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="fw-bold mb-0"><?= htmlspecialchars($r['nom']) ?></h5>
                                <?php
                                $nsBg = ['A'=>'#1a7a1a','B'=>'#5aab1f','C'=>'#f5c800','D'=>'#e07800','E'=>'#d32f2f'];
                                $nsL  = $nutriScores[$r['id']]['lettre'] ?? '?';
                                $nsLbl= $nutriScores[$r['id']]['label']  ?? '';
                                $nsC  = $nsBg[$nsL] ?? '#9e9e9e';
                                if ($nsL !== '?'):
                                ?>
                                <div style="width:34px;height:34px;background:<?= $nsC ?>;color:#fff;
                                     border-radius:7px;display:flex;align-items:center;justify-content:center;
                                     font-size:1rem;font-weight:900;flex-shrink:0;
                                     box-shadow:0 2px 6px <?= $nsC ?>66;"
                                     title="Nutri-Score <?= $nsL ?> — <?= $nsLbl ?>">
                                    <?= $nsL ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Stats -->
                            <div class="d-flex justify-content-between mb-4 mt-2">
                                <span class="text-muted small">
                                    <i class="fa fa-clock text-primary me-1"></i><?= $r['duree'] ?> min
                                </span>
                                <span class="text-muted small">
                                    <i class="fa fa-fire text-danger me-1"></i><?= $r['calories'] ?> kcal
                                </span>
                                <span class="text-muted small">
                                    <i class="fa fa-leaf text-success me-1"></i><?= htmlspecialchars($r['categorie']) ?>
                                </span>
                            </div>

                            <a href="/2A35/RecetteFront/detail/<?= $r['id'] ?>"
                               class="btn btn-primary w-100">
                                Voir la recette <i class="fa fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
</div>

<!-- Pagination frontoffice -->
<?php if (isset($totalPages) && $totalPages > 1): ?>
<div class="container-xxl pb-5">
    <div class="container">
        <div style="display:flex;justify-content:center;align-items:center;gap:10px;flex-wrap:wrap;margin-top:10px;">
            <?php
            $baseUrl = '/2A35/RecetteFront?page=';
            $qs = '';
            if (!empty($_GET['search']))     $qs .= '&search='.urlencode($_GET['search']);
            if (!empty($_GET['categorie']))  $qs .= '&categorie='.urlencode($_GET['categorie']);
            if (!empty($_GET['difficulte'])) $qs .= '&difficulte='.urlencode($_GET['difficulte']);
            ?>
            <style>
            .fp-btn {
                width:46px; height:46px; border-radius:50%;
                display:flex; align-items:center; justify-content:center;
                font-weight:700; font-size:1rem; text-decoration:none;
                border:2px solid #a5d6a7; color:#2e7d32; background:#fff;
                transition:all .2s;
            }
            .fp-btn:hover { background:#e8f5e9; border-color:#2e7d32; transform:scale(1.08); }
            .fp-btn.active { background:#2e7d32; border-color:#2e7d32; color:#fff; box-shadow:0 4px 14px rgba(46,125,50,.3); }
            .fp-btn.disabled { border-color:#e0e0e0; color:#bbb; pointer-events:none; }
            </style>

            <a href="<?= $baseUrl.($page-1).$qs ?>" class="fp-btn <?= $page<=1?'disabled':'' ?>">«</a>
            <?php for ($i=1; $i<=$totalPages; $i++): ?>
                <a href="<?= $baseUrl.$i.$qs ?>" class="fp-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <a href="<?= $baseUrl.($page+1).$qs ?>" class="fp-btn <?= $page>=$totalPages?'disabled':'' ?>">»</a>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// ════════════════════════════════════════════════════════
// RECHERCHE AJAX dynamique
// ════════════════════════════════════════════════════════
const searchFront = document.getElementById('searchFront');
if (searchFront) {
    let timer;
    searchFront.addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => lancerRechercheAjax(), 300);
    });
}

function lancerRechercheAjax() {
    const search = document.getElementById('searchFront')?.value.trim() ?? '';
    const cat    = document.getElementById('selectCategorie')?.value ?? '';
    const diff   = document.getElementById('selectDifficulte')?.value ?? '';
    if (!search && !cat && !diff) { location.reload(); return; }
    fetch(`/2A35/RecetteFront/ajax?search=${encodeURIComponent(search)}&categorie=${encodeURIComponent(cat)}&difficulte=${encodeURIComponent(diff)}`)
        .then(r => r.json())
        .then(recettes => {
            const grid = document.getElementById('recettesGrid');
            if (!grid) return;
            if (recettes.length === 0) {
                grid.innerHTML = '<div class="col-12 text-center py-5"><i class="fa fa-utensils fa-4x text-primary mb-3 d-block"></i><h4 class="text-muted">Aucune recette trouvée.</h4></div>';
                return;
            }
            grid.innerHTML = recettes.map(r => `
                <div class="col-lg-4 col-md-6">
                    <div class="rounded overflow-hidden shadow-sm h-100 bg-white">
                        <div style="position:relative;height:230px;overflow:hidden;">
                            ${r.image
                                ? `<img src="/2A35/assets/uploads/recettes/${r.image}" class="w-100 h-100" style="object-fit:cover;" alt="">`
                                : `<div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#2e7d32,#66bb6a);"><i class="fa fa-utensils fa-4x text-white opacity-75"></i></div>`
                            }
                            <span class="position-absolute top-0 start-0 m-3 badge rounded-pill" style="background:#2e7d32;font-size:.78rem;">${r.categorie}</span>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-${r.difficulte==='facile'?'success':r.difficulte==='moyen'?'warning':'danger'} rounded-pill" style="font-size:.78rem;">${r.difficulte}</span>
                        </div>
                        <div class="p-4">
                            <h5 class="fw-bold mb-3">${r.nom}</h5>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted small"><i class="fa fa-clock text-primary me-1"></i>${r.duree} min</span>
                                <span class="text-muted small"><i class="fa fa-fire text-danger me-1"></i>${r.calories} kcal</span>
                            </div>
                            <a href="/2A35/RecetteFront/detail/${r.id}" class="btn btn-primary w-100">Voir la recette <i class="fa fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>`).join('');
        });
}

// ════════════════════════════════════════════════════════
// SPEECH TO TEXT INTELLIGENT — Analyse de phrases complètes
// Détecte : nom recette, catégorie, difficulté, profil santé
// Aucune API externe — Web Speech API native Chrome/Edge
// ════════════════════════════════════════════════════════
let recognition = null;
let enEcoute    = false;

function demarrerVoix() {
    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
        alert('❌ Recherche vocale non supportée. Utilisez Chrome ou Edge.');
        return;
    }
    if (enEcoute) { recognition.stop(); return; }

    const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SR();
    recognition.lang           = 'fr-FR';
    recognition.continuous     = false;
    recognition.interimResults = true;

    recognition.onstart = function() {
        enEcoute = true;
        const btn = document.getElementById('btnMic');
        btn.style.background = '#c62828';
        btn.style.animation  = 'pulse-mic 1s infinite';
        btn.innerHTML        = '⏹';
        // Afficher la barre d'écoute
        document.getElementById('barreEcoute').style.display = 'block';
        document.getElementById('texteVoix').innerHTML = '<em style="color:#aaa;">En attente de votre voix...</em>';
        document.getElementById('filtresDetectes').style.display = 'none';
    };

    // Afficher le texte en temps réel pendant que l'utilisateur parle
    recognition.onresult = function(event) {
        let texte = '';
        for (let i = event.resultIndex; i < event.results.length; i++) {
            texte += event.results[i][0].transcript;
        }
        // Afficher dans la barre
        document.getElementById('texteVoix').textContent = '"' + texte + '"';

        // Quand la phrase est terminée → analyser et filtrer
        if (event.results[event.results.length - 1].isFinal) {
            analyserPhrase(texte);
        }
    };

    recognition.onend = function() {
        enEcoute = false;
        const btn = document.getElementById('btnMic');
        btn.style.background = '#2e7d32';
        btn.style.animation  = '';
        btn.innerHTML        = '🎤';
        // Masquer la barre après 5 secondes
        setTimeout(() => {
            document.getElementById('barreEcoute').style.display = 'none';
        }, 5000);
    };

    recognition.onerror = function(event) {
        enEcoute = false;
        const btn = document.getElementById('btnMic');
        btn.style.background = '#2e7d32';
        btn.style.animation  = '';
        btn.innerHTML        = '🎤';
        document.getElementById('texteVoix').textContent = '❌ ' + (event.error === 'not-allowed'
            ? 'Microphone refusé. Autorisez l\'accès.'
            : 'Erreur : ' + event.error);
    };

    recognition.start();
}

// ── Analyse intelligente de la phrase vocale ──────────────────────────────────
function analyserPhrase(texte) {
    const t = texte.toLowerCase().trim();
    let cat   = '';
    let diff  = '';
    let infos = [];

    // ── Détection mode ingrédients (priorité absolue) ─────────────────────────
    const motsDeclencheurs = ["j'ai","j ai","avec","frigo","réfrigérateur","ingrédients","je dispose","disponible","chez moi"];
    const modeIngredients  = motsDeclencheurs.some(m => t.includes(m));

    if (modeIngredients) {
        const ingredientsDetectes = extraireIngredients(t);
        if (ingredientsDetectes.length > 0) {
            infos.push('🥦 Ingrédients : <strong>' + ingredientsDetectes.join(', ') + '</strong>');
            const filtresDiv = document.getElementById('filtresDetectes');
            filtresDiv.style.display = 'block';
            filtresDiv.innerHTML = '✅ Mode ingrédients : ' + infos.join(' ');
            document.getElementById('searchFront').value = '';
            rechercherParIngredients(ingredientsDetectes);
            return;
        }
    }

    // ── Détection catégorie ───────────────────────────────────────────────────
    const cats = {
        'petit-dejeuner': ['petit déjeuner','petit-déjeuner','matin','breakfast'],
        'dejeuner':       ['déjeuner','dejeuner','midi','lunch'],
        'diner':          ['dîner','diner','soir','dinner'],
        'collation':      ['collation','goûter','snack','encas'],
        'dessert':        ['dessert','sucré','gâteau','cake'],
        'vegetarien':     ['végétarien','vegetarien','végétal','sans viande','vegan'],
        'regime':         ['régime','regime','minceur','léger','light','diète'],
        'sportif':        ['sportif','sport','musculation','fitness','protéiné'],
    };
    for (const [key, mots] of Object.entries(cats)) {
        if (mots.some(m => t.includes(m))) {
            cat = key;
            infos.push('🗂 Catégorie : <strong>' + key + '</strong>');
            break;
        }
    }

    // ── Détection difficulté ──────────────────────────────────────────────────
    if (t.includes('facile') || t.includes('simple') || t.includes('rapide') || t.includes('débutant')) {
        diff = 'facile';
        infos.push('⚡ Difficulté : <strong>Facile</strong>');
    } else if (t.includes('difficile') || t.includes('complexe') || t.includes('élaboré') || t.includes('chef')) {
        diff = 'difficile';
        infos.push('⚡ Difficulté : <strong>Difficile</strong>');
    } else if (t.includes('moyen') || t.includes('intermédiaire') || t.includes('modéré')) {
        diff = 'moyen';
        infos.push('⚡ Difficulté : <strong>Moyen</strong>');
    }

    // ── Détection profil santé ────────────────────────────────────────────────
    if (!cat) {
        if (t.includes('diabétique') || t.includes('diabete')) {
            cat = 'regime'; infos.push('🩺 Profil : <strong>Diabétique</strong>');
        } else if (t.includes('muscl') || t.includes('protéine')) {
            cat = 'sportif'; infos.push('💪 Profil : <strong>Sportif</strong>');
        } else if (t.includes('minceur') || t.includes('maigrir')) {
            cat = 'regime'; infos.push('⚖️ Profil : <strong>Régime</strong>');
        }
    }

    // ── Extraction nom recette (seulement si aucun filtre) ────────────────────
    let search = '';
    if (cat === '' && diff === '') {
        const motsFiltres = ['recette','recettes','de','du','des','pour','avec','les','une','un',
            'montre','affiche','cherche','trouve','je veux','donne moi','veux',
            'facile','difficile','moyen','simple','rapide','végétarien','vegetarien',
            'sportif','régime','regime','dessert','déjeuner','dejeuner','dîner','diner',
            'collation','petit','breakfast','matin','midi','soir','diabétique','minceur'];
        let nomExtrait = t;
        motsFiltres.forEach(m => { nomExtrait = nomExtrait.replace(new RegExp('\\b' + m + '\\b', 'gi'), ''); });
        nomExtrait = nomExtrait.replace(/\s+/g, ' ').trim();
        if (nomExtrait.length >= 3) {
            search = nomExtrait;
            infos.push('🔍 Recherche : <strong>"' + nomExtrait + '"</strong>');
        }
    }

    // ── Appliquer les filtres ─────────────────────────────────────────────────
    document.getElementById('searchFront').value = search;
    if (cat)  document.getElementById('selectCategorie').value  = cat;
    if (diff) document.getElementById('selectDifficulte').value = diff;

    const filtresDiv = document.getElementById('filtresDetectes');
    if (infos.length > 0) {
        filtresDiv.style.display = 'block';
        filtresDiv.innerHTML = '✅ Filtres détectés : ' + infos.join(' &nbsp;|&nbsp; ');
    } else {
        document.getElementById('searchFront').value = texte;
        filtresDiv.style.display = 'block';
        filtresDiv.innerHTML = '🔍 Recherche par nom : <strong>"' + texte + '"</strong>';
    }
    lancerRechercheAjax();
}

// ── Extraire les ingrédients depuis la phrase ─────────────────────────────────
function extraireIngredients(texte) {
    const stopWords = ["j'ai","j ai","avec","du","de","des","le","la","les","un","une","et","aussi",
        "frigo","réfrigérateur","ingrédients","je dispose","disponible","chez","moi",
        "maison","cuisine","préparer","recette","faire","voici","j'ai"];
    let txt = texte;
    stopWords.forEach(m => { txt = txt.replace(new RegExp('\\b' + m + '\\b', 'gi'), ' '); });
    return [...new Set(txt.split(/[,\s]+/).map(m => m.trim()).filter(m => m.length >= 3))];
}

// ── Recherche AJAX par ingrédients ────────────────────────────────────────────
async function rechercherParIngredients(ingredients) {
    const grid = document.getElementById('recettesGrid');
    if (!grid) return;
    grid.innerHTML = `<div class="col-12 text-center py-5">
        <div class="spinner-border text-success" role="status"></div>
        <p class="mt-3 text-muted">Recherche des recettes avec vos ingrédients...</p>
    </div>`;
    try {
        const resp = await fetch('/2A35/RecetteFront/ajaxIngredients', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ingredients })
        });
        const recettes = await resp.json();
        if (recettes.length === 0) {
            grid.innerHTML = `<div class="col-12 text-center py-5">
                <i class="fa fa-utensils fa-4x text-primary mb-3 d-block"></i>
                <h4 class="text-muted">Aucune recette trouvée avec ces ingrédients.</h4>
            </div>`; return;
        }
        grid.innerHTML = recettes.map(r => {
            const nb  = r.nb_ingredients_trouves || 0;
            const tot = r.nb_ingredients_total   || 0;
            const pct = tot > 0 ? Math.round(nb / tot * 100) : 0;
            const col = pct >= 75 ? '#2e7d32' : pct >= 50 ? '#f57c00' : '#9e9e9e';
            return `<div class="col-lg-4 col-md-6">
                <div class="rounded overflow-hidden shadow-sm h-100 bg-white" style="border:2px solid ${col}30;">
                    <div style="position:relative;height:200px;overflow:hidden;">
                        ${r.image ? `<img src="/2A35/assets/uploads/recettes/${r.image}" class="w-100 h-100" style="object-fit:cover;" alt="">` : `<div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#2e7d32,#66bb6a);"><i class="fa fa-utensils fa-4x text-white opacity-75"></i></div>`}
                        <div style="position:absolute;top:10px;right:10px;background:${col};color:#fff;border-radius:20px;padding:4px 12px;font-size:0.78rem;font-weight:800;">
                            🥦 ${nb}/${tot} ingrédients
                        </div>
                    </div>
                    <div class="p-3">
                        <h5 class="fw-bold mb-2">${r.nom}</h5>
                        <div style="font-size:0.78rem;color:#666;margin-bottom:8px;background:#f9fbe7;padding:6px 10px;border-radius:6px;border-left:3px solid ${col};">
                            ✅ ${r.ingredients_trouves || ''}
                        </div>
                        <div style="background:#e0e0e0;border-radius:4px;height:5px;margin-bottom:10px;">
                            <div style="background:${col};height:5px;border-radius:4px;width:${pct}%;"></div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small"><i class="fa fa-clock text-primary me-1"></i>${r.duree} min</span>
                            <span class="text-muted small"><i class="fa fa-fire text-danger me-1"></i>${r.calories} kcal</span>
                        </div>
                        <a href="/2A35/RecetteFront/detail/${r.id}" class="btn btn-primary w-100 btn-sm">Voir la recette</a>
                    </div>
                </div>
            </div>`;
        }).join('');
    } catch(e) {
        grid.innerHTML = `<div class="col-12 text-center py-5"><h4 class="text-danger">❌ Erreur. Réessayez.</h4></div>`;
    }
}

// Animation pulsation microphone
const styleEl = document.createElement('style');
styleEl.textContent = `
@keyframes pulse-mic {
    0%,100% { box-shadow:0 0 0 0 rgba(198,40,40,.5); }
    50%      { box-shadow:0 0 0 10px rgba(198,40,40,0); }
}`;
document.head.appendChild(styleEl);

// Au chargement : s'assurer que la barre est cachée et les champs propres
window.addEventListener('load', function() {
    document.getElementById('barreEcoute').style.display  = 'none';
    document.getElementById('texteVoix').innerHTML        = '&nbsp;';
    document.getElementById('filtresDetectes').style.display = 'none';
    if (!new URLSearchParams(window.location.search).get('search')) {
        document.getElementById('searchFront').value = '';
    }
});
</script>

<?php include 'View/front/partials/footer.php'; ?>
