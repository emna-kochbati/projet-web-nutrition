<?php include 'View/front/partials/header.php'; ?>

<!-- Leaflet CSS pour la carte -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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

<!-- Onglets -->
<div class="container-fluid" style="background:#fff;border-bottom:2px solid #e9ecef;">
    <div class="container">
        <div style="display:flex;gap:0;">
            <button id="tab-list" onclick="switchTab('list')"
                style="padding:14px 28px;border:none;background:none;font-weight:700;font-size:.95rem;
                       color:#2e7d32;border-bottom:3px solid #2e7d32;cursor:pointer;">
                🍴 Restaurants
            </button>
            <button id="tab-map" onclick="switchTab('map')"
                style="padding:14px 28px;border:none;background:none;font-weight:600;font-size:.95rem;
                       color:#888;border-bottom:3px solid transparent;cursor:pointer;">
                🗺️ Carte
            </button>
        </div>
    </div>
</div>

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

<!-- ── Vue Liste ─────────────────────────────────────────────────────────── -->
<div id="view-list" class="container-fluid py-5">
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

<!-- ── Vue Carte ─────────────────────────────────────────────────────────── -->
<div id="view-map" style="display:none;" class="container-fluid py-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div id="inline-map" style="height:520px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.12);"></div>
            </div>
            <div class="col-lg-4">
                <div id="map-resto-list" style="height:520px;overflow-y:auto;display:flex;flex-direction:column;gap:10px;">
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.map-card { background:#fff;border-radius:10px;padding:14px;box-shadow:0 2px 8px rgba(0,0,0,.07);
            cursor:pointer;border-left:4px solid #2e7d32;transition:transform .2s; }
.map-card:hover { transform:translateX(4px); }
.map-card h6 { font-weight:700;color:#1a1a1a;margin-bottom:4px;font-size:.9rem; }
.map-card .mc-type { background:#e8f5e9;color:#2e7d32;padding:2px 8px;border-radius:10px;font-size:.72rem;font-weight:600; }
.map-card .mc-addr { font-size:.78rem;color:#888;margin-top:4px; }
</style>

<?php
function starsHtml(float $note): string {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($note >= $i) {
            $html .= '<span style="color:#ffc107;font-size:.9rem;">★</span>';
        } elseif ($note >= $i - 0.5) {
            $html .= '<span style="color:#ffc107;font-size:.9rem;">½</span>';
        } else {
            $html .= '<span style="color:rgba(255,255,255,.4);font-size:.9rem;">★</span>';
        }
    }
    return $html;
}

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
                <!-- Étoiles sur l\'image -->
                <div style="position:absolute;bottom:10px;left:12px;display:flex;align-items:center;gap:4px;">
                    '.starsHtml($r['note_moyenne'] ?? 0).'
                    <span style="color:#fff;font-size:.75rem;background:rgba(0,0,0,.45);padding:1px 6px;border-radius:10px;">
                        '.($r['note_moyenne'] > 0 ? number_format($r['note_moyenne'],1).' ('.($r['note_total']).')' : 'Pas encore noté').'
                    </span>
                </div>
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

        // Étoiles dans les cards AJAX
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

// ── Onglets ───────────────────────────────────────────────────────────────
let mapInitialized = false;
let inlineMap      = null;

function switchTab(tab) {
    const isMap = tab === 'map';

    document.getElementById('view-list').style.display = isMap ? 'none'  : 'block';
    document.getElementById('view-map').style.display  = isMap ? 'block' : 'none';

    document.getElementById('tab-list').style.color       = isMap ? '#888'    : '#2e7d32';
    document.getElementById('tab-list').style.borderBottom= isMap ? '3px solid transparent' : '3px solid #2e7d32';
    document.getElementById('tab-map').style.color        = isMap ? '#2e7d32' : '#888';
    document.getElementById('tab-map').style.borderBottom = isMap ? '3px solid #2e7d32' : '3px solid transparent';

    if (isMap && !mapInitialized) {
        initInlineMap();
        mapInitialized = true;
    }
}

function initInlineMap() {
    inlineMap = L.map('inline-map').setView([36.8065, 10.1815], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(inlineMap);

    const ALL = <?= json_encode($restaurants) ?>;
    const markers = [];
    const list    = document.getElementById('map-resto-list');

    ALL.forEach((r, i) => {
        // Carte latérale
        const card = document.createElement('div');
        card.className = 'map-card';
        card.innerHTML = `
            <h6>${esc(r.nom)}</h6>
            <span class="mc-type">${esc(r.type_cuisine)}</span>
            ${!r.latitude ? '<span style="color:#aaa;font-size:.72rem;margin-left:6px;">position non disponible</span>' : ''}
            <div class="mc-addr"><i class="fa fa-map-marker-alt me-1"></i>${esc(r.adresse)}</div>
        `;

        if (r.latitude && r.longitude) {
            const marker = L.marker([parseFloat(r.latitude), parseFloat(r.longitude)], {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="background:#2e7d32;color:#fff;border-radius:50% 50% 50% 0;
                                width:34px;height:34px;display:flex;align-items:center;justify-content:center;
                                transform:rotate(-45deg);box-shadow:0 2px 6px rgba(0,0,0,.3);">
                            <span style="transform:rotate(45deg);font-size:.8rem;">🍴</span>
                           </div>`,
                    iconSize: [34,34], iconAnchor: [17,34], popupAnchor: [0,-34]
                })
            }).addTo(inlineMap);

            const img = r.image
                ? `<img src="/2A35/assets/uploads/restaurants/${esc(r.image)}" style="width:100%;height:90px;object-fit:cover;border-radius:6px;margin-bottom:6px;">`
                : '';
            marker.bindPopup(`<div style="min-width:160px;">${img}
                <strong>${esc(r.nom)}</strong><br>
                <small style="color:#666;">${esc(r.adresse)}</small><br>
                <a href="/2A35/Restaurant/show/${r.id}" style="color:#2e7d32;font-size:.8rem;font-weight:600;">Voir le menu →</a>
            </div>`);

            card.onclick = () => {
                inlineMap.setView([parseFloat(r.latitude), parseFloat(r.longitude)], 16);
                marker.openPopup();
            };
            markers.push(marker);
        }
        list.appendChild(card);
    });

    if (markers.length > 0) {
        inlineMap.fitBounds(L.featureGroup(markers).getBounds().pad(0.2));
    }
}
</script>

<?php include 'View/front/partials/footer.php'; ?>
