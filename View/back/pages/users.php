```html
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin Users - EcoNutri</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ===== BASE ===== */
*, *::before, *::after { box-sizing: border-box; }

body {
  margin: 0;
  font-family: 'DM Sans', sans-serif;
  background: radial-gradient(circle at top, #0b1220, #020617);
  color: white;
  display: flex;
  min-height: 100vh;
}

.content-area {
  margin-left: 220px;
  flex: 1;
  padding: 28px 32px;
}

/* ===== PAGE HEADER ===== */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}

.page-title {
  font-size: 26px;
  font-weight: 700;
  color: #00e676;
  text-shadow: 0 0 18px rgba(0,230,118,0.25);
  letter-spacing: 0.5px;
}

/* ===== STAT CARDS ===== */
.stat-cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-bottom: 24px;
}

.stat-card {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 16px;
  padding: 18px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  transition: transform .2s, box-shadow .2s;
  position: relative;
  overflow: hidden;
}

.stat-card::after {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  border-radius: 16px 16px 0 0;
}

.stat-card.green::after  { background: linear-gradient(90deg, #00e676, #00c853); }
.stat-card.blue::after   { background: linear-gradient(90deg, #2196f3, #1565c0); }
.stat-card.orange::after { background: linear-gradient(90deg, #ff9800, #e65100); }
.stat-card.red::after    { background: linear-gradient(90deg, #ef5350, #b71c1c); }

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 28px rgba(0,0,0,0.4);
}

.stat-icon {
  width: 44px; height: 44px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.stat-card.green  .stat-icon { background: rgba(0,230,118,0.15); }
.stat-card.blue   .stat-icon { background: rgba(33,150,243,0.15); }
.stat-card.orange .stat-icon { background: rgba(255,152,0,0.15); }
.stat-card.red    .stat-icon { background: rgba(239,83,80,0.15); }

.stat-val  { font-size: 26px; font-weight: 700; line-height: 1; }
.stat-lbl  { font-size: 12px; color: rgba(255,255,255,0.45); margin-top: 3px; }

/* ===== TOOLBAR ===== */
.toolbar {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 18px;
  flex-wrap: wrap;
}

.search-wrap {
  position: relative;
  flex: 1;
  min-width: 200px;
}

.search-wrap i {
  position: absolute;
  left: 14px; top: 50%;
  transform: translateY(-50%);
  color: rgba(255,255,255,0.3);
  font-size: 14px;
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 10px 14px 10px 38px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px;
  color: white;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  outline: none;
  transition: border-color .2s, box-shadow .2s;
}

.search-input:focus {
  border-color: #00e676;
  box-shadow: 0 0 0 3px rgba(0,230,118,0.12);
}

.filter-select {
  padding: 10px 14px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px;
  color: rgba(255,255,255,0.8);
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  outline: none;
  cursor: pointer;
  transition: border-color .2s;
  appearance: none;
  padding-right: 32px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='rgba(255,255,255,0.3)'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
}

.filter-select:focus { border-color: #00e676; }
.filter-select option { background: #0b1220; }

/* VOICE BUTTON */
.voice-btn {
  padding: 10px 18px;
  border-radius: 12px;
  border: 1px solid rgba(239,83,80,0.3);
  background: rgba(239,83,80,0.08);
  color: #ef9a9a;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all .25s;
  white-space: nowrap;
  position: relative;
}

.voice-btn:hover {
  border-color: #ef5350;
  background: rgba(239,83,80,0.15);
  color: #fff;
}

.voice-btn.listening {
  border-color: #ef5350;
  background: rgba(239,83,80,0.25);
  color: #fff;
  animation: voicePulse 1.5s ease-in-out infinite;
  box-shadow: 0 0 20px rgba(239,83,80,0.3);
}

@keyframes voicePulse {
  0%, 100% { box-shadow: 0 0 20px rgba(239,83,80,0.3); }
  50% { box-shadow: 0 0 35px rgba(239,83,80,0.5); }
}

.voice-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #ef5350;
  display: none;
}

.voice-btn.listening .voice-dot {
  display: block;
  animation: dotBlink 0.8s ease-in-out infinite;
}

@keyframes dotBlink {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.3; transform: scale(0.7); }
}

.btn-add {
  padding: 10px 20px;
  background: linear-gradient(90deg, #00e676, #00c853);
  border: none;
  border-radius: 12px;
  color: #000;
  font-weight: 700;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  cursor: pointer;
  transition: transform .2s, box-shadow .2s;
  white-space: nowrap;
}

.btn-add:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,230,118,0.35);
}

/* ===== RESULT INFO ===== */
.result-info {
  font-size: 13px;
  color: rgba(255,255,255,0.4);
  margin-bottom: 12px;
}

.result-info strong { color: #00e676; }

/* ===== TABLE ===== */
.table-wrap {
  background: rgba(255,255,255,0.025);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 18px;
  overflow: hidden;
}

#usersTable {
  width: 100%;
  border-collapse: collapse;
}

#usersTable thead tr {
  background: rgba(0,230,118,0.08);
  border-bottom: 1px solid rgba(0,230,118,0.15);
}

#usersTable th {
  padding: 13px 16px;
  font-size: 12px;
  font-weight: 600;
  color: rgba(0,230,118,0.8);
  text-transform: uppercase;
  letter-spacing: .06em;
  white-space: nowrap;
}

#usersTable td {
  padding: 12px 16px;
  font-size: 14px;
  border-bottom: 1px solid rgba(255,255,255,0.04);
  vertical-align: middle;
}

#usersTable tbody tr {
  transition: background .15s;
}

#usersTable tbody tr:hover {
  background: rgba(0,230,118,0.04);
}

#usersTable tbody tr:last-child td {
  border-bottom: none;
}

/* USER BADGE */
.user-badge {
  display: flex;
  align-items: center;
  gap: 10px;
}

.avatar {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, #00e676, #00bcd4);
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 14px;
  color: #000;
  flex-shrink: 0;
}

.user-name  { font-weight: 500; font-size: 14px; }
.user-email { font-size: 12px; color: rgba(255,255,255,0.4); }

/* STATUS BADGE */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: opacity .2s, transform .3s, background .3s, color .3s;
  user-select: none;
}

.status-badge:hover { opacity: .8; }

.status-badge .dot {
  width: 6px; height: 6px;
  border-radius: 50%;
}

.status-badge.active   { background: rgba(0,230,118,0.15); color: #00e676; }
.status-badge.active .dot { background: #00e676; box-shadow: 0 0 6px #00e676; }

.status-badge.inactive { background: rgba(255,152,0,0.15); color: #ffa726; }
.status-badge.inactive .dot { background: #ffa726; }

.status-badge.banned   { background: rgba(239,83,80,0.15); color: #ef5350; }
.status-badge.banned .dot { background: #ef5350; }

/* Animation on status change */
.status-badge.just-changed {
  animation: statusFlash 0.8s ease;
  transform: scale(1.15);
}

@keyframes statusFlash {
  0%   { transform: scale(1); opacity: 1; }
  25%  { transform: scale(1.2); opacity: 0.6; }
  50%  { transform: scale(0.95); opacity: 1; }
  75%  { transform: scale(1.05); }
  100% { transform: scale(1); opacity: 1; }
}

/* Row highlight on change */
tr.just-updated {
  animation: rowHighlight 1.5s ease;
}

@keyframes rowHighlight {
  0%   { background: rgba(255,152,0,0.15); }
  100% { background: transparent; }
}

tr.just-banned {
  animation: rowBanned 1.5s ease;
}

@keyframes rowBanned {
  0%   { background: rgba(239,83,80,0.2); }
  100% { background: transparent; }
}

/* ACTION BUTTONS */
.btn-action {
  border: none;
  padding: 6px 10px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13px;
  transition: transform .15s, box-shadow .15s;
  margin: 0 2px;
}

.btn-action:hover { transform: scale(1.1); }

.btn-view   { background: rgba(33,150,243,0.2);  color: #64b5f6; }
.btn-edit   { background: rgba(255,193,7,0.2);   color: #ffd54f; }
.btn-delete { background: rgba(239,83,80,0.2);   color: #ef9a9a; }

/* ===== LOADING SPINNER ===== */
.spinner {
  display: none;
  text-align: center;
  padding: 40px;
  color: rgba(255,255,255,0.3);
  font-size: 13px;
}

.spinner i { display: block; font-size: 28px; margin-bottom: 10px; color: #00e676; animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ===== EMPTY STATE ===== */
.empty-state {
  display: none;
  text-align: center;
  padding: 50px 20px;
  color: rgba(255,255,255,0.3);
}

.empty-state i { font-size: 36px; display: block; margin-bottom: 10px; color: rgba(255,255,255,0.15); }

/* ===== PAGINATION ===== */
.pagination-wrap {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-top: 1px solid rgba(255,255,255,0.06);
  margin-top: 0;
}

.pagination {
  display: flex;
  gap: 6px;
}

.page-btn {
  width: 34px; height: 34px;
  border-radius: 8px;
  border: 1px solid rgba(255,255,255,0.1);
  background: rgba(255,255,255,0.04);
  color: rgba(255,255,255,0.6);
  font-size: 13px;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all .2s;
  font-family: 'DM Sans', sans-serif;
}

.page-btn:hover:not(:disabled) {
  border-color: #00e676;
  color: #00e676;
}

.page-btn.active {
  background: #00e676;
  color: #000;
  border-color: #00e676;
  font-weight: 700;
}

.page-btn:disabled {
  opacity: .3;
  cursor: not-allowed;
}

.page-info {
  font-size: 13px;
  color: rgba(255,255,255,0.35);
}

/* ===== VOICE STATUS BAR ===== */
.voice-status-bar {
  display: none;
  align-items: center;
  gap: 10px;
  padding: 10px 16px;
  margin-bottom: 14px;
  border-radius: 12px;
  font-size: 13px;
  animation: fadeIn .3s ease;
}

.voice-status-bar.active {
  display: flex;
}

.voice-status-bar.listening {
  background: rgba(239,83,80,0.1);
  border: 1px solid rgba(239,83,80,0.25);
  color: #ef9a9a;
}

.voice-status-bar.success {
  background: rgba(0,230,118,0.1);
  border: 1px solid rgba(0,230,118,0.25);
  color: #a5d6a7;
}

.voice-status-bar.error {
  background: rgba(255,152,0,0.1);
  border: 1px solid rgba(255,152,0,0.25);
  color: #ffa726;
}

@keyframes fadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

/* ===== MODAL ADD ===== */
.modal-overlay {
  display: none;
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.8);
  backdrop-filter: blur(6px);
  z-index: 9999;
  align-items: center;
  justify-content: center;
}

.modal-overlay.open { display: flex; }

.modal-box {
  background: linear-gradient(135deg, #0b1220, #0d1f12);
  border: 1px solid rgba(0,230,118,0.2);
  border-radius: 20px;
  padding: 32px 36px;
  width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 0 50px rgba(0,230,118,0.15);
  animation: modalIn .25s ease;
}

@keyframes modalIn {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

.modal-box h3 {
  color: #00e676;
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 6px;
}

.modal-box .modal-sub {
  font-size: 13px;
  color: rgba(255,255,255,0.35);
  margin-bottom: 22px;
}

.modal-field {
  margin-bottom: 14px;
}

.modal-field label {
  display: block;
  font-size: 12px;
  color: rgba(255,255,255,0.4);
  text-transform: uppercase;
  letter-spacing: .06em;
  margin-bottom: 6px;
}

.modal-field input,
.modal-field select {
  width: 100%;
  padding: 11px 14px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 10px;
  color: white;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  outline: none;
  transition: border-color .2s, box-shadow .2s;
}

.modal-field input:focus,
.modal-field select:focus {
  border-color: #00e676;
  box-shadow: 0 0 0 3px rgba(0,230,118,0.12);
}

/* Validation JS inline */
.modal-field input.v-ok,
.modal-field select.v-ok {
  border-color: #00e676 !important;
  box-shadow: 0 0 0 3px rgba(0,230,118,0.12) !important;
}

.modal-field input.v-err,
.modal-field select.v-err {
  border-color: #ef5350 !important;
  box-shadow: 0 0 0 3px rgba(239,83,80,0.12) !important;
}

.v-msg {
  font-size: 11px;
  color: #ef9a9a;
  margin-top: 4px;
  display: none;
}

.modal-field input::placeholder { color: rgba(255,255,255,0.25); }
.modal-field select option       { background: #0b1220; }

.modal-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.btn-save {
  width: 100%;
  padding: 13px;
  background: linear-gradient(90deg, #00e676, #00c853);
  border: none;
  border-radius: 12px;
  color: #000;
  font-weight: 700;
  font-family: 'DM Sans', sans-serif;
  font-size: 15px;
  cursor: pointer;
  margin-top: 8px;
  transition: transform .2s;
}

.btn-save:hover { transform: translateY(-2px); }

.btn-cancel {
  width: 100%;
  padding: 11px;
  background: transparent;
  border: 1px solid rgba(239,83,80,0.3);
  border-radius: 12px;
  color: #ef9a9a;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  cursor: pointer;
  margin-top: 8px;
  transition: background .2s;
}

.btn-cancel:hover { background: rgba(239,83,80,0.08); }

/* ===== ALERT FLASH ===== */
.flash {
  padding: 12px 18px;
  border-radius: 12px;
  font-size: 14px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 8px;
  animation: fadeIn .3s ease;
}

@keyframes fadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; } }

.flash.success { background: rgba(0,230,118,0.12); border: 1px solid rgba(0,230,118,0.25); color: #a5d6a7; }
.flash.error   { background: rgba(239,83,80,0.12); border: 1px solid rgba(239,83,80,0.25); color: #ef9a9a; }
</style>
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-area">

  <!-- FLASH MESSAGES -->
  <?php if (!empty($_SESSION['admin_success'])): ?>
    <div class="flash success">✓ <?= htmlspecialchars($_SESSION['admin_success']) ?></div>
    <?php unset($_SESSION['admin_success']); ?>
  <?php endif; ?>
  <?php if (!empty($_SESSION['admin_error'])): ?>
    <div class="flash error">⚠ <?= htmlspecialchars($_SESSION['admin_error']) ?></div>
    <?php unset($_SESSION['admin_error']); ?>
  <?php endif; ?>

  <!-- HEADER -->
  <div class="page-header">
    <div class="page-title">👥 Gestion des utilisateurs</div>
    <button class="btn-add" onclick="openModal('add')">
      <i class="fa fa-plus" style="margin-right:6px;filter:none;color:inherit;"></i>Ajouter
    </button>
  </div>

  <!-- STAT CARDS -->
  <div class="stat-cards">
    <div class="stat-card green">
      <div class="stat-icon">👥</div>
      <div>
        <div class="stat-val"><?= $stats['total'] ?></div>
        <div class="stat-lbl">Total utilisateurs</div>
      </div>
    </div>
    <div class="stat-card blue">
      <div class="stat-icon">✅</div>
      <div>
        <div class="stat-val"><?= $stats['active'] ?></div>
        <div class="stat-lbl">Comptes actifs</div>
      </div>
    </div>
    <div class="stat-card orange">
      <div class="stat-icon">⏳</div>
      <div>
        <div class="stat-val"><?= $stats['inactive'] ?></div>
        <div class="stat-lbl">Inactifs</div>
      </div>
    </div>
    <div class="stat-card red">
      <div class="stat-icon">🚫</div>
      <div>
        <div class="stat-val"><?= $stats['banned'] ?></div>
        <div class="stat-lbl">Bannis</div>
      </div>
    </div>
  </div>

  <!-- TOOLBAR : SEARCH + FILTERS + VOICE -->
  <div class="toolbar">
    <div class="search-wrap">
      <i class="fa fa-search"></i>
      <input type="text" class="search-input" id="searchInput"
             placeholder="Rechercher par nom ou email..."
             value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    </div>

    <select class="filter-select" id="filterStatus">
      <option value="">Tous les statuts</option>
      <option value="active"   <?= ($_GET['status']   ?? '') === 'active'   ? 'selected' : '' ?>>✅ Actif</option>
      <option value="inactive" <?= ($_GET['status']   ?? '') === 'inactive' ? 'selected' : '' ?>>⏳ Inactif</option>
      <option value="banned"   <?= ($_GET['status']   ?? '') === 'banned'   ? 'selected' : '' ?>>🚫 Banni</option>
    </select>

    <select class="filter-select" id="filterObjectif">
      <option value="">Tous les objectifs</option>
      <option value="Perte de poids"      <?= ($_GET['objectif'] ?? '') === 'Perte de poids'      ? 'selected' : '' ?>>Perte de poids</option>
      <option value="Prise de masse"      <?= ($_GET['objectif'] ?? '') === 'Prise de masse'      ? 'selected' : '' ?>>Prise de masse</option>
      <option value="Équilibre alimentaire" <?= ($_GET['objectif'] ?? '') === 'Équilibre alimentaire' ? 'selected' : '' ?>>Équilibre</option>
      <option value="Végétarien"          <?= ($_GET['objectif'] ?? '') === 'Végétarien'          ? 'selected' : '' ?>>Végétarien</option>
    </select>

    <button class="voice-btn" id="voiceBtn" onclick="toggleVoice()">
      <div class="voice-dot"></div>
      <i class="fa fa-microphone"></i>
      <span id="voiceLabel">Voix IA</span>
    </button>
  </div>

  <!-- VOICE STATUS BAR -->
  <div class="voice-status-bar" id="voiceStatusBar">
    <i class="fa fa-microphone"></i>
    <span id="voiceStatusText">En écoute... Dites "banned [nom]" ou "unbanned [nom]"</span>
  </div>

  <!-- RESULT INFO -->
  <div class="result-info" id="resultInfo">
    <strong id="resultCount"><?= $total ?></strong> utilisateur(s) trouvé(s)
  </div>

  <!-- TABLE -->
  <div class="table-wrap">
    <div class="spinner" id="spinner">
      <i class="fa fa-circle-notch"></i>Chargement...
    </div>

    <table id="usersTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Utilisateur</th>
          <th>Objectif</th>
          <th>Poids</th>
          <th>Taille</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="usersBody">
        <?php foreach ($users as $u):
          $initials  = strtoupper(substr($u['nom'] ?? '?', 0, 1));
          $statusMap = [
            'active'   => ['label' => 'Actif',   'cls' => 'active'],
            'inactive' => ['label' => 'Inactif', 'cls' => 'inactive'],
            'banned'   => ['label' => 'Banni',   'cls' => 'banned'],
          ];
          $s = $statusMap[$u['status']] ?? ['label' => $u['status'], 'cls' => 'inactive'];
        ?>
          <tr id="user-row-<?= $u['id'] ?>">
            <td style="color:rgba(255,255,255,0.3);font-size:12px;"><?= $u['id'] ?></td>
            <td>
              <div class="user-badge">
                <div class="avatar"><?= $initials ?></div>
                <div>
                  <div class="user-name" id="user-name-<?= $u['id'] ?>"><?= htmlspecialchars($u['nom']) ?></div>
                  <div class="user-email"><?= htmlspecialchars($u['email']) ?></div>
                </div>
              </div>
            </td>
            <td style="font-size:13px;color:rgba(255,255,255,0.6);"><?= htmlspecialchars($u['objectif'] ?? '—') ?></td>
            <td style="font-size:13px;"><?= $u['poids']  ? $u['poids']  . ' kg' : '—' ?></td>
            <td style="font-size:13px;"><?= $u['taille'] ? $u['taille'] . ' cm' : '—' ?></td>
            <td>
              <span class="status-badge <?= $s['cls'] ?>" id="status-<?= $u['id'] ?>"
                    onclick="cycleStatus(<?= $u['id'] ?>, '<?= $u['status'] ?>', this)">
                <span class="dot"></span><?= $s['label'] ?>
              </span>
            </td>
            <td>
              <button class="btn-action btn-view"
                      onclick='openModal("view", <?= json_encode($u) ?>)'>
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn-action btn-edit"
                      onclick='openModal("edit", <?= json_encode($u) ?>)'>
                <i class="fa fa-pen"></i>
              </button>
              <button class="btn-action btn-delete"
                      onclick="confirmDelete(<?= $u['id'] ?>)">
                <i class="fa fa-trash"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="empty-state" id="emptyState">
      <i class="fa fa-users-slash"></i>
      Aucun utilisateur trouvé pour ces critères.
    </div>

    <!-- PAGINATION -->
    <div class="pagination-wrap" id="paginationWrap">
      <div class="page-info" id="pageInfo">
        Page <strong><?= $page ?></strong> / <?= $totalPages ?>
      </div>
      <div class="pagination" id="paginationBtns">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <button class="page-btn <?= $i === $page ? 'active' : '' ?>"
                  onclick="goPage(<?= $i ?>)"><?= $i ?></button>
        <?php endfor; ?>
      </div>
    </div>

  </div><!-- end table-wrap -->
</div><!-- end content-area -->


<!-- ===== MODAL ADD / EDIT / VIEW ===== -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal-box">

    <h3 id="modalTitle">Ajouter un utilisateur</h3>
    <p class="modal-sub" id="modalSub">Remplissez les informations ci-dessous</p>

    <form id="modalForm" method="POST" novalidate>
      <input type="hidden" name="id" id="mId">

      <div class="modal-row">
        <div class="modal-field">
          <label>Nom complet</label>
          <input type="text" name="nom" id="mNom" placeholder="ex: Ahmed Ben Ali" data-v="required">
          <span class="v-msg" id="mNom-msg"></span>
        </div>
        <div class="modal-field">
          <label>Email</label>
          <input type="email" name="email" id="mEmail" placeholder="nom@email.com" data-v="email">
          <span class="v-msg" id="mEmail-msg"></span>
        </div>
      </div>

      <div class="modal-field" id="pwdField">
        <label>Mot de passe <span id="pwdOptional" style="color:rgba(255,255,255,0.25);font-size:10px;">(laisser vide = inchangé)</span></label>
        <input type="password" name="password" id="mPwd" placeholder="••••••••" data-v="optpwd">
        <span class="v-msg" id="mPwd-msg"></span>
      </div>

      <div class="modal-row">
        <div class="modal-field">
          <label>Poids (kg)</label>
          <input type="number" name="poids" id="mPoids" placeholder="70" data-v="posnum">
          <span class="v-msg" id="mPoids-msg"></span>
        </div>
        <div class="modal-field">
          <label>Taille (cm)</label>
          <input type="number" name="taille" id="mTaille" placeholder="175" data-v="posnum">
          <span class="v-msg" id="mTaille-msg"></span>
        </div>
      </div>

      <div class="modal-row">
        <div class="modal-field">
          <label>Objectif</label>
          <select name="objectif" id="mObjectif" data-v="select">
            <option value="">-- Choisir --</option>
            <option value="Perte de poids">Perte de poids</option>
            <option value="Prise de masse">Prise de masse</option>
            <option value="Équilibre alimentaire">Équilibre alimentaire</option>
            <option value="Végétarien">Végétarien</option>
          </select>
          <span class="v-msg" id="mObjectif-msg"></span>
        </div>
        <div class="modal-field">
          <label>Statut</label>
          <select name="status" id="mStatus" data-v="select">
            <option value="active">✅ Actif</option>
            <option value="inactive">⏳ Inactif</option>
            <option value="banned">🚫 Banni</option>
          </select>
          <span class="v-msg" id="mStatus-msg"></span>
        </div>
      </div>

      <button type="submit" class="btn-save" id="saveBtn">💾 Enregistrer</button>
      <button type="button" class="btn-cancel" onclick="closeModal()">Annuler</button>
    </form>

  </div>
</div>


<script>
/* ================================================================
   AJAX SEARCH + FILTER + PAGINATION
================================================================ */

let searchTimer = null;
let currentPage = <?= $page ?>;

const searchInput    = document.getElementById('searchInput');
const filterStatus   = document.getElementById('filterStatus');
const filterObjectif = document.getElementById('filterObjectif');

searchInput.addEventListener('input', () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => { currentPage = 1; fetchUsers(); }, 320);
});

filterStatus.addEventListener('change',   () => { currentPage = 1; fetchUsers(); });
filterObjectif.addEventListener('change', () => { currentPage = 1; fetchUsers(); });

function goPage(p) { currentPage = p; fetchUsers(); }

function fetchUsers() {
  const search   = searchInput.value.trim();
  const status   = filterStatus.value;
  const objectif = filterObjectif.value;

  const params = new URLSearchParams({ search, status, objectif, page: currentPage });

  document.getElementById('spinner').style.display    = 'block';
  document.getElementById('usersTable').style.display = 'none';
  document.getElementById('emptyState').style.display = 'none';

  fetch('/ProjetWeb-User/index.php?url=Admin/searchUsers&' + params)
    .then(r => r.json())
    .then(data => {
      document.getElementById('spinner').style.display = 'none';
      renderTable(data.users);
      renderPagination(data.page, data.totalPages, data.total);
    })
    .catch(() => {
      document.getElementById('spinner').style.display = 'none';
    });
}

function renderTable(users) {
  const tbody = document.getElementById('usersBody');
  const table = document.getElementById('usersTable');
  const empty = document.getElementById('emptyState');

  if (!users.length) {
    table.style.display = 'none';
    empty.style.display = 'block';
    return;
  }

  table.style.display = '';
  empty.style.display = 'none';

  tbody.innerHTML = users.map(u => {
    const initials = u.nom ? u.nom.charAt(0).toUpperCase() : '?';
    const statusMap = {
      active:   { label: 'Actif',   cls: 'active' },
      inactive: { label: 'Inactif', cls: 'inactive' },
      banned:   { label: 'Banni',   cls: 'banned' }
    };
    const s = statusMap[u.status] || { label: u.status, cls: 'inactive' };

    return `
      <tr id="user-row-${u.id}">
        <td style="color:rgba(255,255,255,0.3);font-size:12px;">${u.id}</td>
        <td>
          <div class="user-badge">
            <div class="avatar">${initials}</div>
            <div>
              <div class="user-name" id="user-name-${u.id}">${esc(u.nom)}</div>
              <div class="user-email">${esc(u.email)}</div>
            </div>
          </div>
        </td>
        <td style="font-size:13px;color:rgba(255,255,255,0.6);">${esc(u.objectif || '—')}</td>
        <td style="font-size:13px;">${u.poids ? u.poids + ' kg' : '—'}</td>
        <td style="font-size:13px;">${u.taille ? u.taille + ' cm' : '—'}</td>
        <td>
          <span class="status-badge ${s.cls}" id="status-${u.id}" onclick="cycleStatus(${u.id}, '${u.status}', this)">
            <span class="dot"></span>${s.label}
          </span>
        </td>
        <td>
          <button class="btn-action btn-view"  onclick='openModal("view", ${JSON.stringify(u)})'><i class="fa fa-eye"></i></button>
          <button class="btn-action btn-edit"  onclick='openModal("edit", ${JSON.stringify(u)})'><i class="fa fa-pen"></i></button>
          <button class="btn-action btn-delete" onclick="confirmDelete(${u.id})"><i class="fa fa-trash"></i></button>
        </td>
      </tr>`;
  }).join('');
}

function renderPagination(page, totalPages, total) {
  document.getElementById('resultCount').textContent = total;
  document.getElementById('pageInfo').innerHTML = `Page <strong>${page}</strong> / ${totalPages}`;

  const wrap = document.getElementById('paginationBtns');
  wrap.innerHTML = '';

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement('button');
    btn.className = 'page-btn' + (i === page ? ' active' : '');
    btn.textContent = i;
    btn.onclick = () => goPage(i);
    wrap.appendChild(btn);
  }
}

function esc(str) {
  if (!str) return '';
  return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ================================================================
   TOGGLE STATUS (clic sur badge)
================================================================ */
const statusCycle = { active: 'inactive', inactive: 'banned', banned: 'active' };
const statusLabels = { active: 'Actif', inactive: 'Inactif', banned: 'Banni' };

function cycleStatus(id, current, el) {
  const next = statusCycle[current] || 'inactive';
  updateStatus(id, next, el);
}

function updateStatus(id, newStatus, el) {
  fetch('/ProjetWeb-User/index.php?url=Admin/toggleStatus', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id=${id}&status=${newStatus}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      if (el) {
        el.className = `status-badge ${newStatus} just-changed`;
        el.innerHTML = `<span class="dot"></span>${statusLabels[newStatus]}`;
        el.setAttribute('onclick', `cycleStatus(${id}, '${newStatus}', this)`);
        setTimeout(() => el.classList.remove('just-changed'), 1000);
      }
      const row = document.getElementById('user-row-' + id);
      if (row) {
        row.classList.add(newStatus === 'banned' ? 'just-banned' : 'just-updated');
        setTimeout(() => {
          row.classList.remove('just-banned', 'just-updated');
        }, 1500);
      }
      showVoiceStatus(`✓ Utilisateur ${data.nom || 'mis à jour'} → ${statusLabels[newStatus]}`, 'success');
    }
  });
}

/* ================================================================
   DELETE CONFIRMATION
================================================================ */
function confirmDelete(id) {
  if (!confirm('Supprimer cet utilisateur ?')) return;
  window.location = `/ProjetWeb-User/index.php?url=Admin/deleteUser/${id}`;
}

/* ================================================================
   MODAL ADD / EDIT / VIEW
================================================================ */
let modalMode = 'add';

function openModal(mode, user = null) {
  modalMode = mode;
  const overlay = document.getElementById('modalOverlay');
  overlay.classList.add('open');

  const form    = document.getElementById('modalForm');
  const saveBtn = document.getElementById('saveBtn');
  const pwdOpt  = document.getElementById('pwdOptional');

  form.querySelectorAll('[data-v]').forEach(el => {
    el.classList.remove('v-ok', 'v-err');
  });
  form.querySelectorAll('.v-msg').forEach(el => {
    el.style.display = 'none'; el.textContent = '';
  });

  if (mode === 'add') {
    document.getElementById('modalTitle').textContent = '➕ Ajouter un utilisateur';
    document.getElementById('modalSub').textContent   = 'Remplissez les informations ci-dessous';
    form.action = '/ProjetWeb-User/index.php?url=Admin/addUser';
    form.reset();
    pwdOpt.style.display = 'none';
    saveBtn.style.display = 'block';
    form.querySelectorAll('input, select').forEach(el => el.disabled = false);

  } else if (mode === 'edit') {
    document.getElementById('modalTitle').textContent = '✏ Modifier utilisateur';
    document.getElementById('modalSub').textContent   = `Modification de ${user.nom}`;
    form.action = `/ProjetWeb-User/index.php?url=Admin/updateUser/${user.id}`;
    fillModal(user);
    pwdOpt.style.display = 'inline';
    saveBtn.style.display = 'block';
    form.querySelectorAll('input, select').forEach(el => el.disabled = false);

  } else {
    document.getElementById('modalTitle').textContent = '👁 Détails utilisateur';
    document.getElementById('modalSub').textContent   = user.nom;
    fillModal(user);
    saveBtn.style.display = 'none';
    form.querySelectorAll('input, select').forEach(el => el.disabled = true);
  }
}

function fillModal(u) {
  document.getElementById('mId').value      = u.id;
  document.getElementById('mNom').value     = u.nom     || '';
  document.getElementById('mEmail').value   = u.email   || '';
  document.getElementById('mPoids').value   = u.poids   || '';
  document.getElementById('mTaille').value  = u.taille  || '';
  document.getElementById('mPwd').value     = '';
  document.getElementById('mObjectif').value = u.objectif || '';
  document.getElementById('mStatus').value   = u.status   || 'inactive';
}

function closeModal() {
  document.getElementById('modalOverlay').classList.remove('open');
}

document.getElementById('modalOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

/* ================================================================
   VALIDATION MODALE
================================================================ */
document.getElementById('modalForm').addEventListener('submit', function(e) {
  if (modalMode === 'view') return;

  let valid = true;

  this.querySelectorAll('[data-v]').forEach(el => {
    const rule = el.dataset.v;
    const val  = el.value.trim();
    const msg  = document.getElementById(el.id + '-msg');
    let error  = null;

    if (rule === 'required' && val === '')  error = 'Ce champ est obligatoire.';
    if (rule === 'email') {
      if (val === '') error = 'Email obligatoire.';
      else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) error = 'Email invalide.';
    }
    if (rule === 'posnum' && val !== '') {
      if (isNaN(val) || parseFloat(val) <= 0) error = 'Valeur invalide.';
    }
    if (rule === 'select' && val === '') error = 'Veuillez choisir une option.';
    if (rule === 'optpwd' && val !== '' && val.length < 8) error = 'Minimum 8 caractères.';

    if (error) {
      el.classList.add('v-err'); el.classList.remove('v-ok');
      if (msg) { msg.textContent = error; msg.style.display = 'block'; }
      valid = false;
    } else {
      el.classList.remove('v-err');
      if (val !== '') el.classList.add('v-ok');
      if (msg) { msg.style.display = 'none'; }
    }
  });

  if (!valid) e.preventDefault();
});

document.getElementById('modalForm').querySelectorAll('[data-v]').forEach(el => {
  el.addEventListener('input', () => {
    el.classList.remove('v-err', 'v-ok');
    const msg = document.getElementById(el.id + '-msg');
    if (msg) msg.style.display = 'none';
    if (el.value.trim()) el.classList.add('v-ok');
  });
});

/* ================================================================
   VOICE IA — RECONNAISSANCE VOCALE
================================================================ */
let recognition = null;
let isListening = false;
const voiceBtn       = document.getElementById('voiceBtn');
const voiceLabel     = document.getElementById('voiceLabel');
const voiceStatusBar = document.getElementById('voiceStatusBar');
const voiceStatusTxt = document.getElementById('voiceStatusText');

function toggleVoice() {
  if (isListening) {
    stopVoice();
  } else {
    startVoice();
  }
}

function startVoice() {
  if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
    showVoiceStatus('❌ Votre navigateur ne supporte pas la reconnaissance vocale.', 'error');
    return;
  }

  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  recognition = new SpeechRecognition();
  recognition.lang = 'fr-FR';
  recognition.continuous = true;
  recognition.interimResults = true;

  recognition.onstart = function() {
    isListening = true;
    voiceBtn.classList.add('listening');
    voiceLabel.textContent = 'Écoute...';
    voiceStatusBar.className = 'voice-status-bar active listening';
    voiceStatusTxt.textContent = '🎤 En écoute... Dites "banned [nom]" ou "unbanned [nom]"';
  };

  recognition.onresult = function(event) {
    let transcript = '';
    for (let i = event.resultIndex; i < event.results.length; i++) {
      transcript += event.results[i][0].transcript;
    }

    const lower = transcript.toLowerCase().trim();
    voiceStatusTxt.textContent = '🎤 ' + transcript;

    /* Détecter "banned [nom]" ou "unbanned [nom]" */
    const bannedMatch   = lower.match(/(?:banned|ban)\s+(\w+)/);
    const unbannedMatch = lower.match(/(?:unbanned|unban|dés?ban)\s+(\w+)/);

    if (bannedMatch) {
      const nom = bannedMatch[1];
      findAndBanUser(nom, 'banned');
    } else if (unbannedMatch) {
      const nom = unbannedMatch[1];
      findAndBanUser(nom, 'active');
    }
  };

  recognition.onerror = function(event) {
    if (event.error !== 'no-speech') {
      showVoiceStatus('❌ Erreur vocale : ' + event.error, 'error');
    }
  };

  recognition.onend = function() {
    if (isListening) {
      /* Redémarrer si toujours actif */
      try { recognition.start(); } catch(e) {}
    }
  };

  try {
    recognition.start();
  } catch(e) {
    showVoiceStatus('❌ Impossible de démarrer la reconnaissance vocale.', 'error');
  }
}

function stopVoice() {
  isListening = false;
  if (recognition) {
    recognition.stop();
  }
  voiceBtn.classList.remove('listening');
  voiceLabel.textContent = 'Voix IA';
  voiceStatusBar.className = 'voice-status-bar active';
  voiceStatusBar.style.display = 'none';
}

function findAndBanUser(nom, targetStatus) {
  /* Chercher l'utilisateur par nom dans le tableau actuel */
  const rows = document.querySelectorAll('#usersBody tr');
  let found = false;

  rows.forEach(row => {
    const nameEl = row.querySelector('.user-name');
    if (nameEl) {
      const name = nameEl.textContent.toLowerCase();
      if (name.includes(nom.toLowerCase()) || nom.toLowerCase().includes(name.substring(0, 3))) {
        const tr = row.closest('tr');
        const id = tr ? tr.id.replace('user-row-', '') : null;
        if (id) {
          found = true;
          const statusEl = document.getElementById('status-' + id);
          showVoiceStatus(`🎯 Utilisateur trouvé : ${nameEl.textContent} → ${targetStatus === 'banned' ? 'Banni' : 'Actif'}`, 'listening');
          updateStatus(parseInt(id), targetStatus, statusEl);
        }
      }
    }
  });

  if (!found) {
    showVoiceStatus(`⚠ Utilisateur "${nom}" non trouvé dans la liste actuelle.`, 'error');
  }
}

function showVoiceStatus(text, type) {
  voiceStatusBar.className = 'voice-status-bar active ' + type;
  voiceStatusTxt.textContent = text;
  voiceStatusBar.style.display = 'flex';

  setTimeout(() => {
    if (voiceStatusBar.className.includes(type)) {
      voiceStatusBar.style.display = 'none';
    }
  }, 4000);
}
</script>

</body>
</html>
```