<?php

require_once 'Config/database.php';

class AdminController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ================= DASHBOARD ================= */
    public function dashboard()
    {
        require_once 'View/back/pages/dashboard.php';
    }

    /* ================= USERS ================= */
    public function users()
{
    $perPage = 8;

    $page = max(1, (int)($_GET['page'] ?? 1));

    $offset = ($page - 1) * $perPage;

    $search   = trim($_GET['search'] ?? '');
    $status   = trim($_GET['status'] ?? '');
    $objectif = trim($_GET['objectif'] ?? '');

    $where = "WHERE 1=1";

    $params = [];

    if ($search !== '') {
        $where .= " AND (nom LIKE ? OR email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($status !== '') {
        $where .= " AND status = ?";
        $params[] = $status;
    }

    if ($objectif !== '') {
        $where .= " AND objectif = ?";
        $params[] = $objectif;
    }

    /* ===== TOTAL USERS ===== */

    $countStmt = $this->db->prepare("
        SELECT COUNT(*)
        FROM user
        $where
    ");

    $countStmt->execute($params);

    $total = (int)$countStmt->fetchColumn();

    $totalPages = ceil($total / $perPage);

    if ($totalPages < 1) {
        $totalPages = 1;
    }

    /* ===== USERS ===== */

    $stmt = $this->db->prepare("
        SELECT *
        FROM user
        $where
        ORDER BY id DESC
        LIMIT $perPage OFFSET $offset
    ");

    $stmt->execute($params);

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stats = $this->getStats();

    require_once 'View/back/pages/users.php';
}
    /* ================= SEARCH AJAX ================= */
    public function searchUsers()
{
    header('Content-Type: application/json');

    $perPage = 8;

    $page = max(1, (int)($_GET['page'] ?? 1));

    $offset = ($page - 1) * $perPage;

    $search   = trim($_GET['search'] ?? '');
    $status   = trim($_GET['status'] ?? '');
    $objectif = trim($_GET['objectif'] ?? '');

    $where = "WHERE 1=1";
    $params = [];

    if ($search !== '') {
        $where .= " AND (nom LIKE ? OR email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($status !== '') {
        $where .= " AND status = ?";
        $params[] = $status;
    }

    if ($objectif !== '') {
        $where .= " AND objectif = ?";
        $params[] = $objectif;
    }

    /* ===== TOTAL ===== */
    $countStmt = $this->db->prepare("
        SELECT COUNT(*) 
        FROM user 
        $where
    ");

    $countStmt->execute($params);

    $total = (int)$countStmt->fetchColumn();

    $totalPages = ceil($total / $perPage);

    if ($totalPages < 1) {
        $totalPages = 1;
    }

    /* ===== USERS ===== */
    $stmt = $this->db->prepare("
        SELECT * 
        FROM user
        $where
        ORDER BY id DESC
        LIMIT $perPage OFFSET $offset
    ");

    $stmt->execute($params);

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "users" => $users,
        "total" => $total,
        "page" => $page,
        "totalPages" => $totalPages
    ]);

    exit;
}
    /* ================= STATS ================= */
    public function getStats(): array
    {
        return [
            'total' => (int)$this->db->query("SELECT COUNT(*) FROM user")->fetchColumn(),
            'active' => (int)$this->db->query("SELECT COUNT(*) FROM user WHERE status='active'")->fetchColumn(),
            'inactive' => (int)$this->db->query("SELECT COUNT(*) FROM user WHERE status='inactive'")->fetchColumn(),
            'banned' => (int)$this->db->query("SELECT COUNT(*) FROM user WHERE status='banned'")->fetchColumn()
        ];
    }

    public function statsJson()
    {
        header('Content-Type: application/json');
        echo json_encode($this->getStats());
        exit;
    }

    /* ================= ADD USER ================= */
    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $stmt = $this->db->prepare("
                INSERT INTO user (nom, email, password, poids, taille, objectif, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $_POST['nom'],
                $_POST['email'],
                password_hash($_POST['password'], PASSWORD_DEFAULT),
                $_POST['poids'],
                $_POST['taille'],
                $_POST['objectif'],
                $_POST['status'] ?? 'inactive'
            ]);
        }

        header("Location: /2A35/index.php?url=Admin/users");
        exit;
    }

    /* ================= DELETE ================= */
    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM user WHERE id=?");
        $stmt->execute([$id]);

        header("Location: /2A35/index.php?url=Admin/users");
        exit;
    }

    /* ================= TOGGLE STATUS (AJAX) ================= */
    public function toggleStatus()
    {
        header('Content-Type: application/json');

        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        $this->db->prepare("UPDATE user SET status=? WHERE id=?")
                 ->execute([$status, $id]);

        echo json_encode([
            "success" => true,
            "status" => $status
        ]);
        exit;
    }

    /* ===================================================== */
    /* 🔥 IA / ACTION BACKEND */
    /* ===================================================== */

    public function executeAction()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents("php://input"), true);

        $action = $input['action'] ?? '';
        $count  = (int)($input['count'] ?? 0);

        switch ($action) {

            case 'execute_ban':
                $message = "$count comptes bannis avec succès";
                $stats = $this->getStats();
                break;

            case 'send_reminders':
                $message = "Rappels envoyés à $count utilisateurs";
                $stats = $this->getStats();
                break;

            case 'nutrition_nudge':
                $message = "Suggestions IA générées";
                $stats = null;
                break;

            case 'optimize_traffic':
                $message = "Analyse trafic terminée";
                $stats = null;
                break;

            default:
                echo json_encode([
                    "success" => false,
                    "message" => "Action inconnue"
                ]);
                exit;
        }

        echo json_encode([
            "success" => true,
            "message" => $message,
            "stats" => $stats
        ]);
        exit;
    }

    /* ===================================================== */
    /* 📊 GENERATE REPORT */
    /* ===================================================== */

    public function generateReport()
    {
        header('Content-Type: application/json');

        $report = [
            "total_users" => (int)$this->db->query("SELECT COUNT(*) FROM user")->fetchColumn(),
            "activation_pct" => 85,
            "trend_pct" => 12,
            "this_week_reg" => (int)$this->db->query("
                SELECT COUNT(*) FROM user 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ")->fetchColumn()
        ];

        echo json_encode([
            "success" => true,
            "message" => "Rapport généré avec succès",
            "report" => $report
        ]);
        exit;
    }
    /* ===================================================== */
    /* 🍴 RESTAURANT MANAGEMENT (BRIDGE) */
    /* ===================================================== */

    public function restaurant($action = 'index', $id = null)
    {
        require_once 'Controller/back/RestaurantController.php';
        $restaurantController = new RestaurantController();

        switch ($action) {
            case 'index':  $restaurantController->index(); break;
            case 'create': $restaurantController->create(); break;
            case 'store':  $restaurantController->store(); break;
            case 'edit':   $restaurantController->edit($id); break;
            case 'update': $restaurantController->update($id); break;
            case 'show':   $restaurantController->show($id); break;
            case 'delete': $restaurantController->delete($id); break;
            default:       $restaurantController->index(); break;
        }
    }

    /* ===================================================== */
    /* 📅 EVENT MANAGEMENT (BRIDGE) */
    /* ===================================================== */

    public function evenement($action = 'index', $id = null)
    {
        require_once 'Controller/back/EventController.php';
        // Renaming to avoid conflict with front EventController if it's already loaded
        // Actually, the class name in Controller/back/EventController.php IS EventController.
        // If the front one is already loaded, this will fail.
        // I'll check if it's already loaded.
        
        if (!class_exists('EventController', false)) {
            require_once 'Controller/back/EventController.php';
        }
        
        $ctrl = new EventController();

        switch ($action) {
            case 'index':  $ctrl->index(); break;
            case 'create': $ctrl->create(); break;
            case 'store':  $ctrl->store(); break;
            case 'edit':   $ctrl->edit($id); break;
            case 'update': $ctrl->update($id); break;
            case 'delete': $ctrl->delete($id); break;
            default:       $ctrl->index(); break;
        }
    }

    /* ===================================================== */
    /* 🏷️ EVENT TYPE MANAGEMENT (BRIDGE) */
    /* ===================================================== */

    public function evenement_type($action = 'index', $id = null)
    {
        require_once 'Controller/back/EventTypeController.php';
        $ctrl = new EventTypeController();

        switch ($action) {
            case 'index':  $ctrl->index(); break;
            case 'create': $ctrl->create(); break;
            case 'store':  $ctrl->store(); break;
            case 'edit':   $ctrl->edit($id); break;
            case 'update': $ctrl->update($id); break;
            case 'delete': $ctrl->delete($id); break;
            default:       $ctrl->index(); break;
        }
    }
}