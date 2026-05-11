```php
<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: /2A35/index.php?url=User/auth"); exit; }

$user = $_SESSION['user'];

$imc = ($user['poids']>0 && $user['taille']>0)
    ? round($user['poids'] / pow($user['taille']/100,2),1) : 0;

$imcCat='—'; $imcColor='#00b96b'; $imcBg='#e6faf2'; $imcState='normal';
if($imc>0){
  if     ($imc<18.5){$imcCat='Insuffisance pondérale';$imcColor='#2979ff';$imcBg='#e8f0ff';$imcState='underweight';}
  elseif ($imc<25)  {$imcCat='Poids normal';           $imcColor='#00b96b';$imcBg='#e6faf2';$imcState='normal';}
  elseif ($imc<30)  {$imcCat='Surpoids';               $imcColor='#ff6b2b';$imcBg='#fff0eb';$imcState='overweight';}
  else              {$imcCat='Obésité';                 $imcColor='#e53935';$imcBg='#ffebee';$imcState='obese';}
}

$piMin=$user['taille']>0?round(18.5*pow($user['taille']/100,2),1):0;
$piMax=$user['taille']>0?round(24.9*pow($user['taille']/100,2),1):0;

$done=0;
foreach(['nom','email','poids','taille','age','objectif'] as $f) if($user[$f]??'') $done++;
$completPct=round($done/6*100);

/* AI Advice generation */
$h = (int)date('H');
$period = $h < 12 ? 'Bonjour' : ($h < 18 ? 'Bon après-midi' : 'Bonsoir');

include 'View/front/partials/header.php';
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
  display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;
  border:1px solid var(--bdr);font-size:13px;font-weight:500;color:var(--ink2);
  text-decoration:none;transition:all .15s;
}
.tb-logout:hover{border-color:var(--or);color:var(--or);}

/* ════ HERO BANNER ════ */
.hero{
  position:relative;height:240px;overflow:hidden;
  background:linear-gradient(135deg,#0d1f0f 0%,#162a1e 40%,#1a3528 100%);
}
.hero-overlay{
  position:absolute;inset:0;
  background:radial-gradient(ellipse at 30% 50%, rgba(0,185,107,.15) 0%, transparent 60%),
             radial-gradient(ellipse at 80% 30%, rgba(255,107,43,.08) 0%, transparent 50%);
}
.hero-pattern{
  position:absolute;inset:0;opacity:.06;
  background-image:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2300b96b' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.hero-content{
  position:absolute;bottom:0;left:0;right:0;
  padding:0 48px 32px;
  display:flex;align-items:flex-end;gap:22px;
  z-index:1;
}

.hero-av{
  width:96px;height:96px;border-radius:50%;
  background:linear-gradient(135deg,var(--g),var(--gd));
  border:4px solid rgba(255,255,255,.9);
  box-shadow:0 8px 28px rgba(0,0,0,.3);
  display:flex;align-items:center;justify-content:center;
  font-family:var(--fh);font-size:36px;font-weight:900;color:#fff;
  flex-shrink:0;transform:translateY(28px);
  position:relative;z-index:1;
}

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
.wrap{max-width:1280px;margin:0 auto;padding:40px 40px 80px;}

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
.g2{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px;}
.g-main{display:grid;grid-template-columns:340px 1fr;gap:24px;}

/* ════ CARD BASE ════ */
.card{
  background:#fff;border:1px solid var(--bdr);border-radius:20px;
  padding:24px;position:relative;overflow:hidden;
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
.c-tag{font-size:11px;font-weight:600;padding:4px 12px;border-radius:20px;}
.c-tag-g{background:var(--bg3);color:var(--g);border:1px solid #c3e8d6;}
.c-tag-o{background:#fff0eb;color:var(--or);border:1px solid #ffd5bf;}

/* ════ METRIC CARDS ════ */
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
  font-family:var(--fh);font-size:56px;font-weight:900;
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

/* ════ BTN ═══ */
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

/* ═══════════════════════════════════════════ */
/* ════ AI AGENT — NOUVELLE SECTION ════ */
/* ═══════════════════════════════════════════ */
.ai-agent-card{
  background:linear-gradient(160deg,#0d1f0f 0%,#162a1e 40%,#1a3528 100%);
  border:1px solid #2a4a3a;border-radius:24px;overflow:hidden;
  position:relative;margin-bottom:28px;
}
.ai-agent-card::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 40% 30%, rgba(0,185,107,.12) 0%, transparent 60%);
  pointer-events:none;z-index:1;
}

.ai-agent-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:20px 24px 16px;position:relative;z-index:2;
}
.ai-agent-title{display:flex;align-items:center;gap:10px;}
.ai-agent-title-icon{
  width:36px;height:36px;border-radius:10px;
  background:linear-gradient(135deg,var(--g),var(--g2));
  display:flex;align-items:center;justify-content:center;font-size:16px;
  box-shadow:0 4px 12px rgba(0,185,107,.3);
}
.ai-agent-title-text{font-family:var(--fh);font-size:16px;font-weight:700;color:#fff;}
.ai-agent-title-text span{color:var(--g2);font-style:normal;}
.ai-badge{
  display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;
  background:rgba(0,185,107,.15);border:1px solid rgba(0,185,107,.3);
  font-size:11px;font-weight:700;color:var(--g2);letter-spacing:.05em;
}
.ai-badge-dot{width:6px;height:6px;border-radius:50%;background:var(--g2);animation:aiPulse 2s ease-in-out infinite;}
@keyframes aiPulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(.7);}}

.ai-agent-body{
  display:flex;gap:20px;padding:0 24px 20px;position:relative;z-index:1;
}

/* AI Avatar */
.ai-avatar-wrap{
  flex:0 0 100px;display:flex;flex-direction:column;align-items:center;gap:10px;
}
.ai-avatar{
  width:90px;height:90px;border-radius:50%;
  background:linear-gradient(135deg,#1a3528,#0d1f0f);
  border:2px solid rgba(0,185,107,.3);
  display:flex;align-items:center;justify-content:center;
  position:relative;
}
.ai-avatar-img{
  width:80px;height:80px;border-radius:50%;object-fit:cover;
}
.ai-avatar-ring{
  position:absolute;inset:-4px;border-radius:50%;
  border:2px solid rgba(0,185,107,.2);
  animation:aiRing 3s ease-in-out infinite;
}
@keyframes aiRing{0%,100%{transform:scale(1);opacity:.3;}50%{transform:scale(1.08);opacity:.6;}}

.ai-avatar-ring.speaking{
  border-color:rgba(0,230,118,.6);
  animation:aiSpeak 0.8s ease-in-out infinite;
}
@keyframes aiSpeak{
  0%{transform:scale(1);box-shadow:0 0 0 0 rgba(0,230,118,.4);}
  50%{transform:scale(1.1);box-shadow:0 0 0 8px rgba(0,230,118,0);}
  100%{transform:scale(1);box-shadow:0 0 0 0 rgba(0,230,118,0);}
}

.ai-avatar-label{
  font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
  color:rgba(255,255,255,.4);
}

/* AI Content */
.ai-content{flex:1;display:flex;flex-direction:column;gap:12px;}

.ai-greeting{
  font-size:14px;font-weight:600;color:var(--g2);
  display:flex;align-items:center;gap:6px;
}

.ai-advice-box{
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);
  border-radius:14px;padding:16px;min-height:100px;
  position:relative;
}
.ai-advice-text{
  font-size:13px;color:rgba(255,255,255,.75);line-height:1.7;
  min-height:60px;
}
.ai-advice-text .typing-cursor{
  display:inline-block;width:2px;height:14px;background:var(--g2);
  margin-left:2px;animation:blink 0.8s step-end infinite;vertical-align:text-bottom;
}
@keyframes blink{0%,100%{opacity:1;}50%{opacity:0;}}

.ai-sound-waves{
  display:flex;align-items:center;gap:3px;height:20px;
  position:absolute;bottom:16px;right:16px;opacity:0;transition:opacity .3s;
}
.ai-sound-waves.active{opacity:1;}
.ai-wave-bar{
  width:3px;background:var(--g2);border-radius:2px;
  animation:none;
}
.ai-sound-waves.active .ai-wave-bar{animation:waveAnim 0.6s ease-in-out infinite alternate;}
.ai-sound-waves.active .ai-wave-bar:nth-child(1){animation-delay:0s;height:8px;}
.ai-sound-waves.active .ai-wave-bar:nth-child(2){animation-delay:0.1s;height:14px;}
.ai-sound-waves.active .ai-wave-bar:nth-child(3){animation-delay:0.2s;height:10px;}
.ai-sound-waves.active .ai-wave-bar:nth-child(4){animation-delay:0.3s;height:16px;}
.ai-sound-waves.active .ai-wave-bar:nth-child(5){animation-delay:0.15s;height:12px;}
@keyframes waveAnim{0%{height:4px;}100%{height:18px;}}

.ai-controls{display:flex;gap:8px;flex-wrap:wrap;}
.ai-ctrl-btn{
  padding:10px 18px;border-radius:10px;border:1px solid rgba(255,255,255,.1);
  background:rgba(255,255,255,.04);color:rgba(255,255,255,.8);
  font-size:12px;font-weight:600;font-family:var(--fb);cursor:pointer;
  transition:all .2s;display:flex;align-items:center;gap:6px;
}
.ai-ctrl-btn:hover{background:rgba(0,185,107,.15);border-color:var(--g);color:var(--g2);}
.ai-ctrl-btn.primary{
  background:linear-gradient(135deg,rgba(0,185,107,.2),rgba(0,230,118,.15));
  border-color:rgba(0,185,107,.4);color:var(--g2);
}
.ai-ctrl-btn.primary:hover{
  background:linear-gradient(135deg,rgba(0,185,107,.3),rgba(0,230,118,.25));
  box-shadow:0 4px 16px rgba(0,185,107,.2);
}
.ai-ctrl-btn.stop{
  background:rgba(229,57,53,.15);border-color:rgba(229,57,53,.3);color:#ff8a80;
}
.ai-ctrl-btn.stop:hover{background:rgba(229,57,53,.25);}

.ai-state-tags{
  display:flex;gap:6px;flex-wrap:wrap;
}
.ai-state-tag{
  padding:4px 10px;border-radius:16px;font-size:10px;font-weight:600;
  background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
  color:rgba(255,255,255,.5);
}

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
  position:relative;
}
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

.fs-title{
  font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;
  color:var(--ink3);margin-bottom:14px;margin-top:22px;
  display:flex;align-items:center;gap:8px;
}
.fs-title:first-child{margin-top:0;}
.fs-title::after{content:'';flex:1;height:1px;background:var(--bdr);}

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
.fg input.ok,.fg select.ok{border-color:var(--g)!important;box-shadow:0 0 0 3px rgba(0,185,107,.12)!important;background:#f7fdf9!important;}
.fg input.err,.fg select.err{border-color:var(--or)!important;box-shadow:0 0 0 3px rgba(255,107,43,.1)!important;background:#fff8f5!important;}
.fg-msg{font-size:11px;margin-top:4px;display:none;}
.fg-msg.show-err{display:block;color:var(--or);}
.fg-msg.show-ok{display:block;color:var(--g);}
.fg-ico{position:absolute;right:13px;top:36px;font-size:13px;pointer-events:none;opacity:0;transition:opacity .2s;}
.fg.is-ok  .fg-ico{opacity:1;color:var(--g);}
.fg.is-err .fg-ico{opacity:1;color:var(--or);}
.fg-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}

.pwd-s{height:4px;background:var(--bg3);border-radius:2px;overflow:hidden;margin:6px 0 4px;}
.pwd-b{height:100%;border-radius:2px;width:0;transition:width .3s,background .3s;}

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
@media(max-width:1024px){
  .g4{grid-template-columns:repeat(2,1fr);}
  .g2{grid-template-columns:1fr;}
  .g-main{grid-template-columns:1fr;}
}
@media(max-width:640px){
  .wrap{padding:24px 16px 60px;}
  .hero-content{padding:0 20px 28px;}
  .g4{grid-template-columns:1fr 1fr;}
  .tb{padding:0 16px;}
  .drawer{width:100%;right:-100%;}
  .ai-agent-body{flex-direction:column;align-items:center;text-align:center;}
  .ai-agent-header{flex-direction:column;gap:10px;text-align:center;}
  .ai-controls{justify-content:center;}
  .ai-state-tags{justify-content:center;}
}
</style>



<!-- ════ HERO BANNER ════ -->
<div class="hero">
  <div class="hero-pattern"></div>
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

<!-- ════ WRAP ════ -->
<div class="wrap">

  <!-- ════ AI AGENT — NOUVELLE SECTION ════ -->
  <div class="sec-lbl"><i class="fa fa-robot" style="margin-right:4px;"></i> Assistant IA Nutritionnel</div>
  <div class="ai-agent-card">
    <div class="ai-agent-header">
      <div class="ai-agent-title">
        <div class="ai-agent-title-icon">🧠</div>
        <div class="ai-agent-title-text">Conseiller <span>du jour</span></div>
      </div>
      <div class="ai-badge"><div class="ai-badge-dot"></div>IA ACTIVE</div>
    </div>
    <div class="ai-agent-body">
      <div class="ai-avatar-wrap">
        <div class="ai-avatar">
          <div class="ai-avatar-ring" id="aiRing"></div>
          <img class="ai-avatar-img" src="https://image.qwenlm.ai/public_source/87cb434f-34f4-4f82-92ec-abe9ffebe063/185087949-0eee-4f55-8b00-a3a4bea532f6.png" alt="AI Avatar">
        </div>
        <div class="ai-avatar-label">EcoNutri IA</div>
      </div>
      <div class="ai-content">
        <div class="ai-greeting" id="aiGreeting">🌿 <?= $period ?>, <?= htmlspecialchars($user['nom']) ?> !</div>
        <div class="ai-advice-box">
          <div class="ai-advice-text" id="aiAdviceText">
            <span id="aiTypedText"></span><span class="typing-cursor" id="aiCursor"></span>
          </div>
          <div class="ai-sound-waves" id="aiSoundWaves">
            <div class="ai-wave-bar"></div>
            <div class="ai-wave-bar"></div>
            <div class="ai-wave-bar"></div>
            <div class="ai-wave-bar"></div>
            <div class="ai-wave-bar"></div>
          </div>
        </div>
        <div class="ai-state-tags">
          <span class="ai-state-tag">🩺 IMC: <?= $imc ?: '—' ?> (<?= $imcCat ?>)</span>
          <span class="ai-state-tag">🎯 <?= htmlspecialchars($user['objectif'] ?: 'Non défini') ?></span>
          <span class="ai-state-tag">🏃 <?= htmlspecialchars($user['activite'] ?: 'Non définie') ?></span>
          <span class="ai-state-tag">⚖️ <?= $user['poids'] ?: '—' ?>kg</span>
          <span class="ai-state-tag">📏 <?= $user['taille'] ?: '—' ?>cm</span>
        </div>
        <div class="ai-controls">
          <button class="ai-ctrl-btn primary" onclick="speakAdvice()" id="btnSpeak"><i class="fa fa-volume-high"></i> Écouter le conseil</button>
          <button class="ai-ctrl-btn" onclick="stopSpeaking()" id="btnStop" style="display:none;"><i class="fa fa-stop"></i> Arrêter</button>
          <button class="ai-ctrl-btn" onclick="refreshAdvice()"><i class="fa fa-arrows-rotate"></i> Nouveau conseil</button>
          <button class="ai-ctrl-btn" onclick="showMoreTips()"><i class="fa fa-lightbulb"></i> Plus de conseils</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ════ METRIC CARDS ═══ -->
  <div class="sec-lbl">Données personnelles</div>
  <div class="g4">
    <div class="metric-card">
      <div class="metric-img" style="background:linear-gradient(135deg,#e8f0ff,#f0f5ff);display:flex;align-items:center;justify-content:center;font-size:36px;">🎂</div>
      <div class="metric-body">
        <span class="metric-ico">🎂</span>
        <div class="metric-lbl">Âge</div>
        <div class="metric-val" style="color:#2979ff"><?= $user['age']??'—' ?><small style="font-size:12px;font-weight:400;color:var(--ink3);"> ans</small></div>
      </div>
    </div>
    <div class="metric-card">
      <div class="metric-img" style="background:linear-gradient(135deg,#fff0eb,#fff6f2);display:flex;align-items:center;justify-content:center;font-size:36px;">⚖️</div>
      <div class="metric-body">
        <span class="metric-ico">⚖️</span>
        <div class="metric-lbl">Poids</div>
        <div class="metric-val" style="color:var(--or)"><?= $user['poids']??'—' ?><small style="font-size:12px;font-weight:400;color:var(--ink3);"> kg</small></div>
      </div>
    </div>
    <div class="metric-card">
      <div class="metric-img" style="background:linear-gradient(135deg,#f3eaff,#f8f3ff);display:flex;align-items:center;justify-content:center;font-size:36px;">📏</div>
      <div class="metric-body">
        <span class="metric-ico">📏</span>
        <div class="metric-lbl">Taille</div>
        <div class="metric-val" style="color:#7c3aed"><?= $user['taille']??'—' ?><small style="font-size:12px;font-weight:400;color:var(--ink3);"> cm</small></div>
      </div>
    </div>
    <div class="metric-card">
      <div class="metric-img" style="background:linear-gradient(135deg,#e6faf2,#f0fdf6);display:flex;align-items:center;justify-content:center;font-size:36px;"></div>
      <div class="metric-body">
        <span class="metric-ico"></span>
        <div class="metric-lbl">IMC</div>
        <div class="metric-val" style="color:<?= $imcColor ?>"><?= $imc?:'—' ?></div>
      </div>
    </div>
  </div>

  <!-- ════ MAIN BODY ════ -->
  <div class="g-main">

    <!-- ── COLONNE GAUCHE ── -->
    <div>
      <div class="card">
        <div class="profil-av-wrap">
          <div class="profil-av"><?= strtoupper(substr($user['nom'],0,1)) ?></div>
          <div class="profil-name"><?= htmlspecialchars($user['nom']) ?></div>
          <div class="profil-email"><?= htmlspecialchars($user['email']) ?></div>
          <?php if($user['objectif']??''): ?>
          <div class="profil-obj"> 🎯 <?= htmlspecialchars($user['objectif']) ?></div>
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

      <!-- 2 cols -->
      <div class="g2">

        <div class="card" style="padding:0;overflow:hidden;margin-bottom:0;">
          <img src="https://image.qwenlm.ai/public_source/87cb434f-34f4-4f82-92ec-abe9ffebe063/1321ab2b8-6acd-477b-a93f-edfc1ba9c277.png"
               style="width:100%;height:170px;object-fit:cover;display:block;" alt="Alimentation">
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
  <div class="dw-hd">
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
        <select id="f-obj" name="objectif" data-r="select" style="background-image:url('image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'10\' height=\'6\'><path d=\'M0 0l5 6 5-6z\' fill=\'%238aa898\'/></svg>');background-repeat:no-repeat;background-position:right 12px center;padding-right:36px;">
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
    <button type="button" class="dw-cancel" onclick="closeDrawer()">
  Annuler
</button>
    <button type="button" class="dw-save" onclick="submitPf()">
  💾 Enregistrer les modifications
</button>
  </div>
</div>

<?php if(!empty($_SESSION['profile_success'])): ?>
<div class="flash" id="flash">✅ <?= htmlspecialchars($_SESSION['profile_success']) ?></div>
<?php unset($_SESSION['profile_success']); ?>
<script>setTimeout(()=>{const f=document.getElementById('flash');if(f){f.style.opacity=0;setTimeout(()=>f.remove(),400);}},3500);</script>
<?php endif; ?>

<script>
/* ═══════════════════════════════════════════ */
/* ════ AI AGENT — VOICE & ADVICE SYSTEM ════ */
/* ═══════════════════════════════════════════ */

const AI_ADVICE_POOL = [
  "Votre IMC de <?= $imc ?: 0 ?> indique un état <?= $imcState === 'normal' ? 'normal et sain' : ($imcState === 'underweight' ? 'd insuffisance pondérale' : ($imcState === 'overweight' ? 'de surpoids' : 'd obésité')) ?>. Je vous recommande de <?= $imcState === 'normal' ? 'maintenir vos habitudes actuelles' : ($imcState === 'underweight' ? 'augmenter votre apport calorique avec des aliments nutritifs' : ($imcState === 'overweight' ? 'réduire les sucres rapides et augmenter votre activité physique' : 'consulter un professionnel de santé pour un suivi adapté')) ?>.",
  "Pour votre objectif de <?= htmlspecialchars($user['objectif'] ?: 'non défini') ?>, concentrez-vous sur <?= str_contains(strtolower($user['objectif']??''),'perte') ? 'un déficit calorique modéré et une alimentation riche en fibres' : (str_contains(strtolower($user['objectif']??''),'masse') ? 'un surplus calorique avec des protéines suffisantes' : 'un équilibre entre tous les macronutriments') ?>.",
  "N oubliez pas de boire au moins <?= $user['poids'] ? round($user['poids'] * 0.033, 1) : '2' ?> litres d eau par jour. L hydratation est essentielle pour votre métabolisme et votre énergie.",
  "Essayez de dormir entre 7 et 8 heures par nuit. Le sommeil joue un rôle crucial dans la régulation des hormones de la faim et de la satiété.",
  "Les protéines sont essentielles pour <?= str_contains(strtolower($user['objectif']??''),'masse') ? 'la construction musculaire' : 'la satiété et la préservation musculaire' ?>. Visez <?= round(($user['poids'] ?: 70) * 1.6) ?> grammes par jour.",
  "Les fruits et légumes devraient représenter au moins 5 portions par jour. Variez les couleurs pour maximiser les apports en vitamines et antioxydants.",
  "Réduisez votre consommation de sel à moins de 5 grammes par jour pour protéger votre santé cardiovasculaire.",
  "L activité physique régulière d au moins 30 minutes par jour peut réduire de 30% le risque de maladies chroniques.",
  "Privilégiez les graisses insaturées comme l huile d olive, les noix et les avocats plutôt que les graisses saturées.",
  "Ne sautez jamais de repas. Cela ralentit votre métabolisme et peut entraîner des compulsions alimentaires plus tard."
];

let currentAdviceIndex = 0;
let isSpeaking = false;
let typingTimeout = null;

function getAdviceText(){
  return AI_ADVICE_POOL[currentAdviceIndex % AI_ADVICE_POOL.length];
}

/* Typing animation */
function typeAdvice(text, callback){
  const el = document.getElementById('aiTypedText');
  const cursor = document.getElementById('aiCursor');
  el.textContent = '';
  cursor.style.display = 'inline-block';
  let i = 0;
  const speed = 25;

  function typeChar(){
    if(i < text.length){
      el.textContent += text.charAt(i);
      i++;
      typingTimeout = setTimeout(typeChar, speed + Math.random() * 30);
    } else {
      if(callback) callback();
    }
  }
  typeChar();
}

/* Text-to-Speech */
function speakAdvice(){
  if('speechSynthesis' in window){
    window.speechSynthesis.cancel();

    const utterance = new SpeechSynthesisUtterance(getAdviceText());
    utterance.lang = 'fr-FR';
    utterance.rate = 0.95;
    utterance.pitch = 1;
    utterance.volume = 1;

    /* Try to find a French voice */
    const voices = window.speechSynthesis.getVoices();
    const frVoice = voices.find(v => v.lang.startsWith('fr'));
    if(frVoice) utterance.voice = frVoice;

    utterance.onstart = function(){
      isSpeaking = true;
      document.getElementById('btnSpeak').style.display = 'none';
      document.getElementById('btnStop').style.display = 'flex';
      document.getElementById('aiRing').classList.add('speaking');
      document.getElementById('aiSoundWaves').classList.add('active');
    };

    utterance.onend = function(){
      isSpeaking = false;
      document.getElementById('btnSpeak').style.display = 'flex';
      document.getElementById('btnStop').style.display = 'none';
      document.getElementById('aiRing').classList.remove('speaking');
      document.getElementById('aiSoundWaves').classList.remove('active');
      currentAdviceIndex++;
    };

    utterance.onerror = function(){
      isSpeaking = false;
      document.getElementById('btnSpeak').style.display = 'flex';
      document.getElementById('btnStop').style.display = 'none';
      document.getElementById('aiRing').classList.remove('speaking');
      document.getElementById('aiSoundWaves').classList.remove('active');
    };

    window.speechSynthesis.speak(utterance);
  } else {
    alert('La synthèse vocale n est pas supportée par votre navigateur.');
  }
}

function stopSpeaking(){
  if('speechSynthesis' in window){
    window.speechSynthesis.cancel();
    isSpeaking = false;
    document.getElementById('btnSpeak').style.display = 'flex';
    document.getElementById('btnStop').style.display = 'none';
    document.getElementById('aiRing').classList.remove('speaking');
    document.getElementById('aiSoundWaves').classList.remove('active');
  }
}

function refreshAdvice(){
  stopSpeaking();
  currentAdviceIndex++;
  const text = getAdviceText();
  typeAdvice(text);
}

function showMoreTips(){
  stopSpeaking();
  const tips = [
    "💡 Astuce: Mangez lentement et mastiquez bien. Cela aide à mieux digérer et à se sentir rassasié plus vite.",
    "💡 Astuce: Planifiez vos repas à l avance pour éviter les choix impulsifs et les grignotages.",
    "💡 Astuce: Gardez une bouteille d eau visible sur votre bureau comme rappel constant de vous hydrater.",
    "💡 Astuce: Remplacez les collations industrielles par des fruits frais, des noix ou du yaourt nature.",
    "💡 Astuce: Cuisinez vos repas maison autant que possible pour contrôler les ingrédients et les portions."
  ];
  const randomTip = tips[Math.floor(Math.random() * tips.length)];
  typeAdvice(randomTip);
}

/* Load voices */
if('speechSynthesis' in window){
  window.speechSynthesis.onvoiceschanged = function(){
    window.speechSynthesis.getVoices();
  };
  window.speechSynthesis.getVoices();
}

/* ══════════════════════════════════════════ */
/* ════ FORM VALIDATION ════ */
/* ═══════════════════════════════════════════ */

const rules={
  required:v=>v.trim()!==''?null:'Ce champ est obligatoire.',
  email:v=>{if(!v.trim())return'Email obligatoire.';return/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())?null:'Format invalide.';},
  age:v=>{if(!v)return'Âge obligatoire.';const n=parseInt(v);return(!isNaN(n)&&n>=10&&n<=120)?null:'Entre 10 et 120 ans.';},
  poids:v=>{if(!v)return'Poids obligatoire.';const n=parseFloat(v);return(!isNaN(n)&&n>=20&&n<=300)?null:'Entre 20 et 300 kg.';},
  taille:v=>{if(!v)return'Taille obligatoire.';const n=parseFloat(v);return(!isNaN(n)&&n>=100&&n<=250)?null:'Entre 100 et 250 cm.';},
  optpwd:v=>v===''?null:(v.length>=8?null:'Minimum 8 caractères.'),
  select:v=>v!==''?null:'Veuillez choisir.',
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

['nom','email','age','poids','taille'].forEach(id=>{
  const el=document.getElementById('f-'+id);
  if(!el)return;
  el.addEventListener('input',()=>{if(el.value.trim().length>0)vField(id);else{el.classList.remove('ok','err');document.getElementById('g-'+id).classList.remove('is-ok','is-err');document.getElementById('m-'+id).className='fg-msg';}});
  el.addEventListener('blur',()=>vField(id));
});
document.getElementById('f-obj').addEventListener('change',()=>vField('obj'));

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
  else{const first=document.querySelector('.fg.is-err input,.fg.is-err select');if(first){first.scrollIntoView({behavior:'smooth',block:'center'});first.focus();}}
}

function openDrawer(){document.getElementById('drawer').classList.add('open');document.getElementById('overlay').classList.add('open');document.body.style.overflow='hidden';}
function closeDrawer(){document.getElementById('drawer').classList.remove('open');document.getElementById('overlay').classList.remove('open');document.body.style.overflow='';}

/* ════ INIT ═══ */
window.addEventListener('load',()=>{
  /* Animate bars */
  document.querySelectorAll('.prog-fill,.compl-fill').forEach(el=>{
    const w=el.style.width;el.style.width='0';
    setTimeout(()=>{el.style.width=w;},300);
  });

  /* AI: Type initial advice */
  setTimeout(()=>{
    typeAdvice(getAdviceText(), ()=>{
      /* Auto-play voice after typing */
      setTimeout(()=>{ speakAdvice(); }, 500);
    });
  }, 800);
});
</script>

<?php include 'View/front/partials/footer.php'; ?>
