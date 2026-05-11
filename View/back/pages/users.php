```html
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin Users - EcoNutri</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ===== BASE ===== */
*, *::before, *::after { box-sizing: border-box; }

:root {
    --bg: #f1f5f9;
    --bg-card: #ffffff;
    --bg-hover: #f8fafc;
    --bg-tertiary: #f1f5f9;
    --border: #e2e8f0;
    --border-hover: #cbd5e1;
    --text-primary: #0f172a;
    --text-secondary: #475569;
    --text-tertiary: #94a3b8;
    --accent: #00c853;
    --accent-light: #e8f5e9;
    --accent-glow: rgba(0, 200, 83, 0.15);
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.07), 0 2px 4px rgba(0,0,0,0.04);
    --shadow-lg: 0 12px 40px rgba(0,0,0,0.1), 0 4px 12px rgba(0,0,0,0.05);
    --shadow-xl: 0 20px 60px rgba(0,0,0,0.12);
    --radius: 16px;
    --radius-sm: 12px;
    --radius-xs: 10px;
}

body {
    margin: 0;
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg);
    color: var(--text-primary);
    display: flex;
    min-height: 100vh;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* ===== ANIMATIONS ===== */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

@keyframes pulse-ring {
    0%, 100% { transform: scale(1); opacity: 0.6; }
    50% { transform: scale(1.15); opacity: 0.3; }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@keyframes statusFlash {
    0%   { transform: scale(1); opacity: 1; }
    25%  { transform: scale(1.2); opacity: 0.6; }
    50%  { transform: scale(0.95); opacity: 1; }
    75%  { transform: scale(1.05); }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes rowHighlight {
    0%   { background: rgba(255, 152, 0, 0.12); }
    100% { background: transparent; }
}

@keyframes rowBanned {
    0%   { background: rgba(239, 83, 80, 0.12); }
    100% { background: transparent; }
}

@keyframes dotBlink {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.3; transform: scale(0.7); }
}

/* ===== CONTENT AREA ===== */
.content-area {
    margin-left: 250px;
    flex: 1;
    padding: 32px 36px;
    background: var(--bg);
    min-height: 100vh;
}

/* ===== PAGE HEADER ===== */
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 30px;
    animation: fadeInUp 0.4s ease-out forwards;
    opacity: 0;
    animation-delay: 0.05s;
}

.page-title {
    font-size: 28px;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -0.5px;
}

.page-title span {
    color: var(--accent);
}

/* ===== FLASH MESSAGES ===== */
.flash {
    padding: 14px 20px;
    border-radius: var(--radius-sm);
    font-size: 14px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
    animation: fadeInUp 0.3s ease-out forwards;
    border: 1px solid;
}

@keyframes fadeInUp { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }

.flash.success {
    background: var(--accent-light);
    border-color: #c8e6c9;
    color: #00a844;
}

.flash.error {
    background: #ffebee;
    border-color: #ffcdd2;
    color: #d32f2f;
}

/* ===== STAT CARDS ===== */
.stat-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 28px;
}

.stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 22px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.4s ease-out forwards;
    opacity: 0;
    box-shadow: var(--shadow-sm);
}

.stat-card:nth-child(1) { animation-delay: 0.08s; }
.stat-card:nth-child(2) { animation-delay: 0.14s; }
.stat-card:nth-child(3) { animation-delay: 0.2s; }
.stat-card:nth-child(4) { animation-delay: 0.26s; }

.stat-card::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: var(--radius) var(--radius) 0 0;
}

.stat-card.green::after  { background: linear-gradient(90deg, #00c853, #00e676); }
.stat-card.blue::after   { background: linear-gradient(90deg, #2196f3, #42a5f5); }
.stat-card.orange::after { background: linear-gradient(90deg, #ff9800, #ffa726); }
.stat-card.red::after    { background: linear-gradient(90deg, #ef5350, #e57373); }

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: transparent;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}

.stat-card.green  .stat-icon { background: var(--accent-light); }
.stat-card.blue   .stat-icon { background: #e3f2fd; }
.stat-card.orange .stat-icon { background: #fff8e1; }
.stat-card.red    .stat-icon { background: #ffebee; }

.stat-val  {
    font-size: 30px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -1px;
}

.stat-card.green  .stat-val { color: #00c853; }
.stat-card.blue   .stat-val { color: #2196f3; }
.stat-card.orange .stat-val { color: #ff9800; }
.stat-card.red    .stat-val { color: #ef5350; }

.stat-lbl  {
    font-size: 13px;
    color: var(--text-tertiary);
    margin-top: 4px;
    font-weight: 500;
}

/* ===== TOOLBAR ===== */
.toolbar {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 18px;
    flex-wrap: wrap;
    animation: fadeInUp 0.4s ease-out forwards;
    opacity: 0;
    animation-delay: 0.2s;
}

.search-wrap {
    position: relative;
    flex: 1;
    min-width: 200px;
}

.search-wrap i {
    position: absolute;
    left: 16px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-tertiary);
    font-size: 14px;
    pointer-events: none;
}

.search-input {
    width: 100%;
    padding: 12px 16px 12px 42px;
    background: var(--bg-card);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-primary);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    outline: none;
    transition: all 0.25s ease;
    font-weight: 500;
    box-shadow: var(--shadow-sm);
}

.search-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 4px var(--accent-glow);
}

.search-input::placeholder {
    color: var(--text-tertiary);
}

.filter-select {
    padding: 11px 36px 11px 16px;
    background: var(--bg-card);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-secondary);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    font-weight: 500;
    outline: none;
    cursor: pointer;
    transition: all 0.2s ease;
    appearance: none;
    background-image: url("image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    box-shadow: var(--shadow-sm);
}

.filter-select:focus { border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-glow); }
.filter-select option { background: #ffffff; }

/* VOICE BUTTON */
.voice-btn {
    padding: 11px 20px;
    border-radius: var(--radius-sm);
    border: 1.5px solid #ffcdd2;
    background: #fff8f8;
    color: #d32f2f;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.25s;
    white-space: nowrap;
    position: relative;
    box-shadow: var(--shadow-sm);
}

.voice-btn:hover {
    border-color: #ef5350;
    background: #fff0f0;
    color: #d32f2f;
}

.voice-btn.listening {
    border-color: #ef5350;
    background: #ffe5e5;
    color: #d32f2f;
    box-shadow: 0 0 24px rgba(239, 83, 80, 0.2);
    animation: voicePulse 1.5s ease-in-out infinite;
}

@keyframes voicePulse {
    0%, 100% { box-shadow: 0 0 24px rgba(239, 83, 80, 0.2); }
    50% { box-shadow: 0 0 36px rgba(239, 83, 80, 0.35); }
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

.btn-add {
    padding: 11px 24px;
    background: linear-gradient(135deg, #00c853, #00e676);
    border: none;
    border-radius: var(--radius-sm);
    color: #fff;
    font-weight: 700;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
    white-space: nowrap;
    box-shadow: 0 4px 14px rgba(0, 200, 83, 0.3);
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 200, 83, 0.4);
}

/* ===== RESULT INFO ===== */
.result-info {
    font-size: 13.5px;
    color: var(--text-tertiary);
    margin-bottom: 14px;
    font-weight: 500;
}

.result-info strong {
    color: var(--accent);
    font-weight: 700;
}

/* ===== TABLE ===== */
.table-wrap {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    animation: fadeInUp 0.4s ease-out forwards;
    opacity: 0;
    animation-delay: 0.28s;
}

#usersTable {
    width: 100%;
    border-collapse: collapse;
}

/* ═══════════════════════════════════════════════ */
/* ════ THEAD EN VERT ════ */
/* ═══════════════════════════════════════════════ */
#usersTable thead tr {
    background: linear-gradient(135deg, #00c853, #00e676) !important;
    border-bottom: none;
}

#usersTable th {
    padding: 16px 18px;
    font-size: 12px;
    font-weight: 700;
    color: #ffffff !important;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    white-space: nowrap;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
}

#usersTable td {
    padding: 14px 18px;
    font-size: 14px;
    border-bottom: 1px solid var(--bg-tertiary);
    vertical-align: middle;
}

#usersTable tbody tr {
    transition: background 0.15s;
}

#usersTable tbody tr:hover {
    background: var(--accent-light);
}

#usersTable tbody tr:last-child td {
    border-bottom: none;
}

/* USER BADGE */
.user-badge {
    display: flex;
    align-items: center;
    gap: 12px;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00c853, #00e676);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(0, 200, 83, 0.25);
}

.user-name  { font-weight: 600; font-size: 14px; color: var(--text-primary); }
.user-email { font-size: 12px; color: var(--text-tertiary); }

/* STATUS BADGE */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    user-select: none;
    border: 1px solid;
}

.status-badge:hover { opacity: 0.85; }

.status-badge .dot {
    width: 7px; height: 7px;
    border-radius: 50%;
}

.status-badge.active {
    background: var(--accent-light);
    color: #00a844;
    border-color: #c8e6c9;
}
.status-badge.active .dot {
    background: #00c853;
    box-shadow: 0 0 6px #00c853;
}

.status-badge.inactive {
    background: #fff8e1;
    color: #f57c00;
    border-color: #ffe0b2;
}
.status-badge.inactive .dot {
    background: #ffa726;
    animation: dotBlink 1.5s ease-in-out infinite;
}

.status-badge.banned {
    background: #ffebee;
    color: #d32f2f;
    border-color: #ffcdd2;
}
.status-badge.banned .dot {
    background: #ef5350;
    box-shadow: 0 0 6px #ef5350;
}

.status-badge.just-changed {
    animation: statusFlash 0.8s ease;
}

tr.just-updated {
    animation: rowHighlight 1.5s ease;
}

tr.just-banned {
    animation: rowBanned 1.5s ease;
}

/* ACTION BUTTONS */
.btn-action {
    border: 1px solid var(--border);
    padding: 8px 10px;
    border-radius: var(--radius-xs);
    cursor: pointer;
    font-size: 13px;
    transition: all 0.2s;
    margin: 0 2px;
    background: var(--bg-card);
}

.btn-action:hover { transform: scale(1.08); }

.btn-view {
    color: #2196f3;
    border-color: #bbdefb;
    background: #e3f2fd;
}
.btn-view:hover { background: #bbdefb; }

.btn-edit {
    color: #f57c00;
    border-color: #ffe0b2;
    background: #fff8e1;
}
.btn-edit:hover { background: #ffe0b2; }

.btn-delete {
    color: #d32f2f;
    border-color: #ffcdd2;
    background: #ffebee;
}
.btn-delete:hover { background: #ffcdd2; }

/* ===== LOADING SPINNER ===== */
.spinner {
    display: none;
    text-align: center;
    padding: 50px;
    color: var(--text-tertiary);
    font-size: 14px;
    font-weight: 500;
}

.spinner i {
    display: block;
    font-size: 32px;
    margin-bottom: 12px;
    color: var(--accent);
    animation: spin 1s linear infinite;
}

/* ===== EMPTY STATE ===== */
.empty-state {
    display: none;
    text-align: center;
    padding: 60px 20px;
    color: var(--text-tertiary);
    font-weight: 500;
}

.empty-state i {
    font-size: 40px;
    display: block;
    margin-bottom: 12px;
    color: var(--border);
}

/* ===== PAGINATION ===== */
.pagination-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-top: 1px solid var(--border);
    background: var(--bg-card);
}

.pagination {
    display: flex;
    gap: 6px;
}

.page-btn {
    width: 36px; height: 36px;
    border-radius: var(--radius-xs);
    border: 1.5px solid var(--border);
    background: var(--bg-card);
    color: var(--text-secondary);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    font-family: 'DM Sans', sans-serif;
}

.page-btn:hover:not(:disabled) {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--accent-light);
}

.page-btn.active {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(0, 200, 83, 0.3);
}

.page-btn:disabled {
    opacity: 0.35;
    cursor: not-allowed;
}

.page-info {
    font-size: 13px;
    color: var(--text-tertiary);
    font-weight: 500;
}

.page-info strong {
    color: var(--text-primary);
}

/* ===== VOICE STATUS BAR ===== */
.voice-status-bar {
    display: none;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    margin-bottom: 16px;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 500;
    animation: fadeIn 0.3s ease;
}

.voice-status-bar.active { display: flex; }

.voice-status-bar.listening {
    background: #fff0f0;
    border: 1px solid #ffcdd2;
    color: #d32f2f;
}

.voice-status-bar.success {
    background: var(--accent-light);
    border: 1px solid #c8e6c9;
    color: #00a844;
}

.voice-status-bar.error {
    background: #fff8e1;
    border: 1px solid #ffe0b2;
    color: #f57c00;
}

/* ===== MODAL ===== */
.modal-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(8px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.modal-overlay.open { display: flex; }

.modal-box {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 32px 36px;
    width: 520px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-xl);
    animation: modalIn 0.3s ease;
}

@keyframes modalIn {
    from { opacity: 0; transform: translateY(16px) scale(0.98); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

.modal-box h3 {
    color: var(--text-primary);
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 6px;
    letter-spacing: -0.3px;
}

.modal-box .modal-sub {
    font-size: 13px;
    color: var(--text-tertiary);
    margin-bottom: 24px;
}

.modal-field {
    margin-bottom: 16px;
}

.modal-field label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 7px;
}

.modal-field input,
.modal-field select {
    width: 100%;
    padding: 12px 14px;
    background: var(--bg-secondary);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-xs);
    color: var(--text-primary);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    outline: none;
    transition: all 0.25s ease;
    font-weight: 500;
}

.modal-field input:focus,
.modal-field select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 4px var(--accent-glow);
}

.modal-field input::placeholder { color: var(--text-tertiary); }
.modal-field select option { background: #ffffff; }

.modal-field input.v-ok,
.modal-field select.v-ok {
    border-color: #00c853 !important;
    box-shadow: 0 0 0 4px var(--accent-glow) !important;
}

.modal-field input.v-err,
.modal-field select.v-err {
    border-color: #ef5350 !important;
    box-shadow: 0 0 0 4px rgba(239, 83, 80, 0.12) !important;
}

.v-msg {
    font-size: 11px;
    color: #ef5350;
    margin-top: 5px;
    display: none;
    font-weight: 500;
}

.modal-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

.btn-save {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #00c853, #00e676);
    border: none;
    border-radius: var(--radius-sm);
    color: #fff;
    font-weight: 700;
    font-family: 'DM Sans', sans-serif;
    font-size: 15px;
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.25s;
    box-shadow: 0 4px 14px rgba(0, 200, 83, 0.3);
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 200, 83, 0.4);
}

.btn-cancel {
    width: 100%;
    padding: 12px;
    background: transparent;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-secondary);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background: #ffebee;
    border-color: #ffcdd2;
    color: #d32f2f;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 1200px) {
    .stat-cards { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .content-area { margin-left: 0; padding: 20px 16px; }
    .stat-cards { grid-template-columns: 1fr; }
    .toolbar { flex-direction: column; }
    .search-wrap { width: 100%; }
    .modal-box { width: 95%; padding: 24px 20px; }
}
</style>
</head>
<body>

<?php include 'view/back/partials/sidebar.php'; ?>

<div class="content-area">

    <!-- FLASH MESSAGES -->
    <?php if (!empty($_SESSION['admin_success'])): ?>
        <div class="flash success"><i class="fa fa-check-circle"></i> <?= htmlspecialchars($_SESSION['admin_success']) ?></div>
        <?php unset($_SESSION['admin_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['admin_error'])): ?>
        <div class="flash error"><i class="fa fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['admin_error']) ?></div>
        <?php unset($_SESSION['admin_error']); ?>
    <?php endif; ?>

    <!-- HEADER -->
    <div class="page-header">
        <div class="page-title">Gestion des utilisateurs <span>✦</span></div>
        <button class="btn-add" onclick="openModal('add')">
            <i class="fa fa-plus"></i>Ajouter un utilisateur
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

    <!-- TOOLBAR -->
    <div class="toolbar">
        <div class="search-wrap">
            <i class="fa fa-magnifying-glass"></i>
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
            <i class="fa fa-circle-notch"></i>Chargement des données...
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
                        <td style="color:var(--text-tertiary);font-size:12px;font-weight:600;"><?= $u['id'] ?></td>
                        <td>
                            <div class="user-badge">
                                <div class="avatar"><?= $initials ?></div>
                                <div>
                                    <div class="user-name" id="user-name-<?= $u['id'] ?>"><?= htmlspecialchars($u['nom']) ?></div>
                                    <div class="user-email"><?= htmlspecialchars($u['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px;color:var(--text-secondary);"><?= htmlspecialchars($u['objectif'] ?? '—') ?></td>
                        <td style="font-size:13px;font-weight:500;"><?= $u['poids']  ? $u['poids']  . ' kg' : '—' ?></td>
                        <td style="font-size:13px;font-weight:500;"><?= $u['taille'] ? $u['taille'] . ' cm' : '—' ?></td>
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

    </div>
</div>

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
                <label>Mot de passe <span id="pwdOptional" style="color:var(--text-tertiary);font-size:10px;font-weight:400;">(laisser vide = inchangé)</span></label>
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

    fetch('/2A35/index.php?url=Admin/searchUsers&' + params)
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
            <td style="color:var(--text-tertiary);font-size:12px;font-weight:600;">${u.id}</td>
            <td>
              <div class="user-badge">
                <div class="avatar">${initials}</div>
                <div>
                  <div class="user-name" id="user-name-${u.id}">${esc(u.nom)}</div>
                  <div class="user-email">${esc(u.email)}</div>
                </div>
              </div>
            </td>
            <td style="font-size:13px;color:var(--text-secondary);">${esc(u.objectif || '—')}</td>
            <td style="font-size:13px;font-weight:500;">${u.poids ? u.poids + ' kg' : '—'}</td>
            <td style="font-size:13px;font-weight:500;">${u.taille ? u.taille + ' cm' : '—'}</td>
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
    page = parseInt(page);
    totalPages = parseInt(totalPages);
    total = parseInt(total);

    if (isNaN(page) || page < 1) page = 1;
    if (isNaN(totalPages) || totalPages < 1) totalPages = 1;

    currentPage = page;

    document.getElementById('resultCount').textContent = total;

    document.getElementById('pageInfo').innerHTML =
        `Page <strong>${page}</strong> / ${totalPages}`;

    const wrap = document.getElementById('paginationBtns');
    wrap.innerHTML = '';

    const prevBtn = document.createElement('button');
    prevBtn.className = 'page-btn';
    prevBtn.innerHTML = '&laquo;';
    prevBtn.disabled = page <= 1;
    prevBtn.onclick = () => { if (page > 1) goPage(page - 1); };
    wrap.appendChild(prevBtn);

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.className = 'page-btn';
        if (i === page) btn.classList.add('active');
        btn.textContent = i;
        btn.onclick = () => { goPage(i); };
        wrap.appendChild(btn);
    }

    const nextBtn = document.createElement('button');
    nextBtn.className = 'page-btn';
    nextBtn.innerHTML = '&raquo;';
    nextBtn.disabled = page >= totalPages;
    nextBtn.onclick = () => { if (page < totalPages) goPage(page + 1); };
    wrap.appendChild(nextBtn);
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
    fetch('/2A35/index.php?url=Admin/toggleStatus', {
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
    window.location = `/2A35/index.php?url=Admin/deleteUser/${id}`;
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
        form.action = '/2A35/index.php?url=Admin/addUser';
        form.reset();
        pwdOpt.style.display = 'none';
        saveBtn.style.display = 'block';
        form.querySelectorAll('input, select').forEach(el => el.disabled = false);

    } else if (mode === 'edit') {
        document.getElementById('modalTitle').textContent = '✏️ Modifier utilisateur';
        document.getElementById('modalSub').textContent   = `Modification de ${user.nom}`;
        form.action = `/2A35/index.php?url=Admin/updateUser/${user.id}`;
        fillModal(user);
        pwdOpt.style.display = 'inline';
        saveBtn.style.display = 'block';
        form.querySelectorAll('input, select').forEach(el => el.disabled = false);

    } else {
        document.getElementById('modalTitle').textContent = '👁️ Détails utilisateur';
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