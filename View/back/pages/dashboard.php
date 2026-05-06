Pour connecter une "vraie" logique IA à ton calendrier, il faut transformer les événements statiques en **actions exécutables** basées sur tes données réelles.

Voici comment on va procéder :
1. **Analyse réelle en PHP** : On détecte les utilisateurs à bannir automatiquement (>30 jours d'inactivité) et ceux à relancer.
2. **Prédiction intelligente** : L'IA analyse tes graphiques pour suggérer le meilleur moment pour envoyer des emails.
3. **Interaction JS avancée** : Quand on clique sur un événement du calendrier, une **fenêtre d'action** s'ouvre pour exécuter la tâche (ex: "Bannir 5 comptes", "Envoyer 15 rappels").

Voici le code complet mis à jour :

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

// 1. Détection Inactifs > 7 jours (À relancer)
// On simule ici : Utilisateurs 'inactive' créés il y a plus de 7 jours
$inactiveRelance = $db->query("
    SELECT COUNT(*) FROM user 
    WHERE status='inactive' 
    AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
")->fetchColumn();

// 2. Détection Auto-Ban > 30 jours (Nettoyage DB)
// Utilisateurs 'inactive' créés il y a plus de 30 jours
$autoBanList = $db->query("
    SELECT id, nom, email FROM user 
    WHERE status='inactive' 
    AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
")->fetchAll(PDO::FETCH_ASSOC);
$autoBanCount = count($autoBanList);

// 3. Analyse des tendances (Peak vs Dead days)
$avgWeek = count($weekCounts) > 0 ? array_sum($weekCounts) / count($weekCounts) : 0;
$maxCount = max($weekCounts);
$peakDay = array_search($maxCount, $weekCounts);
$peakDayName = $peakDay !== false ? $weekLabels[$peakDay] : 'Lundi';

$minCount = min($weekCounts);
$deadDay = array_search($minCount, $weekCounts);
$deadDayName = $deadDay !== false ? $weekLabels[$deadDay] : 'Dimanche';

// 4. Recommandation Nutrition (Simulation)
$usersWithoutGoal = (int)$db->query("SELECT COUNT(*) FROM user WHERE objectif IS NULL OR objectif = ''")->fetchColumn();

// AI Insights generation
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

// ═══════════════════════════════════════════════════
// 📅 GÉNÉRATION DU CALENDRIER INTELLIGENT
// ═══════════════════════════════════════════════════
$today = date('Y-m-d');
$aiCalendarEvents = [];

// 1. Tâche immédiate (Aujourd'hui)
$aiCalendarEvents[] = [
    'title' => '🤖 Analyse quotidienne', 
    'start' => $today, 
    'className' => 'event-ai', 
    'description' => 'Vérification automatique des métriques et alertes',
    'extendedProps' => ['type' => 'system', 'action' => 'analyze']
];

// 2. Relance des inactifs (J+1)
if ($inactiveRelance > 0) {
    $aiCalendarEvents[] = [
        'title' => '⚠️ Relance ' . $inactiveRelance . ' inactifs', 
        'start' => date('Y-m-d', strtotime('+1 day')), 
        'className' => 'event-warning', 
        'description' => "Envoyer un email de motivation aux $inactiveRelance utilisateurs inactifs depuis >7 jours.",
        'extendedProps' => ['type' => 'action', 'action' => 'send_reminders', 'count' => $inactiveRelance, 'icon' => 'envelope']
    ];
}

// 3. Rapport d'activité (J+2)
$aiCalendarEvents[] = [
    'title' => '📊 Rapport IA Hebdo', 
    'start' => date('Y-m-d', strtotime('+2 day')), 
    'className' => 'event-info', 
    'description' => 'Générer le rapport de croissance et analyser la rétention.',
    'extendedProps' => ['type' => 'info', 'action' => 'generate_report', 'icon' => 'chart-line']
];

// 4. Auto-Ban (J+3)
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

// 5. Prédiction Nutrition (J+4)
if ($usersWithoutGoal > 0) {
    $aiCalendarEvents[] = [
        'title' => '🍎 Défaut Objectifs (' . $usersWithoutGoal . ')', 
        'start' => date('Y-m-d', strtotime('+4 day')), 
        'className' => 'event-warning', 
        'description' => "$usersWithoutGoal utilisateurs n'ont pas défini d'objectif nutritionnel. Suggérer l'assistant IA.",
        'extendedProps' => ['type' => 'action', 'action' => 'nutrition_nudge', 'count' => $usersWithoutGoal, 'icon' => 'apple-whole']
    ];
}

// 6. Suggestion Optimisation (J+5)
$aiCalendarEvents[] = [
    'title' => '💡 Optimisation Pic (' . $peakDayName . ')', 
    'start' => date('Y-m-d', strtotime('+5 day')), 
    'className' => 'event-success', 
    'description' => "L'IA a détecté que $peakDayName est votre jour de pic d'inscription. Programmer une promo ?",
    'extendedProps' => ['type' => 'suggestion', 'action' => 'optimize_traffic', 'icon' => 'bolt']
];

?>

<style>
/* ═══ BASE & LAYOUT ═══ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { margin: 0; font-family: 'DM Sans', sans-serif; background: radial-gradient(circle at top, #0b1220, #020617); color: white; display: flex; min-height: 100vh; }

/* ═══ SIDEBAR ═══ */
.sidebar { width: 220px; position: fixed; left: 0; top: 0; bottom: 0; background: rgba(11,18,32,0.95); border-right: 1px solid rgba(0,230,118,0.15); padding: 24px 14px; display: flex; flex-direction: column; gap: 2px; z-index: 100; backdrop-filter: blur(20px); }
.sidebar-logo { display: flex; align-items: center; gap: 10px; padding: 0 8px 22px; border-bottom: 1px solid rgba(0,230,118,0.12); margin-bottom: 8px; }
.sidebar-logo-icon { width: 32px; height: 32px; background: linear-gradient(135deg, #00e676, #00c853); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.sidebar-logo-name { font-size: 16px; font-weight: 700; color: #fff; letter-spacing: -.3px; }
.sidebar-logo-name span { color: #00e676; }
.sidebar-section { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,0.2); padding: 12px 10px 5px; }
.sidebar-link { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 9px; text-decoration: none; font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.5); transition: all .18s; position: relative; }
.sidebar-link i { font-size: 13px; color: rgba(255,255,255,0.25); width: 16px; text-align: center; filter: none; transition: color .18s; }
.sidebar-link:hover { background: rgba(0,230,118,0.08); color: rgba(255,255,255,0.9); }
.sidebar-link:hover i { color: #00e676; }
.sidebar-link.active { background: rgba(0,230,118,0.12); color: #00e676; }
.sidebar-link.active i { color: #00e676; }
.sidebar-link.active::before { content: ''; position: absolute; left: 0; top: 20%; bottom: 20%; width: 3px; background: #00e676; border-radius: 0 3px 3px 0; }
.sidebar-notif { margin-left: auto; background: rgba(255,152,0,.2); color: #ffa726; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 20px; }
.sidebar-bottom { margin-top: auto; padding-top: 14px; border-top: 1px solid rgba(0,230,118,0.1); }

/* ═══ TOPBAR ═══ */
.topbar { position: fixed; top: 0; left: 220px; right: 0; height: 60px; background: rgba(11,18,32,0.98); border-bottom: 1px solid rgba(0,230,118,0.12); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; z-index: 90; backdrop-filter: blur(12px); }
.topbar-left { display: flex; align-items: center; gap: 12px; }
.topbar-page { font-size: 15px; font-weight: 700; color: #fff; letter-spacing: -.3px; }
.topbar-date { font-size: 12px; color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.04); padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.06); }
.topbar-right { display: flex; align-items: center; gap: 10px; }
.topbar-search { position: relative; }
.topbar-search i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.3); font-size: 13px; filter: none; }
#globalSearch { width: 280px; padding: 8px 14px 8px 36px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: white; font-family: 'DM Sans', sans-serif; font-size: 13px; outline: none; transition: border-color .2s, box-shadow .2s; }
#globalSearch:focus { border-color: #00e676; box-shadow: 0 0 0 3px rgba(0,230,118,0.12); }
#globalSearch::placeholder { color: rgba(255,255,255,0.28); }
#searchResults { position: absolute; top: calc(100% + 8px); left: 0; right: 0; background: #0d1a26; border: 1px solid rgba(0,230,118,0.2); border-radius: 12px; box-shadow: 0 16px 48px rgba(0,0,0,.5); z-index: 200; max-height: 320px; overflow-y: auto; display: none; }
#searchResults.show { display: block; }
.sr-item { display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-bottom: 1px solid rgba(255,255,255,0.04); cursor: pointer; transition: background .15s; }
.sr-item:last-child { border-bottom: none; }
.sr-item:hover { background: rgba(0,230,118,0.06); }
.sr-av { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, #00e676, #00bcd4); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #000; flex-shrink: 0; }
.sr-name  { font-size: 13px; font-weight: 500; }
.sr-email { font-size: 11px; color: rgba(255,255,255,.35); }
.sr-badge { margin-left: auto; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 20px; flex-shrink: 0; }
.sr-badge.active   { background: rgba(0,230,118,.15); color: #00e676; }
.sr-badge.inactive { background: rgba(255,152,0,.15);  color: #ffa726; }
.sr-badge.banned   { background: rgba(239,83,80,.15);  color: #ef5350; }
.sr-empty { padding: 20px; text-align: center; font-size: 13px; color: rgba(255,255,255,.3); }
.sr-loading { padding: 16px; text-align: center; font-size: 12px; color: rgba(255,255,255,.3); }
.topbar-icon { width: 36px; height: 36px; border-radius: 9px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; cursor: pointer; color: rgba(255,255,255,0.4); transition: all .18s; position: relative; }
.topbar-icon:hover { border-color: #00e676; color: #00e676; }
.topbar-icon i { font-size: 13px; filter: none; }
.notif-dot { position: absolute; top: 6px; right: 6px; width: 6px; height: 6px; border-radius: 50%; background: #ef5350; border: 1px solid #0b1220; }
.admin-chip { display: flex; align-items: center; gap: 8px; padding: 5px 12px 5px 5px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 30px; cursor: pointer; transition: border-color .18s; }
.admin-chip:hover { border-color: rgba(0,230,118,0.3); }
.admin-av { width: 26px; height: 26px; border-radius: 50%; background: linear-gradient(135deg,#00e676,#00c853); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #000; }
.admin-name { font-size: 12px; font-weight: 500; }

/* ═══ CONTENT ═══ */
.content-area { margin-left: 220px; padding-top: 60px; flex: 1; min-height: 100vh; }
.main { padding: 28px 32px 48px; }
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; }
.page-title { font-size: 22px; font-weight: 700; color: #00e676; text-shadow: 0 0 18px rgba(0,230,118,0.25); letter-spacing: .3px; }
.page-sub { font-size: 13px; color: rgba(255,255,255,.35); margin-top: 3px; }
.sec-title { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; color: rgba(0,230,118,0.6); margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
.sec-title::after { content:''; flex:1; height:1px; background:rgba(0,230,118,0.12); }
.kpi-row { display: grid; grid-template-columns: repeat(5,1fr); gap: 14px; margin-bottom: 26px; }
.kpi { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); border-radius: 14px; padding: 18px 18px; position: relative; overflow: hidden; transition: transform .2s, box-shadow .2s; }
.kpi:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,.4); }
.kpi::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; border-radius: 14px 14px 0 0; }
.kpi.g::before { background: linear-gradient(90deg,#00e676,#00c853); }
.kpi.b::before { background: linear-gradient(90deg,#2196f3,#1565c0); }
.kpi.o::before { background: linear-gradient(90deg,#ff9800,#e65100); }
.kpi.r::before { background: linear-gradient(90deg,#ef5350,#b71c1c); }
.kpi.p::before { background: linear-gradient(90deg,#ab47bc,#6a1b9a); }
.kpi-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
.kpi-icon { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 15px; }
.kpi.g .kpi-icon { background: rgba(0,230,118,0.12); }
.kpi.b .kpi-icon { background: rgba(33,150,243,0.12); }
.kpi.o .kpi-icon { background: rgba(255,152,0,0.12); }
.kpi.r .kpi-icon { background: rgba(239,83,80,0.12); }
.kpi.p .kpi-icon { background: rgba(171,71,188,0.12); }
.kpi-badge { font-size: 10px; font-weight: 600; padding: 3px 8px; border-radius: 20px; }
.kpi.g .kpi-badge { background: rgba(0,230,118,0.12); color: #00e676; }
.kpi.b .kpi-badge { background: rgba(33,150,243,0.12); color: #64b5f6; }
.kpi.o .kpi-badge { background: rgba(255,152,0,0.12);  color: #ffa726; }
.kpi.r .kpi-badge { background: rgba(239,83,80,0.12);  color: #ef9a9a; }
.kpi.p .kpi-badge { background: rgba(171,71,188,0.12); color: #ce93d8; }
.kpi-val { font-size: 30px; font-weight: 800; line-height: 1; letter-spacing: -1.5px; margin-bottom: 3px; }
.kpi.g .kpi-val { color: #00e676; }
.kpi.b .kpi-val { color: #64b5f6; }
.kpi.o .kpi-val { color: #ffa726; }
.kpi.r .kpi-val { color: #ef9a9a; }
.kpi.p .kpi-val { color: #ce93d8; }
.kpi-lbl { font-size: 12px; color: rgba(255,255,255,0.4); }
.kpi-sub { font-size: 11px; color: rgba(255,255,255,0.2); margin-top: 2px; }
.g3  { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; margin-bottom: 22px; }
.g2  { display: grid; grid-template-columns: 1fr 1fr;       gap: 18px; margin-bottom: 22px; }
.g21 { display: grid; grid-template-columns: 2fr 1fr;       gap: 18px; margin-bottom: 22px; }
.g31 { display: grid; grid-template-columns: 1fr 1fr 1fr;   gap: 18px; margin-bottom: 22px; }
.card { background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 20px 22px; }
.card-hd { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.card-title { font-size: 14px; font-weight: 600; color: #fff; display: flex; align-items: center; gap: 8px; }
.card-title i { font-size: 13px; color: #00e676; filter: none; }
.card-badge { font-size: 11px; font-weight: 500; padding: 3px 10px; border-radius: 20px; background: rgba(0,230,118,0.1); color: #00e676; border: 1px solid rgba(0,230,118,0.2); }
.card-badge.o { background: rgba(255,152,0,0.1);  color: #ffa726; border-color: rgba(255,152,0,0.2); }
.card-badge.r { background: rgba(239,83,80,0.1);  color: #ef9a9a; border-color: rgba(239,83,80,0.2); }
.prog-item { margin-bottom: 13px; }
.prog-item:last-child { margin-bottom: 0; }
.prog-top { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 5px; }
.prog-lbl { color: rgba(255,255,255,0.5); font-weight: 500; }
.prog-val { color: #fff; font-weight: 600; }
.prog-bar { height: 5px; background: rgba(255,255,255,0.06); border-radius: 3px; overflow: hidden; }
.prog-fill { height: 100%; border-radius: 3px; transition: width 1.1s ease; }
.ring-wrap { display: flex; flex-direction: column; align-items: center; padding: 6px 0 4px; }
.ring-svg  { transform: rotate(-90deg); }
.ring-bg   { fill: none; stroke: rgba(255,255,255,0.06); }
.ring-fill { fill: none; stroke: #00e676; stroke-linecap: round; transition: stroke-dashoffset 1.4s ease; filter: drop-shadow(0 0 6px rgba(0,230,118,0.5)); }
.ring-center { text-align: center; margin-top: 8px; }
.ring-pct { font-size: 32px; font-weight: 800; color: #fff; letter-spacing: -2px; }
.ring-sub { font-size: 12px; color: rgba(255,255,255,0.35); }
.u-table { width: 100%; border-collapse: collapse; }
.u-table th { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: rgba(0,230,118,0.7); text-align: left; padding: 0 12px 10px 0; border-bottom: 1px solid rgba(0,230,118,0.12); }
.u-table td { padding: 10px 12px 10px 0; font-size: 13px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
.u-table tr:last-child td { border-bottom: none; }
.u-table tr:hover td { background: rgba(0,230,118,0.03); }
.u-badge { display: flex; align-items: center; gap: 10px; }
.u-av { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg,#00e676,#00bcd4); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #000; flex-shrink: 0; }
.u-name  { font-size: 13px; font-weight: 500; }
.u-email { font-size: 11px; color: rgba(255,255,255,0.35); }
.status-dot { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; white-space: nowrap; }
.status-dot::before { content:''; width:5px; height:5px; border-radius:50%; }
.status-dot.active   { background:rgba(0,230,118,0.15); color:#00e676; border:1px solid rgba(0,230,118,0.3); }
.status-dot.active::before   { background:#00e676; box-shadow:0 0 6px #00e676; }
.status-dot.inactive { background:rgba(255,152,0,0.15);  color:#ffa726; border:1px solid rgba(255,152,0,0.3); }
.status-dot.inactive::before { background:#ffa726; box-shadow:0 0 6px #ffa726; animation:blinkOrange 1.5s infinite; }
.status-dot.banned   { background:rgba(239,83,80,0.15);  color:#ef5350; border:1px solid rgba(239,83,80,0.3); }
.status-dot.banned::before   { background:#ef5350; box-shadow:0 0 6px #ef5350; }
@keyframes blinkOrange { 0%,100%{opacity:1;} 50%{opacity:.3;} }

.ai-alerts-section { margin-bottom: 22px; }
.ai-alert-item { display: flex; align-items: flex-start; gap: 14px; padding: 14px 16px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; margin-bottom: 10px; transition: all .2s; position: relative; overflow: hidden; }
.ai-alert-item:hover { background: rgba(255,255,255,0.04); border-color: rgba(0,230,118,0.15); }
.ai-alert-item::before { content:''; position:absolute; left:0; top:0; bottom:0; width:3px; }
.ai-alert-item.critical::before { background:#ef5350; }
.ai-alert-item.warning::before  { background:#ffa726; }
.ai-alert-item.success::before  { background:#00e676; }
.ai-alert-item.info::before     { background:#64b5f6; }
.ai-alert-item.danger::before   { background:#ef5350; }
.ai-alert-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; }
.ai-alert-item.critical .ai-alert-icon { background:rgba(239,83,80,0.12); color:#ef5350; }
.ai-alert-item.warning .ai-alert-icon  { background:rgba(255,152,0,0.12);  color:#ffa726; }
.ai-alert-item.success .ai-alert-icon  { background:rgba(0,230,118,0.12);  color:#00e676; }
.ai-alert-item.info .ai-alert-icon     { background:rgba(33,150,243,0.12);  color:#64b5f6; }
.ai-alert-item.danger .ai-alert-icon   { background:rgba(239,83,80,0.12);  color:#ef5350; }
.ai-alert-content { flex:1; }
.ai-alert-title  { font-size:13px; font-weight:600; color:#fff; margin-bottom:2px; }
.ai-alert-desc   { font-size:12px; color:rgba(255,255,255,0.4); line-height:1.5; }
.ai-alert-tag { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; padding:2px 8px; border-radius:10px; flex-shrink:0; margin-top:2px; }
.ai-alert-item.critical .ai-alert-tag { background:rgba(239,83,80,0.15); color:#ef9a9a; }
.ai-alert-item.warning .ai-alert-tag  { background:rgba(255,152,0,0.15);  color:#ffa726; }
.ai-alert-item.success .ai-alert-tag  { background:rgba(0,230,118,0.15);  color:#69f0ae; }
.ai-alert-item.info .ai-alert-tag     { background:rgba(33,150,243,0.15);  color:#90caf9; }
.ai-alert-item.danger .ai-alert-tag   { background:rgba(239,83,80,0.15);  color:#ef9a9a; }

.ai-insight-card { background: linear-gradient(135deg, rgba(0,230,118,0.05), rgba(33,150,243,0.05)); border: 1px solid rgba(0,230,118,0.12); border-radius: 16px; padding: 20px 22px; margin-bottom: 22px; position: relative; overflow: hidden; }
.ai-insight-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background: linear-gradient(90deg, #00e676, #2196f3, #00e676); }
.ai-insight-header { display:flex; align-items:center; gap:10px; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid rgba(0,230,118,0.1); }
.ai-insight-icon { width:32px; height:32px; border-radius:8px; background: linear-gradient(135deg, #00e676, #00c853); display:flex; align-items:center; justify-content:center; font-size:14px; color:#000; }
.ai-insight-title { font-size:14px; font-weight:700; color:#00e676; }
.ai-insight-sub   { font-size:11px; color:rgba(255,255,255,0.3); }

/* ═══ CALENDAR STYLES ═══ */
.fc { color: rgba(255,255,255,0.7) !important; font-family: 'DM Sans', sans-serif !important; }
.fc-toolbar-title { font-size: 14px !important; font-weight: 700 !important; color: #fff !important; }
.fc-button { background: rgba(255,255,255,0.06) !important; border: 1px solid rgba(255,255,255,0.1) !important; color: rgba(255,255,255,0.6) !important; border-radius: 8px !important; font-size: 11px !important; font-family: 'DM Sans', sans-serif !important; padding: 4px 10px !important; box-shadow: none !important; }
.fc-button:hover, .fc-button-active { background: rgba(0,230,118,0.12) !important; color: #00e676 !important; border-color: rgba(0,230,118,0.3) !important; }
.fc-daygrid-day-number  { color: rgba(255,255,255,0.4) !important; font-size: 11px; }
.fc-col-header-cell-cushion { color: rgba(255,255,255,0.25) !important; font-size: 10px; font-weight: 600; text-transform: uppercase; }
.fc-daygrid-day.fc-day-today { background: rgba(0,230,118,0.06) !important; }
.fc-event { border: none !important; border-radius: 5px !important; padding: 3px 5px !important; font-size: 11px !important; cursor: pointer; }
.event-ai       { background: rgba(0,230,118,0.7) !important; color: #000 !important; }
.event-warning  { background: rgba(255,152,0,0.8) !important; color: #000 !important; }
.event-info     { background: rgba(33,150,243,0.8) !important; color: #fff !important; }
.event-danger   { background: rgba(239,83,80,0.8) !important; color: #fff !important; }
.event-success  { background: rgba(0,230,118,0.6) !important; color: #fff !important; }
.event-suggestion { background: rgba(171,71,188,0.8) !important; color: #fff !important; }
.fc-scrollgrid, .fc-scrollgrid-section > td { border-color: rgba(255,255,255,0.06) !important; }
.fc-daygrid-body, .fc-scrollgrid-sync-table  { border-color: rgba(255,255,255,0.06) !important; }

.btn-g { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: linear-gradient(90deg,#00e676,#00c853); border: none; border-radius: 9px; color: #000; font-size: 13px; font-weight: 700; font-family: 'DM Sans', sans-serif; cursor: pointer; text-decoration: none; transition: transform .18s, box-shadow .18s; }
.btn-g:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,230,118,0.3); color: #000; }
.btn-outline { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: transparent; color: rgba(255,255,255,0.5); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; font-size: 12px; font-weight: 500; font-family: 'DM Sans', sans-serif; cursor: pointer; text-decoration: none; transition: all .15s; }
.btn-outline:hover { border-color: #00e676; color: #00e676; }
.spin { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 1200px) { .kpi-row { grid-template-columns: repeat(3,1fr); } .g3 { grid-template-columns: repeat(2,1fr); } .g21, .g31 { grid-template-columns: 1fr; } }
@media (max-width: 768px) { .sidebar { display: none; } .content-area { margin-left: 0; } .topbar { left: 0; } .kpi-row, .g3, .g2, .g21, .g31 { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-icon">🌿</div>
    <div class="sidebar-logo-name">Eco<span>Nutri</span></div>
  </div>
  <div class="sidebar-section">Principal</div>
  <a href="index.php?url=Admin/dashboard" class="sidebar-link active"><i class="fa fa-gauge"></i>Dashboard</a>
  <a href="index.php?url=Admin/users" class="sidebar-link"><i class="fa fa-users"></i>Utilisateurs</a>
  <div class="sidebar-section">Contenu</div>
  <a href="#" class="sidebar-link"><i class="fa fa-bowl-food"></i>Recettes</a>
  <a href="#" class="sidebar-link"><i class="fa fa-chart-pie"></i>Nutrition</a>
  <div class="sidebar-section">Système</div>
  <a href="#" class="sidebar-link"><i class="fa fa-bell"></i>Alertes IA <?php if ($inactiveUsers + $bannedUsers > 0): ?><span class="sidebar-notif"><?= $inactiveUsers + $bannedUsers ?></span><?php endif; ?></a>
  <a href="#" class="sidebar-link"><i class="fa fa-robot"></i>Intelligence</a>
  <a href="#" class="sidebar-link"><i class="fa fa-gear"></i>Paramètres</a>
  <div class="sidebar-bottom">
    <a href="index.php?url=User/logout" class="sidebar-link" style="color:#ef9a9a;"><i class="fa fa-right-from-bracket" style="color:#ef9a9a;"></i>Déconnexion</a>
  </div>
</aside>

<!-- ══ TOPBAR ══ -->
<div class="topbar">
  <div class="topbar-left">
    <div class="topbar-page">Dashboard</div>
    <div class="topbar-date"><?= date('d/m/Y') ?> — <?= $greeting ?></div>
  </div>
  <div class="topbar-right">
    <div class="topbar-search">
      <i class="fa fa-magnifying-glass"></i>
      <input type="text" id="globalSearch" placeholder="Rechercher un utilisateur...">
      <div id="searchResults"></div>
    </div>
    <div class="topbar-icon"><i class="fa fa-bell"></i><?php if ($inactiveUsers > 0): ?><div class="notif-dot"></div><?php endif; ?></div>
    <div class="topbar-icon"><i class="fa fa-gear"></i></div>
    <div class="admin-chip"><div class="admin-av">A</div><div class="admin-name">Admin</div></div>
  </div>
</div>

<!-- ══ CONTENT ══ -->
<div class="content-area">
<div class="main">

  <div class="page-header">
    <div>
      <div class="page-title">📊 Vue d'ensemble</div>
      <div class="page-sub"><?= $greeting ?> — tableau de bord intelligent en temps réel</div>
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
      <div style="text-align:center; padding:10px 0;">
        <div style="font-size:28px; font-weight:800; color:#00e676;"><?= $activePct ?>%</div>
        <div style="font-size:11px; color:rgba(255,255,255,0.4); margin-top:4px;">Taux d'activation</div>
        <div style="font-size:10px; color:<?= $activePct >= 80 ? '#69f0ae' : ($activePct >= 50 ? '#ffa726' : '#ef9a9a') ?>; margin-top:2px;">
          <?= $activePct >= 80 ? '✓ Excellent' : ($activePct >= 50 ? '⚠ Moyen' : '✗ Critique') ?>
        </div>
      </div>
      <div style="text-align:center; padding:10px 0;">
        <div style="font-size:28px; font-weight:800; color:#64b5f6;"><?= round(array_sum($weekCounts)) ?></div>
        <div style="font-size:11px; color:rgba(255,255,255,0.4); margin-top:4px;">Inscriptions cette semaine</div>
        <div style="font-size:10px; color:#90caf9; margin-top:2px;">Tendance: <?= array_sum($weekCounts) > 0 ? '📈 Active' : '📉 Stable' ?></div>
      </div>
      <div style="text-align:center; padding:10px 0;">
        <div style="font-size:28px; font-weight:800; color:#ffa726;"><?= $inactiveUsers + $bannedUsers ?></div>
        <div style="font-size:11px; color:rgba(255,255,255,0.4); margin-top:4px;">Alertes à traiter</div>
        <div style="font-size:10px; color:<?= ($inactiveUsers + $bannedUsers) > 0 ? '#ffa726' : '#69f0ae' ?>; margin-top:2px;">
          <?= ($inactiveUsers + $bannedUsers) > 0 ? '⚠ Action requise' : '✓ Tout est OK' ?>
        </div>
      </div>
    </div>
  </div>

  <!-- AI ALERTS -->
  <div class="sec-title">Alertes IA intelligentes</div>
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
  <div class="sec-title">Indicateurs clés</div>
  <div class="kpi-row">
    <div class="kpi g"><div class="kpi-top"><div class="kpi-icon">👥</div><span class="kpi-badge">total</span></div><div class="kpi-val"><?= number_format($totalUsers) ?></div><div class="kpi-lbl">Utilisateurs</div><div class="kpi-sub">inscrits</div></div>
    <div class="kpi b"><div class="kpi-top"><div class="kpi-icon">✅</div><span class="kpi-badge"><?= $activePct ?>%</span></div><div class="kpi-val"><?= $activeUsers ?></div><div class="kpi-lbl">Comptes actifs</div><div class="kpi-sub">sur <?= $totalUsers ?></div></div>
    <div class="kpi o"><div class="kpi-top"><div class="kpi-icon">⏳</div><span class="kpi-badge">attente</span></div><div class="kpi-val"><?= $inactiveUsers ?></div><div class="kpi-lbl">Inactifs</div><div class="kpi-sub">à activer</div></div>
    <div class="kpi r"><div class="kpi-top"><div class="kpi-icon">🚫</div><span class="kpi-badge">bannis</span></div><div class="kpi-val"><?= $bannedUsers ?></div><div class="kpi-lbl">Bannis</div><div class="kpi-sub">comptes bloqués</div></div>
    <div class="kpi p"><div class="kpi-top"><div class="kpi-icon">📈</div><span class="kpi-badge">taux</span></div><div class="kpi-val"><?= $activePct ?>%</div><div class="kpi-lbl">Taux activation</div><div class="kpi-sub">objectif : 90%</div></div>
  </div>

  <!-- GRAPHS -->
  <div class="sec-title">Analyses & Statistiques</div>
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
      <div style="margin-top:16px; display:flex; gap:12px; flex-wrap:wrap;">
        <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:rgba(255,255,255,0.4);"><span style="width:8px;height:8px;border-radius:50%;background:#00e676;display:inline-block;"></span> Actifs: <?= $activeUsers ?></span>
        <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:rgba(255,255,255,0.4);"><span style="width:8px;height:8px;border-radius:50%;background:#ffa726;display:inline-block;"></span> Inactifs: <?= $inactiveUsers ?></span>
        <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:rgba(255,255,255,0.4);"><span style="width:8px;height:8px;border-radius:50%;background:#ef5350;display:inline-block;"></span> Bannis: <?= $bannedUsers ?></span>
      </div>
    </div>
    <div class="card">
      <div class="card-hd"><div class="card-title"><i class="fa fa-person-running"></i> Niveaux d'activité</div></div>
      <?php
      $actColors = ['#00e676','#64b5f6','#ffa726','#ce93d8','#ef9a9a'];
      if(!empty($activiteRaw)):
        foreach($activiteRaw as $ai => $actRow):
          $actPct = $totalUsers > 0 ? round($actRow['cnt']/$totalUsers*100) : 0;
          $actCol = $actColors[$ai % count($actColors)];
      ?>
      <div class="prog-item">
        <div class="prog-top"><span class="prog-lbl"><?= htmlspecialchars($actRow['activite']) ?></span><span class="prog-val" style="color:<?= $actCol ?>"><?= $actRow['cnt'] ?> <small style="color:rgba(255,255,255,.25);font-weight:400;">(<?= $actPct ?>%)</small></span></div>
        <div class="prog-bar"><div class="prog-fill" style="width:<?= $actPct ?>%;background:<?= $actCol ?>;"></div></div>
      </div>
      <?php endforeach; ?>
      <?php else: ?>
      <p style="font-size:13px;color:rgba(255,255,255,.3);text-align:center;padding:20px 0;">Aucune donnée</p>
      <?php endif; ?>
    </div>
    <div class="card">
      <div class="card-hd"><div class="card-title"><i class="fa fa-circle-check"></i> Taux d'activation</div></div>
      <div class="ring-wrap">
        <svg class="ring-svg" width="120" height="120" viewBox="0 0 120 120">
          <circle class="ring-bg"   cx="60" cy="60" r="48" stroke-width="9"/>
          <circle class="ring-fill" cx="60" cy="60" r="48" stroke-width="9" stroke-dasharray="301.59" stroke-dashoffset="<?= 301.59 * (1 - $activePct/100) ?>"/>
        </svg>
        <div class="ring-center"><div class="ring-pct"><?= $activePct ?>%</div><div class="ring-sub"><?= $activeUsers ?> / <?= $totalUsers ?> actifs</div></div>
      </div>
      <div style="margin-top:16px;">
        <div class="prog-item"><div class="prog-top"><span class="prog-lbl">Actifs <span style="color:#00e676;">●</span></span><span class="prog-val"><?= $activeUsers ?></span></div><div class="prog-bar"><div class="prog-fill" style="width:<?= $activePct ?>%;background:#00e676;"></div></div></div>
        <div class="prog-item"><div class="prog-top"><span class="prog-lbl">Inactifs <span style="color:#ffa726;">●</span></span><span class="prog-val"><?= $inactiveUsers ?></span></div><div class="prog-bar"><div class="prog-fill" style="width:<?= $totalUsers>0?round($inactiveUsers/$totalUsers*100):0 ?>%;background:#ffa726;"></div></div></div>
        <div class="prog-item"><div class="prog-top"><span class="prog-lbl">Bannis <span style="color:#ef5350;">●</span></span><span class="prog-val"><?= $bannedUsers ?></span></div><div class="prog-bar"><div class="prog-fill" style="width:<?= $totalUsers>0?round($bannedUsers/$totalUsers*100):0 ?>%;background:#ef5350;"></div></div></div>
      </div>
    </div>
  </div>

  <!-- LAST USERS + CALENDAR -->
  <div class="sec-title">Activité récente & Calendrier IA</div>
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
            <td style="font-size:12px;color:rgba(255,255,255,.45);"><?= htmlspecialchars($u['objectif'] ?? '—') ?></td>
            <td><span class="status-dot <?= $status ?>"><?= $sl ?></span></td>
            <td><a href="index.php?url=Admin/users" class="btn-outline" style="padding:4px 10px;font-size:11px;"><i class="fa fa-pen" style="font-size:10px;"></i> Éditer</a></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($lastUsers)): ?>
          <tr><td colspan="4" style="text-align:center;color:rgba(255,255,255,.3);padding:20px 0;font-size:13px;">Aucun utilisateur</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <div class="card-hd">
        <div class="card-title">
          <i class="fa fa-calendar"></i> Calendrier IA 
          <span style="font-size:10px; color:rgba(0,230,118,0.5); margin-left:6px;">automatique & interactif</span>
        </div>
      </div>
      <div id="calendar"></div>
      <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap; font-size:10px; color:rgba(255,255,255,0.4);">
        <span><span style="width:8px;height:8px;border-radius:3px;background:rgba(0,230,118,0.7);display:inline-block;margin-right:4px;"></span> IA</span>
        <span><span style="width:8px;height:8px;border-radius:3px;background:rgba(255,152,0,0.8);display:inline-block;margin-right:4px;"></span> Rapport</span>
        <span><span style="width:8px;height:8px;border-radius:3px;background:rgba(239,83,80,0.8);display:inline-block;margin-right:4px;"></span> Danger</span>
        <span><span style="width:8px;height:8px;border-radius:3px;background:rgba(33,150,243,0.8);display:inline-block;margin-right:4px;"></span> Info</span>
        <span><span style="width:8px;height:8px;border-radius:3px;background:rgba(171,71,188,0.8);display:inline-block;margin-right:4px;"></span> Suggestion</span>
      </div>
    </div>
  </div>

</div>
</div>

<script>

Chart.defaults.color = 'rgba(255,255,255,0.35)';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
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
      backgroundColor: 'rgba(0,230,118,0.2)', 
      borderColor: '#00e676', 
      borderWidth: 1.5, 
      borderRadius: 6, 
      borderSkipped: false 
    }] 
  },
  options: { 
    responsive: true, 
    plugins: { legend: { display: false } }, 
    scales: { x: { grid: { display: false } }, y: { grid: { color: 'rgba(255,255,255,0.04)' }, beginAtZero: true, ticks: { stepSize: 1 } } } 
  }
});

new Chart(document.getElementById('chartObj'), {
  type: 'doughnut', 
  data: { 
    labels: <?= json_encode(array_map(fn($l) => $l ?: 'Non défini', $objLabels)) ?>, 
    datasets: [{ 
      data: <?= json_encode($objCounts) ?>, 
      backgroundColor: ['#00e676','#64b5f6','#ffa726','#ce93d8','#ef9a9a'], 
      borderWidth: 3, borderColor: '#0d1a26', hoverOffset: 8 
    }] 
  },
  options: { cutout: '62%', plugins: { legend: { position: 'bottom', labels: { color: 'rgba(255,255,255,0.45)', padding: 12, usePointStyle: true } } } }
});

new Chart(document.getElementById('chartMal'), {
  type: 'bar', 
  data: { 
    labels: <?= json_encode(count($malLabels) > 0 ? $malLabels : ['Aucune donnée']) ?>, 
    datasets: [{ 
      data: <?= json_encode(count($malCounts) > 0 ? $malCounts : [0]) ?>, 
      backgroundColor: ['#ffa726','#ef9a9a','#ce93d8','#64b5f6','#00e676','rgba(255,255,255,.2)'], 
      borderRadius: 5, borderSkipped: false 
    }] 
  },
  options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { grid: { color: 'rgba(255,255,255,0.04)' }, beginAtZero: true }, y: { grid: { display: false } } } }
});

new Chart(document.getElementById('chartStatus'), {
  type: 'doughnut', 
  data: { 
    labels: <?= json_encode($statusData['labels']) ?>, 
    datasets: [{ 
      data: <?= json_encode($statusData['counts']) ?>, 
      backgroundColor: ['#00e676', '#ffa726', '#ef5350'], 
      borderWidth: 3, borderColor: '#0d1a26', hoverOffset: 8 
    }] 
  },
  options: { cutout: '58%', plugins: { legend: { display: false } } }
});

/* ═══════════════════════════════════════════════ */
/* ════ CALENDRIER IA → VRAI BACKEND ════ */
/* ═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl) return;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 350,
    headerToolbar: { left: 'prev,next', center: 'title', right: 'today' },
    events: <?= json_encode($aiCalendarEvents) ?>,
    
    // 👉 CLIC SUR ÉVÉNEMENT → SWEETALERT → FETCH → BACKEND
    eventClick: async function(info) {
      const ev = info.event;
      const props = ev.extendedProps;

      // Cas 1: Auto-Ban (danger)
      if (props.type === 'danger') {
        const result = await Swal.fire({
          title: '🚫 Auto-Ban Requise',
          html: `<div style="text-align:left; font-size:14px; color:#333;">
            <p><strong>${props.count || 0} comptes</strong> inactifs >30 jours.</p>
            <p style="color:#666; font-size:12px;">${props.description || ''}</p>
            <hr><p style="color:#d32f2f; font-size:12px;">⚠️ Irréversible.</p>
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

      // Cas 2: Actions IA / Suggestions
      else if (props.type === 'action' || props.type === 'suggestion') {
        const result = await Swal.fire({
          title: '⚡ Action IA Suggérée',
          html: `<div style="text-align:left; font-size:14px; color:#333;">
            <p><strong>${ev.title}</strong></p>
            <p style="color:#666; font-size:12px;">${props.description || ''}</p>
          </div>`,
          icon: 'info',
          showCancelButton: true,
          confirmButtonColor: '#00b96b',
          confirmButtonText: 'Exécuter',
          cancelButtonText: 'Plus tard'
        });
        if (result.isConfirmed) await executeAction(props.action, props.count, ev);
      }

      // Cas 3: Rapport IA
      else if (props.action === 'generate_report') {
        const result = await Swal.fire({
          title: '📊 Rapport IA Hebdomadaire',
          text: 'Générer le rapport statistique réel depuis MySQL ?',
          icon: 'info',
          showCancelButton: true,
          confirmButtonColor: '#00b96b',
          confirmButtonText: 'Générer',
          cancelButtonText: 'Annuler'
        });
        if (result.isConfirmed) await executeAction('generate_report', 0, ev);
      }

      // Cas 4: Info simple
      else {
        Swal.fire({ title: ev.title, text: props.description, icon: 'info', confirmButtonColor: '#00b96b' });
      }
    }
  });

  calendar.render();
});

/* ═══════════════════════════════════════════════ */
/* ════ FONCTION UNIVERSELLE → BACKEND PHP ════ */
/* ═══════════════════════════════════════════════ */
async function executeAction(action, count, event) {
  const endpoint = action === 'generate_report' 
    ? '/ProjetWeb-User/Controllers/Admin/generate-report.php'
    : '/ProjetWeb-User/Controllers/Admin/execute-action.php';

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

    const text = await response.text(); // 👈 IMPORTANT

    let data;
    try {
      data = JSON.parse(text);
    } catch (e) {
      console.error("❌ Réponse backend NON JSON :", text);
      Swal.fire({
        icon: 'error',
        title: 'Erreur backend',
        html: `<pre style="text-align:left;font-size:12px">${text}</pre>`
      });
      return;
    }

    if (!data.success) {
      Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: data.message || 'Erreur inconnue'
      });
      return;
    }

    event.setProp('className', 'event-success');
    event.setProp('title', '✅ ' + (event.title || 'Action'));

    if (data.stats) updateKPIs(data.stats);

    Swal.fire({
      icon: 'success',
      title: 'Succès',
      html: buildSuccessHTML(action, data)
    });

  } catch (error) {
    console.error(error);
    Swal.fire({
      icon: 'error',
      title: 'Erreur réseau',
      text: error.message
    });
  }
}
/* ═══════════════════════════════════════════════ */
/* ════ UTILITAIRES ════ */
/* ═══════════════════════════════════════════════ */
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
    if (data.suggestion) html += `<p style="font-size:12px; color:#00b96b;">💡 ${data.suggestion}</p>`;
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
    setTimeout(() => { el.style.width = w; }, 120);
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
  searchResults.innerHTML = '<div class="sr-loading"><i class="fa fa-circle-notch spin" style="filter:none;color:#00e676;margin-right:6px;"></i>Recherche...</div>';
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
        if (data.total > 6) searchResults.innerHTML += `<div style="padding:10px 16px;text-align:center;border-top:1px solid rgba(255,255,255,0.04);"><a href="/ProjetWeb-User/index.php?url=Admin/users&search=${encodeURIComponent(q)}" style="font-size:12px;color:#00e676;text-decoration:none;">Voir les ${data.total} résultats →</a></div>`;
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
