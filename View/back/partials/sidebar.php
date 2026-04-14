<style>
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 220px;
    height: 100vh;
    background: #2e7d32;
    color: white;
    font-family: Arial, sans-serif;
    padding: 0;
    box-shadow: 3px 0 10px rgba(0,0,0,0.2);
    z-index: 1000;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 20px 15px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.15);
    background: #1b5e20;
}

.sidebar-header img {
    width: 100px;
    height: auto;
}

.sidebar-header .site-name {
    color: white;
    font-size: 1rem;
    font-weight: 700;
    margin-top: 8px;
    letter-spacing: 0.05em;
}

.sidebar nav {
    flex: 1;
    padding: 12px 0;
}

.sidebar nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

/* Item principal */
.nav-item {
    margin: 2px 8px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 14px;
    border-radius: 6px;
    text-decoration: none;
    color: rgba(255,255,255,0.9);
    font-size: 0.92rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
    justify-content: space-between;
}

.nav-link:hover,
.nav-link.active {
    background: rgba(255,255,255,0.18);
    color: white;
}

.nav-link .nav-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.nav-arrow {
    font-size: 0.7rem;
    transition: transform 0.2s;
}

/* Sous-menu */
.sub-menu {
    list-style: none;
    padding: 0 0 0 10px;
    margin: 0;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.sub-menu.open {
    max-height: 300px;
}

.sub-menu li a {
    display: block;
    padding: 8px 14px;
    border-radius: 5px;
    text-decoration: none;
    color: rgba(255,255,255,0.8);
    font-size: 0.85rem;
    margin: 2px 0;
    transition: background 0.2s;
}

.sub-menu li a:hover {
    background: rgba(255,255,255,0.15);
    color: white;
}

/* Section active (menu ouvert) */
.nav-item.active > .nav-link {
    background: rgba(255,255,255,0.2);
    color: white;
}

.nav-item.active > .nav-link .nav-arrow {
    transform: rotate(90deg);
}

/* Séparateur */
.nav-divider {
    height: 1px;
    background: rgba(255,255,255,0.12);
    margin: 8px 14px;
}

/* Footer sidebar */
.sidebar-footer {
    padding: 12px 15px;
    border-top: 1px solid rgba(255,255,255,0.15);
    font-size: 0.75rem;
    color: rgba(255,255,255,0.5);
    text-align: center;
}

/* Contenu principal décalé */
.content-area {
    margin-left: 220px;
}
</style>

<aside class="sidebar">
    <!-- Header -->
    <div class="sidebar-header">
        <div class="site-name">🥗 NutriSmart</div>
    </div>

    <!-- Navigation -->
    <nav>
        <ul>
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="/2A35/Admin/dashboard" class="nav-link <?= ($active_menu ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <span class="nav-left">📊 Dashboard</span>
                </a>
            </li>

            <div class="nav-divider"></div>

            <?php
            $sections = [
                'utilisateur' => ['👤 Utilisateur', [
                    ['label' => '📋 Liste',    'url' => '/2A35/Admin/utilisateur'],
                    ['label' => '➕ Nouveau',  'url' => '/2A35/Admin/utilisateur/create'],
                ]],
                'restaurant' => ['🤝 Partenaire', [
                    ['label' => '📋 Liste restaurants',  'url' => '/2A35/Admin/restaurant'],
                    ['label' => '➕ Nouveau restaurant', 'url' => '/2A35/Admin/restaurant/create'],
                ]],
                'recette' => ['🥘 Recette', [
                    ['label' => '📋 Liste recettes',  'url' => '/2A35/Admin/recette'],
                    ['label' => '➕ Nouvelle recette', 'url' => '/2A35/Admin/recette/create'],
                ]],
                'programme' => ['🏋️ Programme', [
                    ['label' => '📋 Liste',    'url' => '/2A35/Admin/programme'],
                    ['label' => '➕ Nouveau',  'url' => '/2A35/Admin/programme/create'],
                ]],
                'evenement' => ['📅 Événement', [
                    ['label' => '📋 Liste',    'url' => '/2A35/Admin/evenement'],
                    ['label' => '➕ Nouveau',  'url' => '/2A35/Admin/evenement/create'],
                ]],
            ];

            foreach ($sections as $key => [$label, $items]):
                $isActive = ($active_menu ?? '') === $key;
            ?>
            <li class="nav-item <?= $isActive ? 'active' : '' ?>">
                <div class="nav-link <?= $isActive ? 'active' : '' ?>" onclick="toggleMenu('<?= $key ?>')">
                    <span class="nav-left"><?= $label ?></span>
                    <span class="nav-arrow" id="arrow-<?= $key ?>">▶</span>
                </div>
                <ul class="sub-menu <?= $isActive ? 'open' : '' ?>" id="sub-<?= $key ?>">
                    <?php foreach ($items as $item): ?>
                    <li><a href="<?= $item['url'] ?>"><?= $item['label'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endforeach; ?>

            <div class="nav-divider"></div>

            <!-- Login -->
            <li class="nav-item">
                <a href="/2A35/Login" class="nav-link">
                    <span class="nav-left">🔐 Connexion</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        NutriSmart Admin © 2026
    </div>
</aside>

<script>
function toggleMenu(key) {
    const sub   = document.getElementById('sub-' + key);
    const arrow = document.getElementById('arrow-' + key);
    const item  = sub.closest('.nav-item');

    const isOpen = sub.classList.contains('open');

    // Fermer tous les autres
    document.querySelectorAll('.sub-menu').forEach(s => s.classList.remove('open'));
    document.querySelectorAll('.nav-arrow').forEach(a => a.style.transform = '');
    document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));

    if (!isOpen) {
        sub.classList.add('open');
        arrow.style.transform = 'rotate(90deg)';
        item.classList.add('active');
    }
}

// Ouvrir automatiquement le menu actif au chargement
document.addEventListener('DOMContentLoaded', function() {
    const activeItem = document.querySelector('.nav-item.active');
    if (activeItem) {
        const sub   = activeItem.querySelector('.sub-menu');
        const arrow = activeItem.querySelector('.nav-arrow');
        if (sub)   sub.classList.add('open');
        if (arrow) arrow.style.transform = 'rotate(90deg)';
    }
});
</script>
