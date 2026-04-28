<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin — EcoNutri</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<?php
require_once __DIR__ . '/../../../Config/database.php';
$db = Database::getConnection();

// ── Stats réelles ──
$totalUsers    = (int)$db->query("SELECT COUNT(*) FROM user")->fetchColumn();
$activeUsers   = (int)$db->query("SELECT COUNT(*) FROM user WHERE status='active'")->fetchColumn();
$inactiveUsers = (int)$db->query("SELECT COUNT(*) FROM user WHERE status='inactive'")->fetchColumn();
$bannedUsers   = (int)$db->query("SELECT COUNT(*) FROM user WHERE status='banned'")->fetchColumn();

$activePct = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0;

// Objectifs
$objectifsRaw = $db->query("SELECT objectif, COUNT(*) as cnt FROM user GROUP BY objectif ORDER BY cnt DESC")->fetchAll(PDO::FETCH_ASSOC);
$objLabels    = array_map(fn($r) => $r['objectif'] ?: 'Non défini', $objectifsRaw);
$objCounts    = array_column($objectifsRaw, 'cnt');

// Maladies
$maladiesRaw = $db->query("SELECT maladie, COUNT(*) as cnt FROM user WHERE maladie IS NOT NULL AND maladie!='' GROUP BY maladie ORDER BY cnt DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
$malLabels   = array_map(fn($r) => $r['maladie'], $maladiesRaw);
$malCounts   = array_column($maladiesRaw, 'cnt');

// Nouveaux inscrits par jour — 7 jours
try {
    $weekData = [];
    for ($i = 6; $i >= 0; $i--) {
        $day   = date('Y-m-d', strtotime("-$i days"));
        $label = date('D', strtotime($day));
        $cnt   = (int)$db->prepare("SELECT COUNT(*) FROM user WHERE DATE(created_at)=?")->execute([$day]) ? $db->query("SELECT COUNT(*) FROM user WHERE DATE(created_at)='$day'")->fetchColumn() : 0;
        $weekData[] = ['label' => $label, 'cnt' => (int)$cnt];
    }
} catch(Exception $e) {
    $weekData = array_map(fn($i) => ['label' => date('D', strtotime("-$i days")), 'cnt' => 0], range(6,0));
}
$weekLabels = array_column($weekData, 'label');
$weekCounts = array_column($weekData, 'cnt');

// Activité (objectif) répartition
$activiteRaw = $db->query("SELECT activite, COUNT(*) as cnt FROM user WHERE activite IS NOT NULL AND activite!='' GROUP BY activite")->fetchAll(PDO::FETCH_ASSOC);

// Derniers inscrits
try {
    $lastUsers = $db->query("SELECT nom, email, status, objectif, created_at FROM user ORDER BY id DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $lastUsers = $db->query("SELECT nom, email, status, objectif FROM user ORDER BY id DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
}

// Heure
$h = (int)date('H');
$greeting = $h < 12 ? 'Bonjour' : ($h < 18 ? 'Bon après-midi' : 'Bonsoir');
?>

<style>
/* ── BASE ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Inter', sans-serif;
  background: #f5f7f5;
  color: #1a2e1a;
  min-height: 100vh;
}

/* ── TOPBAR ── */
.adm-topbar {
  background: #ffffff;
  border-bottom: 1px solid #e0e8e0;
  height: 62px;
  padding: 0 36px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 200;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.adm-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 17px;
  font-weight: 700;
  color: #1a2e1a;
  text-decoration: none;
}

.adm-logo-leaf {
  width: 30px; height: 30px;
  background: #2e7d32;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px;
}

.adm-nav {
  display: flex;
  align-items: center;
  gap: 4px;
}

.adm-nav-link {
  display: flex; align-items: center; gap: 6px;
  padding: 7px 14px;
  border-radius: 7px;
  text-decoration: none;
  font-size: 13px; font-weight: 500;
  color: #546e54;
  transition: all .15s;
}

.adm-nav-link:hover { background: #f1f8f1; color: #1a2e1a; }
.adm-nav-link.active { background: #e8f5e9; color: #2e7d32; }
.adm-nav-link i { font-size: 13px; color: inherit; }

.adm-topbar-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

.adm-icon-btn {
  width: 36px; height: 36px;
  background: #f8fdf8;
  border: 1px solid #e0e8e0;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; position: relative;
  color: #546e54;
  transition: all .15s;
}

.adm-icon-btn:hover { border-color: #2e7d32; color: #2e7d32; }
.adm-icon-btn i { font-size: 13px; }

.notif-badge {
  position: absolute;
  top: 5px; right: 5px;
  width: 7px; height: 7px;
  border-radius: 50%;
  background: #e65100;
  border: 1.5px solid #fff;
}

.adm-admin-chip {
  display: flex; align-items: center; gap: 8px;
  padding: 5px 12px 5px 5px;
  border: 1px solid #e0e8e0;
  border-radius: 30px;
  background: #fff;
  cursor: pointer;
}

.adm-admin-av {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: #2e7d32;
  color: white;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 600;
}

.adm-admin-name { font-size: 13px; font-weight: 500; }

/* ── LAYOUT ── */
.adm-layout {
  display: flex;
  min-height: calc(100vh - 62px);
}

/* ── SIDEBAR ── */
.adm-sidebar {
  width: 220px;
  flex-shrink: 0;
  background: #ffffff;
  border-right: 1px solid #e0e8e0;
  padding: 20px 12px;
  display: flex;
  flex-direction: column;
  gap: 2px;
  position: sticky;
  top: 62px;
  height: calc(100vh - 62px);
  overflow-y: auto;
}

.adm-sec-label {
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .1em;
  color: #9e9e9e;
  padding: 12px 10px 5px;
}

.adm-side-link {
  display: flex; align-items: center; gap: 9px;
  padding: 9px 12px;
  border-radius: 8px;
  text-decoration: none;
  font-size: 13px; font-weight: 500;
  color: #546e54;
  transition: all .15s;
}

.adm-side-link i { font-size: 13px; color: #9e9e9e; }
.adm-side-link:hover { background: #f1f8f1; color: #1a2e1a; }
.adm-side-link:hover i { color: #2e7d32; }
.adm-side-link.active { background: #e8f5e9; color: #2e7d32; }
.adm-side-link.active i { color: #2e7d32; }

.adm-sidebar-bottom {
  margin-top: auto;
  padding-top: 12px;
  border-top: 1px solid #e8f0e8;
}

/* ── MAIN CONTENT ── */
.adm-main {
  flex: 1;
  padding: 28px 32px 48px;
  overflow-x: hidden;
}

/* ── PAGE HEADER ── */
.adm-page-hd {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 28px;
  padding-bottom: 20px;
  border-bottom: 1px solid #e0e8e0;
}

.adm-page-greeting {
  font-size: 11px; font-weight: 600;
  text-transform: uppercase; letter-spacing: .1em;
  color: #2e7d32; margin-bottom: 5px;
}

.adm-page-title {
  font-size: 24px; font-weight: 700;
  color: #1a2e1a; letter-spacing: -.4px;
}

.adm-page-date { font-size: 13px; color: #9e9e9e; }

/* ── SECTION TITLE ── */
.sec-hd {
  font-size: 12px; font-weight: 600;
  text-transform: uppercase; letter-spacing: .08em;
  color: #78909c;
  display: flex; align-items: center; gap: 8px;
  margin-bottom: 14px;
}

.sec-hd::after { content: ''; flex: 1; height: 1px; background: #e8f0e8; }

/* ── GRID ── */
.g5  { display: grid; grid-template-columns: repeat(5,1fr); gap: 14px; margin-bottom: 24px; }
.g4  { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 24px; }
.g3  { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 24px; }
.g2  { display: grid; grid-template-columns: 1fr 1fr;        gap: 16px; margin-bottom: 24px; }
.g21 { display: grid; grid-template-columns: 2fr 1fr;        gap: 16px; margin-bottom: 24px; }
.g12 { display: grid; grid-template-columns: 1fr 2fr;        gap: 16px; margin-bottom: 24px; }

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
  padding: 18px 20px;
  position: relative;
  overflow: hidden;
  transition: transform .2s, box-shadow .2s;
}

.kpi:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.07); }

.kpi::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0;
  height: 3px; border-radius: 12px 12px 0 0;
}

.kpi.g::before  { background: #2e7d32; }
.kpi.o::before  { background: #e65100; }
.kpi.b::before  { background: #1565c0; }
.kpi.r::before  { background: #c62828; }
.kpi.p::before  { background: #6a1b9a; }

.kpi-top {
  display: flex; justify-content: space-between;
  align-items: flex-start; margin-bottom: 10px;
}

.kpi-icon {
  width: 36px; height: 36px; border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px;
}

.kpi.g .kpi-icon { background: #e8f5e9; }
.kpi.o .kpi-icon { background: #fff3e0; }
.kpi.b .kpi-icon { background: #e3f2fd; }
.kpi.r .kpi-icon { background: #ffebee; }
.kpi.p .kpi-icon { background: #f3e5f5; }

.kpi-trend {
  font-size: 11px; font-weight: 600;
  padding: 3px 8px; border-radius: 20px;
}

.kpi.g .kpi-trend { background: #e8f5e9; color: #2e7d32; }
.kpi.o .kpi-trend { background: #fff3e0; color: #e65100; }
.kpi.b .kpi-trend { background: #e3f2fd; color: #1565c0; }
.kpi.r .kpi-trend { background: #ffebee; color: #c62828; }
.kpi.p .kpi-trend { background: #f3e5f5; color: #6a1b9a; }

.kpi-val {
  font-size: 30px; font-weight: 800;
  line-height: 1; letter-spacing: -1.5px;
  margin-bottom: 3px;
}

.kpi.g .kpi-val { color: #2e7d32; }
.kpi.o .kpi-val { color: #e65100; }
.kpi.b .kpi-val { color: #1565c0; }
.kpi.r .kpi-val { color: #c62828; }
.kpi.p .kpi-val { color: #6a1b9a; }

.kpi-lbl { font-size: 12px; color: #78909c; }
.kpi-sub { font-size: 11px; color: #bdbdbd; margin-top: 2px; }

/* ── CARD HEADER ── */
.card-hd {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 16px; padding-bottom: 12px;
  border-bottom: 1px solid #f0f4f0;
}

.card-title {
  font-size: 14px; font-weight: 600; color: #1a2e1a;
  display: flex; align-items: center; gap: 7px;
}

.card-title i { color: #2e7d32; font-size: 13px; }

.card-tag {
  font-size: 11px; font-weight: 500;
  padding: 3px 10px; border-radius: 20px;
  background: #f1f8f1; color: #2e7d32;
  border: 1px solid #c8e6c9;
}

.card-tag.o { background: #fff3e0; color: #e65100; border-color: #ffe0b2; }
.card-tag.b { background: #e3f2fd; color: #1565c0; border-color: #bbdefb; }
.card-tag.r { background: #ffebee; color: #c62828; border-color: #ffcdd2; }

/* ── USER TABLE ── */
.u-table { width: 100%; border-collapse: collapse; }

.u-table th {
  font-size: 11px; font-weight: 600; text-transform: uppercase;
  letter-spacing: .06em; color: #9e9e9e;
  text-align: left; padding: 0 12px 10px 0;
  border-bottom: 1px solid #f0f4f0;
}

.u-table td {
  padding: 11px 12px 11px 0;
  font-size: 13px;
  border-bottom: 1px solid #f8faf8;
  vertical-align: middle;
}

.u-table tr:last-child td { border-bottom: none; }
.u-table tr:hover td { background: #fafdf8; }

.u-badge-wrap {
  display: flex; align-items: center; gap: 10px;
}

.u-av {
  width: 32px; height: 32px; border-radius: 50%;
  background: #e8f5e9;
  color: #2e7d32;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700; flex-shrink: 0;
}

.u-name  { font-size: 13px; font-weight: 500; }
.u-email { font-size: 11px; color: #9e9e9e; }

.status-dot {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 12px; font-weight: 500;
  padding: 3px 10px; border-radius: 20px;
}

.status-dot.active   { background: #e8f5e9; color: #2e7d32; }
.status-dot.inactive { background: #fff3e0; color: #e65100; }
.status-dot.banned   { background: #ffebee; color: #c62828; }

.status-dot::before {
  content: '';
  width: 6px; height: 6px; border-radius: 50%;
}

.status-dot.active::before   { background: #2e7d32; }
.status-dot.inactive::before { background: #e65100; }
.status-dot.banned::before   { background: #c62828; }

/* ── PROGRESS BARS ── */
.prog-item { margin-bottom: 13px; }
.prog-item:last-child { margin-bottom: 0; }
.prog-top { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 5px; }
.prog-lbl { color: #546e54; font-weight: 500; }
.prog-val { color: #1a2e1a; font-weight: 600; }
.prog-bar { height: 6px; background: #f0f4f0; border-radius: 3px; overflow: hidden; }
.prog-fill { height: 100%; border-radius: 3px; transition: width 1.1s ease; }

/* ── ACTIVATION RING ── */
.ring-wrap { display: flex; flex-direction: column; align-items: center; padding: 8px 0 4px; }
.ring-svg  { transform: rotate(-90deg); }
.ring-bg   { fill: none; stroke: #e8f0e8; }
.ring-fill { fill: none; stroke: #2e7d32; stroke-linecap: round;
             transition: stroke-dashoffset 1.4s ease; }
.ring-center {
  text-align: center; margin-top: 8px;
}
.ring-pct { font-size: 32px; font-weight: 800; color: #1a2e1a; letter-spacing: -2px; }
.ring-sub { font-size: 12px; color: #9e9e9e; }

/* ── ALERT ITEMS ── */
.alert-item {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 12px 0; border-bottom: 1px solid #f0f4f0;
}

.alert-item:last-child { border-bottom: none; padding-bottom: 0; }

.alert-ico {
  width: 30px; height: 30px; border-radius: 7px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 13px;
}

.alert-ico.o { background: #fff3e0; }
.alert-ico.r { background: #ffebee; }
.alert-ico.g { background: #e8f5e9; }

.alert-txt  { font-size: 13px; font-weight: 500; }
.alert-desc { font-size: 12px; color: #78909c; margin-top: 1px; }

/* ── CALENDAR ── */
.fc { color: #1a2e1a !important; font-family: 'Inter', sans-serif !important; }
.fc-toolbar-title { font-size: 15px !important; font-weight: 700 !important; color: #1a2e1a !important; }
.fc-button {
  background: #f1f8f1 !important; border: 1px solid #e0e8e0 !important;
  color: #546e54 !important; border-radius: 7px !important;
  font-size: 12px !important; font-family: 'Inter', sans-serif !important;
  padding: 5px 10px !important; box-shadow: none !important;
}
.fc-button:hover, .fc-button-active {
  background: #e8f5e9 !important; color: #2e7d32 !important; border-color: #c8e6c9 !important;
}
.fc-daygrid-day-number  { color: #546e54 !important; font-size: 12px; }
.fc-col-header-cell-cushion { color: #9e9e9e !important; font-size: 11px; font-weight: 600; text-transform: uppercase; }
.fc-daygrid-day.fc-day-today { background: #f1f8f1 !important; }
.fc-event { border: none !important; border-radius: 5px !important; padding: 2px 5px !important; font-size: 11px !important; font-family: 'Inter' !important; }
.event-ai       { background: #2e7d32 !important; color: #fff !important; }
.event-calories { background: #e65100 !important; color: #fff !important; }
.event-risk     { background: #c62828 !important; color: #fff !important; }
.fc-scrollgrid, .fc-scrollgrid-section > td { border-color: #f0f4f0 !important; }
.fc-daygrid-body, .fc-scrollgrid-sync-table  { border-color: #f0f4f0 !important; }

/* ── SEARCH BAR ── */
.adm-search-wrap {
  position: relative;
  flex: 1; max-width: 320px;
}

.adm-search-wrap i {
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  color: #9e9e9e; font-size: 13px;
}

.adm-search {
  width: 100%;
  padding: 9px 12px 9px 36px;
  border: 1px solid #e0e8e0;
  border-radius: 8px;
  background: #fff;
  font-family: 'Inter', sans-serif;
  font-size: 13px; color: #1a2e1a;
  outline: none; transition: border-color .15s;
}

.adm-search:focus { border-color: #2e7d32; }

/* ── BOUTONS ── */
.btn-g {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 9px 18px;
  background: #2e7d32; color: white;
  border: none; border-radius: 8px;
  font-size: 13px; font-weight: 500; font-family: 'Inter', sans-serif;
  cursor: pointer; text-decoration: none;
  transition: background .15s;
}

.btn-g:hover { background: #1b5e20; color: white; }

.btn-outline {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 9px 16px;
  background: transparent; color: #546e54;
  border: 1px solid #e0e8e0; border-radius: 8px;
  font-size: 13px; font-weight: 500; font-family: 'Inter', sans-serif;
  cursor: pointer; text-decoration: none;
  transition: all .15s;
}

.btn-outline:hover { border-color: #2e7d32; color: #2e7d32; background: #f1f8f1; }

/* ── RESPONSIVE ── */
@media (max-width: 1200px) {
  .g5 { grid-template-columns: repeat(3,1fr); }
  .g3 { grid-template-columns: repeat(2,1fr); }
  .g21, .g12 { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
  .adm-sidebar { display: none; }
  .adm-main { padding: 20px 16px 40px; }
  .g5, .g4, .g3, .g2, .g21, .g12 { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- TOPBAR -->
<div class="adm-topbar">
  <a href="#" class="adm-logo">
    <div class="adm-logo-leaf">🌿</div>
    EcoNutri Admin
  </a>

  <nav class="adm-nav">
    <a href="index.php?url=Admin/dashboard" class="adm-nav-link active">
      <i class="fa fa-gauge"></i> Dashboard
    </a>
    <a href="index.php?url=Admin/users" class="adm-nav-link">
      <i class="fa fa-users"></i> Utilisateurs
    </a>
    <a href="#" class="adm-nav-link"><i class="fa fa-leaf"></i> Recettes</a>
    <a href="#" class="adm-nav-link"><i class="fa fa-chart-bar"></i> Rapports</a>
  </nav>

  <div class="adm-topbar-right">
    <div class="adm-icon-btn">
      <i class="fa fa-bell"></i>
      <?php if ($inactiveUsers > 0): ?>
      <div class="notif-badge"></div>
      <?php endif; ?>
    </div>
    <div class="adm-icon-btn"><i class="fa fa-gear"></i></div>
    <div class="adm-admin-chip">
      <div class="adm-admin-av">A</div>
      <div class="adm-admin-name">Admin</div>
    </div>
  </div>
</div>

<!-- LAYOUT -->
<div class="adm-layout">

  <!-- SIDEBAR -->
  <aside class="adm-sidebar">
    <div class="adm-sec-label">Principal</div>
    <a href="index.php?url=Admin/dashboard" class="adm-side-link active"><i class="fa fa-gauge"></i>Dashboard</a>
    <a href="index.php?url=Admin/users"     class="adm-side-link"><i class="fa fa-users"></i>Utilisateurs</a>

    <div class="adm-sec-label">Contenu</div>
    <a href="#" class="adm-side-link"><i class="fa fa-bowl-food"></i>Recettes</a>
    <a href="#" class="adm-side-link"><i class="fa fa-chart-pie"></i>Nutrition</a>
    <a href="#" class="adm-side-link"><i class="fa fa-calendar"></i>Calendrier</a>

    <div class="adm-sec-label">Système</div>
    <a href="#" class="adm-side-link"><i class="fa fa-bell"></i>
      Alertes
      <?php if ($inactiveUsers > 0): ?>
      <span style="margin-left:auto;background:#fff3e0;color:#e65100;font-size:10px;font-weight:600;padding:2px 7px;border-radius:20px;"><?= $inactiveUsers ?></span>
      <?php endif; ?>
    </a>
    <a href="#" class="adm-side-link"><i class="fa fa-gear"></i>Paramètres</a>

    <div class="adm-sidebar-bottom">
      <a href="index.php?url=User/logout" class="adm-side-link" style="color:#c62828;">
        <i class="fa fa-right-from-bracket" style="color:#c62828;"></i>Déconnexion
      </a>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="adm-main">

    <!-- Page Header -->
    <div class="adm-page-hd">
      <div>
        <div class="adm-page-greeting"><?= $greeting ?> — Tableau de bord</div>
        <div class="adm-page-title">Vue d'ensemble</div>
      </div>
      <div style="display:flex;align-items:center;gap:10px;">
        <div class="adm-search-wrap">
          <i class="fa fa-magnifying-glass"></i>
          <input type="text" class="adm-search" placeholder="Rechercher un utilisateur...">
        </div>
        <a href="index.php?url=Admin/users" class="btn-g">
          <i class="fa fa-plus" style="font-size:11px;"></i> Ajouter
        </a>
      </div>
    </div>

    <!-- ══ KPI ══ -->
    <div class="sec-hd">Indicateurs en temps réel</div>
    <div class="g5">

      <div class="kpi g">
        <div class="kpi-top">
          <div class="kpi-icon">👥</div>
          <span class="kpi-trend">total</span>
        </div>
        <div class="kpi-val"><?= number_format($totalUsers) ?></div>
        <div class="kpi-lbl">Utilisateurs</div>
        <div class="kpi-sub">inscrits</div>
      </div>

      <div class="kpi b">
        <div class="kpi-top">
          <div class="kpi-icon">✅</div>
          <span class="kpi-trend"><?= $activePct ?>%</span>
        </div>
        <div class="kpi-val"><?= $activeUsers ?></div>
        <div class="kpi-lbl">Comptes actifs</div>
        <div class="kpi-sub">sur <?= $totalUsers ?></div>
      </div>

      <div class="kpi o">
        <div class="kpi-top">
          <div class="kpi-icon">⏳</div>
          <span class="kpi-trend">en attente</span>
        </div>
        <div class="kpi-val"><?= $inactiveUsers ?></div>
        <div class="kpi-lbl">Inactifs</div>
        <div class="kpi-sub">à activer</div>
      </div>

      <div class="kpi r">
        <div class="kpi-top">
          <div class="kpi-icon">🚫</div>
          <span class="kpi-trend">bannis</span>
        </div>
        <div class="kpi-val"><?= $bannedUsers ?></div>
        <div class="kpi-lbl">Bannis</div>
        <div class="kpi-sub">comptes bloqués</div>
      </div>

      <div class="kpi p">
        <div class="kpi-top">
          <div class="kpi-icon">📊</div>
          <span class="kpi-trend">taux</span>
        </div>
        <div class="kpi-val"><?= $activePct ?>%</div>
        <div class="kpi-lbl">Taux activation</div>
        <div class="kpi-sub">objectif 90%</div>
      </div>

    </div>

    <!-- ══ GRAPHES ══ -->
    <div class="sec-hd">Analyses & Graphiques</div>
    <div class="g3">

      <!-- Inscriptions semaine -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fa fa-chart-line"></i> Inscriptions — 7 jours</div>
          <span class="card-tag">Semaine</span>
        </div>
        <canvas id="chartWeek" height="140"></canvas>
      </div>

      <!-- Objectifs répartition -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fa fa-chart-pie"></i> Objectifs nutritionnels</div>
          <span class="card-tag"><?= $totalUsers ?> users</span>
        </div>
        <canvas id="chartObj" height="140"></canvas>
      </div>

      <!-- Maladies -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fa fa-stethoscope"></i> Maladies déclarées</div>
          <span class="card-tag o">Santé</span>
        </div>
        <canvas id="chartMal" height="140"></canvas>
      </div>

    </div>

    <!-- ══ TAUX ACTIVATION + OBJECTIFS BARRES + CALENDRIER ══ -->
    <div class="g3">

      <!-- Taux activation ring -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fa fa-circle-check"></i> Taux d'activation</div>
        </div>
        <div class="ring-wrap">
          <svg class="ring-svg" width="120" height="120" viewBox="0 0 120 120">
            <circle class="ring-bg"   cx="60" cy="60" r="48" stroke-width="9"/>
            <circle class="ring-fill" cx="60" cy="60" r="48" stroke-width="9"
                    stroke-dasharray="301.59"
                    stroke-dashoffset="<?= 301.59 * (1 - $activePct/100) ?>"/>
          </svg>
          <div class="ring-center">
            <div class="ring-pct"><?= $activePct ?>%</div>
            <div class="ring-sub"><?= $activeUsers ?> / <?= $totalUsers ?> actifs</div>
          </div>
        </div>
        <div style="margin-top:14px;">
          <div class="prog-item">
            <div class="prog-top"><span class="prog-lbl">Actifs</span><span class="prog-val"><?= $activeUsers ?></span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:<?= $activePct ?>%;background:#2e7d32;"></div></div>
          </div>
          <div class="prog-item">
            <div class="prog-top"><span class="prog-lbl">Inactifs</span><span class="prog-val"><?= $inactiveUsers ?></span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:<?= $totalUsers>0?round($inactiveUsers/$totalUsers*100):0 ?>%;background:#e65100;"></div></div>
          </div>
          <div class="prog-item">
            <div class="prog-top"><span class="prog-lbl">Bannis</span><span class="prog-val"><?= $bannedUsers ?></span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:<?= $totalUsers>0?round($bannedUsers/$totalUsers*100):0 ?>%;background:#c62828;"></div></div>
          </div>
        </div>
      </div>

      <!-- Objectifs barres -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fa fa-bullseye"></i> Répartition objectifs</div>
        </div>
        <?php
        $colorsObj = ['#2e7d32','#1565c0','#e65100','#6a1b9a','#c62828'];
        foreach($objectifsRaw as $i => $obj):
          $pct = $totalUsers > 0 ? round($obj['cnt']/$totalUsers*100) : 0;
          $col = $colorsObj[$i % count($colorsObj)];
        ?>
        <div class="prog-item">
          <div class="prog-top">
            <span class="prog-lbl"><?= htmlspecialchars($obj['objectif'] ?: 'Non défini') ?></span>
            <span class="prog-val" style="color:<?= $col ?>"><?= $obj['cnt'] ?> <small style="color:#9e9e9e;font-weight:400;">(<?= $pct ?>%)</small></span>
          </div>
          <div class="prog-bar">
            <div class="prog-fill" style="width:<?= $pct ?>%;background:<?= $col ?>;"></div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($objectifsRaw)): ?>
        <p style="font-size:13px;color:#9e9e9e;text-align:center;padding:20px 0;">Aucune donnée</p>
        <?php endif; ?>
      </div>

      <!-- Alertes système -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fa fa-triangle-exclamation" style="color:#e65100;"></i> Alertes système</div>
          <span class="card-tag o"><?= $inactiveUsers + $bannedUsers ?></span>
        </div>

        <div class="alert-item">
          <div class="alert-ico g">✅</div>
          <div>
            <div class="alert-txt">Système opérationnel</div>
            <div class="alert-desc">Base de données — connexion active</div>
          </div>
        </div>

        <?php if ($inactiveUsers > 0): ?>
        <div class="alert-item">
          <div class="alert-ico o">⏳</div>
          <div>
            <div class="alert-txt"><?= $inactiveUsers ?> compte(s) en attente d'activation</div>
            <div class="alert-desc">Vérifier les emails de confirmation</div>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($bannedUsers > 0): ?>
        <div class="alert-item">
          <div class="alert-ico r">🚫</div>
          <div>
            <div class="alert-txt"><?= $bannedUsers ?> compte(s) banni(s)</div>
            <div class="alert-desc">Comptes bloqués manuellement</div>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($totalUsers > 0 && $activePct < 60): ?>
        <div class="alert-item">
          <div class="alert-ico o">📊</div>
          <div>
            <div class="alert-txt">Taux d'activation faible : <?= $activePct ?>%</div>
            <div class="alert-desc">Objectif recommandé : 80%+</div>
          </div>
        </div>
        <?php endif; ?>

        <div style="margin-top:12px;">
          <a href="index.php?url=Admin/users" class="btn-g" style="width:100%;justify-content:center;">
            <i class="fa fa-users" style="font-size:12px;"></i> Gérer les utilisateurs
          </a>
        </div>
      </div>

    </div>

    <!-- ══ DERNIERS INSCRITS + CALENDRIER ══ -->
    <div class="sec-hd">Dernières activités</div>
    <div class="g21">

      <!-- Table derniers inscrits -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fa fa-user-plus"></i> Derniers inscrits</div>
          <a href="index.php?url=Admin/users" class="btn-outline" style="padding:5px 12px;font-size:12px;">Voir tout →</a>
        </div>
        <table class="u-table">
          <thead>
            <tr>
              <th>Utilisateur</th>
              <th>Objectif</th>
              <th>Statut</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($lastUsers as $u):
              $init   = strtoupper(substr($u['nom'] ?? '?', 0, 1));
              $status = $u['status'] ?? 'inactive';
              $statusLabel = ['active' => 'Actif', 'inactive' => 'Inactif', 'banned' => 'Banni'][$status] ?? $status;
            ?>
            <tr>
              <td>
                <div class="u-badge-wrap">
                  <div class="u-av"><?= $init ?></div>
                  <div>
                    <div class="u-name"><?= htmlspecialchars($u['nom']) ?></div>
                    <div class="u-email"><?= htmlspecialchars($u['email']) ?></div>
                  </div>
                </div>
              </td>
              <td style="font-size:12px;color:#546e54;"><?= htmlspecialchars($u['objectif'] ?? '—') ?></td>
              <td><span class="status-dot <?= $status ?>"><?= $statusLabel ?></span></td>
              <td>
                <a href="index.php?url=Admin/users" class="btn-outline" style="padding:4px 10px;font-size:11px;">
                  <i class="fa fa-pen" style="font-size:10px;"></i> Éditer
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($lastUsers)): ?>
            <tr><td colspan="4" style="text-align:center;color:#9e9e9e;padding:20px 0;font-size:13px;">Aucun utilisateur</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Calendrier -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fa fa-calendar"></i> Calendrier</div>
        </div>
        <div id="calendar"></div>
      </div>

    </div>

  </main>
</div><!-- /layout -->

<script>
Chart.defaults.color       = '#9e9e9e';
Chart.defaults.borderColor = '#f0f4f0';
Chart.defaults.font.family = 'Inter';
Chart.defaults.font.size   = 12;

/* ── INSCRIPTIONS SEMAINE ── */
new Chart(document.getElementById('chartWeek'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($weekLabels) ?>,
    datasets: [{
      label: 'Inscriptions',
      data: <?= json_encode($weekCounts) ?>,
      backgroundColor: '#e8f5e9',
      borderColor: '#2e7d32',
      borderWidth: 1.5,
      borderRadius: 6,
      borderSkipped: false,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false } },
      y: { grid: { color: '#f5f5f5' }, beginAtZero: true, ticks: { stepSize: 1 } }
    }
  }
});

/* ── OBJECTIFS DONUT ── */
new Chart(document.getElementById('chartObj'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_map(fn($l) => $l ?: 'Non défini', $objLabels)) ?>,
    datasets: [{
      data: <?= json_encode($objCounts) ?>,
      backgroundColor: ['#2e7d32','#1565c0','#e65100','#6a1b9a','#c62828'],
      borderWidth: 3,
      borderColor: '#ffffff',
      hoverOffset: 6
    }]
  },
  options: {
    cutout: '62%',
    plugins: {
      legend: {
        position: 'bottom',
        labels: { color: '#546e54', padding: 12, usePointStyle: true, pointStyleWidth: 8 }
      }
    }
  }
});

/* ── MALADIES BARRE HORIZONTALE ── */
new Chart(document.getElementById('chartMal'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(count($malLabels) > 0 ? $malLabels : ['Aucune donnée']) ?>,
    datasets: [{
      data: <?= json_encode(count($malCounts) > 0 ? $malCounts : [0]) ?>,
      backgroundColor: ['#e65100','#c62828','#6a1b9a','#1565c0','#2e7d32','#78909c'],
      borderRadius: 5, borderSkipped: false,
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: '#f5f5f5' }, beginAtZero: true, ticks: { stepSize: 1 } },
      y: { grid: { display: false } }
    }
  }
});

/* ── FULLCALENDAR ── */
document.addEventListener('DOMContentLoaded', () => {
  new FullCalendar.Calendar(document.getElementById('calendar'), {
    initialView: 'dayGridMonth',
    height: 340,
    headerToolbar: { left: 'prev,next', center: 'title', right: 'today' },
    events: [
      { title: 'Audit utilisateurs',  date: '<?= date("Y-m-d") ?>',                   className: 'event-ai' },
      { title: 'Maintenance système', date: '<?= date("Y-m-d", strtotime("+4 days")) ?>', className: 'event-ai' },
      { title: 'Rapport mensuel',     date: '<?= date("Y-m-d", strtotime("+7 days")) ?>', className: 'event-calories' },
      { title: 'Vérif. inactifs',     date: '<?= date("Y-m-d", strtotime("+2 days")) ?>', className: 'event-risk' },
    ],
    eventClick: (info) => alert('📅 ' + info.event.title + '\n' + info.event.startStr)
  }).render();
});

/* ── ANIMATE PROG BARS ── */
window.addEventListener('load', () => {
  document.querySelectorAll('.prog-fill').forEach(el => {
    const w = el.style.width;
    el.style.width = '0';
    setTimeout(() => { el.style.width = w; }, 100);
  });
});
</script>

</body>
</html>