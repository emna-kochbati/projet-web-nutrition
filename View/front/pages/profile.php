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

if($imc < 18.5) $etat = "Maigre";
elseif($imc < 25) $etat = "Normal";
elseif($imc < 30) $etat = "Surpoids";
else $etat = "Obésité";
?>

<style>
body{
    margin:0;
    font-family:Segoe UI;
    background:#070b14;
    color:white;
}

/* HERO WOW */
.hero{
    height:260px;
    background:url('https://images.unsplash.com/photo-1517838277536-f5f99be501cd') center/cover;
    position:relative;
    border-bottom-left-radius:40px;
    border-bottom-right-radius:40px;
}
.hero::after{
    content:'';
    position:absolute;
    inset:0;
    background:linear-gradient(180deg, rgba(0,0,0,0.2), #070b14);
}

/* PROFILE */
.profile{
    display:flex;
    align-items:center;
    gap:20px;
    padding:0 30px;
    margin-top:-70px;
}

.avatar{
    width:95px;
    height:95px;
    border-radius:50%;
    background:linear-gradient(135deg,#00ff88,#ffb300);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:30px;
    font-weight:bold;
    color:black;
    box-shadow:0 0 30px rgba(0,255,140,0.4);
}

/* NAME */
.name-box h2{
    margin:0;
    font-size:26px;
    font-weight:800;
    background:linear-gradient(90deg,#00ff88,#ffb300);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

/* BADGE */
.badge{
    background:linear-gradient(90deg,#00ff88,#ffb300);
    color:black;
    padding:6px 14px;
    border-radius:25px;
    font-weight:bold;
    font-size:12px;
    display:inline-block;
    margin-top:6px;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
    padding:30px;
}

/* CARD WOW BORDER */
.card{
    background:rgba(255,255,255,0.05);
    border-radius:20px;
    overflow:hidden;
    border:1px solid rgba(0,255,140,0.15);
    backdrop-filter:blur(18px);
    transition:0.3s;
    box-shadow:0 0 0 rgba(0,0,0,0);
}
.card:hover{
    transform:translateY(-8px) scale(1.02);
    border:1px solid rgba(0,255,140,0.4);
    box-shadow:0 0 25px rgba(0,255,140,0.15);
}

/* IMAGE */
.img{
    height:140px;
    background-size:cover;
    background-position:center;
    position:relative;
}
.img::after{
    content:'';
    position:absolute;
    inset:0;
    background:linear-gradient(to top, rgba(0,0,0,0.6), transparent);
}

/* CONTENT */
.content{
    padding:16px;
}

/* ===== NEW INFO WITH IMAGES ===== */

.icon-img{
    width:100%;
    height:120px;
    border-radius:12px;
    object-fit:cover;
    margin-bottom:10px;
    box-shadow:0 0 15px rgba(0,255,140,0.1);
}

/* BUTTON */
.btn{
    width:calc(100% - 60px);
    margin:0 30px 30px;
    padding:13px;
    border:none;
    border-radius:14px;
    font-weight:bold;
    cursor:pointer;
    background:linear-gradient(90deg,#00ff88,#ffb300);
}

/* FORM PANEL WOW */
.form-panel{
    position:fixed;
    top:0;
    right:-430px;
    width:400px;
    height:100%;
    background:rgba(255,255,255,0.07);
    backdrop-filter:blur(30px);
    border-left:1px solid rgba(0,255,140,0.2);
    padding:25px;
    transition:0.45s ease;
    overflow-y:auto;
    z-index:999;
}
.form-panel.active{
    right:0;
}

/* TITLE */
.form-title{
    font-size:20px;
    margin-bottom:18px;
    color:#ffb300;
}

/* SECTION GLASS */
.section{
    background:rgba(255,255,255,0.04);
    padding:14px;
    border-radius:14px;
    margin-bottom:14px;
    border:1px solid rgba(0,255,140,0.1);
}

/* LABEL */
label{
    font-size:11px;
    opacity:0.7;
    display:block;
    margin-top:10px;
}

/* INPUT */
input,select{
    width:100%;
    padding:12px;
    margin-top:6px;
    border-radius:12px;
    border:none;
    background:rgba(255,255,255,0.08);
    color:white;
}

/* BUTTONS */
.action-btn{
    width:100%;
    padding:13px;
    border:none;
    border-radius:14px;
    font-weight:bold;
    cursor:pointer;
    margin-top:12px;
}

.save{
    background:linear-gradient(90deg,#00ff88,#ffb300);
}

.close{
    background:rgba(255,60,60,0.2);
    border:1px solid rgba(255,60,60,0.4);
    color:#ff6b6b;
}

/* COLORS */
.green{color:#00ff88;}
.orange{color:#ffb300;}
</style>

<!-- HERO -->
<div class="hero"></div>

<!-- PROFILE -->
<div class="profile">

    <div class="avatar">
        <?= strtoupper(substr($user['nom'],0,1)) ?>
    </div>

    <div class="name-box">
        <h2><?= $user['nom'] ?></h2>
        <p class="green">🔥 Elite Fitness Dashboard</p>
        <span class="badge"><?= $user['objectif'] ?></span>
    </div>

</div>

<!-- DASHBOARD -->
<div class="grid">

    <div class="card">
        <img class="icon-img" src="https://images.unsplash.com/photo-1517838277536-f5f99be501cd">
        <div class="content">
            <h4 class="orange">🏋 Training Power</h4>
        </div>
    </div>

    <div class="card">
        <img class="icon-img" src="https://images.unsplash.com/photo-1554284126-aa88f22d8b74">
        <div class="content">
            <h4 class="green">🔥 Calories</h4>
            <p><?= ($user['poids'] * 24) ?> kcal</p>
        </div>
    </div>

    <div class="card">
        <img class="icon-img" src="https://images.unsplash.com/photo-1505751172876-fa1923c5c528">
        <div class="content">
            <h4 class="orange">⚖ IMC</h4>
            <p><?= round($imc,1) ?> - <?= $etat ?></p>
        </div>
    </div>

</div>

<!-- NEW INFO WOW -->
<div class="grid">

    <div class="card">
        <img class="icon-img" src="https://images.unsplash.com/photo-1558611848-73f7eb4001a1">
        <div class="content">
            <h4 class="green">💧 Hydratation</h4>
            <p><?= round($user['poids'] * 0.033,1) ?> L / jour</p>
        </div>
    </div>

    <div class="card">
        <img class="icon-img" src="https://images.unsplash.com/photo-1526506118085-60ce8714f8c5">
        <div class="content">
            <h4 class="green">🎯 Objectif</h4>
            <p><?= $user['objectif'] ?></p>
        </div>
    </div>

    <div class="card">
        <img class="icon-img" src="https://images.unsplash.com/photo-1554284115-5c0a8c3a8b0a">
        <div class="content">
            <h4 class="orange">📊 Score santé</h4>
            <p><?= min(100, round(($user['poids'] + $user['taille']) / 3)) ?>/100</p>
        </div>
    </div>

</div>

<!-- BUTTON -->
<button class="btn" onclick="openForm()">✏ Modifier mon profil</button>

<!-- FORM WOW -->
<div class="form-panel" id="formPanel">

    <div class="form-title">🧑 Profile Editor Pro</div>

    <form method="POST" action="/ProjetWeb-User/index.php?url=User/update">

        <div class="section">
            <label>Nom</label>
            <input name="nom" value="<?= $user['nom'] ?>">

            <label>Email</label>
            <input name="email" value="<?= $user['email'] ?>">

            <label>Password</label>
            <input name="password" placeholder="••••••">
        </div>

        <div class="section">
            <label>Age</label>
            <input name="age" value="<?= $user['age'] ?? '' ?>">

            <label>Poids</label>
            <input name="poids" value="<?= $user['poids'] ?>">

            <label>Taille</label>
            <input name="taille" value="<?= $user['taille'] ?>">
        </div>

        <div class="section">
            <label>Objectif</label>
            <select name="objectif">
                <option>Sportif</option>
                <option>Perte de poids</option>
                <option>Prise de masse</option>
            </select>
        </div>

        <button type="submit" class="action-btn save">💾 Save Profile</button>
        <button type="button" class="action-btn close" onclick="closeForm()">✖ Close</button>

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

<?php include __DIR__ . '/../partials/footer.php'; ?>