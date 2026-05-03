<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: /ProjetWeb-User/index.php?url=User/auth"); exit; }

$user = $_SESSION['user'];

$imc = ($user['poids']>0 && $user['taille']>0)
    ? round($user['poids'] / pow($user['taille']/100,2),1) : 0;

$imcCat='—'; $imcColor='#00b96b'; $imcBg='#e6faf2';
if($imc>0){
  if     ($imc<18.5){$imcCat='Insuffisance pondérale';$imcColor='#2979ff';$imcBg='#e8f0ff';}
  elseif ($imc<25)  {$imcCat='Poids normal';           $imcColor='#00b96b';$imcBg='#e6faf2';}
  elseif ($imc<30)  {$imcCat='Surpoids';               $imcColor='#ff6b2b';$imcBg='#fff0eb';}
  else              {$imcCat='Obésité';                 $imcColor='#e53935';$imcBg='#ffebee';}
}

$piMin=$user['taille']>0?round(18.5*pow($user['taille']/100,2),1):0;
$piMax=$user['taille']>0?round(24.9*pow($user['taille']/100,2),1):0;

$done=0;
foreach(['nom','email','poids','taille','age','objectif'] as $f) if($user[$f]??'') $done++;
$completPct=round($done/6*100);

include __DIR__ . '/../partials/header.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,700;0,9..144,900;1,9..144,700&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
  height:64px;background:#fff;border-bottom:1px solid var(--bdr);
  padding:0 40px;display:flex;align-items:center;justify-content:space-between;
  position:sticky;top:0;z-index:200;
}
.tb-logo{display:flex;align-items:center;gap:10px;text-decoration:none;}
.tb-logo-mark{
  width:34px;height:34px;border-radius:10px;
  background:linear-gradient(135deg,var(--g),var(--g2));
  display:flex;align-items:center;justify-content:center;font-size:16px;
  box-shadow:0 4px 12px rgba(0,185,107,.3);
}
.tb-logo-name{font-family:var(--fh);font-size:20px;font-weight:700;color:var(--ink);}
.tb-logo-name em{color:var(--g);font-style:normal;}
.tb-nav{display:flex;gap:2px;}
.tb-link{
  display:flex;align-items:center;gap:7px;
  padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;
  color:var(--ink2);text-decoration:none;transition:all .18s;
}
.tb-link:hover{background:var(--bg3);color:var(--ink);}
.tb-link.on{background:var(--bg3);color:var(--g);}
.tb-link i{font-size:12px;color:inherit;}
.tb-right{display:flex;align-items:center;gap:10px;}
.tb-logout{
  display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;
  border:1px solid var(--bdr);font-size:13px;font-weight:500;color:var(--ink2);
  text-decoration:none;transition:all .15s;
}
.tb-logout:hover{border-color:var(--or);color:var(--or);}

/* ════ HERO BANNER ════ */
.hero{
  position:relative;height:240px;overflow:hidden;
}
.hero-img{
  width:100%;height:100%;object-fit:cover;
  filter:brightness(.65) saturate(1.3);
  transform:scale(1.04);
  transition:transform 10s ease;
}
.hero:hover .hero-img{transform:scale(1);}

.hero-overlay{
  position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(0,122,71,.7),rgba(255,107,43,.3));
}

/* Grain */
.hero-overlay::after{
  content:'';position:absolute;inset:0;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
  opacity:.4;pointer-events:none;
}

.hero-content{
  position:absolute;bottom:0;left:0;right:0;
  padding:0 48px 32px;
  display:flex;align-items:flex-end;gap:22px;
}

.hero-av{
  width:96px;height:96px;border-radius:50%;
  background:linear-gradient(135deg,var(--g),var(--gd));
  border:4px solid #fff;
  box-shadow:0 8px 28px rgba(0,0,0,.2);
  display:flex;align-items:center;justify-content:center;
  font-family:var(--fh);font-size:36px;font-weight:900;color:#fff;
  flex-shrink:0;transform:translateY(28px);
  position:relative;z-index:1;
}

/* Pulse ring */
.hero-av::before{
  content:'';
  position:absolute;inset:-6px;border-radius:50%;
  border:2px solid rgba(0,185,107,.4);
  animation:avPulse 2.5s ease-in-out infinite;
}
@keyframes avPulse{0%,100%{transform:scale(1);opacity:.4}50%{transform:scale(1.06);opacity:.8}}

.hero-info{padding-bottom:4px;}
.hero-name{
  font-family:var(--fh);font-size:26px;font-weight:900;
  color:#fff;letter-spacing:-.5px;
  text-shadow:0 2px 12px rgba(0,0,0,.2);
}
.hero-sub{font-size:13px;color:rgba(255,255,255,.7);margin-top:4px;}

.hero-chips{
  margin-left:auto;display:flex;gap:8px;padding-bottom:4px;flex-wrap:wrap;
}
.hchip{
  padding:6px 16px;border-radius:30px;font-size:12px;font-weight:600;
  background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);
  color:#fff;backdrop-filter:blur(6px);
  transition:all .2s;
}
.hchip:hover{background:rgba(255,255,255,.28);}

/* ════ WRAP ════ */
.wrap{max-width:1200px;margin:0 auto;padding:52px 40px 80px;}

/* ════ GRID ════ */
.g-main{display:grid;grid-template-columns:320px 1fr;gap:24px;}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.g4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;}

/* ════ CARD ════ */
.card{
  background:#fff;border:1px solid var(--bdr);border-radius:20px;
  padding:24px;margin-bottom:18px;position:relative;overflow:hidden;
  transition:box-shadow .22s;
}
.card:hover{box-shadow:0 8px 32px rgba(0,0,0,.06);}

.card-hd{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:18px;padding-bottom:14px;
  border-bottom:1px solid var(--bg3);
}
.card-ttl{
  font-size:13px;font-weight:700;color:var(--ink);
  display:flex;align-items:center;gap:8px;
}
.card-ttl i{font-size:13px;color:var(--g);}
.c-tag{
  font-size:11px;font-weight:600;padding:4px 12px;border-radius:20px;
}
.c-tag-g{background:#e6faf2;color:var(--g);border:1px solid #c3e8d6;}
.c-tag-o{background:#fff0eb;color:var(--or);border:1px solid #ffd5bf;}

/* ════ PHOTO METRIC CARDS ════ */
.metric-card{
  border-radius:18px;overflow:hidden;
  border:1px solid var(--bdr);background:#fff;
  transition:transform .22s,box-shadow .22s;
}
.metric-card:hover{transform:translateY(-5px);box-shadow:0 14px 36px rgba(0,0,0,.1);}
.metric-img{width:100%;height:100px;object-fit:cover;display:block;}
.metric-body{padding:14px;text-align:center;}
.metric-ico{font-size:20px;margin-bottom:4px;display:block;}
.metric-lbl{
  font-size:9px;font-weight:700;text-transform:uppercase;
  letter-spacing:.1em;color:var(--ink3);
}
.metric-val{
  font-family:var(--fh);font-size:22px;font-weight:900;
  letter-spacing:-1px;margin-top:3px;
}

/* ════ PROFIL SIDEBAR ════ */
.profil-av-wrap{text-align:center;margin-bottom:20px;}
.profil-av{
  width:80px;height:80px;border-radius:50%;
  background:linear-gradient(135deg,var(--g),var(--gd));
  display:flex;align-items:center;justify-content:center;
  font-family:var(--fh);font-size:30px;font-weight:900;color:#fff;
  margin:0 auto 12px;
  box-shadow:0 8px 24px rgba(0,185,107,.3);
}
.profil-name{font-family:var(--fh);font-size:18px;font-weight:700;}
.profil-email{font-size:12px;color:var(--ink3);margin-top:2px;}
.profil-obj{
  display:inline-block;margin-top:10px;
  padding:5px 14px;border-radius:20px;
  background:#e6faf2;color:var(--g);
  border:1px solid #c3e8d6;font-size:11px;font-weight:600;
}

/* Stat rows */
.sr{
  display:flex;align-items:center;padding:11px 0;
  border-bottom:1px solid var(--bg3);
}
.sr:last-child{border-bottom:none;}
.sr-ico{
  width:32px;height:32px;border-radius:9px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;font-size:14px;
}
.sr-ico.g{background:#e6faf2;}
.sr-ico.o{background:#fff0eb;}
.sr-ico.b{background:#e8f0ff;}
.sr-ico.v{background:#f3eaff;}
.sr-lbl{font-size:12px;color:var(--ink3);margin-left:10px;flex:1;}
.sr-val{font-size:13px;font-weight:700;color:var(--ink);}

/* Completeness bar */
.compl-wrap{margin-top:16px;}
.compl-top{display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;}
.compl-lbl{color:var(--ink2);font-weight:500;}
.compl-val{font-weight:700;color:var(--g);}
.compl-bar{height:6px;background:var(--bg3);border-radius:3px;overflow:hidden;}
.compl-fill{
  height:100%;border-radius:3px;
  background:linear-gradient(90deg,var(--g),var(--g2));
  transition:width 1.2s cubic-bezier(.34,1.56,.64,1);
}

/* Objectif card */
.obj-card{
  background:linear-gradient(135deg,#e6faf2,#f0fdf7);
  border:1px solid #c3e8d6;border-radius:14px;padding:16px;margin-top:18px;
}
.obj-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--ink3);margin-bottom:6px;}
.obj-val{font-family:var(--fh);font-size:19px;font-weight:700;color:var(--gd);}
.obj-sub{font-size:12px;color:var(--ink3);margin-top:2px;}

/* ════ IMC SCALE ════ */
.imc-big{
  font-family:var(--fh);font-size:60px;font-weight:900;
  letter-spacing:-3px;line-height:1;margin-bottom:4px;
}
.imc-cat{font-size:13px;font-weight:600;margin-bottom:20px;}
.imc-scale{
  height:10px;border-radius:5px;
  background:linear-gradient(90deg,#2979ff 0%,var(--g) 35%,var(--or) 65%,#e53935 100%);
  position:relative;margin-bottom:8px;
  box-shadow:0 2px 8px rgba(0,0,0,.1);
}
.imc-needle{
  position:absolute;top:-5px;
  width:5px;height:20px;border-radius:3px;
  background:var(--ink);transform:translateX(-50%);
  box-shadow:0 2px 6px rgba(0,0,0,.3);
  transition:left 1.2s cubic-bezier(.34,1.56,.64,1);
}
.imc-labels{display:flex;justify-content:space-between;font-size:10px;color:var(--ink3);}
.imc-pills{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-top:18px;}
.imc-pill{
  text-align:center;padding:12px 6px;
  background:var(--bg3);border:1px solid var(--bdr);border-radius:12px;
  transition:all .2s;
}
.imc-pill:hover{border-color:var(--g);background:#e6faf2;}
.imc-pill-val{font-size:14px;font-weight:700;color:var(--ink);}
.imc-pill-lbl{font-size:9px;color:var(--ink3);margin-top:2px;text-transform:uppercase;letter-spacing:.06em;}

/* ════ PROGRESS BARS ════ */
.prog-item{margin-bottom:14px;}
.prog-item:last-child{margin-bottom:0;}
.prog-top{display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px;}
.prog-lbl{color:var(--ink2);font-weight:500;}
.prog-val{font-weight:700;color:var(--ink);}
.prog-bar{height:6px;background:var(--bg3);border-radius:3px;overflow:hidden;}
.prog-fill{height:100%;border-radius:3px;transition:width 1.1s cubic-bezier(.34,1.56,.64,1);}

/* ════ BTN ════ */
.btn-g{
  display:flex;align-items:center;justify-content:center;gap:8px;
  padding:13px;border-radius:12px;
  background:linear-gradient(135deg,var(--g),var(--g2));
  color:#fff;font-size:13px;font-weight:700;
  font-family:var(--fb);border:none;cursor:pointer;
  transition:all .22s;box-shadow:0 6px 20px rgba(0,185,107,.3);
  width:100%;text-decoration:none;margin-top:18px;
}
.btn-g:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(0,185,107,.4);color:#fff;}

/* ════ OVERLAY / DRAWER ════ */
.overlay{
  position:fixed;inset:0;background:rgba(13,31,15,.35);
  z-index:900;opacity:0;pointer-events:none;
  transition:opacity .3s;backdrop-filter:blur(3px);
}
.overlay.open{opacity:1;pointer-events:all;}

.drawer{
  position:fixed;top:0;right:-500px;
  width:480px;height:100vh;
  background:#fff;z-index:901;
  box-shadow:-8px 0 40px rgba(0,0,0,.12);
  display:flex;flex-direction:column;
  transition:right .35s cubic-bezier(.25,.46,.45,.94);
  overflow:hidden;
}
.drawer.open{right:0;}

.dw-hd{
  padding:24px 28px 20px;
  border-bottom:1px solid var(--bdr);
  display:flex;align-items:center;justify-content:space-between;
  flex-shrink:0;
  background:linear-gradient(135deg,var(--bg3),#fff);
}

/* Accent line */
.dw-hd::after{
  content:'';
  position:absolute;bottom:0;left:0;
  width:64px;height:2px;
  background:linear-gradient(90deg,var(--g),var(--or));
}

.dw-hd-title{font-family:var(--fh);font-size:20px;font-weight:700;color:var(--ink);}
.dw-hd-sub{font-size:12px;color:var(--ink3);margin-top:2px;}

.dw-close{
  width:34px;height:34px;border-radius:9px;
  background:var(--bg3);border:1px solid var(--bdr);
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;color:var(--ink2);font-size:13px;transition:all .15s;
}
.dw-close:hover{background:#ffebee;border-color:#ffcdd2;color:#e53935;}

.dw-body{
  flex:1;overflow-y:auto;padding:24px 28px;
  scrollbar-width:thin;scrollbar-color:var(--g) transparent;
}

/* Form sections */
.fs-title{
  font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;
  color:var(--ink3);margin-bottom:14px;margin-top:22px;
  display:flex;align-items:center;gap:8px;
}
.fs-title:first-child{margin-top:0;}
.fs-title::after{content:'';flex:1;height:1px;background:var(--bdr);}

/* Field */
.fg{margin-bottom:14px;position:relative;}
.fg label{display:block;font-size:12px;font-weight:600;color:var(--ink2);margin-bottom:5px;}

.fg input,.fg select{
  width:100%;padding:12px 14px;
  border:1.5px solid var(--bdr);border-radius:11px;
  font-family:var(--fb);font-size:14px;color:var(--ink);
  background:#fff;outline:none;
  transition:border-color .2s,box-shadow .2s,background .2s;
  appearance:none;
}

.fg input::placeholder{color:var(--ink3);}

.fg input.ok,.fg select.ok{
  border-color:var(--g)!important;
  box-shadow:0 0 0 3px rgba(0,185,107,.12)!important;
  background:#f7fdf9!important;
}
.fg input.err,.fg select.err{
  border-color:var(--or)!important;
  box-shadow:0 0 0 3px rgba(255,107,43,.1)!important;
  background:#fff8f5!important;
}

.fg-msg{font-size:11px;margin-top:4px;display:none;}
.fg-msg.show-err{display:block;color:var(--or);}
.fg-msg.show-ok{display:block;color:var(--g);}

/* State icon */
.fg-ico{
  position:absolute;right:13px;top:36px;
  font-size:13px;pointer-events:none;opacity:0;transition:opacity .2s;
}
.fg.is-ok  .fg-ico{opacity:1;color:var(--g);}
.fg.is-err .fg-ico{opacity:1;color:var(--or);}

.fg-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}

/* Pwd strength */
.pwd-s{height:4px;background:var(--bg3);border-radius:2px;overflow:hidden;margin:6px 0 4px;}
.pwd-b{height:100%;border-radius:2px;width:0;transition:width .3s,background .3s;}

/* Drawer footer */
.dw-ft{
  padding:16px 28px;border-top:1px solid var(--bdr);
  display:flex;gap:10px;flex-shrink:0;background:var(--bg3);
}
.dw-save{
  flex:1;padding:13px;
  background:linear-gradient(135deg,var(--g),var(--g2));
  color:#fff;border:none;border-radius:11px;
  font-size:14px;font-weight:700;font-family:var(--fb);
  cursor:pointer;transition:all .22s;
  box-shadow:0 4px 16px rgba(0,185,107,.3);
}
.dw-save:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(0,185,107,.4);}
.dw-cancel{
  padding:13px 20px;background:#fff;color:var(--ink2);
  border:1.5px solid var(--bdr);border-radius:11px;
  font-size:13px;font-weight:600;font-family:var(--fb);
  cursor:pointer;transition:all .15s;
}
.dw-cancel:hover{border-color:var(--or);color:var(--or);}

/* ════ FLASH ════ */
.flash{
  position:fixed;top:76px;right:24px;
  background:#fff;border:1px solid #c3e8d6;
  border-left:4px solid var(--g);
  border-radius:12px;padding:14px 20px;
  display:flex;align-items:center;gap:10px;
  font-size:13px;font-weight:500;color:var(--ink);
  box-shadow:0 8px 28px rgba(0,0,0,.1);z-index:9999;
  animation:flashIn .35s ease;
}
@keyframes flashIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}

/* ════ RESPONSIVE ════ */
@media(max-width:960px){
  .g-main{grid-template-columns:1fr;}
  .imc-pills{grid-template-columns:repeat(2,1fr);}
  .g4{grid-template-columns:1fr 1fr;}
  .drawer{width:100%;right:-100%;}
}
@media(max-width:640px){
  .wrap{padding:40px 16px 60px;}
  .hero-content{padding:0 20px 28px;}
  .g4{grid-template-columns:1fr 1fr;}
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
    <a href="index.php?url=User/dashboard" class="tb-link"><i class="fa fa-gauge"></i> Dashboard</a>
    <a href="index.php?url=User/profile"   class="tb-link on"><i class="fa fa-user"></i> Profil</a>
  </nav>
  <div class="tb-right">
    <a href="index.php?url=User/logout" class="tb-logout"><i class="fa fa-right-from-bracket"></i> Déconnexion</a>
  </div>
</div>

<!-- ════ HERO BANNER ════ -->
<div class="hero">
  <img class="hero-img"
       src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=1400&q=85"
       alt="profile banner">
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-av"><?= strtoupper(substr($user['nom'],0,1)) ?></div>
    <div class="hero-info">
      <div class="hero-name"><?= htmlspecialchars($user['nom']) ?></div>
      <div class="hero-sub"><?= htmlspecialchars($user['email']) ?> &nbsp;·&nbsp; Membre EcoNutri</div>
    </div>
    <div class="hero-chips">
      <?php if($user['objectif']??''): ?><span class="hchip">🎯 <?= htmlspecialchars($user['objectif']) ?></span><?php endif; ?>
      <?php if($user['activite']??''): ?><span class="hchip">🏃 <?= htmlspecialchars($user['activite']) ?></span><?php endif; ?>
      <?php if($imc>0): ?><span class="hchip" style="background:<?= $imcColor ?>33;border-color:<?= $imcColor ?>55;">IMC <?= $imc ?></span><?php endif; ?>
    </div>
  </div>
</div>

<!-- ════ METRIC CARDS ════ -->
<div style="max-width:1200px;margin:0 auto;padding:28px 40px 0;">
  <div class="g4">
    <div class="metric-card">
      <img class="metric-img" src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&q=80" alt="">
      <div class="metric-body">
        <span class="metric-ico">🎂</span>
        <div class="metric-lbl">Âge</div>
        <div class="metric-val" style="color:#2979ff"><?= $user['age']??'—' ?><small style="font-size:12px;font-weight:400;color:var(--ink3);"> ans</small></div>
      </div>
    </div>
    <div class="metric-card">
      <img class="metric-img" src="https://images.unsplash.com/photo-1554284126-aa88f22d8b74?w=400&q=80" alt="">
      <div class="metric-body">
        <span class="metric-ico">⚖️</span>
        <div class="metric-lbl">Poids</div>
        <div class="metric-val" style="color:var(--or)"><?= $user['poids']??'—' ?><small style="font-size:12px;font-weight:400;color:var(--ink3);"> kg</small></div>
      </div>
    </div>
    <div class="metric-card">
      <img class="metric-img" src="https://images.unsplash.com/photo-1518611012118-696072aa579a?w=400&q=80" alt="">
      <div class="metric-body">
        <span class="metric-ico">📏</span>
        <div class="metric-lbl">Taille</div>
        <div class="metric-val" style="color:#7c3aed"><?= $user['taille']??'—' ?><small style="font-size:12px;font-weight:400;color:var(--ink3);"> cm</small></div>
      </div>
    </div>
    <div class="metric-card">
      <img class="metric-img" src="https://images.unsplash.com/photo-1505576399279-565b52d4ac71?w=400&q=80" alt="">
      <div class="metric-body">
        <span class="metric-ico">🩺</span>
        <div class="metric-lbl">IMC</div>
        <div class="metric-val" style="color:<?= $imcColor ?>"><?= $imc?:'—' ?></div>
      </div>
    </div>
  </div>
</div>

<!-- ════ MAIN BODY ════ -->
<div class="wrap">
<div class="g-main">

  <!-- ── COLONNE GAUCHE ── -->
  <div>
    <div class="card">
      <div class="profil-av-wrap">
        <div class="profil-av"><?= strtoupper(substr($user['nom'],0,1)) ?></div>
        <div class="profil-name"><?= htmlspecialchars($user['nom']) ?></div>
        <div class="profil-email"><?= htmlspecialchars($user['email']) ?></div>
        <?php if($user['objectif']??''): ?>
        <div class="profil-obj">🎯 <?= htmlspecialchars($user['objectif']) ?></div>
        <?php endif; ?>
      </div>

      <div class="sr"><div class="sr-ico g">🎂</div><span class="sr-lbl">Âge</span><span class="sr-val"><?= $user['age']??'—' ?> ans</span></div>
      <div class="sr"><div class="sr-ico o">⚖️</div><span class="sr-lbl">Poids</span><span class="sr-val"><?= $user['poids']??'—' ?> kg</span></div>
      <div class="sr"><div class="sr-ico b">📏</div><span class="sr-lbl">Taille</span><span class="sr-val"><?= $user['taille']??'—' ?> cm</span></div>
      <div class="sr"><div class="sr-ico v">🏃</div><span class="sr-lbl">Activité</span><span class="sr-val"><?= htmlspecialchars($user['activite']??'—') ?></span></div>

      <div class="compl-wrap">
        <div class="compl-top">
          <span class="compl-lbl">Profil complété</span>
          <span class="compl-val"><?= $completPct ?>%</span>
        </div>
        <div class="compl-bar"><div class="compl-fill" style="width:<?= $completPct ?>%"></div></div>
      </div>

      <button class="btn-g" onclick="openDrawer()">
        <i class="fa fa-pen" style="font-size:11px;"></i> Modifier mon profil
      </button>
    </div>

    <div class="obj-card">
      <div class="obj-lbl">Mon objectif</div>
      <div class="obj-val"><?= htmlspecialchars($user['objectif']??'Non défini') ?></div>
      <div class="obj-sub">Programme nutrition personnalisé</div>
    </div>
  </div>

  <!-- ── COLONNE DROITE ── -->
  <div>

    <!-- IMC détaillé -->
    <div class="card">
      <div class="card-hd">
        <div class="card-ttl"><i class="fa fa-weight-scale"></i> Indice de Masse Corporelle</div>
        <span class="c-tag" style="background:<?= $imcBg ?>;color:<?= $imcColor ?>;border:1px solid <?= $imcColor ?>33;"><?= $imcCat ?></span>
      </div>

      <div style="display:flex;align-items:flex-end;gap:28px;margin-bottom:22px;">
        <div>
          <div class="imc-big" style="color:<?= $imcColor ?>"><?= $imc?:'—' ?></div>
          <div class="imc-cat" style="color:<?= $imcColor ?>"><?= $imcCat ?></div>
        </div>
        <div style="flex:1;">
          <div class="imc-scale">
            <div class="imc-needle" style="left:<?= $imc>0?min(97,max(2,($imc/40)*100)):2 ?>%"></div>
          </div>
          <div class="imc-labels">
            <span>&lt;18.5<br>Insuffisant</span>
            <span style="text-align:center">18.5–24.9<br>Normal ✓</span>
            <span style="text-align:center">25–29.9<br>Surpoids</span>
            <span style="text-align:right">&gt;30<br>Obésité</span>
          </div>
        </div>
      </div>

      <div class="imc-pills">
        <div class="imc-pill"><div class="imc-pill-val" style="color:var(--or)"><?= $user['poids']??'—' ?> kg</div><div class="imc-pill-lbl">Poids actuel</div></div>
        <div class="imc-pill"><div class="imc-pill-val" style="color:#7c3aed"><?= $user['taille']??'—' ?> cm</div><div class="imc-pill-lbl">Taille</div></div>
        <div class="imc-pill"><div class="imc-pill-val" style="color:var(--g)"><?= $piMin ?>–<?= $piMax ?> kg</div><div class="imc-pill-lbl">Poids idéal</div></div>
        <div class="imc-pill"><div class="imc-pill-val" style="color:#2979ff">18.5–24.9</div><div class="imc-pill-lbl">IMC cible</div></div>
      </div>
    </div>

    <!-- 2 cols : photo info + objectifs -->
    <div class="g2">

      <div class="card" style="padding:0;overflow:hidden;margin-bottom:0;">
        <img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=600&q=80"
             style="width:100%;height:170px;object-fit:cover;display:block;">
        <div style="padding:18px;">
          <div style="font-size:14px;font-weight:700;margin-bottom:6px;">🥗 Alimentation durable</div>
          <div style="font-size:12px;color:var(--ink2);line-height:1.65;">Adoptez des habitudes alimentaires saines et respectueuses de l'environnement. Chaque repas compte.</div>
        </div>
      </div>

      <div class="card" style="margin-bottom:0;">
        <div class="card-hd">
          <div class="card-ttl"><i class="fa fa-bullseye"></i> Mes objectifs</div>
        </div>
        <div class="prog-item">
          <div class="prog-top"><span class="prog-lbl">Profil complété</span><span class="prog-val" style="color:var(--g)"><?= $completPct ?>%</span></div>
          <div class="prog-bar"><div class="prog-fill" style="width:<?= $completPct ?>%;background:linear-gradient(90deg,var(--g),var(--g2));"></div></div>
        </div>
        <div class="prog-item">
          <div class="prog-top"><span class="prog-lbl">Activité physique</span><span class="prog-val" style="color:var(--or)">60%</span></div>
          <div class="prog-bar"><div class="prog-fill" style="width:60%;background:linear-gradient(90deg,var(--or),var(--or2));"></div></div>
        </div>
        <div class="prog-item">
          <div class="prog-top"><span class="prog-lbl">Hydratation</span><span class="prog-val" style="color:#2979ff">45%</span></div>
          <div class="prog-bar"><div class="prog-fill" style="width:45%;background:linear-gradient(90deg,#2979ff,#63a4ff);"></div></div>
        </div>
        <div class="prog-item">
          <div class="prog-top"><span class="prog-lbl">Alimentation équilibrée</span><span class="prog-val" style="color:#7c3aed">72%</span></div>
          <div class="prog-bar"><div class="prog-fill" style="width:72%;background:linear-gradient(90deg,#7c3aed,#a78bfa);"></div></div>
        </div>
      </div>

    </div>

  </div>
</div>
</div>

<!-- ════ OVERLAY ════ -->
<div class="overlay" id="overlay" onclick="closeDrawer()"></div>

<!-- ════ DRAWER ════ -->
<div class="drawer" id="drawer">
  <div class="dw-hd" style="position:relative;">
    <div>
      <div class="dw-hd-title">✏ Modifier mon profil</div>
      <div class="dw-hd-sub">Mettez à jour vos informations personnelles</div>
    </div>
    <div class="dw-close" onclick="closeDrawer()"><i class="fa fa-xmark"></i></div>
  </div>

  <div class="dw-body">
    <form id="pf" method="POST" action="index.php?url=User/update" novalidate>

      <div class="fs-title">Identité</div>

      <div class="fg" id="g-nom">
        <label>Nom complet</label>
        <input type="text" id="f-nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" placeholder="Ahmed Ben Ali" data-r="required">
        <span class="fg-ico"><i class="fa fa-circle-check"></i></span>
        <div class="fg-msg" id="m-nom"></div>
      </div>

      <div class="fg" id="g-email">
        <label>Adresse email</label>
        <input type="text" id="f-email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="nom@email.com" data-r="email">
        <span class="fg-ico"><i class="fa fa-circle-check"></i></span>
        <div class="fg-msg" id="m-email"></div>
      </div>

      <div class="fg" id="g-pwd">
        <label>Nouveau mot de passe <span style="color:var(--ink3);font-size:10px;font-weight:400;">(vide = inchangé)</span></label>
        <input type="password" id="f-pwd" name="password" placeholder="••••••••" data-r="optpwd">
        <span class="fg-ico"><i class="fa fa-circle-check"></i></span>
        <div class="pwd-s"><div class="pwd-b" id="pwdBar"></div></div>
        <div class="fg-msg" id="m-pwd"></div>
      </div>

      <div class="fs-title">Données santé</div>

      <div class="fg-row">
        <div class="fg" id="g-age">
          <label>Âge</label>
          <input type="number" id="f-age" name="age" value="<?= htmlspecialchars($user['age']??'') ?>" placeholder="28" min="10" max="120" data-r="age">
          <span class="fg-ico"><i class="fa fa-circle-check"></i></span>
          <div class="fg-msg" id="m-age"></div>
        </div>
        <div class="fg" id="g-poids">
          <label>Poids (kg)</label>
          <input type="number" id="f-poids" name="poids" value="<?= htmlspecialchars($user['poids']) ?>" placeholder="70" step="0.1" min="20" max="300" data-r="poids">
          <span class="fg-ico"><i class="fa fa-circle-check"></i></span>
          <div class="fg-msg" id="m-poids"></div>
        </div>
      </div>

      <div class="fg" id="g-taille">
        <label>Taille (cm)</label>
        <input type="number" id="f-taille" name="taille" value="<?= htmlspecialchars($user['taille']) ?>" placeholder="175" min="100" max="250" data-r="taille">
        <span class="fg-ico"><i class="fa fa-circle-check"></i></span>
        <div class="fg-msg" id="m-taille"></div>
      </div>

      <div class="fs-title">Préférences</div>

      <div class="fg" id="g-obj">
        <label>Objectif nutritionnel</label>
        <select id="f-obj" name="objectif" data-r="select" style="background-image:url('data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'10\' height=\'6\'><path d=\'M0 0l5 6 5-6z\' fill=\'%238aa898\'/></svg>');background-repeat:no-repeat;background-position:right 12px center;padding-right:36px;">
          <option value="">-- Choisir --</option>
          <option value="Perte de poids" <?= ($user['objectif']??'')==='Perte de poids'?'selected':'' ?>>Perte de poids</option>
          <option value="Prise de masse" <?= ($user['objectif']??'')==='Prise de masse'?'selected':'' ?>>Prise de masse</option>
          <option value="Équilibre alimentaire" <?= ($user['objectif']??'')==='Équilibre alimentaire'?'selected':'' ?>>Équilibre alimentaire</option>
          <option value="Végétarien" <?= ($user['objectif']??'')==='Végétarien'?'selected':'' ?>>Végétarien</option>
        </select>
        <div class="fg-msg" id="m-obj"></div>
      </div>

    </form>
  </div>

  <div class="dw-ft">
    <button class="dw-cancel" onclick="closeDrawer()">Annuler</button>
    <button class="dw-save" onclick="submitPf()">💾 Enregistrer les modifications</button>
  </div>
</div>

<?php if(!empty($_SESSION['profile_success'])): ?>
<div class="flash" id="flash">✅ <?= htmlspecialchars($_SESSION['profile_success']) ?></div>
<?php unset($_SESSION['profile_success']); ?>
<script>setTimeout(()=>{const f=document.getElementById('flash');if(f){f.style.opacity=0;setTimeout(()=>f.remove(),400);}},3500);</script>
<?php endif; ?>

<script>
/* ── Règles ── */
const rules={
  required:v=>v.trim()!==''?null:'Ce champ est obligatoire.',
  email:v=>{if(!v.trim())return'Email obligatoire.';return/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())?null:'Format invalide (ex: nom@domaine.com).';},
  age:v=>{if(!v)return'Âge obligatoire.';const n=parseInt(v);return(!isNaN(n)&&n>=10&&n<=120)?null:'Entre 10 et 120 ans.';},
  poids:v=>{if(!v)return'Poids obligatoire.';const n=parseFloat(v);return(!isNaN(n)&&n>=20&&n<=300)?null:'Entre 20 et 300 kg.';},
  taille:v=>{if(!v)return'Taille obligatoire.';const n=parseFloat(v);return(!isNaN(n)&&n>=100&&n<=250)?null:'Entre 100 et 250 cm.';},
  optpwd:v=>v===''?null:(v.length>=8?null:'Minimum 8 caractères.'),
  select:v=>v!==''?null:'Veuillez choisir une option.',
};

function applyState(id,error){
  const inp=document.getElementById('f-'+id);
  const grp=document.getElementById('g-'+id);
  const msg=document.getElementById('m-'+id);
  if(!inp||!grp||!msg)return!error;
  const ico=grp.querySelector('.fg-ico');

  if(error){
    inp.classList.remove('ok');inp.classList.add('err');
    grp.classList.remove('is-ok');grp.classList.add('is-err');
    if(ico)ico.innerHTML='<i class="fa fa-circle-xmark"></i>';
    msg.textContent='⚠ '+error;msg.className='fg-msg show-err';
  } else if(inp.value.trim()!==''||inp.tagName==='SELECT'){
    inp.classList.remove('err');inp.classList.add('ok');
    grp.classList.remove('is-err');grp.classList.add('is-ok');
    if(ico)ico.innerHTML='<i class="fa fa-circle-check"></i>';
    msg.textContent='';msg.className='fg-msg';
  } else {
    inp.classList.remove('ok','err');
    grp.classList.remove('is-ok','is-err');
    msg.className='fg-msg';
  }
  return !error;
}

function vField(id){
  const inp=document.getElementById('f-'+id);
  if(!inp)return true;
  return applyState(id,rules[inp.dataset.r]?rules[inp.dataset.r](inp.value):null);
}

/* Live validation */
['nom','email','age','poids','taille'].forEach(id=>{
  const el=document.getElementById('f-'+id);
  if(!el)return;
  el.addEventListener('input',()=>{if(el.value.trim().length>0)vField(id);else{el.classList.remove('ok','err');document.getElementById('g-'+id).classList.remove('is-ok','is-err');document.getElementById('m-'+id).className='fg-msg';}});
  el.addEventListener('blur',()=>vField(id));
});
document.getElementById('f-obj').addEventListener('change',()=>vField('obj'));

/* Pwd strength */
document.getElementById('f-pwd').addEventListener('input',function(){
  const v=this.value;const bar=document.getElementById('pwdBar');
  let s=0;if(v.length>=8)s+=30;if(/[A-Z]/.test(v))s+=20;if(/[0-9]/.test(v))s+=25;if(/[^A-Za-z0-9]/.test(v))s+=25;
  bar.style.width=s+'%';
  bar.style.background=s<40?'var(--or)':s<70?'#ffa726':'var(--g)';
  vField('pwd');
});

function submitPf(){
  const fields=['nom','email','pwd','age','poids','taille','obj'];
  let ok=true;
  fields.forEach(id=>{if(!vField(id))ok=false;});
  if(ok)document.getElementById('pf').submit();
  else{const first=document.querySelector('.fg.is-err input,.fg.is-err select');if(first)first.scrollIntoView({behavior:'smooth',block:'center'});first?.focus();}
}

function openDrawer(){document.getElementById('drawer').classList.add('open');document.getElementById('overlay').classList.add('open');document.body.style.overflow='hidden';}
function closeDrawer(){document.getElementById('drawer').classList.remove('open');document.getElementById('overlay').classList.remove('open');document.body.style.overflow='';}

/* Animate bars */
window.addEventListener('load',()=>{
  document.querySelectorAll('.prog-fill,.compl-fill,.obj-fill').forEach(el=>{
    const w=el.style.width;el.style.width='0';
    setTimeout(()=>{el.style.width=w;},200);
  });
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>