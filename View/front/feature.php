<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header -->
<div class="page-header wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-5 text-white mb-3 animated slideInDown">Nos Fonctionnalités</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a class="text-white" href="/2A35/Home">Accueil</a></li>
                        <li class="breadcrumb-item text-primary active">Fonctionnalités</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Features Grid -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="section-header text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width:500px;">
            <h4 class="text-primary text-uppercase">Pourquoi nous choisir</h4>
            <h2>Tout ce dont vous avez besoin</h2>
        </div>
        <div class="row g-4">
            <?php
            $features = [
                ['icon'=>'fa-leaf',         'title'=>'100% Bio & Naturel',       'text'=>'Tous nos produits et recettes sont issus d\'ingrédients naturels, sans additifs ni conservateurs artificiels.'],
                ['icon'=>'fa-heartbeat',    'title'=>'Suivi nutritionnel',        'text'=>'Suivez vos apports caloriques et nutritionnels au quotidien grâce à nos outils de suivi personnalisés.'],
                ['icon'=>'fa-utensils',     'title'=>'Restaurants certifiés',     'text'=>'Nos restaurants partenaires sont soigneusement sélectionnés et certifiés pour la qualité de leurs plats.'],
                ['icon'=>'fa-book-open',    'title'=>'Recettes équilibrées',      'text'=>'Des centaines de recettes saines et délicieuses, élaborées par nos nutritionnistes et chefs cuisiniers.'],
                ['icon'=>'fa-dumbbell',     'title'=>'Programmes fitness',        'text'=>'Des programmes d\'entraînement adaptés à vos objectifs, combinés à des plans nutritionnels sur mesure.'],
                ['icon'=>'fa-headset',      'title'=>'Support 24/7',              'text'=>'Notre équipe d\'experts est disponible à tout moment pour répondre à vos questions et vous accompagner.'],
                ['icon'=>'fa-mobile-alt',   'title'=>'Application mobile',        'text'=>'Accédez à toutes nos fonctionnalités depuis votre smartphone, où que vous soyez.'],
                ['icon'=>'fa-shield-alt',   'title'=>'Données sécurisées',        'text'=>'Vos données personnelles et de santé sont protégées avec les plus hauts standards de sécurité.'],
                ['icon'=>'fa-star',         'title'=>'Avis vérifiés',             'text'=>'Consultez les avis authentiques de notre communauté pour faire les meilleurs choix alimentaires.'],
            ];
            foreach ($features as $i => $f):
            ?>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="<?= 0.1 * (($i % 3) + 1) ?>s">
                <div class="d-flex flex-column align-items-start bg-light rounded p-4 h-100">
                    <div class="btn-square bg-primary rounded mb-3" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa <?= $f['icon'] ?> text-white"></i>
                    </div>
                    <h5 class="fw-bold mb-2"><?= $f['title'] ?></h5>
                    <p class="text-muted mb-0"><?= $f['text'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- CTA -->
<div class="container-fluid bg-primary py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container text-center text-white">
        <h2 class="mb-3">Prêt à commencer votre parcours santé ?</h2>
        <p class="mb-4 fs-5">Rejoignez des milliers de personnes qui ont déjà transformé leur alimentation avec NutriSmart.</p>
        <a href="/2A35/Restaurant" class="btn btn-secondary py-3 px-5 me-3">Nos restaurants</a>
        <a href="/2A35/Contact" class="btn btn-outline-light py-3 px-5">Nous contacter</a>
    </div>
</div>

<?php include 'View/front/partials/footer.php'; ?>
