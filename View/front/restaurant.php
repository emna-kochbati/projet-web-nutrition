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
                <label class="form-label" style="visibility:hidden;">.</label>
                <button onclick="fetchResults()" class="btn btn-primary w-100 py-2">
                    <i class="fa fa-filter me-1"></i> Filtrer
                </button>
            </div>
            <div class="col-md-2">
                <label class="form-label" style="visibility:hidden;">.</label>
                <button onclick="resetFilters()" class="btn btn-outline-secondary w-100 py-2">✕ Effacer</button>
            </div>
        </div>
    </div>
</div>
<!-- Filtres End -->

<!-- Résultats -->
<div class="container-fluid py-5">
    <div class="container">
        <p id="result-count" class="text-muted mb-4">
            <strong><?= $total ?? count($restaurants) ?></strong>
            restaurant<?= ($total ?? count($restaurants)) > 1 ? 's' : '' ?>
            trouvé<?= ($total ?? count($restaurants)) > 1 ? 's' : '' ?>
        </p>

        <div id="restaurants-grid" class="row g-4">
            <?php foreach ($restaurants as $r): ?>
            <?= renderRestaurantCard($r) ?>
            <?php endforeach; ?>
        </div>

        <!-- ── Pagination circulaire ──────────────────────────────────── -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div style="display:flex;justify-content:center;align-items:center;gap:8px;margin-top:40px;">
            <button onclick="goToPage(<?= $page - 1 ?>)" <?= $page <= 1 ? 'disabled' : '' ?>
                    style="width:44px;height:44px;border-radius:50%;border:2px solid <?= $page<=1?'#ddd':'#a5d6a7' ?>;
                           background:<?= $page<=1?'#f5f5f5':'#fff' ?>;color:<?= $page<=1?'#bbb':'#2e7d32' ?>;
                           font-size:1.1rem;cursor:<?= $page<=1?'default':'pointer' ?>;font-weight:700;
                           display:flex;align-items:center;justify-content:center;">«</button>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <button onclick="goToPage(<?= $i ?>)"
                    style="width:44px;height:44px;border-radius:50%;
                           border:2px solid <?= $i===$page?'#2e7d32':'#a5d6a7' ?>;
                           background:<?= $i===$page?'#2e7d32':'#fff' ?>;
                           color:<?= $i===$page?'#fff':'#2e7d32' ?>;
                           font-size:.95rem;font-weight:700;cursor:pointer;
                           display:flex;align-items:center;justify-content:center;"><?= $i ?></button>
            <?php endfor; ?>
            <button onclick="goToPage(<?= $page + 1 ?>)" <?= $page >= $totalPages ? 'disabled' : '' ?>
                    style="width:44px;height:44px;border-radius:50%;border:2px solid <?= $page>=$totalPages?'#ddd':'#a5d6a7' ?>;
                           background:<?= $page>=$totalPages?'#f5f5f5':'#fff' ?>;color:<?= $page>=$totalPages?'#bbb':'#2e7d32' ?>;
                           font-size:1.1rem;cursor:<?= $page>=$totalPages?'default':'pointer' ?>;font-weight:700;
                           display:flex;align-items:center;justify-content:center;">»</button>
        </div>
        <?php endif; ?>
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

    const spinner = document.createElement('span');
    spinner.className = 'spinner-border spinner-border-sm text-primary';
    spinner.id = 'loading-spinner';
    document.querySelector('.container-fluid.py-5 .container').prepend(spinner);

    fetch('/2A35/Restaurant/search?q=' + encodeURIComponent(q) + '&type=' + encodeURIComponent(type), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        const s = document.getElementById('loading-spinner');
        if (s) s.remove();

        const n = data.length;
        document.getElementById('result-count').innerHTML =
            '<strong>' + n + '</strong> restaurant' + (n > 1 ? 's' : '') + ' trouvé' + (n > 1 ? 's' : '');

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
        const s = document.getElementById('loading-spinner');
        if (s) s.remove();
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

function goToPage(p) {
    const total = <?= isset($totalPages) ? $totalPages : 1 ?>;
    if (p < 1 || p > total) return;
    const q    = document.getElementById('search-input').value.trim();
    const type = document.getElementById('type-input').value;
    let url = '/2A35/Restaurant?page=' + p;
    if (q)    url += '&search=' + encodeURIComponent(q);
    if (type) url += '&type='   + encodeURIComponent(type);
    window.location.href = url;
}
</script>

<?php include 'View/front/partials/footer.php'; ?>
