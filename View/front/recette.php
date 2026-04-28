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
                <select name="categorie" class="form-select">
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
                <select name="difficulte" class="form-select">
                    <option value="">Toutes</option>
                    <?php foreach (['facile'=>'Facile','moyen'=>'Moyen','difficile'=>'Difficile'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($_GET['difficulte'] ?? '')===$v ? 'selected':'' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                <?php if (!empty($_GET['search']) || !empty($_GET['categorie']) || !empty($_GET['difficulte'])): ?>
                    <a href="/2A35/RecetteFront" class="btn btn-outline-secondary">✕</a>
                <?php endif; ?>
            </div>
        </form>
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
                            <h5 class="fw-bold mb-3"><?= htmlspecialchars($r['nom']) ?></h5>

                            <!-- Stats -->
                            <div class="d-flex justify-content-between mb-4">
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
const searchFront = document.getElementById('searchFront');
if (searchFront) {
    let timer;
    searchFront.addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const search = this.value.trim();
            const cat    = document.querySelector('select[name="categorie"]')?.value ?? '';
            const diff   = document.querySelector('select[name="difficulte"]')?.value ?? '';
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
        }, 300);
    });
}
</script>

<?php include 'View/front/partials/footer.php'; ?>
