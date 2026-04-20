<?php 


$user = $_SESSION['user'] ?? null;



if(!$user){
    header("Location: /ProjetWeb-User/User/auth");
    exit;
}

include __DIR__ . '/../partials/header.php';

$imc = ($user['poids'] > 0 && $user['taille'] > 0)
    ? $user['poids'] / (($user['taille']/100)**2)
    : 0;

$progress = 75;
?>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: radial-gradient(circle at top,#0a0f1c,#020617);
    color:white;
}

.container{ padding:40px; }

/* HEADER */
.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.badge{
    background: linear-gradient(90deg,#00ff88,#ffb300);
    padding:10px 18px;
    border-radius:30px;
    font-weight:bold;
    color:black;
}

/* PROGRESS BAR */
.progress-wrapper{
    margin-bottom:25px;
}

.progress-bg{
    width:100%;
    height:20px;
    background:rgba(255,255,255,0.08);
    border-radius:30px;
    overflow:hidden;
}

.progress-fill{
    width:<?= $progress ?>%;
    height:100%;
    background: linear-gradient(90deg,#00ff88,#ffb300);
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
}

/* CARD UNIFORM SIZE */
.card{
    height:260px;
    background: rgba(255,255,255,0.06);
    padding:15px;
    border-radius:18px;
    border:2px solid transparent;
    transition:0.3s;
    overflow:hidden;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}

.card:hover{
    transform:translateY(-6px);
    box-shadow:0 0 25px rgba(0,255,140,0.15);
}

/* ALTERNATE COLORS */
.green-border{
    border-color:#00ff88;
}

.orange-border{
    border-color:#ffb300;
}
/* FORCE TEXT WHITE CLEAN */
p, span, small, h1, h2, h3, h4, h5, div{
    color:white;}

/* TEXT COLORS */
.green{ color:#00ff88; }
.orange{ color:#ffb300; }

h1{ font-size:34px; margin:5px 0; }

.icon{ font-size:22px; }

/* IMAGE FIX */
img{
    width:100%;
    height:120px;
    object-fit:cover;
    border-radius:12px;
    margin-top:8px;
}
</style>

<div class="container">

    <!-- HEADER -->
    <div class="top">
        <h2>👋 Bonjour <span class="green"><?= $user['nom'] ?></span></h2>
        <div class="badge">🔥 EcoNutri Dashboard</div>
    </div>

    <!-- PROGRESS -->
    <div class="progress-wrapper">
        <div class="progress-bg">
            <div class="progress-fill"></div>
        </div>
    </div>

    <!-- GRID -->
    <div class="grid">

        <!-- IMC -->
        <div class="card green-border">
            <div class="icon">⚖️</div>
            <h3>IMC</h3>
            <h1><?= round($imc,1) ?></h1>
            <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b">
        </div>

        <!-- OBJECTIF -->
        <div class="card orange-border">
            <div class="icon">🎯</div>
            <h3>Objectif</h3>
            <p><?= $user['objectif'] ?></p>
            <img src="https://images.unsplash.com/photo-1554284126-aa88f22d8b74">
        </div>

        <!-- CALORIES -->
        <div class="card green-border">
            <div class="icon">🔥</div>
            <h3>Calories</h3>
            <h1><?= ($user['objectif']=='Perte de poids') ? '1600' : '2200' ?></h1>
            <img src="https://images.unsplash.com/photo-1505575967455-40e256f73376">
        </div>

        <div class="card orange-border">
    <div class="icon">⚠️</div>
    <h3>Alerte</h3>
    <p>Évite sucre et fast-food aujourd'hui</p>

    <img src="/ProjetWeb-User/assets/img/alert.png">
</div>

        <!-- CONSEIL -->
        <div class="card green-border">
            <div class="icon">💡</div>
            <h3>Conseil</h3>
            <p>Boire 2L d’eau + sport régulier</p>
            <img src="https://images.unsplash.com/photo-1552674605-db6ffd4facb5">
        </div>

        <!-- SPORT -->
        <div class="card orange-border">
            <div class="icon">🏋️</div>
            <h3>Sport</h3>
            <p>3 séances / semaine</p>
            <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438">
        </div>

    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>