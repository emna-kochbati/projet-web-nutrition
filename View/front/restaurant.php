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

<!-- Filtres Start -->
<div class="container-fluid py-4" style="background:#f7f8fc; border-bottom:1px solid #e9ecef;">
    <div class="container">
        <form method="GET" action="/2A35/Restaurant" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold"><i class="fa fa-search me-1 text-primary"></i> Rechercher</label>
                <input type="text" name="search" class="form-control" placeholder="Nom du restaurant..."
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold"><i class="fa fa-utensils me-1 text-primary"></i> Type de cuisine</label>
                <select name="type" class="form-select">
                    <option value="">Tous les types</option>
                    <?php foreach (['tunisienne','italienne','japonaise','americaine','indienne','mexicaine','française','autre'] as $t): ?>
                        <option value="<?= $t ?>" <?= ($_GET['type'] ?? '') === $t ? 'selected' : '' ?>>
                            <?= ucfirst($t) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 py-2">Filtrer</button>
            </div>
            <?php if (!empty($_GET['search']) || !empty($_GET['type'])): ?>
            <div class="col-md-2">
                <a href="/2A35/Restaurant" class="btn btn-outline-secondary w-100 py-2">✕ Effacer</a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>
<!-- Filtres End -->

<!-- Restaurants Start -->
<div class="container-fluid py-5">
    <div class="container">

        <p class="text-muted mb-4">
            <strong><?= count($restaurants) ?></strong> restaurant<?= count($restaurants) > 1 ? 's' : '' ?> trouvé<?= count($restaurants) > 1 ? 's' : '' ?>
        </p>

        <?php if (empty($restaurants)): ?>
            <div class="text-center py-5">
                <i class="fa fa-utensils fa-3x text-muted mb-3"></i>
                <p class="text-muted fs-5">Aucun restaurant trouvé.</p>
                <a href="/2A35/Restaurant" class="btn btn-primary mt-2">Voir tous les restaurants</a>
            </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($restaurants as $r): ?>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="product-item rounded overflow-hidden h-100 d-flex flex-col" style="flex-direction:column;">

                    <!-- Image -->
                    <div style="position:relative; overflow:hidden; height:220px; background:#2e7d32;">
                        <?php if (!empty($r['image'])): ?>
                            <img src="/2A35/assets/uploads/restaurants/<?= htmlspecialchars($r['image']) ?>"
                                 alt="<?= htmlspecialchars($r['nom']) ?>"
                                 style="width:100%;height:100%;object-fit:cover;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <i class="fa fa-utensils fa-4x text-white opacity-50"></i>
                            </div>
                        <?php endif; ?>
                        <!-- Badge type cuisine -->
                        <span style="position:absolute;top:12px;left:12px;background:rgba(0,0,0,.55);color:#fff;padding:3px 10px;border-radius:4px;font-size:.78rem;">
                            <?= htmlspecialchars($r['type_cuisine']) ?>
                        </span>
                    </div>

                    <!-- Infos -->
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($r['nom']) ?></h5>

                        <p class="text-muted small mb-2">
                            <i class="fa fa-map-marker-alt me-1 text-primary"></i>
                            <?= htmlspecialchars($r['adresse']) ?>
                        </p>

                        <?php if (!empty($r['description'])): ?>
                            <p class="text-muted small mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                <?= htmlspecialchars($r['description']) ?>
                            </p>
                        <?php endif; ?>

                        <div class="d-flex gap-3 mb-3 flex-wrap">
                            <?php if (!empty($r['telephone'])): ?>
                            <small class="text-muted">
                                <i class="fa fa-phone me-1 text-primary"></i><?= htmlspecialchars($r['telephone']) ?>
                            </small>
                            <?php endif; ?>
                        </div>

                        <div class="mt-auto">
                            <a href="/2A35/Restaurant/show/<?= $r['id'] ?>" class="btn btn-primary w-100">
                                Voir le menu <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</div>
<!-- Restaurants End -->

<?php include 'View/front/partials/footer.php'; ?>
