<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header -->
<div class="page-header wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-5 text-white mb-3 animated slideInDown">Témoignages</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a class="text-white" href="/2A35/Home">Accueil</a></li>
                        <li class="breadcrumb-item text-primary active">Témoignages</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials Grid -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="section-header text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width:500px;">
            <h4 class="text-primary text-uppercase">Avis clients</h4>
            <h2>Ce que disent nos utilisateurs</h2>
        </div>
        <div class="row g-4">
            <?php
            $testimonials = [
                ['img'=>'testimonial-1.jpg','name'=>'Sarah Ben Ali',    'role'=>'Cliente fidèle',    'stars'=>5, 'text'=>'NutriSmart a complètement changé ma façon de manger. Les restaurants partenaires proposent des plats délicieux et vraiment équilibrés. Je me sens en pleine forme !'],
                ['img'=>'testimonial-2.jpg','name'=>'Mohamed Karim',    'role'=>'Sportif amateur',   'stars'=>5, 'text'=>'Grâce aux programmes nutritionnels, j\'ai atteint mes objectifs en seulement 3 mois. Les recettes sont faciles à suivre et très savoureuses.'],
                ['img'=>'testimonial-3.jpg','name'=>'Amira Trabelsi',   'role'=>'Nutritionniste',    'stars'=>5, 'text'=>'En tant que professionnelle de la santé, je recommande NutriSmart à mes patients. Les informations nutritionnelles sont fiables et bien présentées.'],
                ['img'=>'testimonial-4.jpg','name'=>'Karim Laabidi',    'role'=>'Entrepreneur',      'stars'=>4, 'text'=>'Le service est impeccable. J\'utilise NutriSmart chaque jour pour trouver des restaurants sains près de mon bureau. Gain de temps énorme !'],
                ['img'=>'testimonial-1.jpg','name'=>'Nadia Mansour',    'role'=>'Mère de famille',   'stars'=>5, 'text'=>'Grâce aux recettes NutriSmart, toute ma famille mange mieux. Mes enfants adorent les plats que je prépare maintenant. Merci !'],
                ['img'=>'testimonial-2.jpg','name'=>'Yassine Gharbi',   'role'=>'Étudiant',          'stars'=>4, 'text'=>'Même avec un petit budget, je trouve des options saines et délicieuses. L\'application est intuitive et les filtres sont très pratiques.'],
            ];
            foreach ($testimonials as $i => $t):
            ?>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="<?= 0.1 * (($i % 3) + 1) ?>s">
                <div class="bg-light rounded p-4 h-100 d-flex flex-column">
                    <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                    <p class="text-muted flex-grow-1"><?= $t['text'] ?></p>
                    <!-- Étoiles -->
                    <div class="mb-3">
                        <?php for ($s = 1; $s <= 5; $s++): ?>
                            <i class="fa fa-star <?= $s <= $t['stars'] ? 'text-warning' : 'text-muted' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <div class="d-flex align-items-center">
                        <img src="/2A35/assets/img/<?= $t['img'] ?>" class="rounded-circle me-3"
                             style="width:55px;height:55px;object-fit:cover;" alt="<?= $t['name'] ?>">
                        <div>
                            <h6 class="fw-bold mb-0"><?= $t['name'] ?></h6>
                            <small class="text-primary"><?= $t['role'] ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- CTA -->
<div class="container-fluid bg-icon py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center wow fadeInUp" data-wow-delay="0.1s">
                <div class="section-header mx-auto mb-4" style="max-width:500px;">
                    <h4 class="text-primary text-uppercase">Rejoignez-nous</h4>
                    <h2>Partagez votre expérience</h2>
                </div>
                <p class="text-muted mb-4">Vous avez utilisé NutriSmart ? Votre avis compte ! Contactez-nous pour partager votre témoignage.</p>
                <a href="/2A35/Contact" class="btn btn-primary py-3 px-5">Nous écrire</a>
            </div>
        </div>
    </div>
</div>

<?php include 'View/front/partials/footer.php'; ?>
