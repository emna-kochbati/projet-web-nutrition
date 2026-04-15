<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Nutrition Intelligence</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<style>
body{
margin:0;
font-family:Arial;
background: radial-gradient(circle at top,#0f172a,#020617);
color:white;
display:flex;
}

/* ✅ FIX SIDEBAR + CONTENT */
.sidebar{
width:250px;
position:fixed;
left:0;
top:0;
height:100vh;
background:rgba(17,24,39,0.7);
backdrop-filter: blur(20px);
padding:20px;
border-right:1px solid rgba(34,197,94,0.2);
z-index:1000;
}

.content-area{
margin-left:250px;
width:100%;
}

/* ICONES GLOW */
i{
color:#22c55e;
filter: drop-shadow(0 0 6px #22c55e);
transition:0.3s;
}

i:hover{
transform:scale(1.2);
filter: drop-shadow(0 0 12px #22c55e);
}

/* SIDEBAR */
.sidebar h2{
color:#22c55e;
text-shadow:0 0 10px #22c55e;
}

.sidebar a{
display:block;
color:white;
padding:10px;
margin:8px 0;
text-decoration:none;
border-radius:10px;
transition:0.3s;
}

.sidebar a:hover{
background:linear-gradient(90deg,#22c55e,#16a34a);
transform:translateX(6px);
box-shadow:0 0 15px #22c55e;
}

/* MAIN */
.main{
padding:20px;
}

/* TOPBAR */
.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
}

.search{
padding:12px;
width:300px;
border-radius:12px;
border:none;
background:rgba(31,41,55,0.7);
color:white;
outline:none;
box-shadow:0 0 10px rgba(34,197,94,0.2);
}

/* CARDS */
.cards{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
gap:15px;
}

.card{
background:rgba(31,41,55,0.6);
backdrop-filter: blur(15px);
padding:15px;
border-radius:15px;
transition:0.3s;
border:1px solid rgba(255,255,255,0.05);
position:relative;
overflow:hidden;
}

.card:hover{
transform:translateY(-6px);
box-shadow:0 0 25px rgba(34,197,94,0.3);
}

/* GRID */
.grid{
display:grid;
grid-template-columns:2fr 1fr;
gap:20px;
margin-top:20px;
}

.box{
background:rgba(31,41,55,0.6);
padding:15px;
border-radius:15px;
backdrop-filter: blur(15px);
border:1px solid rgba(34,197,94,0.1);
}

/* AI BOX */
.ai-box{
background:linear-gradient(135deg,rgba(11,59,46,0.7),rgba(15,118,110,0.2));
padding:15px;
border-radius:15px;
margin-top:20px;
}

/* ALERT */
.alert{
background:linear-gradient(90deg,#7f1d1d,#ef4444);
padding:10px;
border-radius:10px;
margin-top:10px;
}

/* CALENDAR */
#calendar{
max-width:100%;
background:rgba(17,24,39,0.6);
padding:15px;
border-radius:15px;
border:1px solid rgba(34,197,94,0.2);
}

/* FULLCALENDAR */
.fc{
color:white;
}

.fc-toolbar-title{
color:#22c55e;
text-shadow:0 0 10px #22c55e;
}

.fc-button{
background:rgba(31,41,55,0.8) !important;
border:none !important;
color:white !important;
border-radius:10px !important;
}

.fc-button:hover{
background:#22c55e !important;
box-shadow:0 0 10px #22c55e;
}

.fc-event{
border:none;
border-radius:10px;
padding:3px;
font-size:12px;
}

.event-risk{
background:#ef4444 !important;
box-shadow:0 0 10px #ef4444;
}

.event-calories{
background:#f97316 !important;
box-shadow:0 0 10px #f97316;
}

.event-ai{
background:#22c55e !important;
box-shadow:0 0 10px #22c55e;
}

</style>
</head>

<body>

<!-- SIDEBAR (IMPORTANT) -->
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<!-- CONTENT -->
<div class="content-area">

<div class="main">

<!-- TOP -->
<div class="topbar">
<input class="search" placeholder="🔍 Rechercher utilisateur...">
<div>
<i class="fa fa-bell"></i> Notifications
<i class="fa fa-user"></i> Admin
</div>
</div>

<!-- CARDS -->
<div class="cards">
<div class="card">👤 Utilisateurs <h2>1,240</h2></div>
<div class="card">🥗 Actifs <h2>320</h2></div>
<div class="card">📊 Engagement <h2>78%</h2></div>
<div class="card">⚠ Alertes <h2>3</h2></div>
<div class="card">🔥 Calories <h2>2,150</h2></div>
<div class="card">🧬 Risque <h2>18</h2></div>
</div>

<!-- GRAPHS -->
<div class="grid">

<div class="box">
<h3>📊 Utilisateurs</h3>
<canvas id="chart1"></canvas>
</div>

<div class="box">
<h3>🍏 Nutrition</h3>
<canvas id="chart2"></canvas>
</div>

<div class="box">
<h3>🔥 Calories</h3>
<canvas id="chartCalories"></canvas>
</div>

<div class="box">
<h3>🧬 Maladies</h3>
<canvas id="chartDisease"></canvas>
</div>

</div>

<!-- CALENDAR + SEARCH -->
<div class="grid">

<div class="box">
<h3>📅 Calendrier Intelligent</h3>
<div id="calendar"></div>
</div>

<div class="box">
<h3>🔍 Recherche</h3>
<input class="search" placeholder="chercher utilisateur...">
<br><br>
<button style="padding:10px;background:#22c55e;border:none;color:white;border-radius:10px">
Rechercher
</button>

<div class="alert">⚠ Utilisateur suspect détecté</div>
<div class="alert">⚠ API lente</div>
</div>

</div>

<!-- AI -->
<div class="ai-box">
<h3>🤖 Assistant IA</h3>
<ul>
<li>✔ Système stable</li>
<li>⚠ 2 anomalies détectées</li>
<li>💡 Vérifier utilisateurs inactifs</li>
</ul>

<textarea style="width:100%;height:60px;" placeholder="Demander à l'IA..."></textarea>
<br><br>
<button style="padding:10px;background:#22c55e;border:none;color:white;border-radius:10px">
Envoyer
</button>
</div>

</div>
</div>

<script>

/* CHART USERS */
new Chart(document.getElementById('chart1'), {
type:'line',
data:{
labels:['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'],
datasets:[{
label:'Utilisateurs',
data:[120,190,300,250,400,380,500],
borderColor:'#22c55e'
}]
}
});

/* CHART NUTRITION */
new Chart(document.getElementById('chart2'), {
type:'bar',
data:{
labels:['A','B','C','D'],
datasets:[{
label:'Score',
data:[80,60,90,70],
backgroundColor:'#22c55e'
}]
}
});

/* CHART CALORIES */
new Chart(document.getElementById('chartCalories'), {
type:'line',
data:{
labels:['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'],
datasets:[{
label:'Calories',
data:[1800,2100,2000,2300,2500,2200,2400],
borderColor:'#f97316'
}]
}
});

/* CHART DISEASE */
new Chart(document.getElementById('chartDisease'), {
type:'pie',
data:{
labels:['Diabète','Hypertension','Obésité','Sain'],
datasets:[{
data:[12,8,10,70],
backgroundColor:['#ef4444','#f59e0b','#f97316','#22c55e']
}]
}
});

/* CALENDAR */
document.addEventListener('DOMContentLoaded', function() {

var calendarEl = document.getElementById('calendar');

var calendar = new FullCalendar.Calendar(calendarEl, {

initialView:'dayGridMonth',
height:500,

headerToolbar:{
left:'prev,next today',
center:'title',
right:'dayGridMonth,timeGridWeek,listWeek'
},

events:[
{ title:'Audit utilisateurs', date:'2026-04-10', className:'event-ai' },
{ title:'Maintenance IA', date:'2026-04-12', className:'event-ai' },
{ title:'Calories élevées', date:'2026-04-11', className:'event-calories' },
{ title:'Risque santé élevé', date:'2026-04-13', className:'event-risk' },
{ title:'Alerte nutrition IA', date:'2026-04-14', className:'event-risk' },
{ title:'Optimisation régime', date:'2026-04-15', className:'event-ai' }
]

});

calendar.render();

});

</script>

</body>
</html>