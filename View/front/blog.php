<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header -->
<div class="page-header wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-5 text-white mb-3 animated slideInDown">Notre Blog</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a class="text-white" href="/2A35/Home">Accueil</a></li>
                        <li class="breadcrumb-item text-primary active">Blog</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Blog Grid -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="section-header text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width:500px;">
            <h4 class="text-primary text-uppercase">Actualités</h4>
            <h2>Conseils nutrition & bien-être</h2>
        </div>
        <div class="row g-4">
            <?php
            $posts = [
                ['img'=>'blog-1.jpg', 'cat'=>'Nutrition',  'date'=>'10 Avril 2026',  'title'=>'Les 10 aliments les plus nutritifs à intégrer dans votre alimentation',       'text'=>'Découvrez les super-aliments qui boostent votre énergie et renforcent votre système immunitaire au quotidien.'],
                ['img'=>'blog-2.jpg', 'cat'=>'Recettes',   'date'=>'05 Avril 2026',  'title'=>'5 recettes saines et rapides pour la semaine',                                 'text'=>'Des idées de repas équilibrés préparables en moins de 30 minutes, parfaits pour les personnes actives.'],
                ['img'=>'blog-3.jpg', 'cat'=>'Bien-être',  'date'=>'01 Avril 2026',  'title'=>'Comment établir un programme nutritionnel adapté à vos objectifs',             'text'=>'Nos experts vous guident pour créer un plan alimentaire personnalisé selon votre mode de vie et vos objectifs.'],
                ['img'=>'blog-1.jpg', 'cat'=>'Fitness',    'date'=>'25 Mars 2026',   'title'=>'Alimentation et sport : les combinaisons gagnantes',                           'text'=>'Optimisez vos performances sportives grâce à une alimentation adaptée avant, pendant et après l\'effort.'],
                ['img'=>'blog-2.jpg', 'cat'=>'Nutrition',  'date'=>'20 Mars 2026',   'title'=>'Comprendre les étiquettes nutritionnelles : guide pratique',                   'text'=>'Apprenez à déchiffrer les informations nutritionnelles sur les emballages pour faire de meilleurs choix.'],
                ['img'=>'blog-3.jpg', 'cat'=>'Recettes',   'date'=>'15 Mars 2026',   'title'=>'Cuisine méditerranéenne : saveurs et bienfaits pour la santé',                 'text'=>'La cuisine méditerranéenne est reconnue comme l\'une des plus saines au monde. Découvrez ses secrets.'],
            ];
            foreach ($posts as $i => $p):
            ?>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="<?= 0.1 * (($i % 3) + 1) ?>s">
                <div class="product-item rounded overflow-hidden h-100 d-flex flex-column">
                    <div style="overflow:hidden;height:220px;">
                        <img src="/2A35/assets/img/<?= $p['img'] ?>" class="img-fluid w-100"
                             style="height:100%;object-fit:cover;transition:.5s;" alt="<?= $p['title'] ?>">
                    </div>
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-primary"><?= $p['cat'] ?></span>
                            <small class="text-muted"><i class="fa fa-calendar me-1"></i><?= $p['date'] ?></small>
                        </div>
                        <h5 class="fw-bold mb-2"><?= $p['title'] ?></h5>
                        <p class="text-muted small flex-grow-1"><?= $p['text'] ?></p>
                        <a href="#" class="btn btn-outline-primary mt-2">Lire la suite <i class="fa fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'View/front/partials/footer.php'; ?>
