<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header -->
<div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <h1 class="display-4 text-white fw-bold animated slideInDown">Contactez-nous</h1>
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a class="text-white" href="/2A35/Home">Accueil</a></li>
                <li class="breadcrumb-item text-primary active">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Contact -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width:600px;">
            <div class="btn btn-sm border rounded-pill text-primary px-3 mb-3">Contactez-nous</div>
            <h1 class="mb-4">Nous sommes à votre écoute</h1>
            <p class="text-muted">Une question sur une recette, une suggestion ou un partenariat ? N'hésitez pas à nous écrire, nous vous répondrons dans les plus brefs délais.</p>
        </div>

        <div class="row g-5">
            <!-- Infos contact -->
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                <div class="bg-primary rounded p-4 h-100">

                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-white rounded-circle p-2 me-3" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-map-marker-alt text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-white mb-0">Adresse</h6>
                            <p class="text-white-50 mb-0 small">Tunis, Tunisie</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-white rounded-circle p-2 me-3" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-phone-alt text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-white mb-0">Téléphone</h6>
                            <p class="text-white-50 mb-0 small">+216 71 000 000</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-white rounded-circle p-2 me-3" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-envelope text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-white mb-0">Email</h6>
                            <p class="text-white-50 mb-0 small">recette@gmail.com</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle p-2 me-3" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-clock text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-white mb-0">Horaires</h6>
                            <p class="text-white-50 mb-0 small">Lun – Ven : 9h – 18h</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.3s">
                <form id="contactForm" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Votre nom <span class="text-danger">*</span></label>
                            <input type="text" id="cNom" class="form-control" placeholder="Ex : Ahmed Ben Ali">
                            <div class="invalid-feedback" id="errNom"></div>
                            <div class="valid-feedback">✔ Correct</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Votre email <span class="text-danger">*</span></label>
                            <input type="email" id="cEmail" class="form-control" placeholder="Ex : recette@gmail.com">
                            <div class="invalid-feedback" id="errEmail"></div>
                            <div class="valid-feedback">✔ Correct</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Sujet <span class="text-danger">*</span></label>
                            <input type="text" id="cSujet" class="form-control" placeholder="Ex : Question sur une recette">
                            <div class="invalid-feedback" id="errSujet"></div>
                            <div class="valid-feedback">✔ Correct</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Message <span class="text-danger">*</span></label>
                            <textarea id="cMessage" class="form-control" rows="6" placeholder="Votre message..."></textarea>
                            <div class="invalid-feedback" id="errMessage"></div>
                            <div class="valid-feedback">✔ Correct</div>
                        </div>
                        <div class="col-12">
                            <div id="alertSuccess" class="alert alert-success d-none">
                                ✅ Votre message a été envoyé avec succès ! Nous vous répondrons à <strong>recette@gmail.com</strong>.
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-3">
                                Envoyer le message <i class="fa fa-paper-plane ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validation en temps réel
function valEl(el, errId, testFn) {
    const msg = testFn(el.value.trim());
    if (msg) {
        el.classList.add('is-invalid'); el.classList.remove('is-valid');
        document.getElementById(errId).textContent = msg;
    } else {
        el.classList.remove('is-invalid'); el.classList.add('is-valid');
        document.getElementById(errId).textContent = '';
    }
    return !msg;
}

document.getElementById('cNom').addEventListener('input', function() {
    valEl(this, 'errNom', v => {
        if (!v) return 'Le nom est obligatoire.';
        if (v.length < 3) return 'Le nom doit contenir au moins 3 caractères.';
        if (/\d/.test(v)) return 'Le nom ne doit pas contenir de chiffres.';
        return '';
    });
});

document.getElementById('cEmail').addEventListener('input', function() {
    valEl(this, 'errEmail', v => {
        if (!v) return "L'email est obligatoire.";
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return 'Veuillez entrer un email valide.';
        return '';
    });
});

document.getElementById('cSujet').addEventListener('input', function() {
    valEl(this, 'errSujet', v => {
        if (!v) return 'Le sujet est obligatoire.';
        if (v.length < 5) return 'Le sujet doit contenir au moins 5 caractères.';
        return '';
    });
});

document.getElementById('cMessage').addEventListener('input', function() {
    valEl(this, 'errMessage', v => {
        if (!v) return 'Le message est obligatoire.';
        if (v.length < 10) return 'Le message doit contenir au moins 10 caractères.';
        return '';
    });
});

document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const ok = [
        valEl(document.getElementById('cNom'),     'errNom',     v => !v ? 'Obligatoire.' : v.length < 3 ? 'Min 3 caractères.' : /\d/.test(v) ? 'Pas de chiffres.' : ''),
        valEl(document.getElementById('cEmail'),   'errEmail',   v => !v ? 'Obligatoire.' : !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? 'Email invalide.' : ''),
        valEl(document.getElementById('cSujet'),   'errSujet',   v => !v ? 'Obligatoire.' : v.length < 5 ? 'Min 5 caractères.' : ''),
        valEl(document.getElementById('cMessage'), 'errMessage', v => !v ? 'Obligatoire.' : v.length < 10 ? 'Min 10 caractères.' : ''),
    ].every(Boolean);

    if (ok) {
        document.getElementById('alertSuccess').classList.remove('d-none');
        this.reset();
        document.querySelectorAll('.is-valid').forEach(el => el.classList.remove('is-valid'));
    }
});
</script>

<?php include 'View/front/partials/footer.php'; ?>
