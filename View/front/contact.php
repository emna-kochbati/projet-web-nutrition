<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header -->
<div class="page-header wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-5 text-white mb-3 animated slideInDown">Contactez-nous</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a class="text-white" href="/2A35/Home">Accueil</a></li>
                        <li class="breadcrumb-item text-primary active">Contact</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Contact Info Cards -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="text-center bg-light rounded p-4 h-100">
                    <div class="btn-square bg-primary rounded-circle mx-auto mb-3" style="width:60px;height:60px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa fa-map-marker-alt text-white fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Notre adresse</h5>
                    <p class="text-muted mb-0">123 Rue de la Santé<br>Tunis, Tunisie 1000</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="text-center bg-light rounded p-4 h-100">
                    <div class="btn-square bg-primary rounded-circle mx-auto mb-3" style="width:60px;height:60px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa fa-phone text-white fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Téléphone</h5>
                    <p class="text-muted mb-0">+216 71 000 000<br>+216 98 000 000</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="text-center bg-light rounded p-4 h-100">
                    <div class="btn-square bg-primary rounded-circle mx-auto mb-3" style="width:60px;height:60px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa fa-envelope text-white fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Email</h5>
                    <p class="text-muted mb-0">contact@nutrismart.tn<br>support@nutrismart.tn</p>
                </div>
            </div>
        </div>

        <!-- Form + Map -->
        <div class="row g-5">
            <!-- Formulaire -->
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="section-header text-start mb-4">
                    <h4 class="text-primary text-uppercase">Écrivez-nous</h4>
                    <h2>Envoyez un message</h2>
                </div>

                <?php if (!empty($_GET['sent'])): ?>
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                    <i class="fa fa-check-circle"></i>
                    Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.
                </div>
                <?php endif; ?>

                <form method="POST" action="/2A35/Contact/send" id="contactForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Votre nom"
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                                <label for="name">Votre nom</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Votre email"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                <label for="email">Votre email</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Sujet"
                                       value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                                <label for="subject">Sujet</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="message" name="message" placeholder="Votre message"
                                          style="height:150px;" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                                <label for="message">Votre message</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary w-100 py-3" type="submit">
                                <i class="fa fa-paper-plane me-2"></i>Envoyer le message
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Carte / Horaires -->
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="section-header text-start mb-4">
                    <h4 class="text-primary text-uppercase">Horaires</h4>
                    <h2>Nous sommes disponibles</h2>
                </div>
                <div class="bg-light rounded p-4 mb-4">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="fw-semibold">Lundi – Vendredi</span>
                        <span class="text-primary">08h00 – 18h00</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="fw-semibold">Samedi</span>
                        <span class="text-primary">09h00 – 14h00</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="fw-semibold">Dimanche</span>
                        <span class="text-muted">Fermé</span>
                    </div>
                </div>

                <!-- Carte Google Maps -->
                <div class="rounded overflow-hidden" style="height:280px;">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3193.5!2d10.1815!3d36.8065!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzbCsDQ4JzIzLjQiTiAxMMKwMTAnNTMuNCJF!5e0!3m2!1sfr!2stn!4v1"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'View/front/partials/footer.php'; ?>
