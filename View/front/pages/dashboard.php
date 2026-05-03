<?php
$user = $_SESSION['user'];

$poids  = (float)($user['poids']  ?? 0);
$taille = (float)($user['taille'] ?? 0);
$age    = (int)  ($user['age']    ?? 25);
$obj    = strtolower($user['objectif'] ?? '');

$imc = ($poids > 0 && $taille > 0)
    ? round($poids / pow($taille / 100, 2), 1) : 0;

$imcCat = '—'; $imcColor = '#00b96b'; $imcBg = '#e6faf2';
if ($imc > 0) {
    if      ($imc < 18.5) { $imcCat = 'Insuffisance pondérale'; $imcColor = '#2979ff'; $imcBg = '#e8f0ff'; }
    elseif  ($imc < 25)   { $imcCat = 'Poids normal';           $imcColor = '#00b96b'; $imcBg = '#e6faf2'; }
    elseif  ($imc < 30)   { $imcCat = 'Surpoids';               $imcColor = '#ff6b2b'; $imcBg = '#fff0eb'; }
    else                  { $imcCat = 'Obésité';                 $imcColor = '#e53935'; $imcBg = '#ffebee'; }
}

$bmr      = 10 * $poids + 6.25 * $taille - 5 * $age + 5;
$tdee     = round($bmr * 1.55);
$calories = match(true) {
    str_contains($obj,'perte') => $tdee - 500,
    str_contains($obj,'masse') => $tdee + 500,
    default                    => $tdee,
};
$proteines = round($calories * 0.25 / 4);
$glucides  = round($calories * 0.50 / 4);
$lipides   = round($calories * 0.25 / 9);
$eau       = $poids > 0 ? round($poids * 0.033, 1) : 2.0;

$score = 0;
if ($imc >= 18.5 && $imc < 25) $score += 30; elseif ($imc > 0) $score += 15;
if ($poids > 0)  $score += 20; if ($taille > 0) $score += 10;
if ($age > 0)    $score += 10; if (!empty($user['objectif'])) $score += 20;
if (!empty($user['activite'])) $score += 10;

$h     = (int)date('H');
$salut = $h < 12 ? 'Bonjour' : ($h < 18 ? 'Bon après-midi' : 'Bonsoir');

$poidsMinIdeal = $taille > 0 ? round(18.5 * pow($taille/100,2),1) : 0;
$poidsMaxIdeal = $taille > 0 ? round(24.9 * pow($taille/100,2),1) : 0;

$plan = [
    ['Petit-déjeuner','7h00', 'Flocons d\'avoine, fruit frais, yaourt nature', round($calories*.20), round($proteines*.15)],
    ['Déjeuner',      '12h30','Poulet grillé, légumes vapeur, riz complet',    round($calories*.35), round($proteines*.40)],
    ['Collation',     '16h00','Amandes (20g), pomme',                          round($calories*.10), round($proteines*.10)],
    ['Dîner',         '19h30','Saumon, quinoa, salade verte',                  round($calories*.30), round($proteines*.30)],
    ['Snack',         '21h00','Fromage blanc 0%, miel',                        round($calories*.05), round($proteines*.05)],
];

include __DIR__ . '/../partials/header.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,700;0,9..144,900;1,9..144,700&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root{
  --g:    #00b96b;
  --g2:   #00e676;
  --gd:   #007a47;
  --or:   #ff6b2b;
  --or2:  #ff9a5c;
  --bg:   #ffffff;
  --bg2:  #f7faf8;
  --bg3:  #f0f6f3;
  --ink:  #0d1f0f;
  --ink2: #4a6352;
  --ink3: #8aa898;
  --bdr:  #e2ede8;
  --fh:   'Fraunces', serif;
  --fb:   'Instrument Sans', sans-serif;
}

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}

html{scroll-behavior:smooth;}

body{
  font-family:var(--fb)!important;
  background:var(--bg)!important;
  color:var(--ink)!important;
  overflow-x:hidden;
}

::-webkit-scrollbar{width:3px;}
::-webkit-scrollbar-thumb{background:var(--g);border-radius:10px;}

/* ════ TOPBAR ════ */
.tb{
  height:64px;
  background:#fff;
  border-bottom:1px solid var(--bdr);
  padding:0 40px;
  display:flex;align-items:center;justify-content:space-between;
  position:sticky;top:0;z-index:200;
}

.tb-logo{
  display:flex;align-items:center;gap:10px;text-decoration:none;
}

.tb-logo-mark{
  width:34px;height:34px;border-radius:10px;
  background:linear-gradient(135deg,var(--g),var(--g2));
  display:flex;align-items:center;justify-content:center;
  font-size:16px;
  box-shadow:0 4px 12px rgba(0,185,107,.3);
}

.tb-logo-name{
  font-family:var(--fh);font-size:20px;font-weight:700;color:var(--ink);
}
.tb-logo-name em{color:var(--g);font-style:normal;}

.tb-nav{display:flex;gap:2px;}

.tb-link{
  display:flex;align-items:center;gap:7px;
  padding:8px 16px;border-radius:8px;
  font-size:13px;font-weight:500;color:var(--ink2);
  text-decoration:none;transition:all .18s;
}
.tb-link:hover{background:var(--bg3);color:var(--ink);}
.tb-link.on{background:var(--bg3);color:var(--g);}
.tb-link i{font-size:12px;color:inherit;}

.tb-right{display:flex;align-items:center;gap:12px;}

.tb-chip{
  display:flex;align-items:center;gap:9px;
  padding:5px 14px 5px 5px;
  border:1px solid var(--bdr);border-radius:30px;
  background:#fff;cursor:pointer;
}

.tb-av{
  width:28px;height:28px;border-radius:50%;
  background:linear-gradient(135deg,var(--g),var(--gd));
  display:flex;align-items:center;justify-content:center;
  font-family:var(--fh);font-size:12px;font-weight:700;color:#fff;
}

.tb-name{font-size:13px;font-weight:600;}

.tb-logout{
  display:flex;align-items:center;gap:6px;
  padding:8px 16px;border-radius:8px;
  border:1px solid var(--bdr);
  font-size:13px;font-weight:500;color:var(--ink2);
  text-decoration:none;transition:all .15s;
}
.tb-logout:hover{border-color:var(--or);color:var(--or);}

/* ════ WRAP ════ */
.wrap{max-width:1280px;margin:0 auto;padding:40px 40px 80px;}

/* ════ PAGE HEADER ════ */
.ph{
  display:flex;align-items:flex-end;justify-content:space-between;
  margin-bottom:36px;
  padding-bottom:28px;
  border-bottom:2px solid var(--bg3);
  position:relative;
}

.ph::after{
  content:'';
  position:absolute;bottom:-2px;left:0;
  width:64px;height:2px;
  background:linear-gradient(90deg,var(--g),var(--or));
}

.ph-salut{
  font-size:11px;font-weight:700;text-transform:uppercase;
  letter-spacing:.14em;color:var(--g);margin-bottom:6px;
  display:flex;align-items:center;gap:8px;
}
.ph-salut::before{content:'';width:18px;height:1.5px;background:var(--g);}

.ph-title{
  font-family:var(--fh);
  font-size:clamp(28px,3vw,42px);
  font-weight:900;letter-spacing:-1.5px;color:var(--ink);
  line-height:1.1;
}

.ph-date{
  font-size:13px;color:var(--ink3);
  background:var(--bg3);
  padding:8px 16px;border-radius:20px;
  border:1px solid var(--bdr);
}

/* ════ SECTION LABEL ════ */
.sec-lbl{
  font-size:10px;font-weight:700;text-transform:uppercase;
  letter-spacing:.14em;color:var(--ink3);
  display:flex;align-items:center;gap:10px;
  margin-bottom:18px;
}
.sec-lbl::after{content:'';flex:1;height:1px;background:var(--bdr);}

/* ════ GRIDS ════ */
.g4{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;}
.g3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px;}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px;}
.g21{display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:28px;}
.g12{display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:28px;}

/* ════ CARD BASE ════ */
.card{
  background:#fff;
  border:1px solid var(--bdr);
  border-radius:20px;
  padding:24px;
  position:relative;overflow:hidden;
}

.card-hd{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:20px;padding-bottom:16px;
  border-bottom:1px solid var(--bg3);
}

.card-ttl{
  font-size:13px;font-weight:700;color:var(--ink);
  display:flex;align-items:center;gap:8px;
}
.card-ttl i{font-size:13px;color:var(--g);}

.c-tag{
  font-size:11px;font-weight:600;
  padding:4px 12px;border-radius:20px;
}
.c-tag-g{background:var(--bg3);color:var(--g);border:1px solid #c3e8d6;}
.c-tag-o{background:#fff0eb;color:var(--or);border:1px solid #ffd5bf;}

/* ════ KPI CARDS ════ */
.kpi{
  border-radius:20px;padding:22px;
  position:relative;overflow:hidden;
  transition:transform .22s,box-shadow .22s;
  cursor:default;
}
.kpi:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(0,0,0,.08);}

.kpi::before{
  content:'';
  position:absolute;top:0;right:0;
  width:90px;height:90px;border-radius:50%;
  transform:translate(30px,-30px);
  opacity:.12;
}

.kpi-g{background:linear-gradient(135deg,#e6faf2,#f0fdf6);border:1px solid #c3e8d6;}
.kpi-g::before{background:var(--g);}

.kpi-o{background:linear-gradient(135deg,#fff0eb,#fff6f2);border:1px solid #ffd5bf;}
.kpi-o::before{background:var(--or);}

.kpi-b{background:linear-gradient(135deg,#e8f0ff,#f0f5ff);border:1px solid #c7d9ff;}
.kpi-b::before{background:#2979ff;}

.kpi-v{background:linear-gradient(135deg,#f3eaff,#f8f3ff);border:1px solid #dcc7ff;}
.kpi-v::before{background:#7c3aed;}

.kpi-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;}

.kpi-icon{
  width:42px;height:42px;border-radius:12px;
  display:flex;align-items:center;justify-content:center;
  font-size:19px;
  background:rgba(255,255,255,.7);
  box-shadow:0 2px 8px rgba(0,0,0,.06);
}

.kpi-trend{
  font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
  padding:4px 10px;border-radius:20px;
  background:rgba(255,255,255,.8);
}

.kpi-g .kpi-trend{color:var(--g);}
.kpi-o .kpi-trend{color:var(--or);}
.kpi-b .kpi-trend{color:#2979ff;}
.kpi-v .kpi-trend{color:#7c3aed;}

.kpi-val{
  font-family:var(--fh);
  font-size:36px;font-weight:900;line-height:1;letter-spacing:-2px;
  margin-bottom:4px;
}

.kpi-g .kpi-val{color:var(--gd);}
.kpi-o .kpi-val{color:#c84a15;}
.kpi-b .kpi-val{color:#1a56db;}
.kpi-v .kpi-val{color:#5b21b6;}

.kpi-lbl{font-size:13px;font-weight:500;color:var(--ink2);}
.kpi-sub{font-size:11px;color:var(--ink3);margin-top:2px;}

/* ════ IMC SCALE ════ */
.imc-big{
  font-family:var(--fh);
  font-size:56px;font-weight:900;
  letter-spacing:-3px;line-height:1;margin-bottom:4px;
}
.imc-cat{font-size:13px;font-weight:600;margin-bottom:20px;}

.imc-scale{
  height:10px;border-radius:5px;position:relative;
  background:linear-gradient(90deg,#2979ff 0%,var(--g) 35%,var(--or) 65%,#e53935 100%);
  margin-bottom:8px;
  box-shadow:0 2px 8px rgba(0,0,0,.1);
}
.imc-needle{
  position:absolute;top:-5px;
  width:5px;height:20px;border-radius:3px;
  background:var(--ink);transform:translateX(-50%);
  box-shadow:0 2px 6px rgba(0,0,0,.3);
  transition:left 1.2s cubic-bezier(.34,1.56,.64,1);
}
.imc-labels{
  display:flex;justify-content:space-between;
  font-size:10px;color:var(--ink3);margin-top:4px;
}

.imc-pills{
  display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:18px;
}
.imc-pill{
  text-align:center;padding:12px 8px;
  background:var(--bg3);border:1px solid var(--bdr);
  border-radius:12px;
}
.imc-pill-val{font-size:15px;font-weight:700;color:var(--ink);}
.imc-pill-lbl{font-size:10px;color:var(--ink3);margin-top:2px;}

/* ════ SCORE RING ════ */
.score-wrap{display:flex;align-items:center;gap:24px;}
.ring-svg{transform:rotate(-90deg);}
.ring-bg{fill:none;stroke:var(--bg3);}
.ring-fill{
  fill:none;stroke:var(--g);stroke-linecap:round;
  transition:stroke-dashoffset 1.4s cubic-bezier(.34,1.56,.64,1);
  filter:drop-shadow(0 0 6px rgba(0,185,107,.4));
}
.ring-pos{position:relative;}
.ring-center{
  position:absolute;inset:0;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
}
.ring-num{
  font-family:var(--fh);font-size:30px;font-weight:900;
  color:var(--ink);letter-spacing:-1px;line-height:1;
}
.ring-den{font-size:11px;color:var(--ink3);}

.score-info h4{font-family:var(--fh);font-size:18px;font-weight:700;margin-bottom:6px;}
.score-info p{font-size:13px;color:var(--ink2);line-height:1.7;margin-bottom:14px;}

.score-chips{display:flex;flex-wrap:wrap;gap:6px;}
.s-chip{
  font-size:11px;font-weight:600;
  padding:4px 12px;border-radius:20px;
  background:var(--bg3);color:var(--ink2);
  border:1px solid var(--bdr);
}
.s-chip.g{background:#e6faf2;color:var(--g);border-color:#c3e8d6;}
.s-chip.o{background:#fff0eb;color:var(--or);border-color:#ffd5bf;}

/* ════ MACRO CHART ════ */
.macro-donut-wrap{
  display:flex;align-items:center;gap:24px;
}
.macro-legend{flex:1;}
.macro-row{
  display:flex;align-items:center;justify-content:space-between;
  padding:10px 0;border-bottom:1px solid var(--bg3);
}
.macro-row:last-child{border-bottom:none;}
.macro-dot{
  width:10px;height:10px;border-radius:50%;margin-right:8px;
  display:inline-block;flex-shrink:0;
}
.macro-name{font-size:13px;color:var(--ink2);display:flex;align-items:center;}
.macro-val{font-size:13px;font-weight:700;color:var(--ink);}
.macro-pct{font-size:11px;color:var(--ink3);margin-left:4px;}

/* ════ EAU ════ */
.eau-big{
  font-family:var(--fh);font-size:44px;font-weight:900;
  color:#1a56db;letter-spacing:-2px;line-height:1;
  margin-bottom:2px;
}
.eau-sub{font-size:12px;color:var(--ink3);margin-bottom:16px;}

.eau-track{
  height:10px;background:var(--bg3);border-radius:5px;
  overflow:hidden;margin-bottom:16px;
}
.eau-fill{
  height:100%;border-radius:5px;
  background:linear-gradient(90deg,#1a56db,#63a4ff);
  transition:width 1s ease;
  box-shadow:0 2px 8px rgba(26,86,219,.3);
}

.drops{display:flex;gap:7px;flex-wrap:wrap;}
.drop{
  width:36px;height:36px;border-radius:50%;
  border:1.5px solid #c7d9ff;background:#e8f0ff;
  display:flex;align-items:center;justify-content:center;
  font-size:15px;cursor:pointer;opacity:.3;
  transition:all .2s;
}
.drop.on{opacity:1;background:#bbdefb;border-color:#1a56db;transform:scale(1.1);}
.drop-hint{font-size:11px;color:var(--ink3);margin-top:10px;}

/* ════ OBJ BARS ════ */
.obj-item{margin-bottom:16px;}
.obj-item:last-child{margin-bottom:0;}
.obj-top{display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px;}
.obj-lbl{color:var(--ink2);display:flex;align-items:center;gap:6px;}
.obj-val{font-weight:700;color:var(--ink);}
.obj-bar{height:6px;background:var(--bg3);border-radius:3px;overflow:hidden;}
.obj-fill{height:100%;border-radius:3px;transition:width 1.1s cubic-bezier(.34,1.56,.64,1);}

/* ════ PLAN TABLE ════ */
.pt{width:100%;border-collapse:collapse;}
.pt th{
  font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:var(--ink3);text-align:left;padding:0 12px 14px 0;
  border-bottom:2px solid var(--bg3);
}
.pt td{
  padding:13px 12px 13px 0;font-size:13px;
  border-bottom:1px solid var(--bg3);vertical-align:middle;
}
.pt tr:last-child td{border-bottom:none;}
.pt tr:hover td{background:var(--bg3);}

.pt-badge{
  display:inline-flex;align-items:center;gap:5px;
  padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap;
}
.pt-badge.a{background:#fff0eb;color:var(--or);border:1px solid #ffd5bf;}
.pt-badge.b{background:#e6faf2;color:var(--g);border:1px solid #c3e8d6;}
.pt-badge.c{background:#f3eaff;color:#7c3aed;border:1px solid #dcc7ff;}
.pt-badge.d{background:#e8f0ff;color:#2979ff;border:1px solid #c7d9ff;}

.pt-time{font-size:11px;color:var(--ink3);}
.pt-food{font-size:13px;font-weight:500;}
.pt-desc{font-size:11px;color:var(--ink3);margin-top:2px;}
.pt-kcal{font-weight:700;color:var(--or);}
.pt-prot{font-weight:700;color:var(--g);}

/* ════ ALERT ITEMS ════ */
.ai{
  display:flex;align-items:flex-start;gap:14px;
  padding:14px 0;border-bottom:1px solid var(--bg3);
}
.ai:last-child{border-bottom:none;padding-bottom:0;}
.ai-ico{
  width:36px;height:36px;border-radius:10px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;font-size:16px;
}
.ai-ico.g{background:#e6faf2;}
.ai-ico.o{background:#fff0eb;}
.ai-ico.b{background:#e8f0ff;}
.ai-ttl{font-size:13px;font-weight:600;color:var(--ink);margin-bottom:2px;}
.ai-sub{font-size:12px;color:var(--ink3);line-height:1.5;}

/* ════ PROFIL MINI ════ */
.pm-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;}
.pm-cell{
  text-align:center;padding:14px 8px;
  background:var(--bg3);border:1px solid var(--bdr);border-radius:14px;
  transition:all .2s;
}
.pm-cell:hover{border-color:var(--g);background:#e6faf2;}
.pm-val{font-family:var(--fh);font-size:18px;font-weight:700;color:var(--ink);}
.pm-lbl{font-size:10px;color:var(--ink3);margin-top:2px;text-transform:uppercase;letter-spacing:.06em;}

/* ════ BTN ════ */
.btn-g{
  display:inline-flex;align-items:center;justify-content:center;gap:8px;
  padding:12px 22px;border-radius:12px;
  background:linear-gradient(135deg,var(--g),var(--g2));
  color:#fff;font-size:13px;font-weight:700;
  font-family:var(--fb);text-decoration:none;border:none;
  cursor:pointer;transition:all .22s;
  box-shadow:0 6px 20px rgba(0,185,107,.3);
  width:100%;
}
.btn-g:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(0,185,107,.4);color:#fff;}

/* ════ RESPONSIVE ════ */
@media(max-width:1024px){
  .g4{grid-template-columns:repeat(2,1fr);}
  .g3{grid-template-columns:repeat(2,1fr);}
  .g21,.g12{grid-template-columns:1fr;}
}
@media(max-width:640px){
  .g4,.g3,.g2,.g21,.g12{grid-template-columns:1fr;}
  .wrap{padding:24px 16px 60px;}
  .tb{padding:0 16px;}
}
</style>

<!-- ════ TOPBAR ════ -->
<div class="tb">
  <a href="#" class="tb-logo">
    <div class="tb-logo-mark">🌿</div>
    <div class="tb-logo-name">Eco<em>Nutri</em></div>
  </a>
  <nav class="tb-nav">
    <a href="index.php?url=User/dashboard" class="tb-link on"><i class="fa fa-gauge"></i> Dashboard</a>
    <a href="index.php?url=User/profile"   class="tb-link"><i class="fa fa-user"></i> Profil</a>
  </nav>
  <div class="tb-right">
    <div class="tb-chip">
      <div class="tb-av"><?= strtoupper(substr($user['nom'],0,1)) ?></div>
      <div class="tb-name"><?= htmlspecialchars($user['nom']) ?></div>
    </div>
    <a href="index.php?url=User/logout" class="tb-logout"><i class="fa fa-right-from-bracket"></i> Sortir</a>
  </div>
</div>

<!-- ════ BODY ════ -->
<div class="wrap">

  <!-- Page Header -->
  <div class="ph">
    <div>
      <div class="ph-salut"><?= $salut ?></div>
      <div class="ph-title"><?= htmlspecialchars($user['nom']) ?> 👋</div>
    </div>
    <div class="ph-date"><i class="fa fa-calendar" style="color:var(--g);margin-right:6px;"></i><?= date('d/m/Y — H\hi') ?></div>
  </div>

  <!-- ════ KPI ════ -->
  <div class="sec-lbl">Indicateurs clés du jour</div>
  <div class="g4">

    <div class="kpi kpi-g">
      <div class="kpi-top">
        <div class="kpi-icon">⚖️</div>
        <span class="kpi-trend">IMC</span>
      </div>
      <div class="kpi-val" style="color:<?= $imcColor ?>"><?= $imc ?: '—' ?></div>
      <div class="kpi-lbl">Indice de masse corporelle</div>
      <div class="kpi-sub" style="color:<?= $imcColor ?>"><?= $imcCat ?></div>
    </div>

    <div class="kpi kpi-o">
      <div class="kpi-top">
        <div class="kpi-icon">🔥</div>
        <span class="kpi-trend">kcal/j</span>
      </div>
      <div class="kpi-val"><?= number_format($calories) ?></div>
      <div class="kpi-lbl">Calories recommandées</div>
      <div class="kpi-sub">TDEE <?= number_format($tdee) ?> kcal</div>
    </div>

    <div class="kpi kpi-b">
      <div class="kpi-top">
        <div class="kpi-icon">💧</div>
        <span class="kpi-trend">eau</span>
      </div>
      <div class="kpi-val" style="color:#1a56db"><?= $eau ?>L</div>
      <div class="kpi-lbl">Hydratation quotidienne</div>
      <div class="kpi-sub"><?= $poids ?>kg × 33 ml</div>
    </div>

    <div class="kpi kpi-v">
      <div class="kpi-top">
        <div class="kpi-icon">🥩</div>
        <span class="kpi-trend">g/j</span>
      </div>
      <div class="kpi-val" style="color:#5b21b6"><?= $proteines ?>g</div>
      <div class="kpi-lbl">Protéines recommandées</div>
      <div class="kpi-sub">25 % des apports</div>
    </div>

  </div>

  <!-- ════ BILAN SANTÉ ════ -->
  <div class="sec-lbl">Bilan santé</div>
  <div class="g2">

    <!-- IMC -->
    <div class="card">
      <div class="card-hd">
        <div class="card-ttl"><i class="fa fa-weight-scale"></i> IMC détaillé</div>
        <span class="c-tag" style="background:<?= $imcBg ?>;color:<?= $imcColor ?>;border:1px solid <?= $imcColor ?>33;"><?= $imcCat ?></span>
      </div>
      <div class="imc-big" style="color:<?= $imcColor ?>"><?= $imc ?: '—' ?></div>
      <div class="imc-cat" style="color:<?= $imcColor ?>"><?= $imcCat ?></div>
      <div class="imc-scale">
        <div class="imc-needle" style="left:<?= $imc>0?min(97,max(2,($imc/40)*100)):2 ?>%"></div>
      </div>
      <div class="imc-labels">
        <span>&lt;18.5<br>Insuffisant</span>
        <span style="text-align:center">18.5–24.9<br>Normal ✓</span>
        <span style="text-align:center">25–29.9<br>Surpoids</span>
        <span style="text-align:right">&gt;30<br>Obésité</span>
      </div>
      <div class="imc-pills">
        <div class="imc-pill"><div class="imc-pill-val"><?= $poids ?>kg</div><div class="imc-pill-lbl">Poids actuel</div></div>
        <div class="imc-pill"><div class="imc-pill-val"><?= $taille ?>cm</div><div class="imc-pill-lbl">Taille</div></div>
        <div class="imc-pill"><div class="imc-pill-val" style="font-size:12px;"><?= $poidsMinIdeal ?>–<?= $poidsMaxIdeal ?>kg</div><div class="imc-pill-lbl">Poids idéal</div></div>
      </div>
    </div>

    <!-- Score -->
    <div class="card">
      <div class="card-hd">
        <div class="card-ttl"><i class="fa fa-chart-pie"></i> Score nutritionnel</div>
        <span class="c-tag c-tag-g"><?= $score ?> / 100</span>
      </div>
      <div class="score-wrap">
        <div class="ring-pos">
          <svg class="ring-svg" width="120" height="120" viewBox="0 0 120 120">
            <circle class="ring-bg"   cx="60" cy="60" r="48" stroke-width="9"/>
            <circle class="ring-fill" cx="60" cy="60" r="48" stroke-width="9"
                    stroke-dasharray="301.59"
                    stroke-dashoffset="<?= 301.59*(1-$score/100) ?>"/>
          </svg>
          <div class="ring-center">
            <div class="ring-num"><?= $score ?></div>
            <div class="ring-den">/100</div>
          </div>
        </div>
        <div class="score-info">
          <h4><?= $score>=80?'Excellent 🎉':($score>=60?'Très bon ✓':($score>=40?'À améliorer':'Incomplet')) ?></h4>
          <p>Complétez votre profil et respectez vos objectifs pour améliorer votre score et vos recommandations personnalisées.</p>
          <div class="score-chips">
            <?php if($user['objectif']): ?><span class="s-chip g">🎯 <?= htmlspecialchars($user['objectif']) ?></span><?php endif; ?>
            <?php if($user['activite']??''): ?><span class="s-chip o">🏃 <?= htmlspecialchars($user['activite']) ?></span><?php endif; ?>
            <?php if($imc>0): ?><span class="s-chip" style="background:<?= $imcBg ?>;color:<?= $imcColor ?>;border:1px solid <?= $imcColor ?>33;">IMC <?= $imc ?></span><?php endif; ?>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- ════ NUTRITION & EAU ════ -->
  <div class="sec-lbl">Nutrition & hydratation</div>
  <div class="g3">

    <!-- Macros -->
    <div class="card">
      <div class="card-hd">
        <div class="card-ttl"><i class="fa fa-utensils"></i> Macronutriments</div>
        <span class="c-tag c-tag-o"><?= $calories ?> kcal</span>
      </div>
      <div class="macro-donut-wrap">
        <canvas id="macroChart" width="120" height="120" style="flex-shrink:0;"></canvas>
        <div class="macro-legend">
          <div class="macro-row">
            <div class="macro-name"><span class="macro-dot" style="background:var(--g)"></span>Protéines</div>
            <div><span class="macro-val"><?= $proteines ?>g</span> <span class="macro-pct">25%</span></div>
          </div>
          <div class="macro-row">
            <div class="macro-name"><span class="macro-dot" style="background:#2979ff"></span>Glucides</div>
            <div><span class="macro-val"><?= $glucides ?>g</span> <span class="macro-pct">50%</span></div>
          </div>
          <div class="macro-row">
            <div class="macro-name"><span class="macro-dot" style="background:var(--or)"></span>Lipides</div>
            <div><span class="macro-val"><?= $lipides ?>g</span> <span class="macro-pct">25%</span></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Eau -->
    <div class="card">
      <div class="card-hd">
        <div class="card-ttl"><i class="fa fa-droplet" style="color:#1a56db;"></i> Hydratation</div>
        <span class="c-tag" style="background:#e8f0ff;color:#1a56db;border:1px solid #c7d9ff;">Objectif <?= $eau ?>L</span>
      </div>
      <div class="eau-big" id="eauNum">0.0 L</div>
      <div class="eau-sub">sur <?= $eau ?>L recommandés</div>
      <div class="eau-track"><div class="eau-fill" id="eauFill" style="width:0%"></div></div>
      <div class="drops" id="drops">
        <?php $dc=max(6,min(10,(int)($eau*4))); for($i=0;$i<$dc;$i++): ?>
        <div class="drop" onclick="tapEau(<?=$i?>)">💧</div>
        <?php endfor; ?>
      </div>
      <div class="drop-hint">Cliquez pour noter chaque verre consommé</div>
    </div>

    <!-- Objectifs -->
    <div class="card">
      <div class="card-hd">
        <div class="card-ttl"><i class="fa fa-bullseye"></i> Objectifs journaliers</div>
      </div>
      <div class="obj-item">
        <div class="obj-top"><span class="obj-lbl">🔥 Calories</span><span class="obj-val"><?= $calories ?> kcal</span></div>
        <div class="obj-bar"><div class="obj-fill" style="width:68%;background:linear-gradient(90deg,var(--or),var(--or2));"></div></div>
      </div>
      <div class="obj-item">
        <div class="obj-top"><span class="obj-lbl">🥩 Protéines</span><span class="obj-val"><?= $proteines ?>g</span></div>
        <div class="obj-bar"><div class="obj-fill" style="width:55%;background:linear-gradient(90deg,var(--g),var(--g2));"></div></div>
      </div>
      <div class="obj-item">
        <div class="obj-top"><span class="obj-lbl">🏃 Activité</span><span class="obj-val">3× / sem</span></div>
        <div class="obj-bar"><div class="obj-fill" style="width:66%;background:linear-gradient(90deg,var(--g),var(--g2));"></div></div>
      </div>
      <div class="obj-item">
        <div class="obj-top"><span class="obj-lbl">💧 Eau</span><span class="obj-val"><?= $eau ?>L</span></div>
        <div class="obj-bar"><div class="obj-fill" id="eauObjBar" style="width:0%;background:linear-gradient(90deg,#1a56db,#63a4ff);"></div></div>
      </div>
      <div class="obj-item">
        <div class="obj-top"><span class="obj-lbl">😴 Sommeil</span><span class="obj-val">8h</span></div>
        <div class="obj-bar"><div class="obj-fill" style="width:75%;background:linear-gradient(90deg,#5b21b6,#a78bfa);"></div></div>
      </div>
    </div>

  </div>

  <!-- ════ PLAN + ALERTES ════ -->
  <div class="sec-lbl">Plan alimentaire & recommandations</div>
  <div class="g21">

    <!-- Plan -->
    <div class="card">
      <div class="card-hd">
        <div class="card-ttl"><i class="fa fa-clipboard-list"></i> Plan alimentaire — <?= date('d/m/Y') ?></div>
        <span class="c-tag c-tag-g">5 repas</span>
      </div>
      <table class="pt">
        <thead>
          <tr>
            <th>Repas</th><th>Heure</th><th>Aliments</th>
            <th style="text-align:right">Calories</th><th style="text-align:right">Protéines</th>
          </tr>
        </thead>
        <tbody>
          <?php $badges=['a','b','c','d','c'];
          foreach($plan as $i=>$r): ?>
          <tr>
            <td><span class="pt-badge <?= $badges[$i] ?>"><?= $r[0] ?></span></td>
            <td><span class="pt-time"><?= $r[1] ?></span></td>
            <td><div class="pt-food"><?= $r[0] ?></div><div class="pt-desc"><?= $r[2] ?></div></td>
            <td style="text-align:right"><span class="pt-kcal"><?= $r[3] ?> kcal</span></td>
            <td style="text-align:right"><span class="pt-prot"><?= $r[4] ?>g</span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Alertes + Profil mini -->
    <div style="display:flex;flex-direction:column;gap:16px;">

      <div class="card">
        <div class="card-hd">
          <div class="card-ttl"><i class="fa fa-lightbulb" style="color:var(--or);"></i> Recommandations</div>
        </div>
        <?php if($imc>=18.5&&$imc<25): ?>
        <div class="ai"><div class="ai-ico g">✅</div><div><div class="ai-ttl">IMC normal — continuez !</div><div class="ai-sub">Maintenez votre équilibre alimentaire actuel pour rester dans la zone idéale.</div></div></div>
        <?php elseif($imc>=25): ?>
        <div class="ai"><div class="ai-ico o">⚠️</div><div><div class="ai-ttl">IMC <?= $imc ?> — réduire les graisses</div><div class="ai-sub">Limitez les graisses saturées, sucres rapides et sodas.</div></div></div>
        <?php endif; ?>
        <div class="ai"><div class="ai-ico b">💧</div><div><div class="ai-ttl">Boire <?= $eau ?>L d'eau aujourd'hui</div><div class="ai-sub">Un verre toutes les 2h, commencez dès le matin.</div></div></div>
        <?php if(str_contains($obj,'perte')): ?>
        <div class="ai"><div class="ai-ico o">🥗</div><div><div class="ai-ttl">Objectif perte — priorité fibres</div><div class="ai-sub">Glucides complexes uniquement. Évitez les sucres rapides.</div></div></div>
        <?php elseif(str_contains($obj,'masse')): ?>
        <div class="ai"><div class="ai-ico g">💪</div><div><div class="ai-ttl">Objectif masse — maximiser protéines</div><div class="ai-sub"><?= $proteines ?>g en 4–5 prises réparties sur la journée.</div></div></div>
        <?php endif; ?>
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-ttl"><i class="fa fa-user"></i> Mon profil</div>
        </div>
        <div class="pm-grid">
          <div class="pm-cell"><div class="pm-val"><?= $user['age']?:'—' ?></div><div class="pm-lbl">Âge</div></div>
          <div class="pm-cell"><div class="pm-val"><?= $poids?$poids.'kg':'—' ?></div><div class="pm-lbl">Poids</div></div>
          <div class="pm-cell"><div class="pm-val"><?= $taille?$taille.'cm':'—' ?></div><div class="pm-lbl">Taille</div></div>
          <div class="pm-cell"><div class="pm-val" style="font-size:12px;"><?= $user['activite']??'—' ?></div><div class="pm-lbl">Activité</div></div>
        </div>
        <a href="index.php?url=User/profile" class="btn-g"><i class="fa fa-pen" style="font-size:11px;"></i> Modifier mon profil</a>
      </div>

    </div>
  </div>

</div>

<script>
/* Macro donut */
new Chart(document.getElementById('macroChart'),{
  type:'doughnut',
  data:{
    labels:['Protéines','Glucides','Lipides'],
    datasets:[{
      data:[25,50,25],
      backgroundColor:['#00b96b','#2979ff','#ff6b2b'],
      borderWidth:4,borderColor:'#fff',hoverOffset:8
    }]
  },
  options:{
    cutout:'68%',
    plugins:{legend:{display:false}},
    animation:{animateScale:true}
  }
});

/* Eau */
let eauCount=0;
const dc=document.querySelectorAll('.drop').length;
const eauObj=<?= $eau ?>;
const perDrop=eauObj/dc;

function tapEau(idx){
  eauCount=idx+1;
  document.querySelectorAll('.drop').forEach((d,i)=>d.classList.toggle('on',i<eauCount));
  const L=(eauCount*perDrop).toFixed(1);
  document.getElementById('eauNum').textContent=L+' L';
  const pct=Math.min(100,(eauCount/dc)*100);
  document.getElementById('eauFill').style.width=pct+'%';
  document.getElementById('eauObjBar').style.width=pct+'%';
}

/* Animate bars */
window.addEventListener('load',()=>{
  document.querySelectorAll('.obj-fill,.imc-needle').forEach(el=>{
    const w=el.style.width;
    if(w){el.style.width='0';setTimeout(()=>{el.style.width=w;},200);}
  });
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>