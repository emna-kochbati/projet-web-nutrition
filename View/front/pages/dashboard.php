<?php
// $user injecté par UserController::dashboard()
$user = $_SESSION['user'];

// ── Calculs ──
$poids  = (float)($user['poids']  ?? 0);
$taille = (float)($user['taille'] ?? 0);
$age    = (int)  ($user['age']    ?? 25);
$obj    = strtolower($user['objectif'] ?? '');

$imc = ($poids > 0 && $taille > 0)
    ? round($poids / pow($taille / 100, 2), 1) : 0;

$imcCat = '—'; $imcColor = '#2e7d32';
if ($imc > 0) {
    if      ($imc < 18.5) { $imcCat = 'Insuffisance pondérale'; $imcColor = '#1565c0'; }
    elseif  ($imc < 25)   { $imcCat = 'Poids normal';           $imcColor = '#2e7d32'; }
    elseif  ($imc < 30)   { $imcCat = 'Surpoids';               $imcColor = '#e65100'; }
    else                  { $imcCat = 'Obésité';                 $imcColor = '#c62828'; }
}

// Mifflin-St Jeor
$bmr  = 10 * $poids + 6.25 * $taille - 5 * $age + 5;
$tdee = round($bmr * 1.55);
$calories = match(true) {
    str_contains($obj, 'perte') => $tdee - 500,
    str_contains($obj, 'masse') => $tdee + 500,
    default                     => $tdee,
};

$proteines = round($calories * 0.25 / 4);
$glucides  = round($calories * 0.50 / 4);
$lipides   = round($calories * 0.25 / 9);
$eau       = $poids > 0 ? round($poids * 0.033, 1) : 2.0;

// Score profil
$score = 0;
if ($imc >= 18.5 && $imc < 25) $score += 30; elseif ($imc > 0) $score += 15;
if ($poids > 0)  $score += 20;
if ($taille > 0) $score += 10;
if ($age > 0)    $score += 10;
if (!empty($user['objectif'])) $score += 20;
if (!empty($user['activite'])) $score += 10;

// Heure
$h    = (int)date('H');
$salut = $h < 12 ? 'Bonjour' : ($h < 18 ? 'Bon après-midi' : 'Bonsoir');

// Poids idéal
$poidsMinIdeal = $taille > 0 ? round(18.5 * pow($taille/100, 2), 1) : 0;
$poidsMaxIdeal = $taille > 0 ? round(24.9 * pow($taille/100, 2), 1) : 0;

// Plan alimentaire selon objectif
$plan = [
    ['Petit-déjeuner', '7h00',  'Flocons d\'avoine, fruit frais, yaourt nature',     round($calories*0.20), round($proteines*0.15)],
    ['Déjeuner',       '12h30', 'Poulet grillé, légumes vapeur, riz complet',         round($calories*0.35), round($proteines*0.40)],
    ['Collation',      '16h00', 'Amandes (20g), pomme',                               round($calories*0.10), round($proteines*0.10)],
    ['Dîner',          '19h30', 'Saumon, quinoa, salade verte',                       round($calories*0.30), round($proteines*0.30)],
    ['Snack',          '21h00', 'Fromage blanc 0%, miel',                             round($calories*0.05), round($proteines*0.05)],
];

include __DIR__ . '/../partials/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* ── RESET & BASE ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Inter', sans-serif !important;
  background: #f4f6f4 !important;
  color: #1a2e1a !important;
}

/* ── TOPBAR ── */
.db-topbar {
  background: #ffffff;
  border-bottom: 1px solid #e0e8e0;
  padding: 0 40px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.db-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 18px;
  font-weight: 700;
  color: #1a2e1a;
  text-decoration: none;
}

.db-logo-dot {
  width: 10px; height: 10px;
  background: #2e7d32;
  border-radius: 50%;
}

.db-topbar-center {
  font-size: 14px;
  font-weight: 500;
  color: #4a6a4a;
}

.db-topbar-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

.db-user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.db-avatar {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: #2e7d32;
  color: white;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 600;
}

.db-user-name { font-size: 14px; font-weight: 500; }
.db-user-role { font-size: 11px; color: #78909c; }

.db-logout {
  display: flex; align-items: center; gap: 6px;
  padding: 7px 14px;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  text-decoration: none;
  font-size: 13px; color: #546e54;
  transition: all .15s;
}
.db-logout:hover { background: #fafafa; border-color: #bdbdbd; color: #1a2e1a; }

/* ── WRAPPER ── */
.db-wrap {
  max-width: 1200px;
  margin: 0 auto;
  padding: 32px 32px 60px;
}

/* ── PAGE HEADER ── */
.db-page-header {
  margin-bottom: 28px;
  padding-bottom: 20px;
  border-bottom: 1px solid #e0e8e0;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
}

.db-greeting {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .1em;
  color: #2e7d32;
  margin-bottom: 6px;
}

.db-page-title {
  font-size: 26px;
  font-weight: 700;
  color: #1a2e1a;
  letter-spacing: -.4px;
}

.db-page-date {
  font-size: 13px;
  color: #78909c;
}

/* ── SECTION TITLE ── */
.sec-title {
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .08em;
  color: #546e54;
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.sec-title::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #e0e8e0;
}

/* ── GRID ── */
.g4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 24px; }
.g3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 24px; }
.g2 { display: grid; grid-template-columns: 1fr 1fr;        gap: 16px; margin-bottom: 24px; }
.g21{ display: grid; grid-template-columns: 2fr 1fr;        gap: 16px; margin-bottom: 24px; }
.g12{ display: grid; grid-template-columns: 1fr 2fr;        gap: 16px; margin-bottom: 24px; }

/* ── CARD ── */
.card {
  background: #ffffff;
  border: 1px solid #e0e8e0;
  border-radius: 12px;
  padding: 20px 22px;
}

/* ── KPI CARD ── */
.kpi {
  background: #ffffff;
  border: 1px solid #e0e8e0;
  border-radius: 12px;
  padding: 20px;
  position: relative;
  overflow: hidden;
}

.kpi-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.kpi-icon {
  width: 38px; height: 38px;
  border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  font-size: 17px;
}

.kpi-badge {
  font-size: 10px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 20px;
}

.kpi-val {
  font-size: 28px;
  font-weight: 700;
  line-height: 1;
  letter-spacing: -.5px;
  margin-bottom: 3px;
}

.kpi-lbl {
  font-size: 12px;
  color: #78909c;
}

.kpi-sub {
  font-size: 11px;
  color: #9e9e9e;
  margin-top: 2px;
}

/* couleurs KPI */
.kpi-g  .kpi-icon { background: #e8f5e9; }
.kpi-g  .kpi-val  { color: #2e7d32; }
.kpi-g  .kpi-badge{ background: #e8f5e9; color: #2e7d32; }

.kpi-o  .kpi-icon { background: #fff3e0; }
.kpi-o  .kpi-val  { color: #e65100; }
.kpi-o  .kpi-badge{ background: #fff3e0; color: #e65100; }

.kpi-b  .kpi-icon { background: #e3f2fd; }
.kpi-b  .kpi-val  { color: #1565c0; }
.kpi-b  .kpi-badge{ background: #e3f2fd; color: #1565c0; }

.kpi-p  .kpi-icon { background: #fce4ec; }
.kpi-p  .kpi-val  { color: #880e4f; }
.kpi-p  .kpi-badge{ background: #fce4ec; color: #880e4f; }

/* ── CARD HEADER ── */
.card-hd {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
  padding-bottom: 14px;
  border-bottom: 1px solid #f0f4f0;
}

.card-title {
  font-size: 14px;
  font-weight: 600;
  color: #1a2e1a;
  display: flex;
  align-items: center;
  gap: 7px;
}

.card-title i { color: #2e7d32; font-size: 14px; }

.card-tag {
  font-size: 11px;
  font-weight: 500;
  padding: 3px 10px;
  border-radius: 20px;
  background: #f1f8f1;
  color: #2e7d32;
  border: 1px solid #c8e6c9;
}

/* ── IMC METER ── */
.imc-num {
  font-size: 40px;
  font-weight: 700;
  letter-spacing: -2px;
  line-height: 1;
}

.imc-cat {
  font-size: 13px;
  font-weight: 500;
  margin-top: 4px;
  margin-bottom: 18px;
}

.imc-scale {
  height: 8px;
  border-radius: 4px;
  background: linear-gradient(90deg, #1565c0 0%, #2e7d32 35%, #e65100 65%, #c62828 100%);
  position: relative;
  margin-bottom: 6px;
}

.imc-needle {
  position: absolute;
  top: -4px;
  width: 4px; height: 16px;
  background: #1a2e1a;
  border-radius: 2px;
  transform: translateX(-50%);
  transition: left 1s ease;
  box-shadow: 0 1px 4px rgba(0,0,0,.25);
}

.imc-scale-labels {
  display: flex;
  justify-content: space-between;
  font-size: 10px;
  color: #9e9e9e;
  margin-top: 6px;
}

.imc-stats {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 8px;
  margin-top: 16px;
}

.imc-stat {
  text-align: center;
  padding: 10px 8px;
  background: #f8fdf8;
  border: 1px solid #e8f0e8;
  border-radius: 8px;
}

.imc-stat-val { font-size: 16px; font-weight: 700; color: #1a2e1a; }
.imc-stat-lbl { font-size: 10px; color: #78909c; margin-top: 2px; }

/* ── PROGRESS BARS ── */
.prog-item { margin-bottom: 14px; }
.prog-item:last-child { margin-bottom: 0; }

.prog-top {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  margin-bottom: 6px;
}

.prog-lbl { color: #546e54; font-weight: 500; }
.prog-val { color: #1a2e1a; font-weight: 600; }

.prog-bar {
  height: 6px;
  background: #f0f4f0;
  border-radius: 3px;
  overflow: hidden;
}

.prog-fill {
  height: 100%;
  border-radius: 3px;
  transition: width 1.1s ease;
}

/* ── MACRO TABLE ── */
.mac-table {
  width: 100%;
  border-collapse: collapse;
}

.mac-table th {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .06em;
  color: #9e9e9e;
  text-align: left;
  padding: 0 0 10px;
  border-bottom: 1px solid #f0f4f0;
}

.mac-table td {
  padding: 10px 0;
  font-size: 13px;
  border-bottom: 1px solid #f8faf8;
  vertical-align: middle;
}

.mac-table tr:last-child td { border-bottom: none; }

.mac-dot {
  display: inline-block;
  width: 8px; height: 8px;
  border-radius: 50%;
  margin-right: 6px;
}

.mac-bar-cell { width: 80px; }
.mac-mini-bar { height: 4px; background: #f0f4f0; border-radius: 2px; overflow: hidden; }
.mac-mini-fill { height: 100%; border-radius: 2px; }

/* ── SCORE CARD ── */
.score-wrap {
  display: flex;
  align-items: center;
  gap: 20px;
}

.score-ring-wrap { position: relative; flex-shrink: 0; }
.score-svg { transform: rotate(-90deg); }
.score-ring-bg   { fill: none; stroke: #e8f0e8; }
.score-ring-fill { fill: none; stroke: #2e7d32; stroke-linecap: round; transition: stroke-dashoffset 1.4s ease; }

.score-center {
  position: absolute; inset: 0;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
}

.score-num {
  font-size: 28px; font-weight: 700;
  color: #1a2e1a; letter-spacing: -1px; line-height: 1;
}

.score-denom { font-size: 11px; color: #9e9e9e; }

.score-details { flex: 1; }
.score-details h4 { font-size: 15px; font-weight: 600; margin-bottom: 6px; }
.score-details p  { font-size: 13px; color: #546e54; line-height: 1.6; margin-bottom: 12px; }

.score-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.score-chip {
  font-size: 11px; font-weight: 500;
  padding: 4px 10px; border-radius: 20px;
  background: #f1f8f1; color: #2e7d32;
  border: 1px solid #c8e6c9;
}

.score-chip.o { background: #fff3e0; color: #e65100; border-color: #ffe0b2; }

/* ── EAU TRACKER ── */
.water-num {
  font-size: 32px; font-weight: 700;
  color: #1565c0; letter-spacing: -1px; line-height: 1;
}

.water-sub { font-size: 12px; color: #78909c; margin-bottom: 14px; }

.water-bar {
  height: 8px; background: #e3f2fd; border-radius: 4px; overflow: hidden; margin-bottom: 14px;
}

.water-bar-fill {
  height: 100%; border-radius: 4px;
  background: linear-gradient(90deg, #1565c0, #42a5f5);
  transition: width 1s ease;
}

.water-drops { display: flex; gap: 6px; flex-wrap: wrap; }

.wdrop {
  width: 34px; height: 34px;
  border: 1.5px solid #bbdefb;
  border-radius: 50%;
  background: #e3f2fd;
  display: flex; align-items: center; justify-content: center;
  font-size: 15px;
  cursor: pointer;
  opacity: .35;
  transition: all .18s;
  color: #1565c0;
}

.wdrop.on { opacity: 1; background: #bbdefb; border-color: #1565c0; transform: scale(1.1); }
.wdrop-hint { font-size: 11px; color: #9e9e9e; margin-top: 8px; }

/* ── PLAN TABLE ── */
.plan-table { width: 100%; border-collapse: collapse; }

.plan-table th {
  font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em;
  color: #9e9e9e; text-align: left; padding: 0 12px 10px 0;
  border-bottom: 1px solid #f0f4f0;
}

.plan-table td {
  padding: 11px 12px 11px 0;
  font-size: 13px;
  border-bottom: 1px solid #f8faf8;
  vertical-align: middle;
}

.plan-table tr:last-child td { border-bottom: none; }

.plan-tag {
  display: inline-block;
  padding: 2px 10px; border-radius: 20px;
  font-size: 11px; font-weight: 500; white-space: nowrap;
}

.plan-tag.pdj { background: #fff3e0; color: #e65100; }
.plan-tag.dej { background: #e8f5e9; color: #2e7d32; }
.plan-tag.col { background: #fce4ec; color: #880e4f; }
.plan-tag.din { background: #e3f2fd; color: #1565c0; }

.plan-time { font-size: 12px; color: #9e9e9e; }
.plan-desc { font-size: 12px; color: #546e54; margin-top: 2px; }
.plan-cal  { font-weight: 600; color: #e65100; }
.plan-prot { font-weight: 600; color: #2e7d32; }

/* ── ALERTE ITEMS ── */
.alert-item {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid #f0f4f0;
}

.alert-item:last-child { border-bottom: none; padding-bottom: 0; }

.alert-ico {
  width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 14px;
}

.alert-ico.g { background: #e8f5e9; }
.alert-ico.o { background: #fff3e0; }
.alert-ico.b { background: #e3f2fd; }

.alert-txt  { font-size: 13px; font-weight: 500; color: #1a2e1a; }
.alert-desc { font-size: 12px; color: #78909c; margin-top: 2px; }

/* ── OBJECTIFS PROGRESS ── */
.obj-item {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid #f0f4f0;
}

.obj-item:last-child { border-bottom: none; }

.obj-ico {
  font-size: 16px; width: 20px; text-align: center; flex-shrink: 0;
}

.obj-info { flex: 1; }

.obj-top {
  display: flex; justify-content: space-between;
  font-size: 13px; margin-bottom: 5px;
}

.obj-lbl { color: #546e54; }
.obj-val { font-weight: 600; color: #1a2e1a; }

.obj-bar { height: 5px; background: #f0f4f0; border-radius: 3px; overflow: hidden; }
.obj-fill { height: 100%; border-radius: 3px; transition: width 1.1s ease; }

/* ── BOUTON PROFIL ── */
.btn-profil {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 10px 18px;
  background: #2e7d32; color: white;
  border-radius: 8px; text-decoration: none;
  font-size: 13px; font-weight: 500;
  transition: background .15s;
  margin-top: 16px;
}
.btn-profil:hover { background: #1b5e20; color: white; }

/* ── RESPONSIVE ── */
@media (max-width: 1024px) {
  .g4 { grid-template-columns: repeat(2,1fr); }
  .g3 { grid-template-columns: repeat(2,1fr); }
  .g21, .g12 { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
  .g4, .g3, .g2, .g21, .g12 { grid-template-columns: 1fr; }
  .db-wrap { padding: 20px 16px 48px; }
  .db-topbar { padding: 0 16px; }
  .db-topbar-center { display: none; }
}
</style>

<!-- ══ TOPBAR ══ -->
<div class="db-topbar">
  <a href="#" class="db-logo">
    <div class="db-logo-dot"></div>
    EcoNutri
  </a>
  <div class="db-topbar-center">
    Tableau de bord — <?= date('l d F Y') ?>
  </div>
  <div class="db-topbar-right">
    <div class="db-user-info">
      <div class="db-avatar"><?= strtoupper(substr($user['nom'], 0, 1)) ?></div>
      <div>
        <div class="db-user-name"><?= htmlspecialchars($user['nom']) ?></div>
        <div class="db-user-role"><?= htmlspecialchars($user['objectif'] ?: 'Utilisateur') ?></div>
      </div>
    </div>
    <a href="index.php?url=User/logout" class="db-logout">
      <i class="fa fa-right-from-bracket" style="font-size:12px;"></i>
      Déconnexion
    </a>
  </div>
</div>

<!-- ══ BODY ══ -->
<div class="db-wrap">

  <!-- Page header -->
  <div class="db-page-header">
    <div>
      <div class="db-greeting"><?= $salut ?></div>
      <div class="db-page-title"><?= htmlspecialchars($user['nom']) ?></div>
    </div>
    <div class="db-page-date"><?= date('d/m/Y H:i') ?></div>
  </div>

  <!-- ══ KPI ══ -->
  <div class="sec-title">Indicateurs clés</div>
  <div class="g4">

    <div class="kpi kpi-g">
      <div class="kpi-top">
        <div class="kpi-icon">⚖️</div>
        <span class="kpi-badge">IMC</span>
      </div>
      <div class="kpi-val" style="color:<?= $imcColor ?>"><?= $imc ?: '—' ?></div>
      <div class="kpi-lbl">Indice de masse corporelle</div>
      <div class="kpi-sub" style="color:<?= $imcColor ?>"><?= $imcCat ?></div>
    </div>

    <div class="kpi kpi-o">
      <div class="kpi-top">
        <div class="kpi-icon">🔥</div>
        <span class="kpi-badge">kcal</span>
      </div>
      <div class="kpi-val"><?= number_format($calories) ?></div>
      <div class="kpi-lbl">Calories recommandées / jour</div>
      <div class="kpi-sub">TDEE estimé : <?= number_format($tdee) ?> kcal</div>
    </div>

    <div class="kpi kpi-b">
      <div class="kpi-top">
        <div class="kpi-icon">💧</div>
        <span class="kpi-badge">eau</span>
      </div>
      <div class="kpi-val"><?= $eau ?>L</div>
      <div class="kpi-lbl">Hydratation quotidienne</div>
      <div class="kpi-sub"><?= $poids ?>kg × 33 ml/kg</div>
    </div>

    <div class="kpi kpi-p">
      <div class="kpi-top">
        <div class="kpi-icon">🥩</div>
        <span class="kpi-badge">g/j</span>
      </div>
      <div class="kpi-val"><?= $proteines ?>g</div>
      <div class="kpi-lbl">Protéines recommandées</div>
      <div class="kpi-sub">25 % des apports caloriques</div>
    </div>

  </div>

  <!-- ══ IMC + Score ══ -->
  <div class="sec-title">Bilan santé</div>
  <div class="g2">

    <!-- IMC détaillé -->
    <div class="card">
      <div class="card-hd">
        <div class="card-title"><i class="fa fa-weight-scale"></i> IMC détaillé</div>
        <span class="card-tag" style="color:<?= $imcColor ?>;border-color:<?= $imcColor ?>44;background:<?= $imcColor ?>11;"><?= $imcCat ?></span>
      </div>

      <div class="imc-num" style="color:<?= $imcColor ?>"><?= $imc ?: '—' ?></div>
      <div class="imc-cat" style="color:<?= $imcColor ?>"><?= $imcCat ?></div>

      <div class="imc-scale">
        <div class="imc-needle" id="imcNeedle"
             style="left:<?= $imc > 0 ? min(97, max(2, ($imc/40)*100)) : 2 ?>%"></div>
      </div>
      <div class="imc-scale-labels">
        <span>&lt; 18.5<br>Insuffisant</span>
        <span style="text-align:center">18.5 – 24.9<br>Normal</span>
        <span style="text-align:center">25 – 29.9<br>Surpoids</span>
        <span style="text-align:right">&gt; 30<br>Obésité</span>
      </div>

      <div class="imc-stats">
        <div class="imc-stat">
          <div class="imc-stat-val"><?= $poids ?> kg</div>
          <div class="imc-stat-lbl">Poids actuel</div>
        </div>
        <div class="imc-stat">
          <div class="imc-stat-val"><?= $taille ?> cm</div>
          <div class="imc-stat-lbl">Taille</div>
        </div>
        <div class="imc-stat">
          <div class="imc-stat-val"><?= $poidsMinIdeal ?>–<?= $poidsMaxIdeal ?></div>
          <div class="imc-stat-lbl">Poids idéal kg</div>
        </div>
      </div>
    </div>

    <!-- Score profil -->
    <div class="card">
      <div class="card-hd">
        <div class="card-title"><i class="fa fa-chart-pie"></i> Score profil nutritionnel</div>
        <span class="card-tag"><?= $score ?> / 100</span>
      </div>

      <div class="score-wrap">
        <div class="score-ring-wrap">
          <svg class="score-svg" width="110" height="110" viewBox="0 0 110 110">
            <circle class="score-ring-bg"   cx="55" cy="55" r="44" stroke-width="8"/>
            <circle class="score-ring-fill" cx="55" cy="55" r="44" stroke-width="8"
                    stroke-dasharray="276.46"
                    stroke-dashoffset="<?= 276.46 * (1 - $score/100) ?>"/>
          </svg>
          <div class="score-center">
            <div class="score-num"><?= $score ?></div>
            <div class="score-denom">/ 100</div>
          </div>
        </div>
        <div class="score-details">
          <h4>
            <?php
            if ($score >= 80)      echo 'Excellent profil 🎉';
            elseif ($score >= 60)  echo 'Bon profil ✓';
            elseif ($score >= 40)  echo 'Profil à améliorer';
            else                   echo 'Profil incomplet';
            ?>
          </h4>
          <p>Complétez votre profil et respectez vos objectifs pour améliorer votre score et recevoir des recommandations personnalisées.</p>
          <div class="score-chips">
            <?php if ($user['objectif']): ?><span class="score-chip">🎯 <?= htmlspecialchars($user['objectif']) ?></span><?php endif; ?>
            <?php if ($user['activite'] ?? ''): ?><span class="score-chip o">🏃 <?= htmlspecialchars($user['activite']) ?></span><?php endif; ?>
            <?php if ($imc > 0): ?><span class="score-chip" style="background:<?= $imcColor ?>11;color:<?= $imcColor ?>;border-color:<?= $imcColor ?>44;">IMC <?= $imc ?></span><?php endif; ?>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- ══ Macros + Eau + Objectifs ══ -->
  <div class="sec-title">Nutrition & Hydratation</div>
  <div class="g3">

    <!-- Macronutriments -->
    <div class="card">
      <div class="card-hd">
        <div class="card-title"><i class="fa fa-utensils"></i> Macronutriments</div>
        <span class="card-tag"><?= $calories ?> kcal</span>
      </div>

      <div style="margin-bottom:18px;">
        <canvas id="macroChart" height="120"></canvas>
      </div>

      <table class="mac-table">
        <thead>
          <tr>
            <th>Nutriment</th>
            <th>Quantité</th>
            <th>Répartition</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="mac-dot" style="background:#2e7d32"></span>Protéines</td>
            <td><strong><?= $proteines ?>g</strong></td>
            <td class="mac-bar-cell">
              <div class="mac-mini-bar"><div class="mac-mini-fill" style="width:25%;background:#2e7d32;"></div></div>
            </td>
          </tr>
          <tr>
            <td><span class="mac-dot" style="background:#1565c0"></span>Glucides</td>
            <td><strong><?= $glucides ?>g</strong></td>
            <td class="mac-bar-cell">
              <div class="mac-mini-bar"><div class="mac-mini-fill" style="width:50%;background:#1565c0;"></div></div>
            </td>
          </tr>
          <tr>
            <td><span class="mac-dot" style="background:#e65100"></span>Lipides</td>
            <td><strong><?= $lipides ?>g</strong></td>
            <td class="mac-bar-cell">
              <div class="mac-mini-bar"><div class="mac-mini-fill" style="width:25%;background:#e65100;"></div></div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Eau -->
    <div class="card">
      <div class="card-hd">
        <div class="card-title"><i class="fa fa-droplet" style="color:#1565c0;"></i> Hydratation du jour</div>
      </div>

      <div class="water-num" id="waterNum">0.0 L</div>
      <div class="water-sub">objectif : <?= $eau ?>L / jour</div>

      <div class="water-bar">
        <div class="water-bar-fill" id="waterFill" style="width:0%"></div>
      </div>

      <div class="water-drops" id="waterDrops">
        <?php $dCount = max(6, min(10, (int)($eau * 4))); ?>
        <?php for ($i = 0; $i < $dCount; $i++): ?>
        <div class="wdrop" onclick="tapWater(<?= $i ?>)">💧</div>
        <?php endfor; ?>
      </div>
      <div class="wdrop-hint">Cliquez sur chaque goutte pour noter votre consommation (<?= round($eau/$dCount,2) ?>L / unité)</div>
    </div>

    <!-- Objectifs -->
    <div class="card">
      <div class="card-hd">
        <div class="card-title"><i class="fa fa-bullseye"></i> Mes objectifs</div>
      </div>

      <div class="obj-item">
        <div class="obj-ico">🔥</div>
        <div class="obj-info">
          <div class="obj-top"><span class="obj-lbl">Calories</span><span class="obj-val"><?= $calories ?> kcal</span></div>
          <div class="obj-bar"><div class="obj-fill" style="width:68%;background:#e65100;"></div></div>
        </div>
      </div>
      <div class="obj-item">
        <div class="obj-ico">🥩</div>
        <div class="obj-info">
          <div class="obj-top"><span class="obj-lbl">Protéines</span><span class="obj-val"><?= $proteines ?>g</span></div>
          <div class="obj-bar"><div class="obj-fill" style="width:55%;background:#2e7d32;"></div></div>
        </div>
      </div>
      <div class="obj-item">
        <div class="obj-ico">🏃</div>
        <div class="obj-info">
          <div class="obj-top"><span class="obj-lbl">Activité</span><span class="obj-val">3× / sem</span></div>
          <div class="obj-bar"><div class="obj-fill" style="width:66%;background:#2e7d32;"></div></div>
        </div>
      </div>
      <div class="obj-item">
        <div class="obj-ico">💧</div>
        <div class="obj-info">
          <div class="obj-top"><span class="obj-lbl">Eau</span><span class="obj-val"><?= $eau ?>L</span></div>
          <div class="obj-bar"><div class="obj-fill" style="width:40%;background:#1565c0;" id="waterObjBar"></div></div>
        </div>
      </div>
      <div class="obj-item">
        <div class="obj-ico">😴</div>
        <div class="obj-info">
          <div class="obj-top"><span class="obj-lbl">Sommeil</span><span class="obj-val">8h</span></div>
          <div class="obj-bar"><div class="obj-fill" style="width:75%;background:#546e54;"></div></div>
        </div>
      </div>
    </div>

  </div>

  <!-- ══ Plan + Alertes ══ -->
  <div class="sec-title">Plan alimentaire & Recommandations</div>
  <div class="g21">

    <!-- Plan alimentaire -->
    <div class="card">
      <div class="card-hd">
        <div class="card-title"><i class="fa fa-clipboard-list"></i> Plan alimentaire — <?= date('d/m/Y') ?></div>
        <span class="card-tag">5 repas</span>
      </div>
      <table class="plan-table">
        <thead>
          <tr>
            <th>Repas</th>
            <th>Heure</th>
            <th>Aliments</th>
            <th style="text-align:right">Calories</th>
            <th style="text-align:right">Protéines</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $tags = ['pdj','dej','col','din','col'];
          $labels = ['Matin','Midi','Snack','Soir','Snack'];
          foreach($plan as $i => $r): ?>
          <tr>
            <td><span class="plan-tag <?= $tags[$i] ?>"><?= $labels[$i] ?></span></td>
            <td><span class="plan-time"><?= $r[1] ?></span></td>
            <td>
              <div><?= $r[0] ?></div>
              <div class="plan-desc"><?= $r[2] ?></div>
            </td>
            <td style="text-align:right"><span class="plan-cal"><?= $r[3] ?> kcal</span></td>
            <td style="text-align:right"><span class="plan-prot"><?= $r[4] ?>g</span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Alertes & Profil -->
    <div style="display:flex;flex-direction:column;gap:16px;">

      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fa fa-triangle-exclamation" style="color:#e65100;"></i> Recommandations</div>
        </div>

        <?php if ($imc >= 18.5 && $imc < 25): ?>
        <div class="alert-item">
          <div class="alert-ico g">✅</div>
          <div>
            <div class="alert-txt">IMC normal — continuez ainsi</div>
            <div class="alert-desc">Maintenez votre équilibre alimentaire actuel.</div>
          </div>
        </div>
        <?php elseif ($imc >= 25): ?>
        <div class="alert-item">
          <div class="alert-ico o">⚠️</div>
          <div>
            <div class="alert-txt">IMC <?= $imc ?> — réduire les graisses</div>
            <div class="alert-desc">Limitez les graisses saturées et les sucres rapides.</div>
          </div>
        </div>
        <?php endif; ?>

        <div class="alert-item">
          <div class="alert-ico b">💧</div>
          <div>
            <div class="alert-txt">Boire <?= $eau ?>L d'eau aujourd'hui</div>
            <div class="alert-desc">Répartissez sur la journée, 1 verre toutes les 2h.</div>
          </div>
        </div>

        <?php if (str_contains($obj, 'perte')): ?>
        <div class="alert-item">
          <div class="alert-ico o">🥗</div>
          <div>
            <div class="alert-txt">Objectif perte — éviter sucres rapides</div>
            <div class="alert-desc">Préférez les glucides complexes et les fibres.</div>
          </div>
        </div>
        <?php elseif (str_contains($obj, 'masse')): ?>
        <div class="alert-item">
          <div class="alert-ico g">💪</div>
          <div>
            <div class="alert-txt">Objectif masse — augmenter protéines</div>
            <div class="alert-desc">Consommez <?= $proteines ?>g de protéines réparties en 4–5 prises.</div>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Profil -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fa fa-user"></i> Mon profil</div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:4px;">
          <div class="imc-stat">
            <div class="imc-stat-val"><?= $user['age'] ?: '—' ?></div>
            <div class="imc-stat-lbl">Âge</div>
          </div>
          <div class="imc-stat">
            <div class="imc-stat-val"><?= $poids ? $poids.'kg' : '—' ?></div>
            <div class="imc-stat-lbl">Poids</div>
          </div>
          <div class="imc-stat">
            <div class="imc-stat-val"><?= $taille ? $taille.'cm' : '—' ?></div>
            <div class="imc-stat-lbl">Taille</div>
          </div>
          <div class="imc-stat">
            <div class="imc-stat-val" style="font-size:12px;"><?= $user['activite'] ?: '—' ?></div>
            <div class="imc-stat-lbl">Activité</div>
          </div>
        </div>

        <a href="index.php?url=User/profile" class="btn-profil">
          <i class="fa fa-pen" style="font-size:12px;"></i>
          Modifier mon profil
        </a>
      </div>

    </div>
  </div>

</div><!-- /db-wrap -->

<script>
Chart.defaults.color       = '#9e9e9e';
Chart.defaults.borderColor = '#f0f4f0';
Chart.defaults.font.family = 'Inter';

/* ── MACRO DONUT ── */
new Chart(document.getElementById('macroChart'), {
  type: 'doughnut',
  data: {
    labels: ['Protéines 25%', 'Glucides 50%', 'Lipides 25%'],
    datasets: [{
      data: [25, 50, 25],
      backgroundColor: ['#2e7d32', '#1565c0', '#e65100'],
      borderWidth: 3,
      borderColor: '#ffffff',
      hoverOffset: 6
    }]
  },
  options: {
    cutout: '65%',
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          color: '#546e54',
          padding: 14,
          usePointStyle: true,
          pointStyleWidth: 8,
          font: { size: 12, family: 'Inter' }
        }
      }
    }
  }
});

/* ── EAU TRACKER ── */
let waterCount = 0;
const dropCount  = document.querySelectorAll('.wdrop').length;
const waterObj   = <?= $eau ?>;
const perDrop    = waterObj / dropCount;

function tapWater(idx) {
  waterCount = idx + 1;
  document.querySelectorAll('.wdrop').forEach((d, i) => {
    d.classList.toggle('on', i < waterCount);
  });
  const liters = (waterCount * perDrop).toFixed(1);
  document.getElementById('waterNum').textContent  = liters + ' L';
  const pct = Math.min(100, (waterCount / dropCount) * 100);
  document.getElementById('waterFill').style.width = pct + '%';
  if (document.getElementById('waterObjBar')) {
    document.getElementById('waterObjBar').style.width = pct + '%';
  }
}

/* ── ANIMATE PROG BARS ── */
window.addEventListener('load', () => {
  document.querySelectorAll('.prog-fill, .obj-fill, .mac-mini-fill').forEach(el => {
    const w = el.style.width;
    el.style.width = '0';
    setTimeout(() => { el.style.width = w; }, 150);
  });
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>