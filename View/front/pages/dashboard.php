<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /ProjetWeb-User/index.php?url=User/auth");
    exit;
}

$user = $_SESSION['user'];

include __DIR__ . '/../partials/header.php';

// Calcul IMC
$imc = ($user['poids'] > 0 && $user['taille'] > 0)
    ? $user['poids'] / pow(($user['taille']/100), 2)
    : 0;
?>

<!-- HEADER (même style que ancien code) -->
<div class="container-fluid page-header py-5 mb-5"
     style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('/2A35/assets/img/carousel-1.jpg') center/cover no-repeat;">
    <div class="container py-5">
        <h1 class="display-4 text-white fw-bold mb-3">
            👋 Bonjour <?= htmlspecialchars($user['nom']) ?>
        </h1>
        <p class="text-white">Bienvenue dans votre dashboard nutrition</p>
    </div>
</div>

<!-- DASHBOARD -->
<div class="container mb-5">

    <div class="row g-4">

        <!-- IMC -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">

                <div style="height:220px; overflow:hidden;">
                    <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b"
                         class="w-100 h-100" style="object-fit:cover;">
                </div>

                <div class="card-body text-center p-4">
                    <h5 class="fw-bold">⚖️ IMC</h5>
                    <h2 class="text-primary"><?= round($imc,1) ?></h2>
                </div>

            </div>
        </div>

        <!-- OBJECTIF -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">

                <div style="height:220px; overflow:hidden;">
                    <img src="https://images.unsplash.com/photo-1554284126-aa88f22d8b74"
                         class="w-100 h-100" style="object-fit:cover;">
                </div>

                <div class="card-body text-center p-4">
                    <h5 class="fw-bold">🎯 Objectif</h5>
                    <p><?= htmlspecialchars($user['objectif']) ?></p>
                </div>

            </div>
        </div>

        <!-- CALORIES -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">

                <div style="height:220px; overflow:hidden;">
                    <img src="https://images.unsplash.com/photo-1505575967455-40e256f73376"
                         class="w-100 h-100" style="object-fit:cover;">
                </div>

                <div class="card-body text-center p-4">
                    <h5 class="fw-bold">🔥 Calories</h5>
                    <h2 class="text-danger">
                        <?= ($user['objectif']=='Perte de poids') ? '1600' : '2200' ?> kcal
                    </h2>
                </div>

            </div>
        </div>

        <!-- ALERTE -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">

                <div style="height:220px; overflow:hidden;">
                    <img src="/ProjetWeb-User/assets/img/alert.png"
                         class="w-100 h-100" style="object-fit:cover;">
                </div>

                <div class="card-body text-center p-4">
                    <h5 class="fw-bold">⚠️ Alerte</h5>
                    <p>Évite sucre et fast-food aujourd'hui</p>
                </div>

            </div>
        </div>

        <!-- CONSEIL -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">

                <div style="height:220px; overflow:hidden;">
                    <img src="https://images.unsplash.com/photo-1552674605-db6ffd4facb5"
                         class="w-100 h-100" style="object-fit:cover;">
                </div>

                <div class="card-body text-center p-4">
                    <h5 class="fw-bold">💡 Conseil</h5>
                    <p>Boire 2L d’eau + sport régulier</p>
                </div>

            </div>
        </div>

        <!-- SPORT -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">

                <div style="height:220px; overflow:hidden;">
                    <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438"
                         class="w-100 h-100" style="object-fit:cover;">
                </div>

                <div class="card-body text-center p-4">
                    <h5 class="fw-bold">🏋️ Sport</h5>
                    <p>3 séances / semaine</p>
                </div>

            </div>
        </div>

    </div>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>