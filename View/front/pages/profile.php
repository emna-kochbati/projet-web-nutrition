<?php 
session_start();

$user = $_SESSION['user'] ?? null;

if(!$user){
    header("Location: /ProjetWeb-User/User/auth");
    exit;
}

include __DIR__ . '/../partials/header.php';

$imc = ($user['poids'] > 0 && $user['taille'] > 0)
    ? $user['poids'] / (($user['taille']/100)**2)
    : 0;

/* INTERPRETATION IMC */
if($imc < 18.5) $etat = "Maigre";
elseif($imc < 25) $etat = "Normal";
elseif($imc < 30) $etat = "Surpoids";
else $etat = "Obésité";
?>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: radial-gradient(circle at top,#0a0f1c,#020617);
    color:white;
}

/* CONTAINER */
.container{
    padding:40px;
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.profile{
    display:flex;
    align-items:center;
    gap:20px;
}

.avatar{
    width:100px;
    height:100px;
    border-radius:50%;
    background:linear-gradient(135deg,#00ff88,#ffb300);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:35px;
    font-weight:bold;
    color:black;
    box-shadow:0 0 25px rgba(0,255,140,0.4);
}

/* BADGE */
.badge{
    background:linear-gradient(90deg,#00ff88,#ffb300);
    color:black;
    padding:10px 20px;
    border-radius:30px;
    font-weight:bold;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.05);
    padding:20px;
    border-radius:18px;
    border:1px solid rgba(255,255,255,0.08);
    backdrop-filter: blur(15px);
    transition:0.3s;
    position:relative;
}

.card:hover{
    transform:translateY(-8px);
    box-shadow:0 0 30px rgba(0,255,140,0.2);
}

/* COLORS */
.green{ color:#00ff88; }
.orange{ color:#ffb300; }

/* BIG CARD */
.big{ grid-column:span 2; }

/* FULL */
.full{ grid-column:span 4; }

/* IMG */
img{
    width:100%;
    border-radius:12px;
    margin-top:10px;
}

/* PROGRESS BAR */
.progress{
    height:12px;
    background:rgba(255,255,255,0.1);
    border-radius:20px;
    overflow:hidden;
    margin-top:10px;
}

.progress-bar{
    height:100%;
    background:linear-gradient(90deg,#00ff88,#ffb300);
    width:<?= min(100, $imc*4) ?>%;
    box-shadow:0 0 10px #00ff88;
}

/* BADGES */
.badges span{
    display:inline-block;
    padding:6px 12px;
    margin:5px;
    border-radius:20px;
    background:rgba(255,255,255,0.1);
}

/* FORM */
input{
    width:100%;
    padding:10px;
    margin:6px 0;
    border-radius:10px;
    border:none;
    outline:none;
}

/* BUTTON */
button{
    background:linear-gradient(90deg,#00ff88,#ffb300);
    border:none;
    padding:10px;
    width:100%;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
}

/* STATS */
.stat{
    font-size:32px;
    font-weight:bold;
}
</style>

<div class="container">

    <!-- HEADER -->
    <div class="header">

        <div class="profile">
            <div class="avatar">
                <?= strtoupper(substr($user['nom'],0,1)) ?>
            </div>

            <div>
                <h2><?= $user['nom'] ?></h2>
                <p class="green">Utilisateur EcoNutri</p>
            </div>
        </div>

        <div class="badge">🔥 Profil MAX</div>
    </div>

    <!-- PROGRESS GLOBAL -->
    <div class="card full">
        <h3 class="green">🌿 Progression Santé</h3>
        <p>Ton niveau global basé sur ton IMC</p>

        <div class="progress">
            <div class="progress-bar"></div>
        </div>
    </div>

    <!-- GRID -->
    <div class="grid">

        <!-- IMC -->
        <div class="card">
            <h3 class="orange">⚖️ IMC</h3>
            <div class="stat"><?= round($imc,1) ?></div>
            <p><?= $etat ?></p>
        </div>

        <!-- CALORIES -->
        <div class="card">
            <h3 class="green">🔥 Calories</h3>
            <div class="stat"><?= ($user['objectif']=='Perte de poids') ? '1600' : '2200' ?></div>
        </div>

        <!-- OBJECTIF -->
        <div class="card">
            <h3 class="orange">🎯 Objectif</h3>
            <p><?= $user['objectif'] ?></p>
        </div>

        <!-- EMAIL -->
        <div class="card">
            <h3 class="green">📧 Email</h3>
            <p><?= $user['email'] ?></p>
        </div>

        <!-- IMAGE SPORT -->
        <div class="card big">
            <h3 class="orange">🏋️ Activité</h3>
            <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438">
        </div>

        <!-- IMAGE FOOD -->
        <div class="card big">
            <h3 class="green">🥗 Nutrition</h3>
            <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c">
        </div>

        <!-- BADGES -->
        <div class="card">
            <h3 class="orange">🏆 Badges</h3>
            <div class="badges">
                <span>Débutant</span>
                <span>Motivé</span>
                <span>Healthy</span>
            </div>
        </div>

        <!-- CONSEIL -->
        <div class="card">
            <h3 class="green">💡 Conseil</h3>
            <p>Hydratation + sommeil = performance 💧</p>
        </div>

        <!-- FORM UPDATE -->
        <div class="card full">
            <h3 class="orange">✏️ Modifier Profil</h3>

            <form method="POST" action="/ProjetWeb-User/index.php?url=User/update">

    <input type="text" name="nom" value="<?= $user['nom'] ?>" required>
    <input type="email" name="email" value="<?= $user['email'] ?>" required>
    <input type="number" name="poids" value="<?= $user['poids'] ?>">
    <input type="number" name="taille" value="<?= $user['taille'] ?>">

    <button type="submit">Mettre à jour</button>

</form>
                
        </div>

    </div>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>