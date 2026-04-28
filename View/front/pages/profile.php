<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /ProjetWeb-User/index.php?url=User/auth");
    exit;
}

$user = $_SESSION['user'];

$imc = ($user['poids'] > 0 && $user['taille'] > 0)
    ? round($user['poids'] / pow($user['taille'] / 100, 2), 1) : 0;

$imcCat = '—'; $imcColor = '#2e7d32'; $imcBg = '#e8f5e9';
if ($imc > 0) {
    if      ($imc < 18.5) { $imcCat = 'Insuffisance pondérale'; $imcColor = '#1565c0'; $imcBg = '#e3f2fd'; }
    elseif  ($imc < 25)   { $imcCat = 'Poids normal';           $imcColor = '#2e7d32'; $imcBg = '#e8f5e9'; }
    elseif  ($imc < 30)   { $imcCat = 'Surpoids';               $imcColor = '#e65100'; $imcBg = '#fff3e0'; }
    else                  { $imcCat = 'Obésité';                 $imcColor = '#c62828'; $imcBg = '#ffebee'; }
}

$poidsIdealMin = $user['taille'] > 0 ? round(18.5 * pow($user['taille']/100, 2), 1) : 0;
$poidsIdealMax = $user['taille'] > 0 ? round(24.9 * pow($user['taille']/100, 2), 1) : 0;

include __DIR__ . '/../partials/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Inter', sans-serif !important;
  background: #f5f7f5 !important;
  color: #1a2e1a !important;
}

/* ── TOPBAR ── */
.pf-topbar {
  background: #fff;
  border-bottom: 1px solid #e0e8e0;
  height: 60px;
  padding: 0 40px;
  display: flex; align-items: center; justify-content: space-between;
  position: sticky; top: 0; z-index: 100;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}

.pf-logo { font-size: 16px; font-weight: 700; color: #1a2e1a; text-decoration: none; display: flex; align-items: center; gap: 8px; }
.pf-logo-dot { width: 8px; height: 8px; background: #2e7d32; border-radius: 50%; }

.pf-nav { display: flex; gap: 4px; }
.pf-nav-link {
  display: flex; align-items: center; gap: 6px;
  padding: 7px 14px; border-radius: 7px;
  text-decoration: none; font-size: 13px; font-weight: 500; color: #546e54;
  transition: all .15s;
}
.pf-nav-link:hover { background: #f1f8f1; color: #1a2e1a; }
.pf-nav-link.active { background: #e8f5e9; color: #2e7d32; }
.pf-nav-link i { font-size: 12px; color: inherit; }

/* ── HERO BANNER ── */
.pf-hero {
  position: relative;
  height: 220px;
  overflow: hidden;
}

.pf-hero-img {
  width: 100%; height: 100%;
  object-fit: cover;
  filter: brightness(0.7);
}

.pf-hero-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(46,125,50,0.75) 0%, rgba(230,101,0,0.4) 100%);
}

.pf-hero-content {
  position: absolute; bottom: 0; left: 0; right: 0;
  padding: 0 48px 28px;
  display: flex; align-items: flex-end; gap: 22px;
}

.pf-hero-av {
  width: 88px; height: 88px; border-radius: 50%;
  background: #fff;
  border: 4px solid #fff;
  box-shadow: 0 4px 16px rgba(0,0,0,0.2);
  display: flex; align-items: center; justify-content: center;
  font-size: 32px; font-weight: 800; color: #2e7d32;
  flex-shrink: 0;
  transform: translateY(24px);
}

.pf-hero-info { padding-bottom: 4px; }
.pf-hero-name { font-size: 22px; font-weight: 700; color: #fff; letter-spacing: -.3px; }
.pf-hero-sub  { font-size: 13px; color: rgba(255,255,255,.75); margin-top: 3px; }

.pf-hero-chips {
  margin-left: auto; display: flex; gap: 8px; padding-bottom: 4px; flex-wrap: wrap;
}

.pf-chip {
  padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 500;
  background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.35);
  color: #fff; backdrop-filter: blur(4px);
}

/* ── WRAP ── */
.pf-wrap {
  max-width: 1100px; margin: 0 auto;
  padding: 44px 32px 60px;
}

/* ── GRID ── */
.g-3-9 { display: grid; grid-template-columns: 300px 1fr; gap: 24px; }
.g2    { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.g4    { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; }

/* ── CARD ── */
.card {
  background: #fff;
  border: 1px solid #e0e8e0;
  border-radius: 14px;
  padding: 22px;
  margin-bottom: 18px;
}

.card-hd {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 16px; padding-bottom: 14px;
  border-bottom: 1px solid #f0f4f0;
}

.card-title {
  font-size: 14px; font-weight: 600; color: #1a2e1a;
  display: flex; align-items: center; gap: 7px;
}

.card-title i { font-size: 13px; color: #2e7d32; }

.card-tag {
  font-size: 11px; font-weight: 500;
  padding: 3px 10px; border-radius: 20px;
  background: #e8f5e9; color: #2e7d32;
  border: 1px solid #c8e6c9;
}

/* ── STAT ROW ── */
.stat-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 0; border-bottom: 1px solid #f8faf8;
}
.stat-row:last-child { border-bottom: none; padding-bottom: 0; }

.stat-ico {
  width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 14px;
}
.stat-ico.g { background: #e8f5e9; }
.stat-ico.o { background: #fff3e0; }
.stat-ico.b { background: #e3f2fd; }
.stat-ico.r { background: #fce4ec; }

.stat-label { font-size: 12px; color: #78909c; margin-left: 10px; flex: 1; }
.stat-value { font-size: 14px; font-weight: 600; color: #1a2e1a; }

/* ── PHOTO CARDS ── */
.photo-card {
  border-radius: 14px; overflow: hidden;
  border: 1px solid #e0e8e0;
  background: #fff;
  transition: transform .2s, box-shadow .2s;
}

.photo-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(0,0,0,.1); }

.photo-card img {
  width: 100%; height: 110px; object-fit: cover;
  display: block;
}

.photo-card-body { padding: 14px; text-align: center; }
.photo-card-icon { font-size: 20px; margin-bottom: 4px; }
.photo-card-lbl  { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: #9e9e9e; }
.photo-card-val  { font-size: 20px; font-weight: 800; letter-spacing: -1px; margin-top: 2px; }

/* ── IMC SCALE ── */
.imc-scale {
  height: 8px; border-radius: 4px; position: relative;
  background: linear-gradient(90deg, #1565c0 0%, #2e7d32 35%, #e65100 65%, #c62828 100%);
  margin: 14px 0 6px;
}
.imc-needle {
  position: absolute; top: -4px;
  width: 4px; height: 16px;
  background: #1a2e1a; border-radius: 2px;
  transform: translateX(-50%);
  box-shadow: 0 1px 4px rgba(0,0,0,.25);
  transition: left 1s ease;
}
.imc-scale-lbl { display: flex; justify-content: space-between; font-size: 10px; color: #9e9e9e; }

/* ── OBJECTIF CARD ── */
.obj-card {
  background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
  border: 1px solid #c8e6c9;
  border-radius: 12px; padding: 16px;
}

.obj-title  { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: #78909c; margin-bottom: 6px; }
.obj-val    { font-size: 18px; font-weight: 700; color: #2e7d32; }
.obj-sub    { font-size: 12px; color: #546e54; margin-top: 3px; }

/* ── PROGRESS BAR ── */
.prog-item { margin-bottom: 12px; }
.prog-item:last-child { margin-bottom: 0; }
.prog-top  { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px; }
.prog-lbl  { color: #78909c; }
.prog-val  { font-weight: 600; color: #1a2e1a; }
.prog-bar  { height: 5px; background: #f0f4f0; border-radius: 3px; overflow: hidden; }
.prog-fill { height: 100%; border-radius: 3px; transition: width 1.1s ease; }

/* ── BOUTON ── */
.btn-edit {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  width: 100%; padding: 12px;
  background: #2e7d32; color: #fff;
  border: none; border-radius: 10px;
  font-size: 14px; font-weight: 600; font-family: 'Inter', sans-serif;
  cursor: pointer; transition: background .15s;
  text-decoration: none;
}
.btn-edit:hover { background: #1b5e20; color: #fff; }

/* ══════════════════════════════════════════
   DRAWER FORMULAIRE
══════════════════════════════════════════ */
.pf-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,.35);
  z-index: 900;
  opacity: 0; pointer-events: none;
  transition: opacity .3s;
  backdrop-filter: blur(2px);
}

.pf-overlay.open { opacity: 1; pointer-events: all; }

.pf-drawer {
  position: fixed; top: 0; right: -480px;
  width: 460px; height: 100vh;
  background: #fff;
  z-index: 901;
  box-shadow: -4px 0 30px rgba(0,0,0,.12);
  display: flex; flex-direction: column;
  transition: right .3s ease;
  overflow: hidden;
}

.pf-drawer.open { right: 0; }

/* Header drawer */
.drawer-hd {
  padding: 22px 24px 18px;
  border-bottom: 1px solid #e8f0e8;
  display: flex; align-items: center; justify-content: space-between;
  flex-shrink: 0;
  background: linear-gradient(135deg, #f8fdf8, #fff);
}

.drawer-hd-left h3 { font-size: 17px; font-weight: 700; color: #1a2e1a; }
.drawer-hd-left p  { font-size: 12px; color: #78909c; margin-top: 2px; }

.drawer-close {
  width: 32px; height: 32px; border-radius: 8px;
  background: #f5f5f5; border: 1px solid #e0e0e0;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #546e54;
  font-size: 13px; transition: all .15s;
}
.drawer-close:hover { background: #ffebee; border-color: #ef9a9a; color: #c62828; }

/* Drawer body */
.drawer-body {
  flex: 1; overflow-y: auto; padding: 24px;
  scrollbar-width: thin; scrollbar-color: #c8e6c9 transparent;
}

/* Sections */
.form-sec-title {
  font-size: 10px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .1em;
  color: #9e9e9e; margin-bottom: 14px; margin-top: 20px;
  display: flex; align-items: center; gap: 8px;
}
.form-sec-title:first-child { margin-top: 0; }
.form-sec-title::after { content: ''; flex: 1; height: 1px; background: #f0f4f0; }

/* Champ */
.f-group { margin-bottom: 14px; position: relative; }

.f-group label {
  display: block; font-size: 12px; font-weight: 500;
  color: #546e54; margin-bottom: 5px;
}

.f-group input,
.f-group select {
  width: 100%;
  padding: 11px 14px;
  border: 1.5px solid #e0e8e0;
  border-radius: 9px;
  font-family: 'Inter', sans-serif;
  font-size: 14px; color: #1a2e1a;
  background: #fff;
  outline: none;
  transition: border-color .2s, box-shadow .2s, background .2s;
  appearance: none;
}

.f-group input::placeholder { color: #bdbdbd; }

/* États validation JS */
.f-group input.v-ok,
.f-group select.v-ok {
  border-color: #2e7d32 !important;
  box-shadow: 0 0 0 3px rgba(46,125,50,.12) !important;
  background: #fafdf8 !important;
}

.f-group input.v-err,
.f-group select.v-err {
  border-color: #e53935 !important;
  box-shadow: 0 0 0 3px rgba(229,57,53,.1) !important;
  background: #fff8f8 !important;
}

.f-msg {
  font-size: 11px;
  margin-top: 4px;
  padding-left: 2px;
  display: none;
}

.f-msg.err  { color: #e53935; display: block; }
.f-msg.ok   { color: #2e7d32; display: block; }

.f-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

/* Icône état dans le champ */
.f-group .f-icon-state {
  position: absolute; right: 12px; top: 34px;
  font-size: 13px; pointer-events: none;
  opacity: 0; transition: opacity .2s;
}

.f-group.has-ok  .f-icon-state { opacity: 1; color: #2e7d32; }
.f-group.has-err .f-icon-state { opacity: 1; color: #e53935; }

/* Hint pwd */
.pwd-hint {
  font-size: 11px; color: #9e9e9e;
  margin-top: 4px; padding-left: 2px;
}

/* Force pwd */
.pwd-strength { height: 4px; background: #f0f4f0; border-radius: 2px; overflow: hidden; margin: 6px 0 4px; }
.pwd-bar      { height: 100%; border-radius: 2px; width: 0; transition: width .3s, background .3s; }

/* Footer drawer */
.drawer-ft {
  padding: 16px 24px;
  border-top: 1px solid #e8f0e8;
  display: flex; gap: 10px;
  flex-shrink: 0;
  background: #fafdf8;
}

.btn-save {
  flex: 1; padding: 13px;
  background: #2e7d32; color: #fff;
  border: none; border-radius: 10px;
  font-size: 14px; font-weight: 600; font-family: 'Inter', sans-serif;
  cursor: pointer; transition: background .15s;
}
.btn-save:hover { background: #1b5e20; }

.btn-cancel {
  padding: 13px 20px;
  background: #fff; color: #546e54;
  border: 1.5px solid #e0e8e0; border-radius: 10px;
  font-size: 14px; font-weight: 500; font-family: 'Inter', sans-serif;
  cursor: pointer; transition: all .15s;
}
.btn-cancel:hover { border-color: #c62828; color: #c62828; background: #ffebee; }

/* ── SUCCESS FLASH ── */
.pf-flash {
  position: fixed; top: 80px; right: 24px;
  background: #fff; border: 1px solid #c8e6c9;
  border-left: 4px solid #2e7d32;
  border-radius: 10px; padding: 14px 18px;
  display: flex; align-items: center; gap: 10px;
  font-size: 13px; font-weight: 500; color: #1a2e1a;
  box-shadow: 0 4px 20px rgba(0,0,0,.1);
  z-index: 9999;
  animation: slideIn .3s ease;
}
@keyframes slideIn { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }

/* ── RESPONSIVE ── */
@media (max-width: 900px) {
  .g-3-9 { grid-template-columns: 1fr; }
  .g4    { grid-template-columns: 1fr 1fr; }
  .pf-hero-content { flex-wrap: wrap; }
  .pf-hero-chips { margin-left: 0; }
  .pf-drawer { width: 100%; right: -100%; }
}
</style>

<!-- ══ TOPBAR ══ -->
<div class="pf-topbar">
  <a href="#" class="pf-logo">
    <div class="pf-logo-dot"></div>
    EcoNutri
  </a>
  <nav class="pf-nav">
    <a href="index.php?url=User/dashboard" class="pf-nav-link"><i class="fa fa-gauge"></i>Dashboard</a>
    <a href="index.php?url=User/profile"   class="pf-nav-link active"><i class="fa fa-user"></i>Profil</a>
  </nav>
  <a href="index.php?url=User/logout" style="display:flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid #e0e8e0;border-radius:7px;text-decoration:none;font-size:13px;color:#546e54;">
    <i class="fa fa-right-from-bracket" style="font-size:12px;"></i> Déconnexion
  </a>
</div>

<!-- ══ HERO ══ -->
<div class="pf-hero">
  <img class="pf-hero-img"
       src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=1400&q=80"
       alt="nutrition">
  <div class="pf-hero-overlay"></div>
  <div class="pf-hero-content">
    <div class="pf-hero-av"><?= strtoupper(substr($user['nom'], 0, 1)) ?></div>
    <div class="pf-hero-info">
      <div class="pf-hero-name"><?= htmlspecialchars($user['nom']) ?></div>
      <div class="pf-hero-sub"><?= htmlspecialchars($user['email']) ?></div>
    </div>
    <div class="pf-hero-chips">
      <?php if($user['objectif']): ?>
      <span class="pf-chip">🎯 <?= htmlspecialchars($user['objectif']) ?></span>
      <?php endif; ?>
      <?php if($user['activite'] ?? ''): ?>
      <span class="pf-chip">🏃 <?= htmlspecialchars($user['activite']) ?></span>
      <?php endif; ?>
      <span class="pf-chip">IMC <?= $imc ?: '—' ?></span>
    </div>
  </div>
</div>

<!-- ══ BODY ══ -->
<div class="pf-wrap">

  <!-- PHOTO CARDS — 4 métriques -->
  <div class="g4" style="margin-bottom:24px;margin-top:12px;">

    <div class="photo-card">
      <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&q=80" alt="age">
      <div class="photo-card-body">
        <div class="photo-card-icon">🎂</div>
        <div class="photo-card-lbl">Âge</div>
        <div class="photo-card-val" style="color:#1565c0;"><?= $user['age'] ?: '—' ?> <small style="font-size:12px;font-weight:400;color:#9e9e9e;">ans</small></div>
      </div>
    </div>

    <div class="photo-card">
      <img src="https://images.unsplash.com/photo-1554284126-aa88f22d8b74?w=400&q=80" alt="poids">
      <div class="photo-card-body">
        <div class="photo-card-icon">⚖️</div>
        <div class="photo-card-lbl">Poids</div>
        <div class="photo-card-val" style="color:#e65100;"><?= $user['poids'] ?: '—' ?> <small style="font-size:12px;font-weight:400;color:#9e9e9e;">kg</small></div>
      </div>
    </div>

    <div class="photo-card">
      <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?w=400&q=80" alt="taille">
      <div class="photo-card-body">
        <div class="photo-card-icon">📏</div>
        <div class="photo-card-lbl">Taille</div>
        <div class="photo-card-val" style="color:#6a1b9a;"><?= $user['taille'] ?: '—' ?> <small style="font-size:12px;font-weight:400;color:#9e9e9e;">cm</small></div>
      </div>
    </div>

    <div class="photo-card">
      <img src="https://images.unsplash.com/photo-1505576399279-565b52d4ac71?w=400&q=80" alt="imc">
      <div class="photo-card-body">
        <div class="photo-card-icon">🩺</div>
        <div class="photo-card-lbl">IMC</div>
        <div class="photo-card-val" style="color:<?= $imcColor ?>;"><?= $imc ?: '—' ?></div>
      </div>
    </div>

  </div>

  <!-- MAIN GRID -->
  <div class="g-3-9">

    <!-- COLONNE GAUCHE -->
    <div>

      <!-- Profil card -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fa fa-user-circle"></i> Mon profil</div>
        </div>

        <div style="text-align:center;margin-bottom:18px;">
          <div style="width:70px;height:70px;border-radius:50%;background:#e8f5e9;color:#2e7d32;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;margin:0 auto 10px;">
            <?= strtoupper(substr($user['nom'], 0, 1)) ?>
          </div>
          <div style="font-size:16px;font-weight:700;"><?= htmlspecialchars($user['nom']) ?></div>
          <div style="font-size:12px;color:#78909c;margin-top:2px;"><?= htmlspecialchars($user['email']) ?></div>
          <?php if($user['objectif']): ?>
          <div style="margin-top:8px;">
            <span style="background:#e8f5e9;color:#2e7d32;border:1px solid #c8e6c9;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:500;">
              🎯 <?= htmlspecialchars($user['objectif']) ?>
            </span>
          </div>
          <?php endif; ?>
        </div>

        <div>
          <div class="stat-row">
            <div class="stat-ico g">🎂</div>
            <span class="stat-label">Âge</span>
            <span class="stat-value"><?= $user['age'] ?: '—' ?> ans</span>
          </div>
          <div class="stat-row">
            <div class="stat-ico o">⚖️</div>
            <span class="stat-label">Poids</span>
            <span class="stat-value"><?= $user['poids'] ?: '—' ?> kg</span>
          </div>
          <div class="stat-row">
            <div class="stat-ico b">📏</div>
            <span class="stat-label">Taille</span>
            <span class="stat-value"><?= $user['taille'] ?: '—' ?> cm</span>
          </div>
          <div class="stat-row">
            <div class="stat-ico r">🏃</div>
            <span class="stat-label">Activité</span>
            <span class="stat-value"><?= htmlspecialchars($user['activite'] ?? '—') ?></span>
          </div>
        </div>

        <button class="btn-edit" onclick="openDrawer()" style="margin-top:18px;">
          <i class="fa fa-pen" style="font-size:12px;"></i> Modifier mon profil
        </button>
      </div>

      <!-- Objectif card -->
      <div class="obj-card">
        <div class="obj-title">Mon objectif</div>
        <div class="obj-val"><?= htmlspecialchars($user['objectif'] ?: 'Non défini') ?></div>
        <div class="obj-sub">Programme nutrition personnalisé</div>
      </div>

    </div>

    <!-- COLONNE DROITE -->
    <div>

      <!-- IMC détaillé -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fa fa-weight-scale"></i> Indice de Masse Corporelle</div>
          <span class="card-tag" style="color:<?= $imcColor ?>;background:<?= $imcBg ?>;border-color:<?= $imcColor ?>44;"><?= $imcCat ?></span>
        </div>

        <div style="display:flex;align-items:center;gap:24px;">
          <div>
            <div style="font-size:48px;font-weight:800;letter-spacing:-3px;color:<?= $imcColor ?>;line-height:1;"><?= $imc ?: '—' ?></div>
            <div style="font-size:12px;color:#78909c;margin-top:4px;"><?= $user['poids'] ?>kg / (<?= $user['taille'] ?>cm)²</div>
          </div>
          <div style="flex:1;">
            <div class="imc-scale">
              <div class="imc-needle" style="left:<?= $imc > 0 ? min(97, max(2, ($imc/40)*100)) : 2 ?>%"></div>
            </div>
            <div class="imc-scale-lbl">
              <span>&lt;18.5<br>Insuffisant</span>
              <span style="text-align:center">18.5–24.9<br>Normal</span>
              <span style="text-align:center">25–29.9<br>Surpoids</span>
              <span style="text-align:right">&gt;30<br>Obésité</span>
            </div>
          </div>
        </div>

        <div class="g4" style="margin-top:16px;gap:10px;">
          <?php
          $stats4 = [
            ['Poids actuel', $user['poids'].'kg', '#e65100'],
            ['Taille',       $user['taille'].'cm', '#6a1b9a'],
            ['Poids idéal',  $poidsIdealMin.'–'.$poidsIdealMax.'kg', '#2e7d32'],
            ['IMC cible',    '18.5–24.9', '#1565c0'],
          ];
          foreach ($stats4 as $s):
          ?>
          <div style="text-align:center;background:#f8fdf8;border:1px solid #e8f0e8;border-radius:10px;padding:12px 8px;">
            <div style="font-size:14px;font-weight:700;color:<?= $s[2] ?>;"><?= $s[1] ?></div>
            <div style="font-size:10px;color:#9e9e9e;margin-top:2px;"><?= $s[0] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Photo + Conseils -->
      <div class="g2" style="margin-bottom:18px;">

        <div class="card" style="padding:0;overflow:hidden;margin-bottom:0;">
          <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&q=80"
               style="width:100%;height:160px;object-fit:cover;display:block;">
          <div style="padding:16px;">
            <div style="font-size:13px;font-weight:600;margin-bottom:4px;">🥗 Alimentation durable</div>
            <div style="font-size:12px;color:#546e54;line-height:1.6;">Adoptez des habitudes alimentaires saines et respectueuses de l'environnement.</div>
          </div>
        </div>

        <div class="card" style="margin-bottom:0;">
          <div class="card-hd">
            <div class="card-title"><i class="fa fa-bullseye"></i> Objectifs</div>
          </div>
          <div class="prog-item">
            <div class="prog-top"><span class="prog-lbl">Profil complété</span><span class="prog-val" style="color:#2e7d32;">
              <?php
              $done = 0;
              if ($user['nom'])      $done++;
              if ($user['email'])    $done++;
              if ($user['poids'])    $done++;
              if ($user['taille'])   $done++;
              if ($user['age'])      $done++;
              if ($user['objectif']) $done++;
              echo round($done/6*100).'%';
              ?>
            </span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:<?= round($done/6*100) ?>%;background:#2e7d32;"></div></div>
          </div>
          <div class="prog-item">
            <div class="prog-top"><span class="prog-lbl">Activité physique</span><span class="prog-val" style="color:#e65100;">60%</span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:60%;background:#e65100;"></div></div>
          </div>
          <div class="prog-item">
            <div class="prog-top"><span class="prog-lbl">Hydratation</span><span class="prog-val" style="color:#1565c0;">45%</span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:45%;background:#1565c0;"></div></div>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

<!-- ══ OVERLAY ══ -->
<div class="pf-overlay" id="pfOverlay" onclick="closeDrawer()"></div>

<!-- ══ DRAWER FORMULAIRE ══ -->
<div class="pf-drawer" id="pfDrawer">

  <div class="drawer-hd">
    <div class="drawer-hd-left">
      <h3>✏ Modifier mon profil</h3>
      <p>Mettez à jour vos informations personnelles</p>
    </div>
    <div class="drawer-close" onclick="closeDrawer()">
      <i class="fa fa-xmark"></i>
    </div>
  </div>

  <div class="drawer-body">
    <form id="profileForm" method="POST" action="index.php?url=User/update" novalidate>

      <!-- Identité -->
      <div class="form-sec-title">Identité</div>

      <div class="f-group" id="grp-nom">
        <label>Nom complet</label>
        <input type="text" id="f-nom" name="nom"
               value="<?= htmlspecialchars($user['nom']) ?>"
               placeholder="ex: Ahmed Ben Ali"
               data-rule="required">
        <span class="f-icon-state"><i class="fa fa-circle-check"></i></span>
        <div class="f-msg" id="msg-nom"></div>
      </div>

      <div class="f-group" id="grp-email">
        <label>Adresse email</label>
        <input type="text" id="f-email" name="email"
               value="<?= htmlspecialchars($user['email']) ?>"
               placeholder="nom@email.com"
               data-rule="email">
        <span class="f-icon-state"><i class="fa fa-circle-check"></i></span>
        <div class="f-msg" id="msg-email"></div>
      </div>

      <div class="f-group" id="grp-pwd">
        <label>Nouveau mot de passe <span style="color:#bdbdbd;font-size:10px;font-weight:400;">(laisser vide = inchangé)</span></label>
        <input type="password" id="f-pwd" name="password"
               placeholder="••••••••"
               data-rule="optpwd">
        <span class="f-icon-state"><i class="fa fa-circle-check"></i></span>
        <div class="pwd-strength"><div class="pwd-bar" id="pwdBar"></div></div>
        <div class="f-msg" id="msg-pwd"></div>
        <div class="pwd-hint">Minimum 8 caractères, mélanger lettres et chiffres</div>
      </div>

      <!-- Données santé -->
      <div class="form-sec-title">Données santé</div>

      <div class="f-row">
        <div class="f-group" id="grp-age">
          <label>Âge</label>
          <input type="number" id="f-age" name="age"
                 value="<?= htmlspecialchars($user['age'] ?? '') ?>"
                 placeholder="ex: 28" min="10" max="120"
                 data-rule="age">
          <span class="f-icon-state"><i class="fa fa-circle-check"></i></span>
          <div class="f-msg" id="msg-age"></div>
        </div>
        <div class="f-group" id="grp-poids">
          <label>Poids (kg)</label>
          <input type="number" id="f-poids" name="poids"
                 value="<?= htmlspecialchars($user['poids']) ?>"
                 placeholder="ex: 70" step="0.1" min="20" max="300"
                 data-rule="poids">
          <span class="f-icon-state"><i class="fa fa-circle-check"></i></span>
          <div class="f-msg" id="msg-poids"></div>
        </div>
      </div>

      <div class="f-group" id="grp-taille">
        <label>Taille (cm)</label>
        <input type="number" id="f-taille" name="taille"
               value="<?= htmlspecialchars($user['taille']) ?>"
               placeholder="ex: 175" min="100" max="250"
               data-rule="taille">
        <span class="f-icon-state"><i class="fa fa-circle-check"></i></span>
        <div class="f-msg" id="msg-taille"></div>
      </div>

      <!-- Préférences -->
      <div class="form-sec-title">Préférences</div>

      <div class="f-group" id="grp-obj">
        <label>Objectif nutritionnel</label>
        <select id="f-obj" name="objectif" data-rule="select"
                style="padding-right:36px;background-image:url('data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'10\' height=\'6\'><path d=\'M0 0l5 6 5-6z\' fill=\'%239e9e9e\'/></svg>');background-repeat:no-repeat;background-position:right 12px center;">
          <option value="">-- Choisir un objectif --</option>
          <option value="Perte de poids"       <?= $user['objectif']==='Perte de poids'       ? 'selected':'' ?>>Perte de poids</option>
          <option value="Prise de masse"        <?= $user['objectif']==='Prise de masse'        ? 'selected':'' ?>>Prise de masse</option>
          <option value="Équilibre alimentaire" <?= $user['objectif']==='Équilibre alimentaire' ? 'selected':'' ?>>Équilibre alimentaire</option>
          <option value="Végétarien"            <?= $user['objectif']==='Végétarien'            ? 'selected':'' ?>>Végétarien</option>
          <option value="Sportif"               <?= $user['objectif']==='Sportif'               ? 'selected':'' ?>>Sportif</option>
        </select>
        <div class="f-msg" id="msg-obj"></div>
      </div>

    </form>
  </div>

  <div class="drawer-ft">
    <button class="btn-cancel" onclick="closeDrawer()">Annuler</button>
    <button class="btn-save" onclick="submitForm()">
      <i class="fa fa-floppy-disk" style="font-size:13px;margin-right:6px;"></i> Enregistrer
    </button>
  </div>

</div>

<!-- FLASH -->
<?php if (!empty($_SESSION['profile_success'])): ?>
<div class="pf-flash" id="flashMsg">
  ✅ <?= htmlspecialchars($_SESSION['profile_success']) ?>
</div>
<?php unset($_SESSION['profile_success']); ?>
<script>setTimeout(() => { const f = document.getElementById('flashMsg'); if(f) f.style.opacity='0'; }, 3000);</script>
<?php endif; ?>

<!-- ══════════════════════════════════════
     VALIDATION JS — RÈGLES COMPLÈTES
══════════════════════════════════════ -->
<script>
/* ── Règles ── */
const rules = {
  required: v => v.trim() !== '' ? null : 'Ce champ est obligatoire.',
  email:    v => {
    if (!v.trim()) return 'Email obligatoire.';
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()) ? null : 'Format email invalide (ex: nom@domaine.com).';
  },
  age:      v => {
    if (!v) return 'Âge obligatoire.';
    const n = parseInt(v);
    return (!isNaN(n) && n >= 10 && n <= 120) ? null : 'Âge invalide (entre 10 et 120 ans).';
  },
  poids:    v => {
    if (!v) return 'Poids obligatoire.';
    const n = parseFloat(v);
    return (!isNaN(n) && n >= 20 && n <= 300) ? null : 'Poids invalide (entre 20 et 300 kg).';
  },
  taille:   v => {
    if (!v) return 'Taille obligatoire.';
    const n = parseFloat(v);
    return (!isNaN(n) && n >= 100 && n <= 250) ? null : 'Taille invalide (entre 100 et 250 cm).';
  },
  optpwd:   v => v === '' ? null : (v.length >= 8 ? null : 'Minimum 8 caractères requis.'),
  select:   v => v !== '' ? null : 'Veuillez sélectionner une option.',
};

/* ── Appliquer état sur un champ ── */
function applyState(inputEl, grpEl, msgEl, error) {
  if (error) {
    inputEl.classList.remove('v-ok'); inputEl.classList.add('v-err');
    grpEl.classList.remove('has-ok'); grpEl.classList.add('has-err');
    msgEl.textContent = error; msgEl.className = 'f-msg err';
    grpEl.querySelector('.f-icon-state i').className = 'fa fa-circle-xmark';
  } else if (inputEl.value.trim() !== '' || inputEl.tagName === 'SELECT') {
    inputEl.classList.remove('v-err'); inputEl.classList.add('v-ok');
    grpEl.classList.remove('has-err'); grpEl.classList.add('has-ok');
    msgEl.textContent = ''; msgEl.className = 'f-msg';
    grpEl.querySelector('.f-icon-state i').className = 'fa fa-circle-check';
  } else {
    inputEl.classList.remove('v-ok','v-err');
    grpEl.classList.remove('has-ok','has-err');
    msgEl.textContent = ''; msgEl.className = 'f-msg';
  }
  return !error;
}

/* ── Valider un champ ── */
function validateField(id) {
  const input = document.getElementById('f-' + id);
  if (!input) return true;
  const grp   = document.getElementById('grp-' + id);
  const msg   = document.getElementById('msg-' + id);
  const rule  = input.dataset.rule;
  const err   = rules[rule] ? rules[rule](input.value) : null;
  return applyState(input, grp, msg, err);
}

/* ── Force mot de passe ── */
document.getElementById('f-pwd').addEventListener('input', function() {
  const v   = this.value;
  const bar = document.getElementById('pwdBar');
  let score = 0;
  if (v.length >= 8)          score += 30;
  if (/[A-Z]/.test(v))        score += 20;
  if (/[0-9]/.test(v))        score += 25;
  if (/[^A-Za-z0-9]/.test(v)) score += 25;
  bar.style.width      = score + '%';
  bar.style.background = score < 40 ? '#e53935' : score < 70 ? '#ff9800' : '#2e7d32';
  validateField('pwd');
});

/* ── Validation temps réel sur tous les champs ── */
const fieldIds = ['nom','email','age','poids','taille'];

fieldIds.forEach(id => {
  const el = document.getElementById('f-' + id);
  if (!el) return;

  el.addEventListener('input', () => {
    if (el.value.trim().length > 0) validateField(id);
    else {
      el.classList.remove('v-ok','v-err');
      document.getElementById('grp-' + id).classList.remove('has-ok','has-err');
      document.getElementById('msg-' + id).className = 'f-msg';
    }
  });

  el.addEventListener('blur',  () => validateField(id));
  el.addEventListener('focus', () => { if (!el.value.trim()) { el.classList.remove('v-ok','v-err'); } });
});

/* Select objectif */
document.getElementById('f-obj').addEventListener('change', () => validateField('obj'));

/* ── Validation complète avant submit ── */
function submitForm() {
  const fields = ['nom','email','pwd','age','poids','taille','obj'];
  let allOk = true;

  fields.forEach(id => {
    if (!validateField(id)) allOk = false;
  });

  if (allOk) {
    document.getElementById('profileForm').submit();
  } else {
    // Scroll vers le premier champ en erreur
    const firstErr = document.querySelector('.f-group.has-err input, .f-group.has-err select');
    if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
}

/* ── Drawer open/close ── */
function openDrawer() {
  document.getElementById('pfDrawer').classList.add('open');
  document.getElementById('pfOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeDrawer() {
  document.getElementById('pfDrawer').classList.remove('open');
  document.getElementById('pfOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

/* ── Animate progress bars ── */
window.addEventListener('load', () => {
  document.querySelectorAll('.prog-fill').forEach(el => {
    const w = el.style.width;
    el.style.width = '0';
    setTimeout(() => { el.style.width = w; }, 200);
  });
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>