<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header Start -->
<div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s"
     style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('/2A35/assets/img/carousel-1.jpg') center/cover no-repeat;">
    <div class="container py-5">
        <h1 class="display-4 text-white fw-bold mb-3">Nos Recettes</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/2A35/Home" class="text-white">Accueil</a></li>
                <li class="breadcrumb-item text-secondary active">Recettes</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->

<!-- Filtres + Recherche Start -->
<div class="container mb-4 wow fadeInUp" data-wow-delay="0.1s">
    <form method="GET" action="/2A35/Recette" class="row g-3 align-items-end">
        <!-- Recherche -->
        <div class="col-md-4">
            <label class="form-label fw-bold">🔍 Rechercher</label>
            <input type="text" name="search" class="form-control"
                   placeholder="Nom de la recette..."
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>
        <!-- Filtre catégorie -->
        <div class="col-md-3">
            <label class="form-label fw-bold">🗂 Catégorie</label>
            <select name="categorie" class="form-select">
                <option value="">Toutes les catégories</option>
                <?php foreach (['petit-dejeuner'=>'Petit-déjeuner','dejeuner'=>'Déjeuner','diner'=>'Dîner','collation'=>'Collation','dessert'=>'Dessert'] as $v=>$l): ?>
                    <option value="<?= $v ?>" <?= ($_GET['categorie'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Filtre difficulté -->
        <div class="col-md-3">
            <label class="form-label fw-bold">⚡ Difficulté</label>
            <select name="difficulte" class="form-select">
                <option value="">Toutes les difficultés</option>
                <?php foreach (['facile'=>'Facile','moyen'=>'Moyen','difficile'=>'Difficile'] as $v=>$l): ?>
                    <option value="<?= $v ?>" <?= ($_GET['difficulte'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            <?php if (!empty($_GET['search']) || !empty($_GET['categorie']) || !empty($_GET['difficulte'])): ?>
                <a href="/2A35/Recette" class="btn btn-outline-secondary w-100">✕</a>
            <?php endif; ?>
        </div>
    </form>
</div>
<!-- Filtres End -->

<!-- Recettes Start -->
<div class="container mb-5">

    <?php if (empty($recettes)): ?>
        <div class="text-center py-5 wow fadeIn">
            <i class="fa fa-utensils fa-4x text-primary mb-3"></i>
            <h4 class="text-muted">Aucune recette trouvée.</h4>
            <a href="/2A35/Recette" class="btn btn-primary mt-3">Voir toutes les recettes</a>
        </div>
    <?php else: ?>

        <!-- Compteur résultats -->
        <p class="text-muted mb-4 wow fadeIn">
            <strong><?= count($recettes) ?></strong> recette<?= count($recettes) > 1 ? 's' : '' ?> trouvée<?= count($recettes) > 1 ? 's' : '' ?>
        </p>

        <div class="row g-4">
        <?php foreach ($recettes as $i => $r): ?>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="<?= ($i % 3) * 0.1 + 0.1 ?>s">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">

                    <!-- Image -->
                    <div style="height:220px; overflow:hidden; position:relative;">
                        <?php if ($r['image']): ?>
                            <img src="/2A35/assets/uploads/recettes/<?= htmlspecialchars($r['image']) ?>"
                                 class="w-100 h-100" style="object-fit:cover;" alt="<?= htmlspecialchars($r['nom']) ?>">
                        <?php else: ?>
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center"
                                 style="background:linear-gradient(135deg,#2e7d32,#4caf50);">
                                <i class="fa fa-utensils fa-4x text-white opacity-75"></i>
                            </div>
                        <?php endif; ?>
                        <!-- Badge catégorie -->
                        <span class="position-absolute top-0 start-0 m-2 badge"
                              style="background:#2e7d32; font-size:0.75rem;">
                            <?= htmlspecialchars($r['categorie']) ?>
                        </span>
                    </div>

                    <!-- Contenu -->
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-3"><?= htmlspecialchars($r['nom']) ?></h5>

                        <!-- Infos -->
                        <div class="d-flex justify-content-between text-muted small mb-3">
                            <span><i class="fa fa-clock me-1 text-primary"></i><?= $r['duree'] ?> min</span>
                            <span><i class="fa fa-fire me-1 text-danger"></i><?= $r['calories'] ?> kcal</span>
                            <span>
                                <?php
                                $color = match($r['difficulte']) {
                                    'facile'    => 'success',
                                    'moyen'     => 'warning',
                                    'difficile' => 'danger',
                                    default     => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= htmlspecialchars($r['difficulte']) ?></span>
                            </span>
                        </div>

                        <a href="/2A35/RecetteFront/detail/<?= $r['id'] ?>"
                           class="btn btn-primary w-100">
                            Voir la recette <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>
<!-- Recettes End -->

<?php include 'View/front/partials/footer.php'; ?>
