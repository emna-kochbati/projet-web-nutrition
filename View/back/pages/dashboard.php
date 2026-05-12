```html
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin IA — EcoNutri</title>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
require_once __DIR__ . '/../../../Config/database.php';
$db = Database::getConnection();

// ── STATS RÉELLES ──
$totalUsers    = (int)$db->query("SELECT COUNT(*) FROM user")->fetchColumn();
$activeUsers   = (int)$db->query("SELECT COUNT(*) FROM user WHERE status='active'")->fetchColumn();
$inactiveUsers = (int)$db->query("SELECT COUNT(*) FROM user WHERE status='inactive'")->fetchColumn();
$bannedUsers   = (int)$db->query("SELECT COUNT(*) FROM user WHERE status='banned'")->fetchColumn();
$activePct     = $totalUsers > 0 ? round($activeUsers / $totalUsers * 100) : 0;

// Objectifs
$objectifsRaw = $db->query("SELECT objectif, COUNT(*) as cnt FROM user GROUP BY objectif ORDER BY cnt DESC")->fetchAll(PDO::FETCH_ASSOC);
$objLabels    = array_map(fn($r) => $r['objectif'] ?: 'Non défini', $objectifsRaw);
$objCounts    = array_column($objectifsRaw, 'cnt');

// Maladies
$maladiesRaw = $db->query("SELECT maladie, COUNT(*) as cnt FROM user WHERE maladie IS NOT NULL AND maladie!='' GROUP BY maladie ORDER BY cnt DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
$malLabels   = array_map(fn($r) => $r['maladie'], $maladiesRaw);
$malCounts   = array_column($maladiesRaw, 'cnt');

// Inscriptions 7 derniers jours
$weekLabels = []; $weekCounts = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $weekLabels[] = date('D', strtotime($day));
    try {
        $weekCounts[] = (int)$db->query("SELECT COUNT(*) FROM user WHERE DATE(created_at)='$day'")->fetchColumn();
    } catch(Exception $e) { $weekCounts[] = 0; }
}

// Derniers inscrits
try {
    $lastUsers = $db->query("SELECT id,nom,email,status,objectif,created_at FROM user ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    $lastUsers = $db->query("SELECT id,nom,email,status,objectif FROM user ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
}

// Stats activité
$activiteRaw = $db->query("SELECT activite, COUNT(*) as cnt FROM user WHERE activite IS NOT NULL AND activite!='' GROUP BY activite")->fetchAll(PDO::FETCH_ASSOC);

// Stats par statut pour le graphique
$statusData = [
    'labels' => ['Actifs', 'Inactifs', 'Bannis'],
    'counts' => [$activeUsers, $inactiveUsers, $bannedUsers]
];

// ═══════════════════════════════════════════════════
// 🧠 MOTEUR IA : ANALYSE & PRÉDICTION
// ═══════════════════════════════════════════════════

$inactiveRelance = $db->query("
    SELECT COUNT(*) FROM user 
    WHERE status='inactive' 
    AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
")->fetchColumn();

$autoBanList = $db->query("
    SELECT id, nom, email FROM user 
    WHERE status='inactive' 
    AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
")->fetchAll(PDO::FETCH_ASSOC);
$autoBanCount = count($autoBanList);

$avgWeek = count($weekCounts) > 0 ? array_sum($weekCounts) / count($weekCounts) : 0;
$maxCount = max($weekCounts);
$peakDay = array_search($maxCount, $weekCounts);
$peakDayName = $peakDay !== false ? $weekLabels[$peakDay] : 'Lundi';

$minCount = min($weekCounts);
$deadDay = array_search($minCount, $weekCounts);
$deadDayName = $deadDay !== false ? $weekLabels[$deadDay] : 'Dimanche';

$usersWithoutGoal = (int)$db->query("SELECT COUNT(*) FROM user WHERE objectif IS NULL OR objectif = ''")->fetchColumn();

$aiInsights = [];
if($totalUsers > 0){
    if($activePct < 50){
        $aiInsights[] = ['level'=>'critical', 'icon'=>'fa-triangle-exclamation', 'title'=>'Taux d\'activation critique', 'desc'=>'Seulement '.$activePct.'% des comptes sont actifs. Envoi d\'emails de relance recommandé.'];
    } elseif($activePct < 80){
        $aiInsights[] = ['level'=>'warning', 'icon'=>'fa-circle-exclamation', 'title'=>'Activation en dessous de l\'objectif', 'desc'=>'Le taux d\'activation est de '.$activePct.'%. Objectif recommandé: 90%.'];
    } else {
        $aiInsights[] = ['level'=>'success', 'icon'=>'fa-check-circle', 'title'=>'Excellente activation', 'desc'=>$activePct.'% des comptes sont actifs. Continuez ainsi!'];
    }

    if($autoBanCount > 0){
        $aiInsights[] = ['level'=>'danger', 'icon'=>'fa-robot', 'title'=>'Nettoyage automatique requis', 'desc'=>$autoBanCount.' comptes inactifs depuis 30+ jours doivent être bannis.'];
    }
}

$h = (int)date('H');
$greeting = $h < 12 ? 'Bonjour' : ($h < 18 ? 'Bon après-midi' : 'Bonsoir');

$today = date('Y-m-d');
$aiCalendarEvents = [];

$aiCalendarEvents[] = [
    'title' => '🤖 Analyse quotidienne', 
    'start' => $today, 
    'className' => 'event-ai', 
    'description' => 'Vérification automatique des métriques et alertes',
    'extendedProps' => ['type' => 'system', 'action' => 'analyze']
];

if ($inactiveRelance > 0) {
    $aiCalendarEvents[] = [
        'title' => '⚠️ Relance ' . $inactiveRelance . ' inactifs', 
        'start' => date('Y-m-d', strtotime('+1 day')), 
        'className' => 'event-warning', 
        'description' => "Envoyer un email de motivation aux $inactiveRelance utilisateurs inactifs depuis >7 jours.",
        'extendedProps' => ['type' => 'action', 'action' => 'send_reminders', 'count' => $inactiveRelance, 'icon' => 'envelope']
    ];
}

$aiCalendarEvents[] = [
    'title' => '📊 Rapport IA Hebdo', 
    'start' => date('Y-m-d', strtotime('+2 day')), 
    'className' => 'event-info', 
    'description' => 'Générer le rapport de croissance et analyser la rétention.',
    'extendedProps' => ['type' => 'info', 'action' => 'generate_report', 'icon' => 'chart-line']
];

if ($autoBanCount > 0) {
    $names = implode(', ', array_slice(array_column($autoBanList, 'nom'), 0, 3)) . '...';
    $aiCalendarEvents[] = [
        'title' => '🚫 Auto-Ban ' . $autoBanCount . ' comptes', 
        'start' => date('Y-m-d', strtotime('+3 day')), 
        'className' => 'event-danger', 
        'description' => "Supprimer les comptes abandonnés depuis 30 jours ($names). Cette action est irréversible.",
        'extendedProps' => ['type' => 'danger', 'action' => 'execute_ban', 'count' => $autoBanCount, 'icon' => 'ban']
    ];
}

if ($usersWithoutGoal > 0) {
    $aiCalendarEvents[] = [
        'title' => '🍎 Défaut Objectifs (' . $usersWithoutGoal . ')', 
        'start' => date('Y-m-d', strtotime('+4 day')), 
        'className' => 'event-warning', 
        'description' => "$usersWithoutGoal utilisateurs n'ont pas défini d'objectif nutritionnel. Suggérer l'assistant IA.",
        'extendedProps' => ['type' => 'action', 'action' => 'nutrition_nudge', 'count' => $usersWithoutGoal, 'icon' => 'apple-whole']
    ];
}

$aiCalendarEvents[] = [
    'title' => '💡 Optimisation Pic (' . $peakDayName . ')', 
    'start' => date('Y-m-d', strtotime('+5 day')), 
    'className' => 'event-success', 
    'description' => "L'IA a détecté que $peakDayName est votre jour de pic d'inscription. Programmer une promo ?",
    'extendedProps' => ['type' => 'suggestion', 'action' => 'optimize_traffic', 'icon' => 'bolt']
];

// ── RÉCAP CALENDRIER ──
$calendarSummary = [
    ['label' => 'Tâches IA', 'count' => count($aiCalendarEvents), 'color' => '#00c853', 'icon' => 'fa-robot'],
    ['label' => 'Actions requises', 'count' => ($inactiveRelance > 0 ? 1 : 0) + ($autoBanCount > 0 ? 1 : 0), 'color' => '#ff9800', 'icon' => 'fa-bolt'],
    ['label' => 'Alertes critiques', 'count' => $autoBanCount > 0 ? 1 : 0, 'color' => '#ef5350', 'icon' => 'fa-triangle-exclamation'],
];
?>

<style>
/* ═══════════════════════════════════════ */
/*  SIDEBAR — DESIGN ORIGINAL CONSERVÉ    */
/* ═══════════════════════════════════════ */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 220px;
    height: 100vh;
    background: #2e7d32;
    color: white;
    font-family: Arial, sans-serif;
    padding: 0;
    box-shadow: 3px 0 10px rgba(0,0,0,0.2);
    z-index: 1000;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 20px 15px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.15);
    background: #1b5e20;
}

.sidebar-header .site-name {
    color: white;
    font-size: 1rem;
    font-weight: 700;
    margin-top: 8px;
    letter-spacing: 0.05em;
}

.sidebar nav {
    flex: 1;
    padding: 12px 0;
}

.sidebar nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin: 2px 8px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 14px;
    border-radius: 6px;
    text-decoration: none;
    color: rgba(255,255,255,0.9);
    font-size: 0.92rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
    justify-content: space-between;
}

.nav-link:hover,
.nav-link.active {
    background: rgba(255,255,255,0.18);
    color: white;
}

.nav-link.active {
    background: rgba(255,255,255,0.22);
    border-left: 3px solid #ffffff;
}

.nav-logout {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 14px;
    border-radius: 6px;
    text-decoration: none;
    color: #ffcdd2 !important;
    font-size: 0.92rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
    justify-content: flex-start;
    margin: 2px 8px;
}

.nav-logout:hover {
    background: rgba(255, 255, 255, 0.18) !important;
    color: white !important;
}

.nav-logout i {
    transition: transform 0.25s ease;
}

.nav-logout:hover i {
    transform: translateX(3px);
}

.nav-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.nav-arrow {
    font-size: 0.7rem;
    transition: transform 0.2s;
}

.sub-menu {
    list-style: none;
    padding: 0 0 0 10px;
    margin: 0;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.sub-menu.open {
    max-height: 300px;
}

.sub-menu li a {
    display: block;
    padding: 8px 14px;
    border-radius: 5px;
    text-decoration: none;
    color: rgba(255,255,255,0.8);
    font-size: 0.85rem;
    margin: 2px 0;
    transition: all 0.2s ease;
}

.sub-menu li a:hover {
    background: rgba(255,255,255,0.15);
    color: white;
}

.nav-divider {
    height: 1px;
    background: rgba(255,255,255,0.12);
    margin: 8px 14px;
}

.sidebar-footer {
    padding: 12px 15px;
    border-top: 1px solid rgba(255,255,255,0.15);
    font-size: 0.75rem;
    color: rgba(255,255,255,0.5);
    text-align: center;
}

/* ═══ BASE & LAYOUT ═══ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --bg-primary: #ffffff;
    --bg-secondary: #f8fafc;
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
    --shadow-xl: 0 20px 60px rgba(0,0,0,0.12), 0 8px 20px rgba(0,0,0,0.06);
    --radius: 16px;
    --radius-sm: 10px;
    --radius-xs: 8px;
}

body {
    margin: 0;
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg-secondary);
    color: var(--text-primary);
    display: flex;
    min-height: 100vh;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* ═══ ANIMATIONS ═══ */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
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
    0% { transform: scale(1); opacity: 0.6; }
    50% { transform: scale(1.15); opacity: 0.3; }
    100% { transform: scale(1); opacity: 0.6; }
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
}

@keyframes blinkOrange {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@keyframes eventGlow {
    0%, 100% { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    50% { box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
}

.animate-in {
    animation: fadeInUp 0.5s ease-out forwards;
}

/* ═══ TOPBAR ═══ */
.topbar {
    position: fixed;
    top: 0;
    left: 220px;
    right: 0;
    height: 64px;
    background: rgba(255, 255, 255, 0.95);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 32px;
    z-index: 90;
    backdrop-filter: blur(16px);
    box-shadow: var(--shadow-sm);
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.topbar-page {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.3px;
}

.topbar-date {
    font-size: 12px;
    color: var(--text-tertiary);
    background: var(--bg-tertiary);
    padding: 5px 14px;
    border-radius: 20px;
    border: 1px solid var(--border);
    font-weight: 500;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.topbar-search {
    position: relative;
}

.topbar-search i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-tertiary);
    font-size: 13px;
}

#globalSearch {
    width: 300px;
    padding: 9px 16px 9px 38px;
    background: var(--bg-secondary);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-primary);
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    outline: none;
    transition: all 0.25s ease;
    font-weight: 500;
}

#globalSearch:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 4px var(--accent-glow);
    background: var(--bg-primary);
}

#globalSearch::placeholder {
    color: var(--text-tertiary);
}

#searchResults {
    position: absolute;
    top: calc(100% + 10px);
    left: 0;
    right: 0;
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    box-shadow: var(--shadow-xl);
    z-index: 200;
    max-height: 340px;
    overflow-y: auto;
    display: none;
    animation: fadeIn 0.15s ease-out;
}

#searchResults.show {
    display: block;
}

.sr-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 16px;
    border-bottom: 1px solid var(--bg-tertiary);
    cursor: pointer;
    transition: background 0.15s;
}

.sr-item:last-child {
    border-bottom: none;
}

.sr-item:hover {
    background: var(--accent-light);
}

.sr-av {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00c853, #69f0ae);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}

.sr-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
}

.sr-email {
    font-size: 11px;
    color: var(--text-tertiary);
}

.sr-badge {
    margin-left: auto;
    font-size: 10px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    flex-shrink: 0;
}

.sr-badge.active {
    background: var(--accent-light);
    color: #00a844;
    border: 1px solid #c8e6c9;
}

.sr-badge.inactive {
    background: #fff8e1;
    color: #f57c00;
    border: 1px solid #ffe0b2;
}

.sr-badge.banned {
    background: #ffebee;
    color: #d32f2f;
    border: 1px solid #ffcdd2;
}

.sr-empty {
    padding: 20px;
    text-align: center;
    font-size: 13px;
    color: var(--text-tertiary);
}

.sr-loading {
    padding: 16px;
    text-align: center;
    font-size: 12px;
    color: var(--text-tertiary);
}

.topbar-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-xs);
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--text-tertiary);
    transition: all 0.2s ease;
    position: relative;
}

.topbar-icon:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--accent-light);
}

.topbar-icon i {
    font-size: 14px;
}

.notif-dot {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #ef5350;
    border: 2px solid var(--bg-primary);
    animation: pulse-ring 1.5s infinite;
}

.admin-chip {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 5px 14px 5px 5px;
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: 30px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.admin-chip:hover {
    border-color: var(--accent);
    background: var(--accent-light);
}

.admin-av {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00c853, #00e676);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    box-shadow: 0 2px 8px rgba(0, 200, 83, 0.3);
}

.admin-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
}

/* ═══ CONTENT ═══ */
.content-area {
    margin-left: 220px;
    padding-top: 64px;
    flex: 1;
    min-height: 100vh;
    background: var(--bg-secondary);
}

.main {
    padding: 32px 36px 48px;
}

.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
}

.page-title {
    font-size: 26px;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -0.5px;
}

.page-title span {
    color: var(--accent);
}

.page-sub {
    font-size: 14px;
    color: var(--text-tertiary);
    margin-top: 4px;
    font-weight: 500;
}

.sec-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-tertiary);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.sec-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}

/* ═══ KPI CARDS ═══ */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
    margin-bottom: 28px;
}

.kpi {
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
}

.kpi:nth-child(1) { animation-delay: 0.05s; }
.kpi:nth-child(2) { animation-delay: 0.1s; }
.kpi:nth-child(3) { animation-delay: 0.15s; }
.kpi:nth-child(4) { animation-delay: 0.2s; }
.kpi:nth-child(5) { animation-delay: 0.25s; }

.kpi:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: transparent;
}

.kpi::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
}

.kpi.g::before { background: linear-gradient(90deg, #00c853, #00e676); }
.kpi.b::before { background: linear-gradient(90deg, #2196f3, #42a5f5); }
.kpi.o::before { background: linear-gradient(90deg, #ff9800, #ffa726); }
.kpi.r::before { background: linear-gradient(90deg, #ef5350, #e57373); }
.kpi.p::before { background: linear-gradient(90deg, #ab47bc, #ce93d8); }

.kpi-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 14px;
}

.kpi-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.kpi.g .kpi-icon { background: var(--accent-light); }
.kpi.b .kpi-icon { background: #e3f2fd; }
.kpi.o .kpi-icon { background: #fff8e1; }
.kpi.r .kpi-icon { background: #ffebee; }
.kpi.p .kpi-icon { background: #f3e5f5; }

.kpi-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
}

.kpi.g .kpi-badge { background: var(--accent-light); color: #00a844; }
.kpi.b .kpi-badge { background: #e3f2fd; color: #1976d2; }
.kpi.o .kpi-badge { background: #fff8e1; color: #f57c00; }
.kpi.r .kpi-badge { background: #ffebee; color: #d32f2f; }
.kpi.p .kpi-badge { background: #f3e5f5; color: #8e24aa; }

.kpi-val {
    font-size: 34px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -2px;
    margin-bottom: 4px;
}

.kpi.g .kpi-val { color: #00c853; }
.kpi.b .kpi-val { color: #2196f3; }
.kpi.o .kpi-val { color: #ff9800; }
.kpi.r .kpi-val { color: #ef5350; }
.kpi.p .kpi-val { color: #ab47bc; }

.kpi-lbl {
    font-size: 13px;
    color: var(--text-secondary);
    font-weight: 600;
}

.kpi-sub {
    font-size: 11px;
    color: var(--text-tertiary);
    margin-top: 3px;
}

/* ═══ AI INSIGHT CARD ═══ */
.ai-insight-card {
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 24px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
    animation-delay: 0.05s;
}

.ai-insight-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #00c853, #2196f3, #ab47bc, #00c853);
    background-size: 200% 100%;
    animation: shimmer 4s linear infinite;
}

.ai-insight-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--bg-tertiary);
}

.ai-insight-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #00c853, #00e676);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 4px 12px rgba(0, 200, 83, 0.25);
}

.ai-insight-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
}

.ai-insight-sub {
    font-size: 12px;
    color: var(--text-tertiary);
    margin-top: 2px;
}

/* ═══ AI ALERTS ═══ */
.ai-alerts-section {
    margin-bottom: 28px;
}

.ai-alert-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 16px 18px;
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    margin-bottom: 10px;
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.4s ease-out forwards;
    opacity: 0;
}

.ai-alert-item:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--border-hover);
}

.ai-alert-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
}

.ai-alert-item.critical::before { background: #ef5350; }
.ai-alert-item.warning::before { background: #ffa726; }
.ai-alert-item.success::before { background: #00c853; }
.ai-alert-item.info::before { background: #42a5f5; }
.ai-alert-item.danger::before { background: #ef5350; }

.ai-alert-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.ai-alert-item.critical .ai-alert-icon { background: #ffebee; color: #ef5350; }
.ai-alert-item.warning .ai-alert-icon { background: #fff8e1; color: #ffa726; }
.ai-alert-item.success .ai-alert-icon { background: var(--accent-light); color: #00c853; }
.ai-alert-item.info .ai-alert-icon { background: #e3f2fd; color: #42a5f5; }
.ai-alert-item.danger .ai-alert-icon { background: #ffebee; color: #ef5350; }

.ai-alert-content { flex: 1; }

.ai-alert-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 3px;
}

.ai-alert-desc {
    font-size: 13px;
    color: var(--text-tertiary);
    line-height: 1.5;
}

.ai-alert-tag {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 3px 10px;
    border-radius: 20px;
    flex-shrink: 0;
    margin-top: 2px;
}

.ai-alert-item.critical .ai-alert-tag { background: #ffebee; color: #ef5350; border: 1px solid #ffcdd2; }
.ai-alert-item.warning .ai-alert-tag { background: #fff8e1; color: #ffa726; border: 1px solid #ffe0b2; }
.ai-alert-item.success .ai-alert-tag { background: var(--accent-light); color: #00c853; border: 1px solid #c8e6c9; }
.ai-alert-item.info .ai-alert-tag { background: #e3f2fd; color: #42a5f5; border: 1px solid #bbdefb; }
.ai-alert-item.danger .ai-alert-tag { background: #ffebee; color: #ef5350; border: 1px solid #ffcdd2; }

/* ═══ CARDS ═══ */
.g3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px; }
.g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px; }
.g21 { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 28px; }
.g31 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 22px; }

.card {
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 22px 24px;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
}

.card:hover {
    box-shadow: var(--shadow-md);
}

.card-hd {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--bg-tertiary);
}

.card-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-title i {
    font-size: 14px;
    color: var(--accent);
}

.card-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
    background: var(--accent-light);
    color: #00a844;
    border: 1px solid #c8e6c9;
}

.card-badge.o {
    background: #fff8e1;
    color: #f57c00;
    border-color: #ffe0b2;
}

.card-badge.r {
    background: #ffebee;
    color: #d32f2f;
    border-color: #ffcdd2;
}

/* ═══ PROGRESS BARS ═══ */
.prog-item { margin-bottom: 14px; }
.prog-item:last-child { margin-bottom: 0; }

.prog-top {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    margin-bottom: 6px;
}

.prog-lbl { color: var(--text-secondary); font-weight: 600; }
.prog-val { color: var(--text-primary); font-weight: 700; }

.prog-bar {
    height: 6px;
    background: var(--bg-tertiary);
    border-radius: 4px;
    overflow: hidden;
}

.prog-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ═══ RING ═══ */
.ring-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 8px 0 4px;
}

.ring-svg { transform: rotate(-90deg); }

.ring-bg { fill: none; stroke: var(--bg-tertiary); }
.ring-fill { fill: none; stroke: #00c853; stroke-linecap: round; transition: stroke-dashoffset 1.5s ease; filter: drop-shadow(0 0 6px rgba(0, 200, 83, 0.3)); }

.ring-center { text-align: center; margin-top: 10px; }
.ring-pct { font-size: 36px; font-weight: 800; color: var(--text-primary); letter-spacing: -2px; }
.ring-sub { font-size: 12px; color: var(--text-tertiary); font-weight: 500; }

/* ═══ USER TABLE ═══ */
.u-table { width: 100%; border-collapse: collapse; }

.u-table th {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-tertiary);
    text-align: left;
    padding: 0 14px 12px 0;
    border-bottom: 1px solid var(--border);
}

.u-table td {
    padding: 12px 14px 12px 0;
    font-size: 13.5px;
    border-bottom: 1px solid var(--bg-tertiary);
    vertical-align: middle;
}

.u-table tr:last-child td { border-bottom: none; }
.u-table tr:hover td { background: var(--accent-light); }

.u-badge { display: flex; align-items: center; gap: 12px; }

.u-av {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00c853, #00e676);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 200, 83, 0.25);
}

.u-name { font-size: 13.5px; font-weight: 600; color: var(--text-primary); }
.u-email { font-size: 11.5px; color: var(--text-tertiary); }

.status-dot {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 20px;
    white-space: nowrap;
}

.status-dot::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }

.status-dot.active {
    background: var(--accent-light);
    color: #00a844;
    border: 1px solid #c8e6c9;
}
.status-dot.active::before { background: #00c853; box-shadow: 0 0 6px #00c853; }

.status-dot.inactive {
    background: #fff8e1;
    color: #f57c00;
    border: 1px solid #ffe0b2;
}
.status-dot.inactive::before { background: #ffa726; box-shadow: 0 0 6px #ffa726; animation: blinkOrange 1.5s infinite; }

.status-dot.banned {
    background: #ffebee;
    color: #d32f2f;
    border: 1px solid #ffcdd2;
}
.status-dot.banned::before { background: #ef5350; box-shadow: 0 0 6px #ef5350; }

/* ═══ BUTTONS ═══ */
.btn-g {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #00c853, #00e676);
    border: none;
    border-radius: var(--radius-xs);
    color: #fff;
    font-size: 13.5px;
    font-weight: 700;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.25s ease;
    box-shadow: 0 4px 14px rgba(0, 200, 83, 0.3);
}

.btn-g:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 200, 83, 0.4);
    color: #fff;
}

.btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: transparent;
    color: var(--text-secondary);
    border: 1px solid var(--border);
    border-radius: var(--radius-xs);
    font-size: 12.5px;
    font-weight: 600;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-outline:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--accent-light);
}

/* ═══════════════════════════════════════ */
/* ═══ CALENDAR — DESIGN WOW ═══ */
/* ═══════════════════════════════════════ */
.calendar-wrapper {
    position: relative;
    border-radius: var(--radius);
    overflow: hidden;
}

/* Header avec dégradé */
.calendar-header-bg {
    background: linear-gradient(135deg, #00c853 0%, #00e676 40%, #69f0ae 100%);
    padding: 22px 24px 18px;
    position: relative;
    overflow: hidden;
}

.calendar-header-bg::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.calendar-header-bg::after {
    content: '';
    position: absolute;
    bottom: -40%;
    left: -10%;
    width: 150px;
    height: 150px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
}

.calendar-header-bg .card-title {
    color: #fff !important;
    font-size: 15px;
    position: relative;
    z-index: 1;
}

.calendar-header-bg .card-title i {
    color: rgba(255,255,255,0.85) !important;
    font-size: 16px;
}

.calendar-header-subtitle {
    font-size: 11.5px;
    color: rgba(255,255,255,0.7);
    margin-top: 4px;
    position: relative;
    z-index: 1;
    font-weight: 500;
}

.calendar-header-badge {
    background: rgba(255,255,255,0.2) !important;
    border: 1px solid rgba(255,255,255,0.3) !important;
    color: #fff !important;
    position: relative;
    z-index: 1;
    backdrop-filter: blur(8px);
}

/* ── CALENDAR BODY ── */
.calendar-body {
    background: #ffffff;
    padding: 0;
}

/* ── FULLCALENDAR OVERRIDES ── */
.fc {
    color: var(--text-secondary) !important;
    font-family: 'DM Sans', sans-serif !important;
}

.fc-toolbar {
    padding: 14px 16px 6px;
    margin-bottom: 0 !important;
}

.fc-toolbar-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: var(--text-primary) !important;
    text-align: center !important;
}

.fc-toolbar-chunk {
    display: flex;
    align-items: center;
}

.fc-button {
    background: var(--bg-secondary) !important;
    border: 1.5px solid var(--border) !important;
    color: var(--text-secondary) !important;
    border-radius: var(--radius-xs) !important;
    font-size: 11.5px !important;
    font-family: 'DM Sans', sans-serif !important;
    padding: 6px 12px !important;
    box-shadow: none !important;
    font-weight: 600 !important;
    transition: all 0.2s ease !important;
}

.fc-button:hover, .fc-button-active {
    background: var(--accent-light) !important;
    color: #00a844 !important;
    border-color: #c8e6c9 !important;
}

.fc-button:active {
    transform: scale(0.97);
}

.fc .fc-icon {
    font-size: 12px !important;
}

/* En-têtes de jours */
.fc-col-header {
    background: var(--bg-tertiary);
    border-radius: 0;
}

.fc-col-header-cell-cushion {
    color: var(--text-tertiary) !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.12em !important;
    padding: 10px 0 !important;
    text-decoration: none !important;
}

.fc-col-header-cell {
    border-color: var(--border) !important;
}

/* Jours */
.fc-daygrid-day {
    border-color: var(--bg-tertiary) !important;
    transition: background 0.15s ease;
}

.fc-daygrid-day:hover {
    background: rgba(0, 200, 83, 0.03) !important;
}

.fc-daygrid-day-number {
    color: var(--text-secondary) !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    padding: 8px 10px !important;
    text-decoration: none !important;
    transition: all 0.2s ease;
    border-radius: 8px;
    margin: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
}

.fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
    background: linear-gradient(135deg, #00c853, #00e676);
    color: #fff !important;
    font-weight: 800 !important;
    box-shadow: 0 3px 12px rgba(0, 200, 83, 0.35);
}

.fc-daygrid-day.fc-day-today {
    background: rgba(0, 200, 83, 0.04) !important;
}

.fc-daygrid-day-frame {
    border-color: var(--bg-tertiary) !important;
}

.fc-scrollgrid, .fc-scrollgrid-section > td {
    border-color: var(--bg-tertiary) !important;
}

/* Événements */
.fc-event {
    border: none !important;
    border-radius: 6px !important;
    padding: 4px 8px !important;
    font-size: 11px !important;
    cursor: pointer;
    font-weight: 600 !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative;
    overflow: hidden;
    margin: 2px 4px !important;
    animation: fadeInUp 0.4s ease-out forwards;
}

.fc-event::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 3px;
    background: rgba(255,255,255,0.5);
    border-radius: 6px 0 0 6px;
}

.fc-event:hover {
    transform: translateY(-2px) scale(1.02) !important;
    z-index: 10;
}

.event-ai {
    background: linear-gradient(135deg, #00c853, #00e676) !important;
    color: #fff !important;
    box-shadow: 0 3px 10px rgba(0, 200, 83, 0.3) !important;
}
.event-ai::before { background: rgba(255,255,255,0.6); }

.event-warning {
    background: linear-gradient(135deg, #ff9800, #ffa726) !important;
    color: #fff !important;
    box-shadow: 0 3px 10px rgba(255, 152, 0, 0.3) !important;
}
.event-warning::before { background: rgba(255,255,255,0.6); }

.event-info {
    background: linear-gradient(135deg, #2196f3, #42a5f5) !important;
    color: #fff !important;
    box-shadow: 0 3px 10px rgba(33, 150, 243, 0.3) !important;
}
.event-info::before { background: rgba(255,255,255,0.6); }

.event-danger {
    background: linear-gradient(135deg, #ef5350, #e57373) !important;
    color: #fff !important;
    box-shadow: 0 3px 10px rgba(239, 83, 80, 0.3) !important;
}
.event-danger::before { background: rgba(255,255,255,0.6); }

.event-success {
    background: linear-gradient(135deg, #00c853, #69f0ae) !important;
    color: #fff !important;
    box-shadow: 0 3px 10px rgba(0, 200, 83, 0.25) !important;
}
.event-success::before { background: rgba(255,255,255,0.6); }

.event-suggestion {
    background: linear-gradient(135deg, #ab47bc, #ce93d8) !important;
    color: #fff !important;
    box-shadow: 0 3px 10px rgba(171, 71, 188, 0.3) !important;
}
.event-suggestion::before { background: rgba(255,255,255,0.6); }

/* ── RÉCAP CALENDRIER ── */
.calendar-summary {
    display: flex;
    gap: 10px;
    padding: 14px 16px;
    border-top: 1px solid var(--bg-tertiary);
    background: var(--bg-secondary);
}

.calendar-summary-item {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: var(--bg-primary);
    border-radius: 10px;
    border: 1px solid var(--border);
    transition: all 0.2s ease;
}

.calendar-summary-item:hover {
    border-color: transparent;
    box-shadow: var(--shadow-sm);
    transform: translateY(-1px);
}

.calendar-summary-icon {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #fff;
    flex-shrink: 0;
}

.calendar-summary-info {
    flex: 1;
}

.calendar-summary-count {
    font-size: 15px;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
}

.calendar-summary-label {
    font-size: 10px;
    color: var(--text-tertiary);
    font-weight: 500;
    margin-top: 2px;
}

/* ── LÉGENDE AMÉLIORÉE ── */
.calendar-legend {
    margin-top: 16px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding: 14px 16px;
    background: var(--bg-secondary);
    border-radius: 0 0 var(--radius) var(--radius);
}

.legend-tag {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 10.5px;
    font-weight: 600;
    background: var(--bg-primary);
    border: 1px solid var(--border);
    transition: all 0.2s ease;
}

.legend-tag:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
}

.legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.legend-dot.ai { background: linear-gradient(135deg, #00c853, #00e676); }
.legend-dot.warning { background: linear-gradient(135deg, #ff9800, #ffa726); }
.legend-dot.danger { background: linear-gradient(135deg, #ef5350, #e57373); }
.legend-dot.info { background: linear-gradient(135deg, #2196f3, #42a5f5); }
.legend-dot.success { background: linear-gradient(135deg, #00c853, #69f0ae); }
.legend-dot.suggestion { background: linear-gradient(135deg, #ab47bc, #ce93d8); }

/* ── STATUS LEGEND ═══ */
.status-legend {
    margin-top: 18px;
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.status-legend span {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-secondary);
    font-weight: 500;
}

.status-legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.spin { animation: spin 1s linear infinite; }

/* ═══ RESPONSIVE ═══ */
@media (max-width: 1200px) {
    .kpi-row { grid-template-columns: repeat(3, 1fr); }
    .g3 { grid-template-columns: repeat(2, 1fr); }
    .g21, .g31 { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .sidebar { display: none; }
    .content-area { margin-left: 0; }
    .topbar { left: 0; }
    .kpi-row, .g3, .g2, .g21, .g31 { grid-template-columns: 1fr; }
    .main { padding: 20px 16px 40px; }
    .calendar-summary { flex-direction: column; }
}
</style>
</head>
<body>

<!-- ═══════════════════════════════════════ -->
<!--  SIDEBAR — DESIGN ORIGINAL CONSERVÉ    -->
<!-- ═══════════════════════════════════════ -->
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="site-name">🥗 EcoNutri</div>
    </div>

    <nav>
        <ul>
            <li class="nav-item">
                <a href="index.php?url=Admin/dashboard" class="nav-link <?= ($_GET['url'] ?? '') === 'Admin/dashboard' ? 'active' : '' ?>">
                    <span class="nav-left">📊 Dashboard</span>
                </a>
            </li>

            <div class="nav-divider"></div>

            <li class="nav-item">
                <div class="nav-link <?= stripos($_GET['url'] ?? '', 'Admin/users') !== false ? 'active' : '' ?>" onclick="toggleMenu('user')">
                    <span class="nav-left">👤 Utilisateur</span>
                    <span class="nav-arrow" id="arrow-user">▶</span>
                </div>
                <ul class="sub-menu <?= stripos($_GET['url'] ?? '', 'Admin/users') !== false ? 'open' : '' ?>" id="sub-user">
                    <li><a href="index.php?url=Admin/dashboard">📊 Dashboard</a></li>
                    <li><a href="index.php?url=Admin/users">👥 Gestion Utilisateur</a></li>
                </ul>
            </li>

            <div class="nav-divider"></div>

            <?php
            $sections = [
                'partenaire' => ['🤝 Partenaire', [
                    ['label' => '📋 Liste',    'url' => '/ProjetWeb-User/Admin/partenaire'],
                    ['label' => '➕ Nouveau',  'url' => '/ProjetWeb-User/Admin/partenaire/create'],
                ]],
                'recette' => ['🍽️ Recette', [
                    ['label' => '📋 Liste des recettes',  'url' => '/ProjetWeb-User/Admin/recette'],
                    ['label' => '➕ Nouvelle recette',    'url' => '/ProjetWeb-User/Admin/recette/create'],
                ]],
                'programme' => ['🏋️ Programme', [
                    ['label' => '📋 Liste',    'url' => '/ProjetWeb-User/Admin/programme'],
                    ['label' => '➕ Nouveau',  'url' => '/ProjetWeb-User/Admin/programme/create'],
                ]],
                'evenement' => ['📅 Événement', [
                    ['label' => '📋 Liste',    'url' => '/ProjetWeb-User/Admin/evenement'],
                    ['label' => '➕ Nouveau',  'url' => '/ProjetWeb-User/Admin/evenement/create'],
                ]],
            ];

            foreach ($sections as $key => [$label, $items]):
                $isActive = isset($_GET['url']) && stripos($_GET['url'], 'Admin/' . $key) !== false;
            ?>
            <li class="nav-item">
                <div class="nav-link <?= $isActive ? 'active' : '' ?>" onclick="toggleMenu('<?= $key ?>')">
                    <span class="nav-left"><?= $label ?></span>
                    <span class="nav-arrow" id="arrow-<?= $key ?>">▶</span>
                </div>
                <ul class="sub-menu <?= $isActive ? 'open' : '' ?>" id="sub-<?= $key ?>">
                    <?php foreach ($items as $item): ?>
                    <li><a href="<?= $item['url'] ?>"><?= $item['label'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endforeach; ?>

            <div class="nav-divider"></div>

            <li class="nav-item">
                <a href="index.php?url=User/logout" class="nav-logout">
                    <span class="nav-left"><i class="fa fa-right-from-bracket"></i> Déconnexion</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        NutriSmart Admin © 2026
    </div>
</aside>

<!-- ══ TOPBAR ══ -->
<div class="topbar">
  <div class="topbar-left">
    <div class="topbar-page">Dashboard</div>
    <div class="topbar-date">
        <i class="fa fa-calendar" style="margin-right:6px;font-size:10px;"></i>
        <?= date('d M Y') ?> · <?= $greeting ?>
    </div>
  </div>
  <div class="topbar-right">
    <div class="topbar-search">
      <i class="fa fa-magnifying-glass"></i>
      <input type="text" id="globalSearch" placeholder="Rechercher un utilisateur...">
      <div id="searchResults"></div>
    </div>
    <div class="topbar-icon"><i class="fa fa-bell"></i><?php if ($inactiveUsers > 0): ?><div class="notif-dot"></div><?php endif; ?></div>
    <div class="topbar-icon"><i class="fa fa-gear"></i></div>
    <div class="admin-chip">
        <div class="admin-av">A</div>
        <div class="admin-name">Admin</div>
    </div>
  </div>
</div>

<!-- ══ CONTENT ══ -->
<div class="content-area">
<div class="main">

  <div class="page-header">
    <div>
      <div class="page-title">Vue d'ensemble <span>✦</span></div>
      <div class="page-sub"><?= $greeting ?> — Tableau de bord intelligent en temps réel</div>
    </div>
    <a href="index.php?url=Admin/users" class="btn-g"><i class="fa fa-users" style="filter:none;"></i> Gérer les utilisateurs</a>
  </div>

  <!-- AI INSIGHTS -->
  <div class="ai-insight-card">
    <div class="ai-insight-header">
      <div class="ai-insight-icon">🧠</div>
      <div>
        <div class="ai-insight-title">Insights IA — Analyse en temps réel</div>
        <div class="ai-insight-sub">Basé sur <?= $totalUsers ?> utilisateurs · Dernière analyse: <?= date('H:i') ?></div>
      </div>
    </div>
    <div class="g31" style="margin-bottom:0;">
      <div style="text-align:center; padding:14px 10px; background:var(--bg-secondary); border-radius:12px; border:1px solid var(--border);">
        <div style="font-size:30px; font-weight:800; color:#00c853;"><?= $activePct ?>%</div>
        <div style="font-size:12px; color:var(--text-tertiary); margin-top:4px; font-weight:600;">Taux d'activation</div>
        <div style="font-size:11px; color:<?= $activePct >= 80 ? '#00c853' : ($activePct >= 50 ? '#ffa726' : '#ef5350') ?>; margin-top:4px; font-weight:700;">
          <?= $activePct >= 80 ? '✓ Excellent' : ($activePct >= 50 ? '⚠ Moyen' : '✗ Critique') ?>
        </div>
      </div>
      <div style="text-align:center; padding:14px 10px; background:var(--bg-secondary); border-radius:12px; border:1px solid var(--border);">
        <div style="font-size:30px; font-weight:800; color:#2196f3;"><?= round(array_sum($weekCounts)) ?></div>
        <div style="font-size:12px; color:var(--text-tertiary); margin-top:4px; font-weight:600;">Inscriptions cette semaine</div>
        <div style="font-size:11px; color:#42a5f5; margin-top:4px; font-weight:700;">Tendance: <?= array_sum($weekCounts) > 0 ? '📈 Active' : '📉 Stable' ?></div>
      </div>
      <div style="text-align:center; padding:14px 10px; background:var(--bg-secondary); border-radius:12px; border:1px solid var(--border);">
        <div style="font-size:30px; font-weight:800; color:#ffa726;"><?= $inactiveUsers + $bannedUsers ?></div>
        <div style="font-size:12px; color:var(--text-tertiary); margin-top:4px; font-weight:600;">Alertes à traiter</div>
        <div style="font-size:11px; color:<?= ($inactiveUsers + $bannedUsers) > 0 ? '#ffa726' : '#00c853' ?>; margin-top:4px; font-weight:700;">
          <?= ($inactiveUsers + $bannedUsers) > 0 ? '⚠ Action requise' : '✓ Tout est OK' ?>
        </div>
      </div>
    </div>
  </div>

  <!-- AI ALERTS -->
  <div class="sec-title"><i class="fa fa-bolt" style="color:#ffa726; margin-right:4px;"></i>Alertes IA intelligentes</div>
  <div class="ai-alerts-section">
    <?php foreach($aiInsights as $insight): ?>
    <div class="ai-alert-item <?= $insight['level'] ?>">
      <div class="ai-alert-icon"><i class="fa <?= $insight['icon'] ?>"></i></div>
      <div class="ai-alert-content">
        <div class="ai-alert-title"><?= $insight['title'] ?></div>
        <div class="ai-alert-desc"><?= $insight['desc'] ?></div>
      </div>
      <span class="ai-alert-tag"><?= $insight['level'] ?></span>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- KPI -->
  <div class="sec-title"><i class="fa fa-chart-line" style="color:#2196f3; margin-right:4px;"></i>Indicateurs clés</div>
  <div class="kpi-row">
    <div class="kpi g">
        <div class="kpi-top"><div class="kpi-icon">👥</div><span class="kpi-badge">total</span></div>
        <div class="kpi-val"><?= number_format($totalUsers) ?></div>
        <div class="kpi-lbl">Utilisateurs</div>
        <div class="kpi-sub">inscrits</div>
    </div>
    <div class="kpi b">
        <div class="kpi-top"><div class="kpi-icon">✅</div><span class="kpi-badge"><?= $activePct ?>%</span></div>
        <div class="kpi-val"><?= $activeUsers ?></div>
        <div class="kpi-lbl">Comptes actifs</div>
        <div class="kpi-sub">sur <?= $totalUsers ?></div>
    </div>
    <div class="kpi o">
        <div class="kpi-top"><div class="kpi-icon">⏳</div><span class="kpi-badge">attente</span></div>
        <div class="kpi-val"><?= $inactiveUsers ?></div>
        <div class="kpi-lbl">Inactifs</div>
        <div class="kpi-sub">à activer</div>
    </div>
    <div class="kpi r">
        <div class="kpi-top"><div class="kpi-icon">🚫</div><span class="kpi-badge">bannis</span></div>
        <div class="kpi-val"><?= $bannedUsers ?></div>
        <div class="kpi-lbl">Bannis</div>
        <div class="kpi-sub">comptes bloqués</div>
    </div>
    <div class="kpi p">
        <div class="kpi-top"><div class="kpi-icon">📈</div><span class="kpi-badge">taux</span></div>
        <div class="kpi-val"><?= $activePct ?>%</div>
        <div class="kpi-lbl">Taux activation</div>
        <div class="kpi-sub">objectif : 90%</div>
    </div>
  </div>

  <!-- GRAPHS -->
  <div class="sec-title"><i class="fa fa-chart-area" style="color:#00c853; margin-right:4px;"></i>Analyses & Statistiques</div>
  <div class="g3">
    <div class="card">
      <div class="card-hd"><div class="card-title"><i class="fa fa-chart-line"></i> Inscriptions — 7 jours</div><span class="card-badge">Semaine</span></div>
      <canvas id="chartWeek" height="140"></canvas>
    </div>
    <div class="card">
      <div class="card-hd"><div class="card-title"><i class="fa fa-chart-pie"></i> Objectifs nutritionnels</div><span class="card-badge"><?= $totalUsers ?> users</span></div>
      <canvas id="chartObj" height="140"></canvas>
    </div>
    <div class="card">
      <div class="card-hd"><div class="card-title"><i class="fa fa-stethoscope"></i> Maladies déclarées</div><span class="card-badge o">Santé</span></div>
      <canvas id="chartMal" height="140"></canvas>
    </div>
  </div>

  <!-- NEW: STATUS + ACTIVITY + RING -->
  <div class="g3">
    <div class="card">
      <div class="card-hd"><div class="card-title"><i class="fa fa-chart-bar"></i> Répartition par statut</div></div>
      <canvas id="chartStatus" height="160"></canvas>
      <div class="status-legend">
        <span><span class="status-legend-dot" style="background:#00c853;"></span> Actifs: <?= $activeUsers ?></span>
        <span><span class="status-legend-dot" style="background:#ffa726;"></span> Inactifs: <?= $inactiveUsers ?></span>
        <span><span class="status-legend-dot" style="background:#ef5350;"></span> Bannis: <?= $bannedUsers ?></span>
      </div>
    </div>
    <div class="card">
      <div class="card-hd"><div class="card-title"><i class="fa fa-person-running"></i> Niveaux d'activité</div></div>
      <?php
      $actColors = ['#00c853','#2196f3','#ffa726','#ce93d8','#ef9a9a'];
      if(!empty($activiteRaw)):
        foreach($activiteRaw as $ai => $actRow):
          $actPct = $totalUsers > 0 ? round($actRow['cnt']/$totalUsers*100) : 0;
          $actCol = $actColors[$ai % count($actColors)];
      ?>
      <div class="prog-item">
        <div class="prog-top"><span class="prog-lbl"><?= htmlspecialchars($actRow['activite']) ?></span><span class="prog-val" style="color:<?= $actCol ?>"><?= $actRow['cnt'] ?> <small style="color:var(--text-tertiary);font-weight:400;">(<?= $actPct ?>%)</small></span></div>
        <div class="prog-bar"><div class="prog-fill" style="width:<?= $actPct ?>%;background:<?= $actCol ?>;"></div></div>
      </div>
      <?php endforeach; ?>
      <?php else: ?>
      <p style="font-size:13px;color:var(--text-tertiary);text-align:center;padding:20px 0;">Aucune donnée</p>
      <?php endif; ?>
    </div>
    <div class="card">
      <div class="card-hd"><div class="card-title"><i class="fa fa-circle-check"></i> Taux d'activation</div></div>
      <div class="ring-wrap">
        <svg class="ring-svg" width="130" height="130" viewBox="0 0 130 130">
          <circle class="ring-bg"   cx="65" cy="65" r="52" stroke-width="10"/>
          <circle class="ring-fill" cx="65" cy="65" r="52" stroke-width="10" stroke-dasharray="326.72" stroke-dashoffset="<?= 326.72 * (1 - $activePct/100) ?>"/>
        </svg>
        <div class="ring-center"><div class="ring-pct"><?= $activePct ?>%</div><div class="ring-sub"><?= $activeUsers ?> / <?= $totalUsers ?> actifs</div></div>
      </div>
      <div style="margin-top:18px;">
        <div class="prog-item"><div class="prog-top"><span class="prog-lbl">Actifs <span style="color:#00c853;">●</span></span><span class="prog-val"><?= $activeUsers ?></span></div><div class="prog-bar"><div class="prog-fill" style="width:<?= $activePct ?>%;background:#00c853;"></div></div></div>
        <div class="prog-item"><div class="prog-top"><span class="prog-lbl">Inactifs <span style="color:#ffa726;">●</span></span><span class="prog-val"><?= $inactiveUsers ?></span></div><div class="prog-bar"><div class="prog-fill" style="width:<?= $totalUsers>0?round($inactiveUsers/$totalUsers*100):0 ?>%;background:#ffa726;"></div></div></div>
        <div class="prog-item"><div class="prog-top"><span class="prog-lbl">Bannis <span style="color:#ef5350;">●</span></span><span class="prog-val"><?= $bannedUsers ?></span></div><div class="prog-bar"><div class="prog-fill" style="width:<?= $totalUsers>0?round($bannedUsers/$totalUsers*100):0 ?>%;background:#ef5350;"></div></div></div>
      </div>
    </div>
  </div>

  <!-- LAST USERS + CALENDAR -->
  <div class="sec-title"><i class="fa fa-clock" style="color:#ab47bc; margin-right:4px;"></i>Activité récente & Calendrier IA</div>
  <div class="g21">
    <div class="card">
      <div class="card-hd"><div class="card-title"><i class="fa fa-user-plus"></i> Derniers inscrits</div><a href="index.php?url=Admin/users" class="btn-outline">Voir tout →</a></div>
      <table class="u-table">
        <thead><tr><th>Utilisateur</th><th>Objectif</th><th>Statut</th><th>Action</th></tr></thead>
        <tbody>
          <?php foreach($lastUsers as $u):
            $init   = strtoupper(substr($u['nom'] ?? '?', 0, 1));
            $status = $u['status'] ?? 'inactive';
            $sl     = ['active'=>'Actif','inactive'=>'Inactif','banned'=>'Banni'][$status] ?? $status;
          ?>
          <tr>
            <td><div class="u-badge"><div class="u-av"><?= $init ?></div><div><div class="u-name"><?= htmlspecialchars($u['nom']) ?></div><div class="u-email"><?= htmlspecialchars($u['email']) ?></div></div></div></td>
            <td style="font-size:12px;color:var(--text-tertiary);"><?= htmlspecialchars($u['objectif'] ?? '—') ?></td>
            <td><span class="status-dot <?= $status ?>"><?= $sl ?></span></td>
            <td><a href="index.php?url=Admin/users" class="btn-outline" style="padding:5px 12px;font-size:12px;"><i class="fa fa-pen" style="font-size:10px;"></i> Éditer</a></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($lastUsers)): ?>
          <tr><td colspan="4" style="text-align:center;color:var(--text-tertiary);padding:24px 0;font-size:13px;">Aucun utilisateur</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- ═══ CALENDRIER WOW ═══ -->
    <div class="calendar-wrapper card" style="padding: 0; overflow: hidden;">
      <div class="calendar-header-bg">
        <div class="card-title" style="border-bottom: none; padding-bottom: 0; margin-bottom: 0;">
          <i class="fa fa-calendar-days"></i> Calendrier IA
        </div>
        <div class="calendar-header-subtitle">Planning intelligent généré automatiquement · Cliquez sur un événement pour agir</div>
        <span class="card-badge calendar-header-badge">⚡ Automatisé</span>
      </div>

      <div class="calendar-body">
        <div id="calendar"></div>
      </div>

      <!-- RÉCAP CALENDRIER -->
      <div class="calendar-summary">
        <?php foreach ($calendarSummary as $cs): ?>
        <div class="calendar-summary-item">
          <div class="calendar-summary-icon" style="background: <?= $cs['color'] ?>;">
            <i class="fa <?= $cs['icon'] ?>"></i>
          </div>
          <div class="calendar-summary-info">
            <div class="calendar-summary-count"><?= $cs['count'] ?></div>
            <div class="calendar-summary-label"><?= $cs['label'] ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- LÉGENDE -->
      <div class="calendar-legend">
        <span class="legend-tag"><span class="legend-dot ai"></span>IA</span>
        <span class="legend-tag"><span class="legend-dot warning"></span>Rapport</span>
        <span class="legend-tag"><span class="legend-dot danger"></span>Danger</span>
        <span class="legend-tag"><span class="legend-dot info"></span>Info</span>
        <span class="legend-tag"><span class="legend-dot success"></span>Succès</span>
        <span class="legend-tag"><span class="legend-dot suggestion"></span>Suggestion</span>
      </div>
    </div>
  </div>

</div>
</div>

<script>
/* ═══ TOGGLE MENU SIDEBAR ═══ */
function toggleMenu(key) {
    const sub   = document.getElementById("sub-" + key);
    const arrow = document.getElementById("arrow-" + key);
    if (!sub || !arrow) return;
    const isOpen = sub.classList.contains("open");
    document.querySelectorAll(".sub-menu").forEach(s => s.classList.remove("open"));
    document.querySelectorAll(".nav-arrow").forEach(a => a.style.transform = "");
    if (!isOpen) {
        sub.classList.add("open");
        arrow.style.transform = "rotate(90deg)";
    }
}

Chart.defaults.color = '#64748b';
Chart.defaults.borderColor = '#e2e8f0';
Chart.defaults.font.family = 'DM Sans';
Chart.defaults.font.size = 12;

/* ── CHARTS ── */
new Chart(document.getElementById('chartWeek'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($weekLabels) ?>,
    datasets: [{
      label: 'Inscriptions',
      data: <?= json_encode($weekCounts) ?>,
      backgroundColor: 'rgba(0, 200, 83, 0.15)',
      borderColor: '#00c853',
      borderWidth: 2,
      borderRadius: 8,
      borderSkipped: false
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false } },
      y: { grid: { color: '#f1f5f9' }, beginAtZero: true, ticks: { stepSize: 1 } }
    }
  }
});

new Chart(document.getElementById('chartObj'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_map(fn($l) => $l ?: 'Non défini', $objLabels)) ?>,
    datasets: [{
      data: <?= json_encode($objCounts) ?>,
      backgroundColor: ['#00c853','#2196f3','#ffa726','#ce93d8','#ef9a9a'],
      borderWidth: 3,
      borderColor: '#ffffff',
      hoverOffset: 8
    }]
  },
  options: {
    cutout: '62%',
    plugins: {
      legend: {
        position: 'bottom',
        labels: { color: '#64748b', padding: 14, usePointStyle: true, pointStyle: 'circle' }
      }
    }
  }
});

new Chart(document.getElementById('chartMal'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(count($malLabels) > 0 ? $malLabels : ['Aucune donnée']) ?>,
    datasets: [{
      data: <?= json_encode(count($malCounts) > 0 ? $malCounts : [0]) ?>,
      backgroundColor: ['#ffa726','#ef5350','#ce93d8','#42a5f5','#00c853','#e2e8f0'],
      borderRadius: 6,
      borderSkipped: false
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: '#f1f5f9' }, beginAtZero: true },
      y: { grid: { display: false } }
    }
  }
});

new Chart(document.getElementById('chartStatus'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($statusData['labels']) ?>,
    datasets: [{
      data: <?= json_encode($statusData['counts']) ?>,
      backgroundColor: ['#00c853', '#ffa726', '#ef5350'],
      borderWidth: 3,
      borderColor: '#ffffff',
      hoverOffset: 8
    }]
  },
  options: {
    cutout: '58%',
    plugins: { legend: { display: false } }
  }
});

/* ═══════════════════════════════════════════════ */
/* ════ CALENDRIER IA → VRAI BACKEND ════ */
/* ═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl) return;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 400,
    headerToolbar: { left: 'prev,next', center: 'title', right: 'today' },
    events: <?= json_encode($aiCalendarEvents) ?>,
    eventDisplay: 'block',

    eventClick: async function(info) {
      const ev = info.event;
      const props = ev.extendedProps;

      if (props.type === 'danger') {
        const result = await Swal.fire({
          title: '🚫 Auto-Ban Requise',
          html: `<div style="text-align:left; font-size:14px; color:#333;">
            <p><strong>${props.count || 0} comptes</strong> inactifs >30 jours.</p>
            <p style="color:#666; font-size:12px;">${props.description || ''}</p>
            <hr style="margin:12px 0;"><p style="color:#d32f2f; font-size:12px;">⚠️ Irréversible.</p>
          </div>`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#aaa',
          confirmButtonText: 'Oui, bannir',
          cancelButtonText: 'Annuler'
        });
        if (result.isConfirmed) await executeAction('execute_ban', props.count, ev);
      }

      else if (props.type === 'action' || props.type === 'suggestion') {
        const result = await Swal.fire({
          title: '⚡ Action IA Suggérée',
          html: `<div style="text-align:left; font-size:14px; color:#333;">
            <p><strong>${ev.title}</strong></p>
            <p style="color:#666; font-size:12px;">${props.description || ''}</p>
          </div>`,
          icon: 'info',
          showCancelButton: true,
          confirmButtonColor: '#00c853',
          confirmButtonText: 'Exécuter',
          cancelButtonText: 'Plus tard'
        });
        if (result.isConfirmed) await executeAction(props.action, props.count, ev);
      }

      else if (props.action === 'generate_report') {
        const result = await Swal.fire({
          title: '📊 Rapport IA Hebdomadaire',
          text: 'Générer le rapport statistique réel depuis MySQL ?',
          icon: 'info',
          showCancelButton: true,
          confirmButtonColor: '#00c853',
          confirmButtonText: 'Générer',
          cancelButtonText: 'Annuler'
        });
        if (result.isConfirmed) await executeAction('generate_report', 0, ev);
      }

      else {
        Swal.fire({ title: ev.title, text: props.description, icon: 'info', confirmButtonColor: '#00c853' });
      }
    }
  });

  calendar.render();
});

/* ═══════════════════════════════════════════════ */
/* ════ FONCTION UNIVERSELLE → BACKEND PHP ════ */
/* ═══════════════════════════════════════════════ */
async function executeAction(action, count, event) {

  let endpoint = '/ProjetWeb-User/index.php?url=Admin/executeAction';

  if (action === 'generate_report') {
    endpoint = '/ProjetWeb-User/index.php?url=Admin/generateReport';
  }

  Swal.fire({
    title: getLoadingTitle(action),
    html: getLoadingMsg(action, count),
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  try {
    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ action, count })
    });

    const data = await response.json();

    if (!data.success) {
      Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: data.message
      });
      return;
    }

    if (event) {
      event.setProp('className', 'event-success');
      event.setProp('title', '✅ ' + event.title);
    }

    if (data.stats) updateKPIs(data.stats);

    Swal.fire({
      icon: 'success',
      title: 'Succès',
      html: buildSuccessHTML(action, data)
    });

  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Erreur réseau',
      text: error.message
    });
  }
}

function getLoadingTitle(action) {
  return {
    execute_ban: 'Nettoyage...',
    send_reminders: 'Envoi rappels...',
    nutrition_nudge: 'Suggestions...',
    optimize_traffic: 'Analyse...',
    generate_report: 'Rapport...'
  }[action] || 'Traitement...';
}

function getLoadingMsg(action, count) {
  return {
    execute_ban: `Suppression de <b>${count}</b> comptes...`,
    send_reminders: `Préparation de <b>${count}</b> emails...`,
    nutrition_nudge: 'Analyse des profils sans objectifs...',
    optimize_traffic: 'Calcul des pics d\'inscription...',
    generate_report: 'Collecte des statistiques MySQL...'
  }[action] || 'Traitement...';
}

function buildSuccessHTML(action, data) {
  let html = `<p style="font-size:14px;"><strong>${data.message}</strong></p>`;

  if (action === 'send_reminders' && data.users) {
    html += '<ul style="text-align:left; font-size:12px; color:#666;">';
    data.users.slice(0, 5).forEach(u => { html += `<li>${u.nom} (${u.email})</li>`; });
    html += '</ul>';
  }
  else if (action === 'optimize_traffic' && data.best_day) {
    html += `<p style="font-size:12px; color:#666;">📈 Meilleur: <b>${data.best_day}</b> (${data.best_cnt})</p>`;
    html += `<p style="font-size:12px; color:#666;">📉 À éviter: <b>${data.worst_day}</b> (${data.worst_cnt})</p>`;
    if (data.suggestion) html += `<p style="font-size:12px; color:#00c853;">💡 ${data.suggestion}</p>`;
  }
  else if (action === 'generate_report' && data.report) {
    const r = data.report;
    html += `
      <div style="text-align:left; font-size:12px; color:#666;">
        <p>👥 Total: <b>${r.total_users}</b></p>
        <p>✅ Activation: <b>${r.activation_pct}%</b></p>
        <p>📈 Tendance: <b>${r.trend_pct > 0 ? '+' : ''}${r.trend_pct}%</b></p>
        <p>📅 Inscriptions/sem: <b>${r.this_week_reg}</b></p>
      </div>`;
  }

  if (data.stats) html += `<hr style="margin:8px 0;"><p style="font-size:11px; color:#999;">KPIs mis à jour en temps réel</p>`;
  return html;
}

function updateKPIs(stats) {
  const vals = document.querySelectorAll('.kpi-row .kpi .kpi-val');
  if (vals.length >= 4) {
    vals[0].textContent = stats.total.toLocaleString();
    vals[1].textContent = stats.active;
    vals[2].textContent = stats.inactive;
    vals[3].textContent = stats.banned;
  }
  const pct = stats.total > 0 ? Math.round(stats.active / stats.total * 100) : 0;
  const pctEl = document.querySelector('.kpi-row .kpi:last-child .kpi-val');
  if (pctEl) pctEl.textContent = pct + '%';
}

/* ── ANIMATE PROG BARS ── */
window.addEventListener('load', () => {
  document.querySelectorAll('.prog-fill').forEach(el => {
    const w = el.style.width; el.style.width = '0';
    setTimeout(() => { el.style.width = w; }, 150);
  });
});

/* ══════════════════════════════════════
   RECHERCHE AJAX
══════════════════════════════════════ */
const searchInput = document.getElementById('globalSearch');
const searchResults = document.getElementById('searchResults');
let searchTimer = null;

searchInput.addEventListener('input', () => {
  const q = searchInput.value.trim();
  clearTimeout(searchTimer);
  if (q.length < 2) { hideResults(); return; }
  searchResults.innerHTML = '<div class="sr-loading"><i class="fa fa-circle-notch spin" style="color:#00c853;margin-right:6px;"></i>Recherche...</div>';
  searchResults.classList.add('show');
  searchTimer = setTimeout(() => {
    fetch('/ProjetWeb-User/index.php?url=Admin/searchUsers&search=' + encodeURIComponent(q) + '&page=1')
      .then(r => r.json())
      .then(data => {
        if (!data.users || data.users.length === 0) {
          searchResults.innerHTML = '<div class="sr-empty">Aucun résultat pour « ' + esc(q) + ' »</div>'; return;
        }
        searchResults.innerHTML = data.users.slice(0, 6).map(u => {
          const init = u.nom ? u.nom.charAt(0).toUpperCase() : '?';
          const status = u.status || 'inactive';
          const sl = { active: 'Actif', inactive: 'Inactif', banned: 'Banni' }[status] || status;
          return `<div class="sr-item" onclick="goToUser(${u.id})"><div class="sr-av">${init}</div><div><div class="sr-name">${esc(u.nom)}</div><div class="sr-email">${esc(u.email)}</div></div><span class="sr-badge ${status}">${sl}</span></div>`;
        }).join('');
        if (data.total > 6) searchResults.innerHTML += `<div style="padding:12px 16px;text-align:center;border-top:1px solid #f1f5f9;"><a href="/ProjetWeb-User/index.php?url=Admin/users&search=${encodeURIComponent(q)}" style="font-size:12px;color:#00c853;text-decoration:none;font-weight:600;">Voir les ${data.total} résultats →</a></div>`;
      })
      .catch(() => { searchResults.innerHTML = '<div class="sr-empty">Erreur de connexion</div>'; });
  }, 300);
});

function goToUser(id) { window.location = '/ProjetWeb-User/index.php?url=Admin/users'; }
function hideResults() { searchResults.classList.remove('show'); searchResults.innerHTML = ''; }
document.addEventListener('click', (e) => { if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) hideResults(); });
searchInput.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') { hideResults(); searchInput.blur(); }
  if (e.key === 'Enter' && searchInput.value.trim()) window.location = '/ProjetWeb-User/index.php?url=Admin/users&search=' + encodeURIComponent(searchInput.value.trim());
});
function esc(str) { if (!str) return ''; return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>

</body>
</html>
```