<?php include 'View/front/partials/header.php'; ?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Page Header -->
<div class="page-header wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-5 text-white mb-3 animated slideInDown">Carte des Restaurants</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a class="text-white" href="/2A35/Home">Accueil</a></li>
                        <li class="breadcrumb-item"><a class="text-white" href="/2A35/Restaurant">Restaurants</a></li>
                        <li class="breadcrumb-item text-primary active">Carte</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid py-4">
    <div class="container">

        <!-- Barre d'outils -->
        <div class="row g-3 mb-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold"><i class="fa fa-utensils me-1 text-primary"></i> Type de cuisine</label>
                <select id="filter-cuisine" class="form-select" onchange="filterMarkers()">
                    <option value="">Tous les types</option>
                    <?php foreach (['tunisienne','italienne','japonaise','americaine','indienne','mexicaine','française','autre'] as $t): ?>
                        <option value="<?= $t ?>"><?= ucfirst($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold"><i class="fa fa-search me-1 text-primary"></i> Rechercher</label>
                <input type="text" id="filter-search" class="form-control" placeholder="Nom du restaurant..." oninput="filterMarkers()">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold"><i class="fa fa-map-marker-alt me-1 text-primary"></i> Rayon (km)</label>
                <select id="filter-radius" class="form-select">
                    <option value="5">5 km</option>
                    <option value="10" selected>10 km</option>
                    <option value="20">20 km</option>
                    <option value="50">50 km</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button onclick="locateMe()" class="btn btn-primary w-100">
                    <i class="fa fa-location-arrow me-1"></i> Ma position
                </button>
                <button onclick="resetMap()" class="btn btn-outline-secondary">
                    <i class="fa fa-redo"></i>
                </button>
            </div>
        </div>

        <!-- Compteur -->
        <p id="map-count" class="text-muted mb-3 fw-semibold"></p>

        <div class="row g-4">
            <!-- Carte -->
            <div class="col-lg-8">
                <div id="map" style="height:520px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.12);"></div>
            </div>

            <!-- Liste latérale -->
            <div class="col-lg-4">
                <div style="height:520px;overflow-y:auto;display:flex;flex-direction:column;gap:10px;" id="resto-list">
                    <!-- Rempli par JS -->
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.resto-card {
    background:#fff; border-radius:10px; padding:14px;
    box-shadow:0 2px 8px rgba(0,0,0,.07); cursor:pointer;
    border-left:4px solid #2e7d32; transition:transform .2s;
}
.resto-card:hover { transform:translateX(4px); box-shadow:0 4px 14px rgba(0,0,0,.12); }
.resto-card.active { border-left-color:#e65100; background:#fff8f5; }
.resto-card h6 { font-weight:700; color:#1a1a1a; margin-bottom:4px; font-size:.9rem; }
.resto-card .badge-type { background:#e8f5e9; color:#2e7d32; padding:2px 8px; border-radius:10px; font-size:.72rem; font-weight:600; }
.resto-card .addr { font-size:.78rem; color:#888; margin-top:4px; }
.resto-card .dist-badge { background:#e3f2fd; color:#1565c0; padding:2px 8px; border-radius:10px; font-size:.72rem; font-weight:600; }
.user-marker { background:#e53935; border:3px solid #fff; border-radius:50%; width:16px; height:16px; box-shadow:0 2px 6px rgba(0,0,0,.3); }
</style>

<script>
// ── Données PHP → JS ──────────────────────────────────────────────────────
const ALL_RESTAURANTS = <?= json_encode($restaurants) ?>;

// ── Init carte ────────────────────────────────────────────────────────────
const map = L.map('map').setView([36.8065, 10.1815], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19
}).addTo(map);

// Icône verte personnalisée
function greenIcon(label) {
    return L.divIcon({
        className: '',
        html: `<div style="background:#2e7d32;color:#fff;border-radius:50% 50% 50% 0;
                    width:36px;height:36px;display:flex;align-items:center;justify-content:center;
                    font-size:.7rem;font-weight:700;transform:rotate(-45deg);
                    box-shadow:0 2px 8px rgba(0,0,0,.3);">
                <span style="transform:rotate(45deg);">🍴</span>
               </div>`,
        iconSize: [36, 36],
        iconAnchor: [18, 36],
        popupAnchor: [0, -36]
    });
}

let markers     = [];
let userMarker  = null;
let activeCard  = null;

// ── Afficher tous les marqueurs ───────────────────────────────────────────
function renderMarkers(list) {
    // Supprimer anciens marqueurs
    markers.forEach(m => map.removeLayer(m));
    markers = [];

    document.getElementById('resto-list').innerHTML = '';
    document.getElementById('map-count').textContent =
        list.length + ' restaurant' + (list.length > 1 ? 's' : '') + ' affiché' + (list.length > 1 ? 's' : '');

    if (list.length === 0) {
        document.getElementById('resto-list').innerHTML =
            '<p class="text-muted text-center py-4">Aucun restaurant trouvé.</p>';
        return;
    }

    list.forEach((r, i) => {
        if (!r.latitude || !r.longitude) return;

        // Marqueur
        const marker = L.marker([r.latitude, r.longitude], { icon: greenIcon() })
            .addTo(map)
            .bindPopup(buildPopup(r));

        marker.on('click', () => highlightCard(i));
        markers.push(marker);

        // Carte latérale
        const card = document.createElement('div');
        card.className = 'resto-card';
        card.id = 'card-' + i;
        card.innerHTML = `
            <h6>${esc(r.nom)}</h6>
            <span class="badge-type">${esc(r.type_cuisine)}</span>
            ${r.distance_km !== undefined ? `<span class="dist-badge ms-1">📍 ${r.distance_km} km</span>` : ''}
            <div class="addr"><i class="fa fa-map-marker-alt me-1"></i>${esc(r.adresse)}</div>
            ${r.telephone ? `<div class="addr"><i class="fa fa-phone me-1"></i>${esc(r.telephone)}</div>` : ''}
        `;
        card.onclick = () => {
            map.setView([r.latitude, r.longitude], 16);
            marker.openPopup();
            highlightCard(i);
        };
        document.getElementById('resto-list').appendChild(card);
    });

    // Ajuster la vue
    if (markers.length > 0) {
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.2));
    }
}

function buildPopup(r) {
    const img = r.image
        ? `<img src="/2A35/assets/uploads/restaurants/${esc(r.image)}" style="width:100%;height:100px;object-fit:cover;border-radius:6px;margin-bottom:8px;">`
        : '';
    return `<div style="min-width:180px;">
        ${img}
        <strong style="font-size:.9rem;">${esc(r.nom)}</strong><br>
        <span style="background:#e8f5e9;color:#2e7d32;padding:1px 7px;border-radius:8px;font-size:.72rem;">${esc(r.type_cuisine)}</span><br>
        <small style="color:#666;">${esc(r.adresse)}</small><br>
        ${r.telephone ? `<small><i class="fa fa-phone"></i> ${esc(r.telephone)}</small><br>` : ''}
        <a href="/2A35/Restaurant/show/${r.id}" style="color:#2e7d32;font-size:.8rem;font-weight:600;">Voir le menu →</a>
    </div>`;
}

function highlightCard(i) {
    document.querySelectorAll('.resto-card').forEach(c => c.classList.remove('active'));
    const card = document.getElementById('card-' + i);
    if (card) {
        card.classList.add('active');
        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// ── Filtres ───────────────────────────────────────────────────────────────
function filterMarkers() {
    const cuisine = document.getElementById('filter-cuisine').value;
    const search  = document.getElementById('filter-search').value.toLowerCase();

    const filtered = ALL_RESTAURANTS.filter(r => {
        const matchCuisine = !cuisine || r.type_cuisine === cuisine;
        const matchSearch  = !search  || r.nom.toLowerCase().includes(search) || r.adresse.toLowerCase().includes(search);
        return matchCuisine && matchSearch;
    });
    renderMarkers(filtered);
}

// ── Géolocalisation ───────────────────────────────────────────────────────
function locateMe() {
    if (!navigator.geolocation) {
        alert("Géolocalisation non supportée par votre navigateur.");
        return;
    }
    navigator.geolocation.getCurrentPosition(pos => {
        const lat    = pos.coords.latitude;
        const lng    = pos.coords.longitude;
        const radius = parseFloat(document.getElementById('filter-radius').value);

        // Marqueur utilisateur
        if (userMarker) map.removeLayer(userMarker);
        userMarker = L.circleMarker([lat, lng], {
            radius: 10, fillColor: '#e53935', color: '#fff',
            weight: 3, fillOpacity: 1
        }).addTo(map).bindPopup('<strong>📍 Votre position</strong>').openPopup();

        map.setView([lat, lng], 13);

        // Cercle de rayon
        if (window.radiusCircle) map.removeLayer(window.radiusCircle);
        window.radiusCircle = L.circle([lat, lng], {
            radius: radius * 1000,
            color: '#2e7d32', fillColor: '#2e7d32', fillOpacity: 0.05, weight: 1
        }).addTo(map);

        // Appel API nearby
        fetch(`/2A35/Map/nearby?lat=${lat}&lng=${lng}&radius=${radius}`)
            .then(r => r.json())
            .then(data => {
                if (data.error) { alert(data.error); return; }
                renderMarkers(data);
                document.getElementById('map-count').textContent =
                    data.length + ' restaurant' + (data.length > 1 ? 's' : '') +
                    ' dans un rayon de ' + radius + ' km';
            });
    }, () => alert("Impossible d'accéder à votre position."));
}

function resetMap() {
    if (userMarker) { map.removeLayer(userMarker); userMarker = null; }
    if (window.radiusCircle) { map.removeLayer(window.radiusCircle); window.radiusCircle = null; }
    document.getElementById('filter-cuisine').value = '';
    document.getElementById('filter-search').value  = '';
    renderMarkers(ALL_RESTAURANTS);
}

function esc(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}

// ── Chargement initial ────────────────────────────────────────────────────
renderMarkers(ALL_RESTAURANTS);
</script>

<?php include 'View/front/partials/footer.php'; ?>
