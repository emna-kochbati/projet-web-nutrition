```php
<?php
$user = $_SESSION['user'];

$poids  = (float)($user['poids']  ?? 0);
$taille = (float)($user['taille'] ?? 0);
$age    = (int)  ($user['age']    ?? 25);
$obj    = strtolower($user['objectif'] ?? '');
$act    = strtolower($user['activite'] ?? '');

$imc = ($poids > 0 && $taille > 0)
    ? round($poids / pow($taille / 100, 2), 1) : 0;

$imcCat = '—'; $imcColor = '#00b96b'; $imcBg = '#e6faf2'; $imcState = 'normal';
if ($imc > 0) {
    if      ($imc < 18.5) { $imcCat = 'Insuffisance pondérale'; $imcColor = '#2979ff'; $imcBg = '#e8f0ff'; $imcState = 'underweight'; }
    elseif  ($imc < 25)   { $imcCat = 'Poids normal';           $imcColor = '#00b96b'; $imcBg = '#e6faf2'; $imcState = 'normal'; }
    elseif  ($imc < 30)   { $imcCat = 'Surpoids';               $imcColor = '#ff6b2b'; $imcBg = '#fff0eb'; $imcState = 'overweight'; }
    else                  { $imcCat = 'Obésité';                 $imcColor = '#e53935'; $imcBg = '#ffebee'; $imcState = 'obese'; }
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

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
body{font-family:var(--fb)!important;background:var(--bg)!important;color:var(--ink)!important;overflow-x:hidden;}
::-webkit-scrollbar{width:3px;}
::-webkit-scrollbar-thumb{background:var(--g);border-radius:10px;}

.tb{height:64px;background:#fff;border-bottom:1px solid var(--bdr);padding:0 40px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:200;}
.tb-logo{display:flex;align-items:center;gap:10px;text-decoration:none;}
.tb-logo-mark{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,var(--g),var(--g2));display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 4px 12px rgba(0,185,107,.3);}
.tb-logo-name{font-family:var(--fh);font-size:20px;font-weight:700;color:var(--ink);}
.tb-logo-name em{color:var(--g);font-style:normal;}
.tb-nav{display:flex;gap:2px;}
.tb-link{display:flex;align-items:center;gap:7px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;color:var(--ink2);text-decoration:none;transition:all .18s;}
.tb-link:hover{background:var(--bg3);color:var(--ink);}
.tb-link.on{background:var(--bg3);color:var(--g);}
.tb-link i{font-size:12px;color:inherit;}
.tb-right{display:flex;align-items:center;gap:12px;}
.tb-chip{display:flex;align-items:center;gap:9px;padding:5px 14px 5px 5px;border:1px solid var(--bdr);border-radius:30px;background:#fff;cursor:pointer;}
.tb-av{width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--g),var(--gd));display:flex;align-items:center;justify-content:center;font-family:var(--fh);font-size:12px;font-weight:700;color:#fff;}
.tb-name{font-size:13px;font-weight:600;}
.tb-logout{display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;border:1px solid var(--bdr);font-size:13px;font-weight:500;color:var(--ink2);text-decoration:none;transition:all .15s;}
.tb-logout:hover{border-color:var(--or);color:var(--or);}
.wrap{max-width:1280px;margin:0 auto;padding:40px 40px 80px;}
.ph{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:36px;padding-bottom:28px;border-bottom:2px solid var(--bg3);position:relative;}
.ph::after{content:'';position:absolute;bottom:-2px;left:0;width:64px;height:2px;background:linear-gradient(90deg,var(--g),var(--or));}
.ph-salut{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--g);margin-bottom:6px;display:flex;align-items:center;gap:8px;}
.ph-salut::before{content:'';width:18px;height:1.5px;background:var(--g);}
.ph-title{font-family:var(--fh);font-size:clamp(28px,3vw,42px);font-weight:900;letter-spacing:-1.5px;color:var(--ink);line-height:1.1;}
.ph-date{font-size:13px;color:var(--ink3);background:var(--bg3);padding:8px 16px;border-radius:20px;border:1px solid var(--bdr);}
.sec-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--ink3);display:flex;align-items:center;gap:10px;margin-bottom:18px;}
.sec-lbl::after{content:'';flex:1;height:1px;background:var(--bdr);}
.g4{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;}
.g3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px;}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px;}
.g21{display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:28px;}
.card{background:#fff;border:1px solid var(--bdr);border-radius:20px;padding:24px;position:relative;overflow:hidden;}
.card-hd{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--bg3);}
.card-ttl{font-size:13px;font-weight:700;color:var(--ink);display:flex;align-items:center;gap:8px;}
.card-ttl i{font-size:13px;color:var(--g);}
.c-tag{font-size:11px;font-weight:600;padding:4px 12px;border-radius:20px;}
.c-tag-g{background:var(--bg3);color:var(--g);border:1px solid #c3e8d6;}
.c-tag-o{background:#fff0eb;color:var(--or);border:1px solid #ffd5bf;}
.kpi{border-radius:20px;padding:22px;position:relative;overflow:hidden;transition:transform .22s,box-shadow .22s;cursor:default;}
.kpi:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(0,0,0,.08);}
.kpi::before{content:'';position:absolute;top:0;right:0;width:90px;height:90px;border-radius:50%;transform:translate(30px,-30px);opacity:.12;}
.kpi-g{background:linear-gradient(135deg,#e6faf2,#f0fdf6);border:1px solid #c3e8d6;}
.kpi-g::before{background:var(--g);}
.kpi-o{background:linear-gradient(135deg,#fff0eb,#fff6f2);border:1px solid #ffd5bf;}
.kpi-o::before{background:var(--or);}
.kpi-b{background:linear-gradient(135deg,#e8f0ff,#f0f5ff);border:1px solid #c7d9ff;}
.kpi-b::before{background:#2979ff;}
.kpi-v{background:linear-gradient(135deg,#f3eaff,#f8f3ff);border:1px solid #dcc7ff;}
.kpi-v::before{background:#7c3aed;}
.kpi-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;}
.kpi-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:19px;background:rgba(255,255,255,.7);box-shadow:0 2px 8px rgba(0,0,0,.06);}
.kpi-trend{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:4px 10px;border-radius:20px;background:rgba(255,255,255,.8);}
.kpi-g .kpi-trend{color:var(--g);}
.kpi-o .kpi-trend{color:var(--or);}
.kpi-b .kpi-trend{color:#2979ff;}
.kpi-v .kpi-trend{color:#7c3aed;}
.kpi-val{font-family:var(--fh);font-size:36px;font-weight:900;line-height:1;letter-spacing:-2px;margin-bottom:4px;}
.kpi-g .kpi-val{color:var(--gd);}
.kpi-o .kpi-val{color:#c84a15;}
.kpi-b .kpi-val{color:#1a56db;}
.kpi-v .kpi-val{color:#5b21b6;}
.kpi-lbl{font-size:13px;font-weight:500;color:var(--ink2);}
.kpi-sub{font-size:11px;color:var(--ink3);margin-top:2px;}
.imc-big{font-family:var(--fh);font-size:56px;font-weight:900;letter-spacing:-3px;line-height:1;margin-bottom:4px;}
.imc-cat{font-size:13px;font-weight:600;margin-bottom:20px;}
.imc-scale{height:10px;border-radius:5px;position:relative;background:linear-gradient(90deg,#2979ff 0%,var(--g) 35%,var(--or) 65%,#e53935 100%);margin-bottom:8px;box-shadow:0 2px 8px rgba(0,0,0,.1);}
.imc-needle{position:absolute;top:-5px;width:5px;height:20px;border-radius:3px;background:var(--ink);transform:translateX(-50%);box-shadow:0 2px 6px rgba(0,0,0,.3);transition:left 1.2s cubic-bezier(.34,1.56,.64,1);}
.imc-labels{display:flex;justify-content:space-between;font-size:10px;color:var(--ink3);margin-top:4px;}
.imc-pills{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:18px;}
.imc-pill{text-align:center;padding:12px 8px;background:var(--bg3);border:1px solid var(--bdr);border-radius:12px;}
.imc-pill-val{font-size:15px;font-weight:700;color:var(--ink);}
.imc-pill-lbl{font-size:10px;color:var(--ink3);margin-top:2px;}
.score-wrap{display:flex;align-items:center;gap:24px;}
.ring-svg{transform:rotate(-90deg);}
.ring-bg{fill:none;stroke:var(--bg3);}
.ring-fill{fill:none;stroke:var(--g);stroke-linecap:round;transition:stroke-dashoffset 1.4s cubic-bezier(.34,1.56,.64,1);filter:drop-shadow(0 0 6px rgba(0,185,107,.4));}
.ring-pos{position:relative;}
.ring-center{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;}
.ring-num{font-family:var(--fh);font-size:30px;font-weight:900;color:var(--ink);letter-spacing:-1px;line-height:1;}
.ring-den{font-size:11px;color:var(--ink3);}
.score-info h4{font-family:var(--fh);font-size:18px;font-weight:700;margin-bottom:6px;}
.score-info p{font-size:13px;color:var(--ink2);line-height:1.7;margin-bottom:14px;}
.score-chips{display:flex;flex-wrap:wrap;gap:6px;}
.s-chip{font-size:11px;font-weight:600;padding:4px 12px;border-radius:20px;background:var(--bg3);color:var(--ink2);border:1px solid var(--bdr);}
.s-chip.g{background:#e6faf2;color:var(--g);border-color:#c3e8d6;}
.s-chip.o{background:#fff0eb;color:var(--or);border-color:#ffd5bf;}
.macro-donut-wrap{display:flex;align-items:center;gap:24px;}
.macro-legend{flex:1;}
.macro-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--bg3);}
.macro-row:last-child{border-bottom:none;}
.macro-dot{width:10px;height:10px;border-radius:50%;margin-right:8px;display:inline-block;flex-shrink:0;}
.macro-name{font-size:13px;color:var(--ink2);display:flex;align-items:center;}
.macro-val{font-size:13px;font-weight:700;color:var(--ink);}
.macro-pct{font-size:11px;color:var(--ink3);margin-left:4px;}
.eau-big{font-family:var(--fh);font-size:44px;font-weight:900;color:#1a56db;letter-spacing:-2px;line-height:1;margin-bottom:2px;}
.eau-sub{font-size:12px;color:var(--ink3);margin-bottom:16px;}
.eau-track{height:10px;background:var(--bg3);border-radius:5px;overflow:hidden;margin-bottom:16px;}
.eau-fill{height:100%;border-radius:5px;background:linear-gradient(90deg,#1a56db,#63a4ff);transition:width 1s ease;box-shadow:0 2px 8px rgba(26,86,219,.3);}
.drops{display:flex;gap:7px;flex-wrap:wrap;}
.drop{width:36px;height:36px;border-radius:50%;border:1.5px solid #c7d9ff;background:#e8f0ff;display:flex;align-items:center;justify-content:center;font-size:15px;cursor:pointer;opacity:.3;transition:all .2s;}
.drop.on{opacity:1;background:#bbdefb;border-color:#1a56db;transform:scale(1.1);}
.drop-hint{font-size:11px;color:var(--ink3);margin-top:10px;}
.obj-item{margin-bottom:16px;}
.obj-item:last-child{margin-bottom:0;}
.obj-top{display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px;}
.obj-lbl{color:var(--ink2);display:flex;align-items:center;gap:6px;}
.obj-val{font-weight:700;color:var(--ink);}
.obj-bar{height:6px;background:var(--bg3);border-radius:3px;overflow:hidden;}
.obj-fill{height:100%;border-radius:3px;transition:width 1.1s cubic-bezier(.34,1.56,.64,1);}
.pt{width:100%;border-collapse:collapse;}
.pt th{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--ink3);text-align:left;padding:0 12px 14px 0;border-bottom:2px solid var(--bg3);}
.pt td{padding:13px 12px 13px 0;font-size:13px;border-bottom:1px solid var(--bg3);vertical-align:middle;}
.pt tr:last-child td{border-bottom:none;}
.pt tr:hover td{background:var(--bg3);}
.pt-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap;}
.pt-badge.a{background:#fff0eb;color:var(--or);border:1px solid #ffd5bf;}
.pt-badge.b{background:#e6faf2;color:var(--g);border:1px solid #c3e8d6;}
.pt-badge.c{background:#f3eaff;color:#7c3aed;border:1px solid #dcc7ff;}
.pt-badge.d{background:#e8f0ff;color:#2979ff;border:1px solid #c7d9ff;}
.pt-time{font-size:11px;color:var(--ink3);}
.pt-food{font-size:13px;font-weight:500;}
.pt-desc{font-size:11px;color:var(--ink3);margin-top:2px;}
.pt-kcal{font-weight:700;color:var(--or);}
.pt-prot{font-weight:700;color:var(--g);}
.ai{display:flex;align-items:flex-start;gap:14px;padding:14px 0;border-bottom:1px solid var(--bg3);}
.ai:last-child{border-bottom:none;padding-bottom:0;}
.ai-ico{width:36px;height:36px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:16px;}
.ai-ico.g{background:#e6faf2;}
.ai-ico.o{background:#fff0eb;}
.ai-ico.b{background:#e8f0ff;}
.ai-ttl{font-size:13px;font-weight:600;color:var(--ink);margin-bottom:2px;}
.ai-sub{font-size:12px;color:var(--ink3);line-height:1.5;}
.pm-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;}
.pm-cell{text-align:center;padding:14px 8px;background:var(--bg3);border:1px solid var(--bdr);border-radius:14px;transition:all .2s;}
.pm-cell:hover{border-color:var(--g);background:#e6faf2;}
.pm-val{font-family:var(--fh);font-size:18px;font-weight:700;color:var(--ink);}
.pm-lbl{font-size:10px;color:var(--ink3);margin-top:2px;text-transform:uppercase;letter-spacing:.06em;}
.btn-g{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 22px;border-radius:12px;background:linear-gradient(135deg,var(--g),var(--g2));color:#fff;font-size:13px;font-weight:700;font-family:var(--fb);text-decoration:none;border:none;cursor:pointer;transition:all .22s;box-shadow:0 6px 20px rgba(0,185,107,.3);width:100%;}
.btn-g:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(0,185,107,.4);color:#fff;}

/* ═══════════════════════════════════════════ */
/* ════ AVATAR IA 3D — AVATAR HUMAIN RÉALISTE ════ */
/* ═══════════════════════════════════════════ */
.avatar-section{margin-bottom:28px;}
.avatar-card{
  background:linear-gradient(160deg,#0d1f0f 0%,#162a1e 40%,#1a3528 100%);
  border:1px solid #2a4a3a;border-radius:24px;overflow:hidden;position:relative;
}
.avatar-card::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 50% 30%, rgba(0,185,107,.1) 0%, transparent 60%);
  pointer-events:none;z-index:1;
}
.avatar-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:20px 24px 16px;position:relative;z-index:2;
}
.avatar-title{display:flex;align-items:center;gap:10px;}
.avatar-title-icon{
  width:36px;height:36px;border-radius:10px;
  background:linear-gradient(135deg,var(--g),var(--g2));
  display:flex;align-items:center;justify-content:center;font-size:16px;
  box-shadow:0 4px 12px rgba(0,185,107,.3);
}
.avatar-title-text{font-family:var(--fh);font-size:16px;font-weight:700;color:#fff;}
.avatar-title-text span{color:var(--g2);font-style:normal;}
.ai-badge{
  display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;
  background:rgba(0,185,107,.15);border:1px solid rgba(0,185,107,.3);
  font-size:11px;font-weight:700;color:var(--g2);letter-spacing:.05em;
}
.ai-badge-dot{width:6px;height:6px;border-radius:50%;background:var(--g2);animation:aiPulse 2s ease-in-out infinite;}
@keyframes aiPulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(.7);}}

.avatar-body{display:flex;position:relative;z-index:1;padding:0 16px 16px;gap:16px;}
.avatar-canvas-wrap{
  flex:0 0 300px;display:flex;align-items:center;justify-content:center;
  background:radial-gradient(ellipse at center bottom, rgba(0,185,107,.12) 0%, transparent 70%);
  border-radius:18px;position:relative;min-height:400px;
}
#avatar3d{width:300px;height:400px;cursor:grab;border-radius:18px;}
#avatar3d:active{cursor:grabbing;}

.avatar-info{flex:1;display:flex;flex-direction:column;gap:12px;justify-content:center;padding-top:10px;}
.avatar-stat{
  display:flex;align-items:center;gap:12px;padding:12px 14px;
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);
  border-radius:14px;transition:all .2s;
}
.avatar-stat:hover{background:rgba(255,255,255,.08);border-color:rgba(0,185,107,.2);}
.avatar-stat-ico{
  width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;
  font-size:15px;flex-shrink:0;
}
.avatar-stat-ico.morph{background:rgba(0,185,107,.15);color:var(--g2);}
.avatar-stat-ico.goal{background:rgba(255,107,43,.15);color:var(--or2);}
.avatar-stat-ico.act{background:rgba(41,121,255,.15);color:#63a4ff;}
.avatar-stat-ico.state{background:rgba(124,58,237,.15);color:#a78bfa;}
.avatar-stat-label{font-size:10px;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.08em;font-weight:600;}
.avatar-stat-value{font-family:var(--fh);font-size:14px;font-weight:700;color:#fff;margin-top:2px;}

.avatar-controls{display:flex;gap:6px;margin-top:8px;flex-wrap:wrap;}
.av-ctrl-btn{
  padding:8px 14px;border-radius:10px;border:1px solid rgba(255,255,255,.1);
  background:rgba(255,255,255,.04);color:rgba(255,255,255,.7);
  font-size:11px;font-weight:600;font-family:var(--fb);cursor:pointer;
  transition:all .2s;display:flex;align-items:center;gap:5px;
}
.av-ctrl-btn:hover{background:rgba(0,185,107,.15);border-color:var(--g);color:var(--g2);}
.av-ctrl-btn.active{background:rgba(0,185,107,.2);border-color:var(--g);color:var(--g2);}

.avatar-legend{
  display:flex;gap:16px;padding:12px 24px 16px;position:relative;z-index:2;
  border-top:1px solid rgba(255,255,255,.06);
}
.legend-item{display:flex;align-items:center;gap:6px;font-size:11px;color:rgba(255,255,255,.45);}
.legend-dot{width:8px;height:8px;border-radius:50%;}

@media(max-width:1024px){
  .g4{grid-template-columns:repeat(2,1fr);}
  .g3{grid-template-columns:repeat(2,1fr);}
  .g21,.g12{grid-template-columns:1fr;}
}
@media(max-width:640px){
  .g4,.g3,.g2,.g21,.g12{grid-template-columns:1fr;}
  .wrap{padding:24px 16px 60px;}
  .tb{padding:0 16px;}
  .avatar-body{flex-direction:column;}
  .avatar-canvas-wrap{flex:0 0 auto;width:100%;min-height:320px;}
  #avatar3d{width:100%;height:320px;}
  .avatar-info{padding-top:0;}
  .avatar-legend{flex-wrap:wrap;}
}
</style>



<!-- ════ BODY ════ -->
<div class="wrap">
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
    <div class="kpi kpi-g"><div class="kpi-top"><div class="kpi-icon">⚖️</div><span class="kpi-trend">IMC</span></div><div class="kpi-val" style="color:<?= $imcColor ?>"><?= $imc ?: '—' ?></div><div class="kpi-lbl">Indice de masse corporelle</div><div class="kpi-sub" style="color:<?= $imcColor ?>"><?= $imcCat ?></div></div>
    <div class="kpi kpi-o"><div class="kpi-top"><div class="kpi-icon">🔥</div><span class="kpi-trend">kcal/j</span></div><div class="kpi-val"><?= number_format($calories) ?></div><div class="kpi-lbl">Calories recommandées</div><div class="kpi-sub">TDEE <?= number_format($tdee) ?> kcal</div></div>
    <div class="kpi kpi-b"><div class="kpi-top"><div class="kpi-icon">💧</div><span class="kpi-trend">eau</span></div><div class="kpi-val" style="color:#1a56db"><?= $eau ?>L</div><div class="kpi-lbl">Hydratation quotidienne</div><div class="kpi-sub"><?= $poids ?>kg × 33 ml</div></div>
    <div class="kpi kpi-v"><div class="kpi-top"><div class="kpi-icon">🥩</div><span class="kpi-trend">g/j</span></div><div class="kpi-val" style="color:#5b21b6"><?= $proteines ?>g</div><div class="kpi-lbl">Protéines recommandées</div><div class="kpi-sub">25 % des apports</div></div>
  </div>

  <!-- ═══════════════════════════════════════ -->
  <!-- ════ AVATAR IA 3D — HUMAIN RÉALISTE ════ -->
  <!-- ═══════════════════════════════════════ -->
  <div class="avatar-section">
    <div class="sec-lbl"><i class="fa fa-robot" style="margin-right:4px;"></i> Avatar IA — Modélisation corporelle 3D</div>
    <div class="avatar-card">
      <div class="avatar-header">
        <div class="avatar-title">
          <div class="avatar-title-icon">🧬</div>
          <div class="avatar-title-text">Modèle <span>Corporel IA</span></div>
        </div>
        <div class="ai-badge"><div class="ai-badge-dot"></div>IA ACTIVE</div>
      </div>
      <div class="avatar-body">
        <div class="avatar-canvas-wrap">
          <canvas id="avatar3d"></canvas>
        </div>
        <div class="avatar-info">
          <div class="avatar-stat">
            <div class="avatar-stat-ico morph"><i class="fa fa-weight-scale"></i></div>
            <div><div class="avatar-stat-label">Morphologie</div><div class="avatar-stat-value" id="avMorph"><?= $imcState==='underweight'?'Mince':($imcState==='normal'?'Équilibrée':($imcState==='overweight'?'Corpulente':'Ronde')) ?></div></div>
          </div>
          <div class="avatar-stat">
            <div class="avatar-stat-ico goal"><i class="fa fa-bullseye"></i></div>
            <div><div class="avatar-stat-label">Objectif</div><div class="avatar-stat-value" id="avGoal"><?= $user['objectif'] ?: 'Non défini' ?></div></div>
          </div>
          <div class="avatar-stat">
            <div class="avatar-stat-ico act"><i class="fa fa-person-running"></i></div>
            <div><div class="avatar-stat-label">Activité</div><div class="avatar-stat-value" id="avAct"><?= $user['activite'] ?: 'Non définie' ?></div></div>
          </div>
          <div class="avatar-stat">
            <div class="avatar-stat-ico state"><i class="fa fa-heart-pulse"></i></div>
            <div><div class="avatar-stat-label">IMC</div><div class="avatar-stat-value" id="avIMC"><?= $imc ?: '—' ?> — <?= $imcCat ?></div></div>
          </div>
          <div class="avatar-controls">
            <button class="av-ctrl-btn active" onclick="setAvatarPose('idle',this)"><i class="fa fa-person-standing"></i> Repos</button>
            <button class="av-ctrl-btn" onclick="setAvatarPose('walk',this)"><i class="fa fa-person-walking"></i> Marche</button>
            <button class="av-ctrl-btn" onclick="setAvatarPose('flex',this)"><i class="fa fa-dumbbell"></i> Muscle</button>
            <button class="av-ctrl-btn" onclick="toggleAvatarWire(this)"><i class="fa fa-cube"></i> Wire</button>
          </div>
        </div>
      </div>
      <div class="avatar-legend">
        <div class="legend-item"><div class="legend-dot" style="background:#00b96b;"></div>Normal</div>
        <div class="legend-item"><div class="legend-dot" style="background:#2979ff;"></div>Mince</div>
        <div class="legend-item"><div class="legend-dot" style="background:#ff6b2b;"></div>Surpoids</div>
        <div class="legend-item"><div class="legend-dot" style="background:#e53935;"></div>Obésité</div>
        <div class="legend-item"><div class="legend-dot" style="background:#7c3aed;"></div>Wireframe</div>
      </div>
    </div>
  </div>

  <!-- ════ BILAN SANTÉ ════ -->
  <div class="sec-lbl">Bilan santé</div>
  <div class="g2">
    <div class="card">
      <div class="card-hd"><div class="card-ttl"><i class="fa fa-weight-scale"></i> IMC détaillé</div><span class="c-tag" style="background:<?= $imcBg ?>;color:<?= $imcColor ?>;border:1px solid <?= $imcColor ?>33;"><?= $imcCat ?></span></div>
      <div class="imc-big" style="color:<?= $imcColor ?>"><?= $imc ?: '—' ?></div>
      <div class="imc-cat" style="color:<?= $imcColor ?>"><?= $imcCat ?></div>
      <div class="imc-scale"><div class="imc-needle" style="left:<?= $imc>0?min(97,max(2,($imc/40)*100)):2 ?>%"></div></div>
      <div class="imc-labels"><span>&lt;18.5<br>Insuffisant</span><span style="text-align:center">18.5–24.9<br>Normal ✓</span><span style="text-align:center">25–29.9<br>Surpoids</span><span style="text-align:right">&gt;30<br>Obésité</span></div>
      <div class="imc-pills"><div class="imc-pill"><div class="imc-pill-val"><?= $poids ?>kg</div><div class="imc-pill-lbl">Poids actuel</div></div><div class="imc-pill"><div class="imc-pill-val"><?= $taille ?>cm</div><div class="imc-pill-lbl">Taille</div></div><div class="imc-pill"><div class="imc-pill-val" style="font-size:12px;"><?= $poidsMinIdeal ?>–<?= $poidsMaxIdeal ?>kg</div><div class="imc-pill-lbl">Poids idéal</div></div></div>
    </div>
    <div class="card">
      <div class="card-hd"><div class="card-ttl"><i class="fa fa-chart-pie"></i> Score nutritionnel</div><span class="c-tag c-tag-g"><?= $score ?> / 100</span></div>
      <div class="score-wrap">
        <div class="ring-pos">
          <svg class="ring-svg" width="120" height="120" viewBox="0 0 120 120"><circle class="ring-bg" cx="60" cy="60" r="48" stroke-width="9"/><circle class="ring-fill" cx="60" cy="60" r="48" stroke-width="9" stroke-dasharray="301.59" stroke-dashoffset="<?= 301.59*(1-$score/100) ?>"/></svg>
          <div class="ring-center"><div class="ring-num"><?= $score ?></div><div class="ring-den">/100</div></div>
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
    <div class="card">
      <div class="card-hd"><div class="card-ttl"><i class="fa fa-utensils"></i> Macronutriments</div><span class="c-tag c-tag-o"><?= $calories ?> kcal</span></div>
      <div class="macro-donut-wrap">
        <canvas id="macroChart" width="120" height="120" style="flex-shrink:0;"></canvas>
        <div class="macro-legend">
          <div class="macro-row"><div class="macro-name"><span class="macro-dot" style="background:var(--g)"></span>Protéines</div><div><span class="macro-val"><?= $proteines ?>g</span> <span class="macro-pct">25%</span></div></div>
          <div class="macro-row"><div class="macro-name"><span class="macro-dot" style="background:#2979ff"></span>Glucides</div><div><span class="macro-val"><?= $glucides ?>g</span> <span class="macro-pct">50%</span></div></div>
          <div class="macro-row"><div class="macro-name"><span class="macro-dot" style="background:var(--or)"></span>Lipides</div><div><span class="macro-val"><?= $lipides ?>g</span> <span class="macro-pct">25%</span></div></div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-hd"><div class="card-ttl"><i class="fa fa-droplet" style="color:#1a56db;"></i> Hydratation</div><span class="c-tag" style="background:#e8f0ff;color:#1a56db;border:1px solid #c7d9ff;">Objectif <?= $eau ?>L</span></div>
      <div class="eau-big" id="eauNum">0.0 L</div><div class="eau-sub">sur <?= $eau ?>L recommandés</div>
      <div class="eau-track"><div class="eau-fill" id="eauFill" style="width:0%"></div></div>
      <div class="drops" id="drops"><?php $dc=max(6,min(10,(int)($eau*4))); for($i=0;$i<$dc;$i++): ?><div class="drop" onclick="tapEau(<?=$i?>)">💧</div><?php endfor; ?></div>
      <div class="drop-hint">Cliquez pour noter chaque verre consommé</div>
    </div>
    <div class="card">
      <div class="card-hd"><div class="card-ttl"><i class="fa fa-bullseye"></i> Objectifs journaliers</div></div>
      <div class="obj-item"><div class="obj-top"><span class="obj-lbl">🔥 Calories</span><span class="obj-val"><?= $calories ?> kcal</span></div><div class="obj-bar"><div class="obj-fill" style="width:68%;background:linear-gradient(90deg,var(--or),var(--or2));"></div></div></div>
      <div class="obj-item"><div class="obj-top"><span class="obj-lbl">🥩 Protéines</span><span class="obj-val"><?= $proteines ?>g</span></div><div class="obj-bar"><div class="obj-fill" style="width:55%;background:linear-gradient(90deg,var(--g),var(--g2));"></div></div></div>
      <div class="obj-item"><div class="obj-top"><span class="obj-lbl">🏃 Activité</span><span class="obj-val">3× / sem</span></div><div class="obj-bar"><div class="obj-fill" style="width:66%;background:linear-gradient(90deg,var(--g),var(--g2));"></div></div></div>
      <div class="obj-item"><div class="obj-top"><span class="obj-lbl">💧 Eau</span><span class="obj-val"><?= $eau ?>L</span></div><div class="obj-bar"><div class="obj-fill" id="eauObjBar" style="width:0%;background:linear-gradient(90deg,#1a56db,#63a4ff);"></div></div></div>
      <div class="obj-item"><div class="obj-top"><span class="obj-lbl">😴 Sommeil</span><span class="obj-val">8h</span></div><div class="obj-bar"><div class="obj-fill" style="width:75%;background:linear-gradient(90deg,#5b21b6,#a78bfa);"></div></div></div>
    </div>
  </div>

  <!-- ════ PLAN + ALERTES ════ -->
  <div class="sec-lbl">Plan alimentaire & recommandations</div>
  <div class="g21">
    <div class="card">
      <div class="card-hd"><div class="card-ttl"><i class="fa fa-clipboard-list"></i> Plan alimentaire — <?= date('d/m/Y') ?></div><span class="c-tag c-tag-g">5 repas</span></div>
      <table class="pt"><thead><tr><th>Repas</th><th>Heure</th><th>Aliments</th><th style="text-align:right">Calories</th><th style="text-align:right">Protéines</th></tr></thead><tbody><?php $badges=['a','b','c','d','c']; foreach($plan as $i=>$r): ?><tr><td><span class="pt-badge <?= $badges[$i] ?>"><?= $r[0] ?></span></td><td><span class="pt-time"><?= $r[1] ?></span></td><td><div class="pt-food"><?= $r[2] ?></div><div class="pt-desc"><?= $r[0] ?></div></td><td style="text-align:right"><span class="pt-kcal"><?= $r[3] ?> kcal</span></td><td style="text-align:right"><span class="pt-prot"><?= $r[4] ?>g</span></td></tr><?php endforeach; ?></tbody></table>
    </div>
    <div style="display:flex;flex-direction:column;gap:16px;">
      <div class="card">
        <div class="card-hd"><div class="card-ttl"><i class="fa fa-lightbulb" style="color:var(--or);"></i> Recommandations</div></div>
        <?php if($imc>=18.5&&$imc<25): ?>
        <div class="ai"><div class="ai-ico g">✅</div><div><div class="ai-ttl">IMC normal — continuez !</div><div class="ai-sub">Maintenez votre équilibre alimentaire actuel.</div></div></div>
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
        <div class="card-hd"><div class="card-ttl"><i class="fa fa-user"></i> Mon profil</div></div>
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
  data:{labels:['Protéines','Glucides','Lipides'],datasets:[{data:[25,50,25],backgroundColor:['#00b96b','#2979ff','#ff6b2b'],borderWidth:4,borderColor:'#fff',hoverOffset:8}]},
  options:{cutout:'68%',plugins:{legend:{display:false}},animation:{animateScale:true}}
});
/* Eau */
let eauCount=0;
const dc=document.querySelectorAll('.drop').length;
const eauObj=<?= $eau ?>;
const perDrop=eauObj/dc;
function tapEau(idx){eauCount=idx+1;document.querySelectorAll('.drop').forEach((d,i)=>d.classList.toggle('on',i<eauCount));const L=(eauCount*perDrop).toFixed(1);document.getElementById('eauNum').textContent=L+' L';const pct=Math.min(100,(eauCount/dc)*100);document.getElementById('eauFill').style.width=pct+'%';document.getElementById('eauObjBar').style.width=pct+'%';}
window.addEventListener('load',()=>{document.querySelectorAll('.obj-fill,.imc-needle').forEach(el=>{const w=el.style.width;if(w){el.style.width='0';setTimeout(()=>{el.style.width=w;},200);}});});

/* ═══════════════════════════════════════════════════ */
/* ════ THREE.JS — AVATAR HUMAIN RÉALISTE 3D ════ */
/* ═══════════════════════════════════════════════════ */
(function(){
  const cvs = document.getElementById('avatar3d');
  const CW = cvs.parentElement.clientWidth || 300;
  const CH = 400;
  cvs.width = CW; cvs.height = CH;

  const IMC_STATE = <?= json_encode($imcState) ?>;
  const IMC_VAL   = <?= json_encode($imc) ?>;

  /* Scene */
  const scene = new THREE.Scene();
  const cam = new THREE.PerspectiveCamera(38, CW/CH, 0.1, 100);
  cam.position.set(0, 1.0, 4.2);
  cam.lookAt(0, 0.85, 0);

  const ren = new THREE.WebGLRenderer({canvas:cvs, antialias:true, alpha:true});
  ren.setSize(CW, CH);
  ren.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  ren.setClearColor(0x000000, 0);
  ren.shadowMap.enabled = true;
  ren.shadowMap.type = THREE.PCFSoftShadowMap;

  /* Lights */
  scene.add(new THREE.AmbientLight(0xffffff, 0.35));
  const dL = new THREE.DirectionalLight(0xffffff, 0.85);
  dL.position.set(4, 6, 3); dL.castShadow = true;
  scene.add(dL);
  const rimL = new THREE.DirectionalLight(0x00b96b, 0.35);
  rimL.position.set(-3, 3, -2); scene.add(rimL);
  const fillL = new THREE.PointLight(0x00e676, 0.2, 8);
  fillL.position.set(0, -1, 3); scene.add(fillL);
  const topL = new THREE.PointLight(0xffffff, 0.15, 10);
  topL.position.set(0, 5, 0); scene.add(topL);

  /* Color palette based on IMC */
  let skinHue, bodyHue, emissiveVal;
  if(IMC_STATE==='underweight'){
    skinHue = 0x7cb9a0; bodyHue = 0x2979ff; emissiveVal = 0x001133;
  } else if(IMC_STATE==='normal'){
    skinHue = 0x8dc5a8; bodyHue = 0x00b96b; emissiveVal = 0x003322;
  } else if(IMC_STATE==='overweight'){
    skinHue = 0xb8c4a0; bodyHue = 0xff6b2b; emissiveVal = 0x331100;
  } else {
    skinHue = 0xc4b8a0; bodyHue = 0xe53935; emissiveVal = 0x330000;
  }

  const skinMat = new THREE.MeshPhongMaterial({
    color: skinHue, emissive: emissiveVal, emissiveIntensity: 0.08,
    shininess: 50, specular: 0x333333
  });
  const clothMat = new THREE.MeshPhongMaterial({
    color: bodyHue, emissive: emissiveVal, emissiveIntensity: 0.12,
    shininess: 40, specular: 0x222222
  });
  const darkMat = new THREE.MeshPhongMaterial({color: 0x1a1a1a, shininess: 20});
  const eyeWhiteMat = new THREE.MeshBasicMaterial({color: 0xeeeeee});
  const eyePupilMat = new THREE.MeshBasicMaterial({color: 0x111111});
  const lipMat = new THREE.MeshPhongMaterial({color: 0xcc8866, shininess: 30});
  const hairMat = new THREE.MeshPhongMaterial({color: 0x2a1a0a, shininess: 10});

  const bodyGroup = new THREE.Group();
  const allMeshes = [];

  /* ──── BODY PROPORTIONS ──── */
  let scaleF = 1.0;
  if(IMC_STATE==='underweight') scaleF = 0.78;
  else if(IMC_STATE==='normal') scaleF = 1.0;
  else if(IMC_STATE==='overweight') scaleF = 1.25;
  else scaleF = 1.55;

  const torsoW = 0.28 * scaleF;
  const torsoH = 0.42;
  const torsoD = 0.16 * scaleF;
  const limbR  = 0.048 * scaleF;
  const hipW   = 0.22 * scaleF;
  const shoulderW = 0.34 * scaleF;

  /* ──── HEAD (detailed) ──── */
  const headGroup = new THREE.Group();
  headGroup.position.y = torsoH + 0.18;

  /* Skull */
  const skullGeo = new THREE.SphereGeometry(0.12, 20, 20);
  const skull = new THREE.Mesh(skullGeo, skinMat);
  skull.scale.set(1, 1.15, 1);
  headGroup.add(skull);

  /* Face front - flatter */
  const faceGeo = new THREE.SphereGeometry(0.11, 20, 16, 0, Math.PI, 0, Math.PI * 0.55);
  const face = new THREE.Mesh(faceGeo, skinMat);
  face.rotation.x = 0;
  face.position.z = 0.02;
  headGroup.add(face);

  /* Hair */
  const hairGeo = new THREE.SphereGeometry(0.125, 16, 16, 0, Math.PI * 2, 0, Math.PI * 0.4);
  const hair = new THREE.Mesh(hairGeo, hairMat);
  hair.position.y = 0.02;
  headGroup.add(hair);

  /* Eyes */
  const eyeWhiteGeo = new THREE.SphereGeometry(0.022, 12, 12);
  const pupilGeo = new THREE.SphereGeometry(0.013, 8, 8);

  const eyeWL = new THREE.Mesh(eyeWhiteGeo, eyeWhiteMat);
  eyeWL.position.set(-0.038, 0.02, 0.1);
  headGroup.add(eyeWL);
  const pupilL = new THREE.Mesh(pupilGeo, eyePupilMat);
  pupilL.position.set(-0.038, 0.02, 0.118);
  headGroup.add(pupilL);

  const eyeWR = new THREE.Mesh(eyeWhiteGeo, eyeWhiteMat);
  eyeWR.position.set(0.038, 0.02, 0.1);
  headGroup.add(eyeWR);
  const pupilR = new THREE.Mesh(pupilGeo, eyePupilMat);
  pupilR.position.set(0.038, 0.02, 0.118);
  headGroup.add(pupilR);

  /* Eyebrows */
  const browGeo = new THREE.BoxGeometry(0.035, 0.006, 0.008);
  const browL = new THREE.Mesh(browGeo, hairMat);
  browL.position.set(-0.038, 0.045, 0.105);
  headGroup.add(browL);
  const browR = new THREE.Mesh(browGeo, hairMat);
  browR.position.set(0.038, 0.045, 0.105);
  headGroup.add(browR);

  /* Nose */
  const noseGeo = new THREE.ConeGeometry(0.012, 0.03, 8);
  const nose = new THREE.Mesh(noseGeo, skinMat);
  nose.position.set(0, -0.005, 0.115);
  nose.rotation.x = Math.PI * 0.15;
  headGroup.add(nose);

  /* Mouth */
  const mouthGeo = new THREE.TorusGeometry(0.018, 0.005, 8, 12, Math.PI);
  const mouth = new THREE.Mesh(mouthGeo, lipMat);
  mouth.position.set(0, -0.035, 0.1);
  mouth.rotation.x = Math.PI;
  headGroup.add(mouth);

  /* Ears */
  const earGeo = new THREE.SphereGeometry(0.02, 10, 10);
  const earL = new THREE.Mesh(earGeo, skinMat);
  earL.position.set(-0.115, 0.0, 0.0);
  earL.scale.set(0.5, 1, 0.7);
  headGroup.add(earL);
  const earR = new THREE.Mesh(earGeo, skinMat);
  earR.position.set(0.115, 0.0, 0.0);
  earR.scale.set(0.5, 1, 0.7);
  headGroup.add(earR);

  /* Neck */
  const neckGeo = new THREE.CylinderGeometry(0.055 * scaleF, 0.065 * scaleF, 0.1, 12);
  const neck = new THREE.Mesh(neckGeo, skinMat);
  neck.position.y = torsoH + 0.06;
  bodyGroup.add(neck);

  bodyGroup.add(headGroup);

  /* ──── TORSO ──── */
  /* Upper torso / chest */
  const chestGeo = new THREE.BoxGeometry(torsoW, torsoH * 0.4, torsoD, 4, 4, 4);
  const chest = new THREE.Mesh(chestGeo, clothMat);
  chest.position.y = torsoH * 0.72 + 0.1;
  bodyGroup.add(chest);

  /* Pectoral detail */
  const pecGeo = new THREE.SphereGeometry(torsoW * 0.3, 12, 12);
  const pecL = new THREE.Mesh(pecGeo, clothMat);
  pecL.position.set(-torsoW * 0.2, torsoH * 0.78 + 0.1, torsoD * 0.4);
  pecL.scale.set(1, 0.7, 0.5);
  bodyGroup.add(pecL);
  const pecR = new THREE.Mesh(pecGeo, clothMat);
  pecR.position.set(torsoW * 0.2, torsoH * 0.78 + 0.1, torsoD * 0.4);
  pecR.scale.set(1, 0.7, 0.5);
  bodyGroup.add(pecR);

  /* Abdomen */
  const absGeo = new THREE.BoxGeometry(torsoW * 0.9, torsoH * 0.35, torsoD * 0.85, 4, 4, 4);
  const abs = new THREE.Mesh(absGeo, skinMat);
  abs.position.y = torsoH * 0.35 + 0.1;
  bodyGroup.add(abs);

  /* Belt area */
  const beltGeo = new THREE.CylinderGeometry(torsoW * 0.48, torsoW * 0.48, 0.03, 16);
  const belt = new THREE.Mesh(beltGeo, darkMat);
  belt.position.y = torsoH * 0.15 + 0.1;
  bodyGroup.add(belt);

  /* ──── SHOULDERS & ARMS ──── */
  /* Shoulder joints */
  const shGeo = new THREE.SphereGeometry(limbR * 1.3, 12, 12);
  const shL = new THREE.Mesh(shGeo, clothMat);
  shL.position.set(-torsoW / 2 - limbR * 0.2, torsoH + 0.08, 0);
  bodyGroup.add(shL);
  const shR = new THREE.Mesh(shGeo, clothMat);
  shR.position.set(torsoW / 2 + limbR * 0.2, torsoH + 0.08, 0);
  bodyGroup.add(shR);

  /* Upper arms */
  const uaGeo = new THREE.CylinderGeometry(limbR * 0.9, limbR * 0.75, 0.22, 10);
  const uaL = new THREE.Mesh(uaGeo, skinMat);
  uaL.position.set(-torsoW / 2 - limbR * 0.55, torsoH * 0.75 + 0.08, 0);
  uaL.rotation.z = 0.12;
  bodyGroup.add(uaL);
  const uaR = new THREE.Mesh(uaGeo, skinMat);
  uaR.position.set(torsoW / 2 + limbR * 0.55, torsoH * 0.75 + 0.08, 0);
  uaR.rotation.z = -0.12;
  bodyGroup.add(uaR);

  /* Elbow joints */
  const elbGeo = new THREE.SphereGeometry(limbR * 0.75, 8, 8);
  const elbL = new THREE.Mesh(elbGeo, skinMat);
  elbL.position.set(-torsoW / 2 - limbR * 0.75, torsoH * 0.55 + 0.08, 0);
  bodyGroup.add(elbL);
  const elbR = new THREE.Mesh(elbGeo, skinMat);
  elbR.position.set(torsoW / 2 + limbR * 0.75, torsoH * 0.55 + 0.08, 0);
  bodyGroup.add(elbR);

  /* Forearms */
  const faGeo = new THREE.CylinderGeometry(limbR * 0.7, limbR * 0.55, 0.2, 10);
  const faL = new THREE.Mesh(faGeo, skinMat);
  faL.position.set(-torsoW / 2 - limbR * 0.85, torsoH * 0.35 + 0.08, 0);
  faL.rotation.z = 0.08;
  bodyGroup.add(faL);
  const faR = new THREE.Mesh(faGeo, skinMat);
  faR.position.set(torsoW / 2 + limbR * 0.85, torsoH * 0.35 + 0.08, 0);
  faR.rotation.z = -0.08;
  bodyGroup.add(faR);

  /* Hands */
  const handGeo = new THREE.SphereGeometry(limbR * 0.7, 10, 10);
  const handL = new THREE.Mesh(handGeo, skinMat);
  handL.position.set(-torsoW / 2 - limbR * 0.95, torsoH * 0.15 + 0.08, 0);
  handL.scale.set(0.8, 1.2, 0.6);
  bodyGroup.add(handL);
  const handR = new THREE.Mesh(handGeo, skinMat);
  handR.position.set(torsoW / 2 + limbR * 0.95, torsoH * 0.15 + 0.08, 0);
  handR.scale.set(0.8, 1.2, 0.6);
  bodyGroup.add(handR);

  /* ──── HIPS ──── */
  const hipGeo = new THREE.BoxGeometry(hipW, 0.08, torsoD * 0.9, 4, 2, 4);
  const hip = new THREE.Mesh(hipGeo, clothMat);
  hip.position.y = 0.1;
  bodyGroup.add(hip);

  /* ──── LEGS ──── */
  /* Upper legs / thighs */
  const ulGeo = new THREE.CylinderGeometry(limbR * 1.15, limbR * 0.85, 0.28, 10);
  const ulL = new THREE.Mesh(ulGeo, skinMat);
  ulL.position.set(-hipW * 0.3, -0.08, 0);
  bodyGroup.add(ulL);
  const ulR = new THREE.Mesh(ulGeo, skinMat);
  ulR.position.set(hipW * 0.3, -0.08, 0);
  bodyGroup.add(ulR);

  /* Knee joints */
  const kneeGeo = new THREE.SphereGeometry(limbR * 0.85, 10, 10);
  const kneeL = new THREE.Mesh(kneeGeo, skinMat);
  kneeL.position.set(-hipW * 0.3, -0.24, 0);
  bodyGroup.add(kneeL);
  const kneeR = new THREE.Mesh(kneeGeo, skinMat);
  kneeR.position.set(hipW * 0.3, -0.24, 0);
  bodyGroup.add(kneeR);

  /* Lower legs / calves */
  const llGeo = new THREE.CylinderGeometry(limbR * 0.8, limbR * 0.55, 0.3, 10);
  const llL = new THREE.Mesh(llGeo, skinMat);
  llL.position.set(-hipW * 0.3, -0.42, 0);
  bodyGroup.add(llL);
  const llR = new THREE.Mesh(llGeo, skinMat);
  llR.position.set(hipW * 0.3, -0.42, 0);
  bodyGroup.add(llR);

  /* Ankle joints */
  const ankleGeo = new THREE.SphereGeometry(limbR * 0.6, 8, 8);
  const ankleL = new THREE.Mesh(ankleGeo, skinMat);
  ankleL.position.set(-hipW * 0.3, -0.58, 0);
  bodyGroup.add(ankleL);
  const ankleR = new THREE.Mesh(ankleGeo, skinMat);
  ankleR.position.set(hipW * 0.3, -0.58, 0);
  bodyGroup.add(ankleR);

  /* Feet */
  const footGeo = new THREE.BoxGeometry(limbR * 1.6, limbR * 0.45, limbR * 2.8);
  const footL = new THREE.Mesh(footGeo, darkMat);
  footL.position.set(-hipW * 0.3, -0.62, limbR * 0.5);
  bodyGroup.add(footL);
  const footR = new THREE.Mesh(footGeo, darkMat);
  footR.position.set(hipW * 0.3, -0.62, limbR * 0.5);
  bodyGroup.add(footR);

  /* Collect meshes for animation */
  const animParts = {
    uaL, uaR, faL, faR, ulL, ulR, llL, llR,
    handL, handR, headGroup, chest, abs, hip,
    shL, shR, elbL, elbR, kneeL, kneeR, ankleL, ankleR
  };

  scene.add(bodyGroup);

  /* Ground shadow / glow */
  const shadowGeo = new THREE.CircleGeometry(0.8, 32);
  const shadowMat = new THREE.MeshBasicMaterial({color: bodyHue, transparent: true, opacity: 0.1});
  const shadow = new THREE.Mesh(shadowGeo, shadowMat);
  shadow.rotation.x = -Math.PI / 2;
  shadow.position.y = -0.65;
  scene.add(shadow);

  /* Particles */
  const pCount = 50;
  const pGeo = new THREE.BufferGeometry();
  const pPos = new Float32Array(pCount * 3);
  for(let i = 0; i < pCount; i++){
    pPos[i*3]   = (Math.random() - 0.5) * 4;
    pPos[i*3+1] = Math.random() * 3.5;
    pPos[i*3+2] = (Math.random() - 0.5) * 3;
  }
  pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
  const pMat = new THREE.PointsMaterial({color: bodyHue, size: 0.018, transparent: true, opacity: 0.4});
  const particles = new THREE.Points(pGeo, pMat);
  scene.add(particles);

  /* ──── INTERACTION ──── */
  let isDrag = false, prevX = 0, rotY = 0, targetRotY = 0;
  cvs.addEventListener('mousedown', e => { isDrag = true; prevX = e.clientX; });
  cvs.addEventListener('mousemove', e => { if(isDrag){ targetRotY += (e.clientX - prevX) * 0.008; prevX = e.clientX; }});
  cvs.addEventListener('mouseup', () => isDrag = false);
  cvs.addEventListener('mouseleave', () => isDrag = false);
  cvs.addEventListener('touchstart', e => { isDrag = true; prevX = e.touches[0].clientX; });
  cvs.addEventListener('touchmove', e => { if(isDrag){ targetRotY += (e.touches[0].clientX - prevX) * 0.008; prevX = e.touches[0].clientX; }});
  cvs.addEventListener('touchend', () => isDrag = false);

  /* Pose */
  let curPose = 'idle';
  let isWire = false;

  window.setAvatarPose = function(p, btn){
    curPose = p;
    document.querySelectorAll('.av-ctrl-btn').forEach(b => b.classList.remove('active'));
    if(btn) btn.classList.add('active');
  };

  window.toggleAvatarWire = function(btn){
    isWire = !isWire;
    if(btn){
      if(isWire) btn.classList.add('active');
      else btn.classList.remove('active');
    }
    bodyGroup.traverse(child => {
      if(child.isMesh){
        child.material.wireframe = isWire;
        if(isWire){
          child.material.opacity = 0.35;
          child.material.transparent = true;
          child.material.color.copy(new THREE.Color(bodyHue));
        } else {
          child.material.opacity = 1;
          child.material.transparent = false;
        }
      }
    });
  };

  /* ──── ANIMATION LOOP ──── */
  let t = 0;
  function loop(){
    requestAnimationFrame(loop);
    t += 0.016;

    /* Smooth rotation */
    if(!isDrag) targetRotY += 0.004;
    rotY += (targetRotY - rotY) * 0.04;
    bodyGroup.rotation.y = rotY;

    /* Breathing */
    const br = Math.sin(t * 2) * 0.006;
    if(chest) chest.scale.x = 1 + br;
    if(chest) chest.scale.z = 1 + br * 0.5;

    /* Poses */
    switch(curPose){
      case 'idle':
        bodyGroup.position.y = Math.sin(t * 1.2) * 0.008;
        uaL.rotation.x = Math.sin(t * 1) * 0.04;
        uaR.rotation.x = Math.sin(t * 1 + Math.PI) * 0.04;
        faL.rotation.x = -0.05 + Math.sin(t * 0.7) * 0.06;
        faR.rotation.x = -0.05 + Math.sin(t * 0.7 + Math.PI) * 0.06;
        ulL.rotation.x = 0; ulR.rotation.x = 0;
        llL.rotation.x = 0; llR.rotation.x = 0;
        headGroup.rotation.z = Math.sin(t * 0.5) * 0.02;
        break;
      case 'walk':{
        const wc = t * 3;
        bodyGroup.position.y = Math.abs(Math.sin(wc)) * 0.035;
        ulL.rotation.x = Math.sin(wc) * 0.45;
        ulR.rotation.x = Math.sin(wc + Math.PI) * 0.45;
        llL.rotation.x = Math.max(0, -Math.sin(wc)) * 0.35;
        llR.rotation.x = Math.max(0, -Math.sin(wc + Math.PI)) * 0.35;
        uaL.rotation.x = Math.sin(wc + Math.PI) * 0.4;
        uaR.rotation.x = Math.sin(wc) * 0.4;
        faL.rotation.x = -0.4 + Math.sin(wc + Math.PI) * 0.25;
        faR.rotation.x = -0.4 + Math.sin(wc) * 0.25;
        chest.rotation.y = Math.sin(wc) * 0.04;
        headGroup.rotation.z = Math.sin(wc) * 0.03;
        break;
      }
      case 'flex':{
        bodyGroup.position.y = 0;
        uaL.rotation.x = -0.1; uaL.rotation.z = 0.35 + Math.sin(t * 2.5) * 0.04;
        uaR.rotation.x = -0.1; uaR.rotation.z = -0.35 - Math.sin(t * 2.5) * 0.04;
        faL.rotation.x = -1.3 + Math.sin(t * 3) * 0.35;
        faR.rotation.x = -1.3 + Math.sin(t * 3) * 0.35;
        ulL.rotation.x = 0; ulR.rotation.x = 0;
        llL.rotation.x = 0; llR.rotation.x = 0;
        chest.scale.x = 1.08 + Math.sin(t * 3) * 0.02;
        chest.scale.z = 1.05 + Math.sin(t * 3) * 0.02;
        headGroup.rotation.z = 0;
        break;
      }
    }

    /* Particles */
    const arr = particles.geometry.attributes.position.array;
    for(let i = 0; i < pCount; i++){
      arr[i*3+1] += Math.sin(t + i * 0.5) * 0.0008;
      if(arr[i*3+1] > 3.5) arr[i*3+1] = 0;
    }
    particles.geometry.attributes.position.needsUpdate = true;
    particles.rotation.y = t * 0.08;

    shadowMat.opacity = 0.08 + Math.sin(t * 2) * 0.025;

    ren.render(scene, cam);
  }
  loop();

  /* Resize */
  window.addEventListener('resize', () => {
    const nw = cvs.parentElement.clientWidth;
    cvs.width = nw;
    cam.aspect = nw / CH;
    cam.updateProjectionMatrix();
    ren.setSize(nw, CH);
  });
})();
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
