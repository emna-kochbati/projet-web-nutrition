<?php
require_once __DIR__ . '/../../../Config/database.php';

$db = Database::getConnection();
$stmt = $db->query("SELECT * FROM user ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin Users - EcoNutri</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
<?php include "sidebar-style-only.css"; ?>

.content-area{ margin-left:220px; }

/* ===== GLOBAL ===== */
body{
    background: radial-gradient(circle at top,#0b1220,#020617);
    color:white;
    font-family:Arial;
}

/* TITLE */
.page-title{
    font-size:26px;
    font-weight:bold;
    margin-bottom:20px;
    color:#00e676;
}

/* FORM */
.form-box{
    background:linear-gradient(135deg, rgba(0,255,120,0.1), rgba(255,255,255,0.02));
    padding:20px;
    border-radius:15px;
    margin-bottom:25px;
    border:1px solid rgba(0,255,120,0.2);
}

/* INPUT */
.form-control{
    background:rgba(255,255,255,0.08);
    color:white;
    border:none;
}
.form-control::placeholder{
    color:rgba(255,255,255,0.6);
}

/* BUTTON */
.btn-wow{
    background:linear-gradient(90deg,#00e676,#00c853);
    border:none;
    font-weight:bold;
}

/* TABLE */
.table{
    background:rgba(255,255,255,0.04);
    backdrop-filter:blur(10px);
    border-radius:15px;
    overflow:hidden;
}
.table thead{
    background:rgba(0,255,120,0.15);
}
.table th{
    color:#00e676;
}
.table tbody tr:hover{
    background:rgba(0,255,120,0.08);
}

/* BUTTONS */
.btn-icon{
    border:none;
    padding:6px 10px;
    border-radius:8px;
    margin:2px;
}
.view{background:#2196f3;color:white;}
.edit{background:#ffb300;color:white;}
.delete{background:#ef5350;color:white;}

/* MODAL */
.modal{
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.85);
    backdrop-filter:blur(6px);
    justify-content:center;
    align-items:center;
}

.modal.show{
    display:flex;
    animation:fadeIn 0.3s ease;
}

@keyframes fadeIn{
    from{opacity:0;}
    to{opacity:1;}
}

.modal-content{
    background:linear-gradient(135deg,#0b1220,#020617);
    padding:25px;
    border-radius:20px;
    width:450px;
    box-shadow:0 0 40px rgba(0,255,120,0.4);
    border:1px solid rgba(0,255,120,0.2);
}

/* HEADER */
.modal-header{
    text-align:center;
    margin-bottom:15px;
}
.modal-header i{
    font-size:28px;
    color:#00e676;
}
.modal-title{
    color:#00e676;
}

/* LABEL */
.form-label{
    font-size:13px;
    margin-top:10px;
    opacity:0.8;
}

/* INPUT MODAL */
.modal-content .form-control{
    background:rgba(255,255,255,0.07);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:10px;
    padding:10px;
}
.modal-content .form-control:focus{
    border-color:#00e676;
    box-shadow:0 0 10px rgba(0,255,120,0.5);
}

/* BUTTON MODAL */
.btn-save{
    background:linear-gradient(90deg,#00e676,#00c853);
    border:none;
    border-radius:12px;
    font-weight:bold;
    padding:10px;
}

.btn-close-modal{
    background:#ef5350;
    border:none;
    border-radius:12px;
    padding:10px;
}
</style>
</head>

<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-area p-4">

<div class="page-title">👥 Gestion des utilisateurs</div>

<!-- FORM -->
<div class="form-box">
<form method="POST" action="/ProjetWeb-User/index.php?url=Admin/addUser">

<div class="row g-2">
<input name="nom" class="form-control col" placeholder="Nom">
<input name="email" class="form-control col" placeholder="Email">
<input type="password" name="password" class="form-control col" placeholder="Password">

<select name="objectif" class="form-control col">
<option>Sportif</option>
<option>Perte de poids</option>
<option>Prise de masse</option>
<option>Maladie</option>
</select>

<select name="status" class="form-control col">
<option value="active">Active</option>
<option value="inactive">Inactive</option>
<option value="banned">Banned</option>
</select>

<input name="poids" class="form-control col" placeholder="Poids">
<input name="taille" class="form-control col" placeholder="Taille">

<button class="btn-wow mt-2 w-100">🚀 Ajouter</button>
</div>

</form>
</div>

<!-- TABLE -->
<table class="table table-bordered text-center">
<thead>
<tr>
<th>ID</th>
<th>Nom</th>
<th>Email</th>
<th>Objectif</th>
<th>Poids</th>
<th>Taille</th>
<th>Actions</th>
</tr>
</thead>

<tbody>
<?php foreach($users as $u){ ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= $u['nom'] ?></td>
<td><?= $u['email'] ?></td>
<td><?= $u['objectif'] ?></td>
<td><?= $u['poids'] ?></td>
<td><?= $u['taille'] ?></td>

<td>
<button class="btn-icon view"
onclick='openUser(<?= json_encode($u) ?>,"view")'>
<i class="fa fa-eye"></i>
</button>

<button class="btn-icon edit"
onclick='openUser(<?= json_encode($u) ?>,"edit")'>
<i class="fa fa-pen"></i>
</button>

<a href="/ProjetWeb-User/index.php?url=Admin/deleteUser/<?= $u['id'] ?>"
class="btn-icon delete"
onclick="return confirm('Supprimer ?')">
<i class="fa fa-trash"></i>
</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>

</div>

<!-- MODAL -->
<div id="userModal" class="modal">
<div class="modal-content">

<div class="modal-header">
<i class="fa fa-user"></i>
<h4 id="modalTitle" class="modal-title"></h4>
</div>

<form id="userForm" method="POST">

<input type="hidden" name="id" id="uid">

<label class="form-label">Nom complet</label>
<input class="form-control" name="nom" id="nom">

<label class="form-label">Email</label>
<input class="form-control" name="email" id="email">

<div class="row">
<div class="col">
<label class="form-label">Poids</label>
<input class="form-control" name="poids" id="poids">
</div>
<div class="col">
<label class="form-label">Taille</label>
<input class="form-control" name="taille" id="taille">
</div>
</div>

<label class="form-label">Objectif</label>
<select class="form-control" name="objectif" id="objectif">
<option>Sportif</option>
<option>Perte de poids</option>
<option>Prise de masse</option>
<option>Maladie</option>
</select>

<label class="form-label">Status</label>
<select class="form-control" name="status" id="status">
<option value="active">Active</option>
<option value="inactive">Inactive</option>
<option value="banned">Banned</option>
</select>

<button id="saveBtn" class="btn-save w-100 mt-3">💾 Save</button>
<button type="button" onclick="closeModal()" class="btn-close-modal w-100 mt-2">Close</button>

</form>

</div>
</div>

<script>
function openUser(user, mode){

let modal = document.getElementById("userModal");
modal.classList.add("show");

document.getElementById("uid").value = user.id;
document.getElementById("nom").value = user.nom;
document.getElementById("email").value = user.email;
document.getElementById("poids").value = user.poids;
document.getElementById("taille").value = user.taille;
document.getElementById("objectif").value = user.objectif;
document.getElementById("status").value = user.status;

let form = document.getElementById("userForm");
let saveBtn = document.getElementById("saveBtn");

if(mode === "view"){
    document.getElementById("modalTitle").innerText="👁 Voir utilisateur";

    // ❌ ancien bug: bloquait tout le modal
    // form.style.pointerEvents = "none";

    // ✅ nouveau: on désactive فقط les champs
    document.querySelectorAll("#userForm input, #userForm select").forEach(el=>{
        el.disabled = true;
    });

    saveBtn.style.display = "none";
}
else{
    document.getElementById("modalTitle").innerText="✏ Modifier utilisateur";

    document.querySelectorAll("#userForm input, #userForm select").forEach(el=>{
        el.disabled = false;
    });

    saveBtn.style.display = "block";

    form.action="/ProjetWeb-User/index.php?url=Admin/updateUser/"+user.id;
}
}

function closeModal(){
document.getElementById("userModal").classList.remove("show");

// 🔥 reset des champs (optionnel mais propre)
document.querySelectorAll("#userForm input, #userForm select").forEach(el=>{
    el.disabled = false;
});
document.getElementById("saveBtn").style.display = "block";
}
</script>

</body>
</html>