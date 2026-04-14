<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header -->
<div class="page-header wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-5 text-white mb-3 animated slideInDown">À propos de nous</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a class="text-white" href="/2A35/Home">Accueil</a></li>
                        <li class="breadcrumb-item text-primary active">À propos</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- About Story -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                <div class="about-img position-relative overflow-hidden p-5 pe-0">
                    <img class="img-fluid w-100" src="/2A35/assets/img/about.jpg" alt="À propos de NutriSmart">
                </div>
            </div>
            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                <div class="section-header text-start mb-4">
                    <h4 class="text-primary text-uppercase">Notre histoire</h4>
                    <h2>NutriSmart — Votre partenaire santé</h2>
                </div>
                <p class="mb-3">Fondée en 2020, NutriSmart est née d'une passion pour la nutrition et le bien-être. Notre mission est de rendre l'alimentation saine accessible à tous, en connectant les consommateurs avec les meilleurs restaurants et recettes nutritives.</p>
                <p class="mb-4">Nous travaillons avec des nutritionnistes certifiés, des chefs cuisiniers passionnés et des restaurants partenaires soigneusement sélectionnés pour vous offrir la meilleure expérience culinaire et nutritionnelle.</p>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 bg-light rounded p-3">
                            <i class="fa fa-check-circle fa-2x text-primary"></i>
                            <span class="fw-semibold">Produits 100% naturels</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 bg-light rounded p-3">
                            <i class="fa fa-check-circle fa-2x text-primary"></i>
                            <span class="fw-semibold">Recettes équilibrées</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 bg-light rounded p-3">
                            <i class="fa fa-check-circle fa-2x text-primary"></i>
                            <span class="fw-semibold">Restaurants certifiés</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 bg-light rounded p-3">
                            <i class="fa fa-check-circle fa-2x text-primary"></i>
                            <span class="fw-semibold">Suivi personnalisé</span>
                        </div>
                    </div>
                </div>
                <a href="/2A35/Contact" class="btn btn-primary py-3 px-5">Contactez-nous</a>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="container-fluid bg-primary py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container">
        <div class="row g-4 text-center text-white">
            <div class="col-lg-3 col-md-6">
                <i class="fa fa-users fa-3x mb-3"></i>
                <h2 class="fw-bold">5 000+</h2>
                <p class="mb-0">Clients satisfaits</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <i class="fa fa-utensils fa-3x mb-3"></i>
                <h2 class="fw-bold">120+</h2>
                <p class="mb-0">Restaurants partenaires</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <i class="fa fa-book-open fa-3x mb-3"></i>
                <h2 class="fw-bold">500+</h2>
                <p class="mb-0">Recettes disponibles</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <i class="fa fa-award fa-3x mb-3"></i>
                <h2 class="fw-bold">15+</h2>
                <p class="mb-0">Prix & distinctions</p>
            </div>
        </div>
    </div>
</div>

<!-- Team -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="section-header text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width:500px;">
            <h4 class="text-primary text-uppercase">Notre équipe</h4>
            <h2>Les experts derrière NutriSmart</h2>
        </div>
        <div class="row g-4 justify-content-center">
            <?php
            $team = [
                ['img'=>'testimonial-1.jpg','name'=>'Dr. Sarah Ben Ali','role'=>'Nutritionniste en chef'],
                ['img'=>'testimonial-2.jpg','name'=>'Mohamed Karim','role'=>'Chef cuisinier'],
                ['img'=>'testimonial-3.jpg','name'=>'Amira Trabelsi','role'=>'Responsable partenariats'],
                ['img'=>'testimonial-4.jpg','name'=>'Karim Laabidi','role'=>'Directeur général'],
            ];
            foreach ($team as $i => $m):
            ?>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="<?= 0.1 * ($i+1) ?>s">
                <div class="text-center rounded overflow-hidden product-item pb-4">
                    <img src="/2A35/assets/img/<?= $m['img'] ?>" class="img-fluid w-100" style="height:220px;object-fit:cover;" alt="<?= $m['name'] ?>">
                    <div class="pt-3">
                        <h5 class="fw-bold mb-1"><?= $m['name'] ?></h5>
                        <small class="text-primary"><?= $m['role'] ?></small>
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <a href="#" class="btn btn-sm btn-outline-primary rounded-circle" style="width:32px;height:32px;padding:0;line-height:30px;"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="btn btn-sm btn-outline-primary rounded-circle" style="width:32px;height:32px;padding:0;line-height:30px;"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Testimonial -->
<div class="container-fluid bg-icon py-5">
    <div class="container">
        <div class="section-header text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width:500px;">
            <h4 class="text-primary text-uppercase">Témoignages</h4>
            <h2>Ce que disent nos clients</h2>
        </div>
        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
            <?php
            $testimonials = [
                ['img'=>'testimonial-1.jpg','name'=>'Sarah B.','role'=>'Cliente fidèle','text'=>'NutriSmart a complètement changé ma façon de manger. Les restaurants partenaires proposent des plats délicieux et équilibrés !'],
                ['img'=>'testimonial-2.jpg','name'=>'Mohamed K.','role'=>'Sportif','text'=>'Grâce aux programmes nutritionnels, j\'ai atteint mes objectifs en seulement 3 mois. Je recommande vivement !'],
                ['img'=>'testimonial-3.jpg','name'=>'Amira T.','role'=>'Nutritionniste','text'=>'Une plateforme sérieuse avec des informations nutritionnelles fiables. Parfait pour mes patients.'],
            ];
            foreach ($testimonials as $t):
            ?>
            <div class="testimonial-item bg-light rounded p-4">
                <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                <p><?= $t['text'] ?></p>
                <div class="d-flex align-items-center">
                    <img class="rounded-circle" src="/2A35/assets/img/<?= $t['img'] ?>" style="width:60px;height:60px;object-fit:cover;" alt="">
                    <div class="ms-3">
                        <h6 class="fw-bold mb-0"><?= $t['name'] ?></h6>
                        <small class="text-muted"><?= $t['role'] ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'View/front/partials/footer.php'; ?>
