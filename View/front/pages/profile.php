<?php include 'View/front/partials/header.php'; ?>

<?php
$user = $_SESSION['user'] ?? null;

if (!$user) {
    header("Location: /ProjetWeb-User/index.php?url=User/auth");
    exit;
}

$imc = ($user['poids'] > 0 && $user['taille'] > 0)
    ? $user['poids'] / pow(($user['taille']/100), 2)
    : 0;
?>

<!-- STYLE WOW -->
<style>
/* CARDS WOW */
.profile-card{
    background: linear-gradient(135deg,#2e7d32,#66bb6a);
    color:white;
    border-radius:20px;
    padding:30px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
    position:relative;
    overflow:hidden;
}

.profile-card::before{
    content:'';
    position:absolute;
    top:-50px;
    right:-50px;
    width:150px;
    height:150px;
    background:rgba(255,255,255,0.1);
    border-radius:50%;
}

.avatar{
    width:80px;
    height:80px;
    border-radius:50%;
    background:white;
    color:#2e7d32;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
    font-weight:bold;
    margin:auto;
    margin-bottom:15px;
}

/* INFO CARDS */
.info-card{
    border-radius:18px;
    overflow:hidden;
    transition:0.4s;
    cursor:pointer;
}

.info-card:hover{
    transform:translateY(-8px) scale(1.03);
    box-shadow:0 15px 35px rgba(0,0,0,0.2);
}

/* IMAGE HEADER */
.card-img-top{
    height:120px;
    object-fit:cover;
}

/* ICON STYLE */
.icon-box{
    font-size:22px;
    margin-bottom:5px;
}

/* COLORS */
.green{ color:#2e7d32; }
.orange{ color:#ff9800; }
.blue{ color:#2196f3; }
.red{ color:#f44336; }
</style>

<!-- HEADER -->
<div class="container-fluid page-header py-5 mb-5 wow fadeIn"
     style="background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url('/2A35/assets/img/carousel-2.jpg') center/cover no-repeat;">
    <div class="container py-5">
        <h1 class="display-5 text-white fw-bold mb-3">Profil Utilisateur</h1>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-5">

        <!-- PROFIL WOW -->
        <div class="col-lg-5">
            <div class="profile-card">

                <div class="avatar">
                    <?= strtoupper(substr($user['nom'],0,1)) ?>
                </div>

                <h3 class="fw-bold"><?= htmlspecialchars($user['nom']) ?></h3>
                <p><?= htmlspecialchars($user['email']) ?></p>

                <span class="badge bg-light text-dark px-3 py-2">
                    <?= htmlspecialchars($user['objectif']) ?>
                </span>

            </div>
        </div>

        <!-- INFOS WOW -->
        <div class="col-lg-7">
            <div class="row g-4">

                <!-- AGE -->
                <div class="col-md-6">
                    <div class="card info-card border-0">
                        <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438" class="card-img-top">
                        <div class="card-body text-center">
                            <div class="icon-box blue"><i class="fa fa-user"></i></div>
                            <h6>Age</h6>
                            <h4><?= $user['age'] ?></h4>
                        </div>
                    </div>
                </div>

                <!-- POIDS -->
                <div class="col-md-6">
                    <div class="card info-card border-0">
                        <img src="https://images.unsplash.com/photo-1554284126-aa88f22d8b74" class="card-img-top">
                        <div class="card-body text-center">
                            <div class="icon-box red"><i class="fa fa-weight"></i></div>
                            <h6>Poids</h6>
                            <h4><?= $user['poids'] ?> kg</h4>
                        </div>
                    </div>
                </div>

                <!-- TAILLE -->
                <div class="col-md-6">
                    <div class="card info-card border-0">
                        <img src="https://images.unsplash.com/photo-1505751172876-fa1923c5c528" class="card-img-top">
                        <div class="card-body text-center">
                            <div class="icon-box orange"><i class="fa fa-ruler-vertical"></i></div>
                            <h6>Taille</h6>
                            <h4><?= $user['taille'] ?> cm</h4>
                        </div>
                    </div>
                </div>

                <!-- IMC -->
                <div class="col-md-6">
                    <div class="card info-card border-0">
                        <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352" class="card-img-top">
                        <div class="card-body text-center">
                            <div class="icon-box green"><i class="fa fa-heartbeat"></i></div>
                            <h6>IMC</h6>
                            <h4><?= round($imc,1) ?></h4>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- BUTTON -->
    <div class="text-center mt-5">
        <button class="btn btn-success px-4 py-2 shadow" onclick="openForm()">
            ✏ Modifier mon profil
        </button>
    </div>
</div>

<!-- ⚠️ FORMULAIRE = EXACTEMENT TON CODE (NON TOUCHÉ) -->
<style>
.form-panel{
    position:fixed;
    top:0;
    right:-400px;
    width:380px;
    height:100%;
    background:white;
    box-shadow:-5px 0 20px rgba(0,0,0,0.2);
    padding:25px;
    transition:0.4s;
    z-index:9999;
    overflow-y:auto;
}
.form-panel.active{ right:0; }

.form-title{
    font-size:20px;
    font-weight:bold;
    color:#2e7d32;
    margin-bottom:20px;
}

.section{ margin-bottom:15px; }

input, select{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #ddd;
    margin-top:5px;
}

input:focus, select:focus{
    border-color:#2e7d32;
    box-shadow:0 0 5px rgba(46,125,50,0.4);
}

.action-btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    font-weight:bold;
    margin-top:10px;
}

.save{ background:#2e7d32; color:white; }
.close{ background:#ddd; }
</style>

<div class="form-panel" id="formPanel">

    <div class="form-title">Modifier profil</div>

    <form method="POST" action="/ProjetWeb-User/index.php?url=User/update">

        <div class="section">
            <label>Nom</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>">

            <label>Email</label>
            <input type="text" name="email" value="<?= htmlspecialchars($user['email']) ?>">

            <label>Password</label>
            <input type="password" name="password" placeholder="••••••">
        </div>

        <div class="section">
            <label>Age</label>
            <input type="text" name="age" value="<?= htmlspecialchars($user['age'] ?? '') ?>">

            <label>Poids</label>
            <input type="text" name="poids" value="<?= htmlspecialchars($user['poids']) ?>">

            <label>Taille</label>
            <input type="text" name="taille" value="<?= htmlspecialchars($user['taille']) ?>">
        </div>

        <div class="section">
            <label>Objectif</label>
            <select name="objectif">
                <option <?= $user['objectif']=="Sportif" ? "selected":"" ?>>Sportif</option>
                <option <?= $user['objectif']=="Perte de poids" ? "selected":"" ?>>Perte de poids</option>
                <option <?= $user['objectif']=="Prise de masse" ? "selected":"" ?>>Prise de masse</option>
            </select>
        </div>

        <button type="submit" class="action-btn save">💾 Enregistrer</button>
        <button type="button" class="action-btn close" onclick="closeForm()">✖ Fermer</button>

    </form>

</div>

<script>
function openForm(){
    document.getElementById("formPanel").classList.add("active");
}
function closeForm(){
    document.getElementById("formPanel").classList.remove("active");
}
</script>

<?php include 'View/front/partials/footer.php'; ?>