<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header Start -->
<div class="page-header wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-5 text-white mb-3 animated slideInDown"><?= htmlspecialchars($restaurant['nom']) ?></h1>
                <nav aria-label="breadcrumb animated slideInDown">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a class="text-white" href="/2A35/Home">Accueil</a></li>
                        <li class="breadcrumb-item"><a class="text-white" href="/2A35/Restaurant">Restaurants</a></li>
                        <li class="breadcrumb-item text-primary active" aria-current="page"><?= htmlspecialchars($restaurant['nom']) ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- Page Header End -->

<!-- Restaurant Detail Start -->
<div class="container py-5">
    <div class="row g-5 mb-5">

        <!-- Image -->
        <div class="col-lg-5 wow fadeIn" data-wow-delay="0.1s">
            <div class="rounded overflow-hidden" style="height:340px;background:#2e7d32;">
                <?php if (!empty($restaurant['image'])): ?>
                    <img src="/2A35/assets/uploads/restaurants/<?= htmlspecialchars($restaurant['image']) ?>"
                         alt="<?= htmlspecialchars($restaurant['nom']) ?>"
                         style="width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <i class="fa fa-utensils fa-5x text-white opacity-50"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Infos -->
        <div class="col-lg-7 wow fadeIn" data-wow-delay="0.3s">
            <div class="section-header text-start mb-4">
                <h4 class="text-primary text-uppercase">Restaurant</h4>
                <h2><?= htmlspecialchars($restaurant['nom']) ?></h2>
            </div>

            <?php if (!empty($restaurant['description'])): ?>
                <p class="text-muted mb-4"><?= nl2br(htmlspecialchars($restaurant['description'])) ?></p>
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="d-flex align-items-start gap-3">
                        <i class="fa fa-map-marker-alt fa-lg text-primary mt-1"></i>
                        <div>
                            <small class="text-muted d-block">Adresse</small>
                            <span class="fw-semibold"><?= htmlspecialchars($restaurant['adresse']) ?></span>
                        </div>
                    </div>
                </div>
                <?php if (!empty($restaurant['telephone'])): ?>
                <div class="col-sm-6">
                    <div class="d-flex align-items-start gap-3">
                        <i class="fa fa-phone fa-lg text-primary mt-1"></i>
                        <div>
                            <small class="text-muted d-block">Téléphone</small>
                            <span class="fw-semibold"><?= htmlspecialchars($restaurant['telephone']) ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($restaurant['email'])): ?>
                <div class="col-sm-6">
                    <div class="d-flex align-items-start gap-3">
                        <i class="fa fa-envelope fa-lg text-primary mt-1"></i>
                        <div>
                            <small class="text-muted d-block">Email</small>
                            <span class="fw-semibold"><?= htmlspecialchars($restaurant['email']) ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col-sm-6">
                    <div class="d-flex align-items-start gap-3">
                        <i class="fa fa-utensils fa-lg text-primary mt-1"></i>
                        <div>
                            <small class="text-muted d-block">Cuisine</small>
                            <span class="fw-semibold"><?= ucfirst(htmlspecialchars($restaurant['type_cuisine'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu / Plats Start -->
    <div class="wow fadeInUp" data-wow-delay="0.1s">
        <div class="section-header text-start mb-4">
            <h4 class="text-primary text-uppercase">Menu</h4>
            <h2>Nos Plats</h2>
        </div>

        <?php if (empty($meals)): ?>
            <div class="text-center py-5 bg-light rounded">
                <i class="fa fa-concierge-bell fa-3x text-muted mb-3"></i>
                <p class="text-muted">Aucun plat disponible pour ce restaurant.</p>
            </div>
        <?php else: ?>

        <!-- Filtres par catégorie -->
        <?php
        $categories = array_unique(array_column($meals, 'categorie'));
        $catLabels  = ['entree' => 'Entrée', 'plat_principal' => 'Plat principal', 'dessert' => 'Dessert', 'boisson' => 'Boisson', 'snack' => 'Snack'];
        ?>
        <div class="nav-pills d-flex flex-wrap gap-2 mb-4">
            <button class="btn btn-primary btn-sm filter-btn active" data-cat="all">Tous</button>
            <?php foreach ($categories as $cat): ?>
                <button class="btn btn-outline-primary btn-sm filter-btn" data-cat="<?= $cat ?>">
                    <?= $catLabels[$cat] ?? ucfirst($cat) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="row g-4" id="meals-grid">
            <?php foreach ($meals as $m): ?>
            <?php if (!$m['disponible']) continue; ?>
            <div class="col-lg-4 col-md-6 meal-card" data-cat="<?= $m['categorie'] ?>">
                <div class="product-item rounded overflow-hidden h-100" style="flex-direction:column;display:flex;">

                    <!-- Image -->
                    <div style="position:relative;overflow:hidden;height:180px;background:#2e7d32;">
                        <?php if (!empty($m['image'])): ?>
                            <img src="/2A35/assets/uploads/meals/<?= htmlspecialchars($m['image']) ?>"
                                 alt="<?= htmlspecialchars($m['nom']) ?>"
                                 style="width:100%;height:100%;object-fit:cover;transition:.5s;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <i class="fa fa-concierge-bell fa-3x text-white opacity-50"></i>
                            </div>
                        <?php endif; ?>
                        <span style="position:absolute;top:10px;left:10px;background:var(--primary);color:#fff;padding:2px 10px;border-radius:4px;font-size:.75rem;">
                            <?= $catLabels[$m['categorie']] ?? ucfirst($m['categorie']) ?>
                        </span>
                    </div>

                    <!-- Infos -->
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($m['nom']) ?></h6>
                        <?php if (!empty($m['description'])): ?>
                            <p class="text-muted small mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                <?= htmlspecialchars($m['description']) ?>
                            </p>
                        <?php endif; ?>
                        <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
                            <div class="d-flex gap-3">
                                <?php if (!empty($m['calories'])): ?>
                                <small class="text-muted"><i class="fa fa-fire me-1 text-secondary"></i><?= $m['calories'] ?> kcal</small>
                                <?php endif; ?>
                            </div>
                            <span class="fw-bold text-primary fs-6"><?= number_format((float)$m['prix'], 2) ?> DT</span>
                        </div>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div>
    <!-- Menu / Plats End -->

</div>
<!-- Restaurant Detail End -->

<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('active', 'btn-primary');
            b.classList.add('btn-outline-primary');
        });
        this.classList.add('active', 'btn-primary');
        this.classList.remove('btn-outline-primary');

        const cat = this.dataset.cat;
        document.querySelectorAll('.meal-card').forEach(card => {
            card.style.display = (cat === 'all' || card.dataset.cat === cat) ? '' : 'none';
        });
    });
});
</script>

<?php include 'View/front/partials/footer.php'; ?>
