<?php
require_once __DIR__ . '/../../../Model/User.php';

$userModel = new User();
$users = $userModel->getAll();

/* GROUP BY OBJECTIF */
$grouped = [
    "Sportif" => [],
    "Perte de poids" => [],
    "Prise de masse" => [],
    "Maladie" => [],
    "Autre" => []
];

foreach($users as $u){
    $cat = $u['objectif'] ?? "Autre";

    if(!isset($grouped[$cat])){
        $grouped[$cat] = [];
    }

    $grouped[$cat][] = $u;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin Users - EcoNutri</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<!-- ================= SIDEBAR ORIGINAL (INCHANGÉ) ================= -->
<style>
<?php include "sidebar-style-only.css"; ?>

.content-area{
    margin-left:220px;
}
</style>

<!-- ================= UI PFE ================= -->
<style>
body{
    margin:0;
    background: radial-gradient(circle at top,#0b1220,#020617);
    color:white;
    font-family:Arial;
}

/* TITLE */
.page-title{
    font-size:24px;
    font-weight:bold;
    margin-bottom:20px;
}

/* WOW FORM ONLY (AMÉLIORÉ SANS TOUCHER SIDEBAR) */
.form-box{
    background: linear-gradient(135deg, rgba(0,255,120,0.08), rgba(255,255,255,0.03));
    border:1px solid rgba(0,255,120,0.2);
    padding:22px;
    border-radius:16px;
    margin-bottom:25px;
    box-shadow:0 0 20px rgba(0,255,120,0.1);
}

.form-title{
    color:#00e676;
    font-weight:bold;
    margin-bottom:5px;
}

.form-sub{
    font-size:12px;
    opacity:0.7;
    margin-bottom:15px;
}
/* TEXT INPUT COLOR */
.form-control{
    color: white !important;
}

/* PLACEHOLDER COLOR */
.form-control::placeholder{
    color: rgba(255,255,255,0.6) !important;
}

/* SELECT TEXT */
select.form-control{
    color: white !important;
}

/* OPTION COLOR (important aussi) */
select option{
    color: black; /* options restent lisibles */
}

/* INPUTS */
.form-control{
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    color:white;
    border-radius:10px;
}

.form-control:focus{
    background:rgba(0,0,0,0.3);
    color:white;
    border-color:#00e676;
    box-shadow:0 0 10px rgba(0,255,120,0.2);
}

/* BUTTON */
.btn-wow{
    width:100%;
    padding:12px;
    border:none;
    border-radius:12px;
    background: linear-gradient(90deg,#00e676,#00c853);
    font-weight:bold;
    color:black;
    transition:0.3s;
}

.btn-wow:hover{
    transform:scale(1.02);
}

/* CATEGORY */
.category-box{
    margin-bottom:25px;
}

.cat-title{
    font-size:18px;
    color:#00e676;
    margin-bottom:10px;
}

/* SCROLL */
.user-list{
    display:flex;
    overflow-x:auto;
    gap:15px;
    padding:10px;
}

/* CARD */
.user-card{
    min-width:240px;
    background:rgba(255,255,255,0.06);
    padding:15px;
    border-radius:15px;
    border:1px solid rgba(255,255,255,0.08);
    transition:0.3s;
}

.user-card:hover{
    transform:translateY(-6px);
    box-shadow:0 0 20px rgba(0,255,120,0.3);
}

/* BUTTONS */
.btn-icon{
    border:none;
    padding:7px 10px;
    border-radius:8px;
}

.view{background:#2196f3;color:white;}
.edit{background:#ffb300;color:white;}
.delete{background:#ef5350;color:white;}

.actions{
    margin-top:10px;
    display:flex;
    gap:6px;
}
/* MODAL FIX PROPRE */
.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.75);
    justify-content:center;
    align-items:center;
    z-index:9999;
}

/* IMPORTANT : quand modal est visible */
.modal.show{
    display:flex !important;
}

/* CONTENU BIEN LISIBLE */
.modal-content{
    background:#111;
    color:white;
    padding:20px;
    border-radius:15px;
    width:420px;
}

/* INPUTS lisibles dans le modal */
.modal-content .form-control{
    background:rgba(255,255,255,0.08);
    color:white !important;
    border:1px solid rgba(255,255,255,0.2);
}

.modal-content .form-control:focus{
    background:rgba(0,0,0,0.4);
    color:white !important;
}
</style>
</head>

<body>

<!-- ================= SIDEBAR (TON CODE EXACT) ================= -->
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<!-- ================= CONTENT ================= -->
<div class="content-area p-4">

<div class="page-title">👥 Gestion des Utilisateurs</div>

<!-- ================= FORM WOW ================= -->
<div class="form-box">

<div class="form-title">➕ Ajouter un utilisateur</div>
<div class="form-sub">Créer un profil EcoNutri intelligent</div>

<form method="POST" action="/ProjetWeb-User/Admin/addUser">

    <div class="row g-2">

        <div class="col-md-6">
            <input name="nom" class="form-control" placeholder="Nom complet">
        </div>

        <div class="col-md-6">
            <input name="email" class="form-control" placeholder="Email">
        </div>

        <div class="col-md-6">
            <input type="password" name="password" class="form-control" placeholder="Mot de passe">
        </div>

        <div class="col-md-6">
            <select name="objectif" class="form-control">
                <option>Sportif</option>
                <option>Perte de poids</option>
                <option>Prise de masse</option>
                <option>Maladie</option>
            </select>
        </div>

        <div class="col-md-6">
            <input name="poids" class="form-control" placeholder="Poids">
        </div>

        <div class="col-md-6">
            <input name="taille" class="form-control" placeholder="Taille">
        </div>

    </div>

    <button class="btn-wow mt-3">🚀 Ajouter utilisateur</button>

</form>
</div>

<!-- ================= USERS ================= -->
<?php foreach($grouped as $category => $list){ ?>

<div class="category-box">

    <div class="cat-title">🔥 <?= $category ?> (<?= count($list) ?>)</div>

    <div class="user-list">

        <?php foreach($list as $u){ ?>

        <div class="user-card">

            <strong><?= $u['nom'] ?></strong>
            <div style="font-size:13px"><?= $u['email'] ?></div>

            <div style="font-size:13px;margin-top:5px;">
                ⚖ <?= $u['poids'] ?? '-' ?>kg | 📏 <?= $u['taille'] ?? '-' ?>cm
            </div>

            <div class="actions">

                <button class="btn-icon view"
        onclick="openUser(<?= $u['id'] ?>,'view')">
    <i class="fa fa-eye"></i>
</button>

<button class="btn-icon edit"
        onclick="openUser(<?= $u['id'] ?>,'edit')">
    <i class="fa fa-pen"></i>
</button>

                <a href="/ProjetWeb-User/Admin/deleteUser/<?= $u['id'] ?>"
                   class="btn-icon delete"
                   onclick="return confirm('Supprimer ?')">
                    <i class="fa fa-trash"></i>
                </a>

            </div>

        </div>

        <?php } ?>

    </div>
</div>

<?php } ?>

</div>
<!-- ================= MODAL ================= -->
<div id="userModal" class="modal" style="display:none;">
    <div class="modal-content">

        <h3 id="modalTitle">User</h3>

        <form id="userForm" method="POST" onsubmit="return validateForm()">

            <input type="hidden" id="uid" name="id">

            <input class="form-control mt-2" id="nom" name="nom" placeholder="Nom complet">
            <small id="err_nom" style="color:red"></small>

            <input class="form-control mt-2" id="email" name="email" placeholder="Email">
            <small id="err_email" style="color:red"></small>

            <input class="form-control mt-2" id="poids" name="poids" placeholder="Poids">

            <input class="form-control mt-2" id="taille" name="taille" placeholder="Taille">

            <select class="form-control mt-2" id="objectif" name="objectif">
                <option>Sportif</option>
                <option>Perte de poids</option>
                <option>Prise de masse</option>
                <option>Maladie</option>
            </select>

            <button class="btn btn-success w-100 mt-3">💾 Save</button>
            <button type="button" class="btn btn-danger w-100 mt-2" onclick="closeModal()">
                Close
            </button>

        </form>

    </div>
</div>
<script src="/ProjetWeb-User/assets/js/users.js"></script>
</body>
</html>