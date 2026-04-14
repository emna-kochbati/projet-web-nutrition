<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header -->
<div class="container-fluid page-header py-5 mb-5 wow fadeIn"
     style="background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url('/2A35/assets/img/carousel-2.jpg') center/cover no-repeat;">
    <div class="container py-5">
        <h1 class="display-5 text-white fw-bold mb-3"><?= htmlspecialchars($recette['nom']) ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/2A35/Home" class="text-white">Accueil</a></li>
                <li class="breadcrumb-item"><a href="/2A35/RecetteFront" class="text-white">Recettes</a></li>
                <li class="breadcrumb-item text-secondary active"><?= htmlspecialchars($recette['nom']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-5">

        <!-- Image + infos -->
        <div class="col-lg-5 wow fadeInLeft" data-wow-delay="0.1s">
            <?php if ($recette['image']): ?>
                <img src="/2A35/assets/uploads/recettes/<?= htmlspecialchars($recette['image']) ?>"
                     class="img-fluid rounded-3 shadow w-100"
                     style="max-height:380px; object-fit:cover;"
                     alt="<?= htmlspecialchars($recette['nom']) ?>">
            <?php else: ?>
                <div class="rounded-3 d-flex align-items-center justify-content-center shadow"
                     style="height:380px; background:linear-gradient(135deg,#2e7d32,#4caf50);">
                    <i class="fa fa-utensils fa-5x text-white opacity-75"></i>
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="row g-3 mt-3">
                <div class="col-6">
                    <div class="bg-light rounded-3 p-3 text-center">
                        <i class="fa fa-clock fa-2x text-primary mb-2"></i>
                        <div class="fw-bold fs-5"><?= $recette['duree'] ?> min</div>
                        <small class="text-muted">Durée</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-light rounded-3 p-3 text-center">
                        <i class="fa fa-fire fa-2x text-danger mb-2"></i>
                        <div class="fw-bold fs-5"><?= $recette['calories'] ?> kcal</div>
                        <small class="text-muted">Calories</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-light rounded-3 p-3 text-center">
                        <?php $color = match($recette['difficulte']) {'facile'=>'success','moyen'=>'warning','difficile'=>'danger',default=>'secondary'}; ?>
                        <i class="fa fa-signal fa-2x text-<?= $color ?> mb-2"></i>
                        <div class="fw-bold fs-5"><?= htmlspecialchars($recette['difficulte']) ?></div>
                        <small class="text-muted">Difficulté</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-light rounded-3 p-3 text-center">
                        <i class="fa fa-tag fa-2x text-secondary mb-2"></i>
                        <div class="fw-bold" style="font-size:0.95rem"><?= htmlspecialchars($recette['categorie']) ?></div>
                        <small class="text-muted">Catégorie</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails -->
        <div class="col-lg-7 wow fadeInRight" data-wow-delay="0.1s">
            <h2 class="fw-bold mb-4" style="color:#2e7d32;"><?= htmlspecialchars($recette['nom']) ?></h2>

            <!-- Ingrédients -->
            <?php if (!empty($ingredients)): ?>
            <h5 class="fw-bold mb-3"><i class="fa fa-leaf me-2 text-success"></i>Ingrédients</h5>
            <ul class="list-group list-group-flush mb-4">
                <?php foreach ($ingredients as $ing): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="fa fa-circle text-success me-2" style="font-size:0.5rem;"></i><?= htmlspecialchars($ing['nom']) ?></span>
                        <span class="badge" style="background:#2e7d32;"><?= $ing['quantite'] ?> <?= htmlspecialchars($ing['unite']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <div class="d-flex gap-3 mt-4">
                <a href="/2A35/RecetteFront" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Retour aux recettes
                </a>
            </div>
        </div>

    </div>
</div>

<?php include 'View/front/partials/footer.php'; ?>
