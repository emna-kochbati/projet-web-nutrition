<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header Start -->
<div class="page-header wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-5 text-white mb-3 animated slideInDown">Nos Restaurants</h1>
                <nav aria-label="breadcrumb animated slideInDown">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a class="text-white" href="/2A35/Home">Accueil</a></li>
                        <li class="breadcrumb-item text-primary active" aria-current="page">Restaurants</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- Page Header End -->

<!-- Filtres AJAX -->
<div class="container-fluid py-4" style="background:#f7f8fc; border-bottom:1px solid #e9ecef;">
    <div class="container">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold">
                    <i class="fa fa-search me-1 text-primary"></i> Rechercher
                </label>
                <input type="text" id="search-input" class="form-control"
                       placeholder="Nom du restaurant..." autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="fa fa-utensils me-1 text-primary"></i> Type de cuisine
                </label>
                <select id="type-input" class="form-select">
                    <option value="">Tous les types</option>
                    <?php foreach (['tunisienne','italienne','japonaise','americaine','indienne','mexicaine','française','autre'] as $t): ?>
                        <option value="<?= $t ?>"><?= ucfirst($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button onclick="resetFilters()" class="btn btn-outline-secondary w-100 py-2">✕ Effacer</button>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <span id="search-spinner" class="text-primary" style="display:none;">
                    <span class="spinner-border spinner-border-sm me-1"></span> Recherche…
                </span>
            </div>
        </div>
    </div>
</div>
<!-- Filtres End -->

<!-- Résultats -->
<div class="container-fluid py-5">
    <div class="container">
        <p id="result-count" class="text-muted mb-4">
            <strong><?= count($restaurants) ?></strong>
            restaurant<?= count($restaurants) > 1 ? 's' : '' ?>
            trouvé<?= count($restaurants) > 1 ? 's' : '' ?>
        </p>

        <div id="restaurants-grid" class="row g-4">
            <?php foreach ($restaurants as $r): ?>
            <?= renderRestaurantCard($r) ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
function renderRestaurantCard(array $r): string {
    $img = !empty($r['image'])
        ? '<img src="/2A35/assets/uploads/restaurants/'.htmlspecialchars($r['image']).'"
               alt="'.htmlspecialchars($r['nom']).'"
               style="width:100%;height:100%;object-fit:cover;">'
        : '<div class="d-flex align-items-center justify-content-center h-100">
               <i class="fa fa-utensils fa-4x text-white opacity-50"></i>
           </div>';

    $tel = !empty($r['telephone'])
        ? '<small class="text-muted"><i class="fa fa-phone me-1 text-primary"></i>'.htmlspecialchars($r['telephone']).'</small>'
        : '';

    $desc = !empty($r['description'])
        ? '<p class="text-muted small mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">'.htmlspecialchars($r['description']).'</p>'
        : '';

    return '
    <div class="col-lg-4 col-md-6">
        <div class="product-item rounded overflow-hidden h-100" style="flex-direction:column;display:flex;">
            <div style="position:relative;overflow:hidden;height:220px;background:#2e7d32;">
                '.$img.'
                <span style="position:absolute;top:12px;left:12px;background:rgba(0,0,0,.55);color:#fff;padding:3px 10px;border-radius:4px;font-size:.78rem;">
                    '.htmlspecialchars($r['type_cuisine']).'
                </span>
            </div>
            <div class="p-4 d-flex flex-column flex-grow-1">
                <h5 class="fw-bold mb-1">'.htmlspecialchars($r['nom']).'</h5>
                <p class="text-muted small mb-2">
                    <i class="fa fa-map-marker-alt me-1 text-primary"></i>'.htmlspecialchars($r['adresse']).'
                </p>
                '.$desc.'
                <div class="d-flex gap-3 mb-3 flex-wrap">'.$tel.'</div>
                <div class="mt-auto">
                    <a href="/2A35/Restaurant/show/'.$r['id'].'" class="btn btn-primary w-100">
                        Voir le menu <i class="fa fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>';
}
?>

<script>
let searchTimer = null;

document.getElementById('search-input').addEventListener('input', doSearch);
document.getElementById('type-input').addEventListener('change', doSearch);

function doSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(fetchResults, 300);
}

function resetFilters() {
    document.getElementById('search-input').value = '';
    document.getElementById('type-input').value   = '';
    fetchResults();
}

function fetchResults() {
    const q    = document.getElementById('search-input').value.trim();
    const type = document.getElementById('type-input').value;

    document.getElementById('search-spinner').style.display = 'inline-flex';

    fetch('/2A35/Restaurant/search?q=' + encodeURIComponent(q) + '&type=' + encodeURIComponent(type), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('search-spinner').style.display = 'none';

        // Compteur
        const n = data.length;
        document.getElementById('result-count').innerHTML =
            '<strong>' + n + '</strong> restaurant' + (n > 1 ? 's' : '') + ' trouvé' + (n > 1 ? 's' : '');

        // Grille
        const grid = document.getElementById('restaurants-grid');
        if (n === 0) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fa fa-utensils fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted fs-5">Aucun restaurant trouvé.</p>
                </div>`;
            return;
        }

        grid.innerHTML = data.map(r => buildCard(r)).join('');
    })
    .catch(() => {
        document.getElementById('search-spinner').style.display = 'none';
    });
}

function buildCard(r) {
    const img = r.image
        ? `<img src="/2A35/assets/uploads/restaurants/${esc(r.image)}" style="width:100%;height:100%;object-fit:cover;" alt="">`
        : `<div class="d-flex align-items-center justify-content-center h-100"><i class="fa fa-utensils fa-4x text-white opacity-50"></i></div>`;

    const tel = r.telephone
        ? `<small class="text-muted"><i class="fa fa-phone me-1 text-primary"></i>${esc(r.telephone)}</small>`
        : '';

    const desc = r.description
        ? `<p class="text-muted small mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">${esc(r.description)}</p>`
        : '';

    return `
    <div class="col-lg-4 col-md-6">
        <div class="product-item rounded overflow-hidden h-100" style="flex-direction:column;display:flex;">
            <div style="position:relative;overflow:hidden;height:220px;background:#2e7d32;">
                ${img}
                <span style="position:absolute;top:12px;left:12px;background:rgba(0,0,0,.55);color:#fff;padding:3px 10px;border-radius:4px;font-size:.78rem;">
                    ${esc(r.type_cuisine)}
                </span>
            </div>
            <div class="p-4 d-flex flex-column flex-grow-1">
                <h5 class="fw-bold mb-1">${esc(r.nom)}</h5>
                <p class="text-muted small mb-2">
                    <i class="fa fa-map-marker-alt me-1 text-primary"></i>${esc(r.adresse)}
                </p>
                ${desc}
                <div class="d-flex gap-3 mb-3 flex-wrap">${tel}</div>
                <div class="mt-auto">
                    <a href="/2A35/Restaurant/show/${r.id}" class="btn btn-primary w-100">
                        Voir le menu <i class="fa fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>`;
}

function esc(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}
</script>

<?php include 'View/front/partials/footer.php'; ?>
