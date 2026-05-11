<?php
require_once 'Model/Restaurant.php';
require_once 'Model/Meal.php';
require_once 'Config/database.php';

class RestaurantFrontController {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // GET /Restaurant
    public function index(): void {
        $search  = trim($_GET['search'] ?? '');
        $type    = trim($_GET['type']   ?? '');
        $perPage = 6;
        $page    = max(1, (int)($_GET['page'] ?? 1));

        $where  = [];
        $params = [];
        if ($search) { $where[] = "(nom LIKE ? OR adresse LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($type)   { $where[] = "type_cuisine = ?"; $params[] = $type; }
        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM restaurant $whereSQL");
        $stmtCount->execute($params);
        $total      = (int)$stmtCount->fetchColumn();
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        $stmt = $this->db->prepare("SELECT * FROM restaurant $whereSQL ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);
        $restaurants = $stmt->fetchAll();

        // Ajouter les notes
        $restaurants = $this->attachNotes($restaurants);

        require_once 'View/front/restaurant.php';
    }

    // GET /Restaurant/search?q=...&type=... (AJAX)
    public function search(): void {
        $q    = trim($_GET['q']    ?? '');
        $type = trim($_GET['type'] ?? '');

        if ($q) {
            $stmt = $this->db->prepare(
                "SELECT * FROM restaurant WHERE nom LIKE ? OR adresse LIKE ? ORDER BY created_at DESC"
            );
            $stmt->execute(['%'.$q.'%', '%'.$q.'%']);
        } else {
            $stmt = $this->db->query("SELECT * FROM restaurant ORDER BY created_at DESC");
        }
        $results = $stmt->fetchAll();

        if ($type) {
            $results = array_values(array_filter($results, fn($r) => $r['type_cuisine'] === $type));
        }

        header('Content-Type: application/json');
        echo json_encode(array_values($results));
        exit;
    }

    // GET /Restaurant/show/{id}
    public function show(string $id): void {
        $stmt = $this->db->prepare("SELECT * FROM restaurant WHERE id = ?");
        $stmt->execute([(int)$id]);
        $restaurant = $stmt->fetch();

        if (!$restaurant) {
            require_once 'View/front/404.php';
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM meal WHERE restaurant_id = ? ORDER BY categorie, nom");
        $stmt->execute([(int)$id]);
        $meals = $stmt->fetchAll();

        // Note du restaurant
        $noteStmt = $this->db->prepare("SELECT COUNT(*) as total, ROUND(AVG(note),1) as moyenne FROM avis WHERE restaurant_id = ?");
        $noteStmt->execute([(int)$id]);
        $noteStats = $noteStmt->fetch();
        $restaurant['note_moyenne'] = $noteStats['moyenne'] ?? 0;
        $restaurant['note_total']   = $noteStats['total']   ?? 0;

        // Vote de l'utilisateur actuel
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $myVote = $this->db->prepare("SELECT note FROM avis WHERE restaurant_id = ? AND ip = ?");
        $myVote->execute([(int)$id, $ip]);
        $restaurant['ma_note'] = $myVote->fetchColumn() ?: 0;

        require_once 'View/front/restaurant_show.php';
    }

    // Attacher les notes à une liste de restaurants
    private function attachNotes(array $restaurants): array {
        if (empty($restaurants)) return $restaurants;

        // Vérifier que la table avis existe
        try {
            $ids = array_column($restaurants, 'id');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $this->db->prepare(
                "SELECT restaurant_id, COUNT(*) as total, ROUND(AVG(note),1) as moyenne
                 FROM avis WHERE restaurant_id IN ($placeholders) GROUP BY restaurant_id"
            );
            $stmt->execute($ids);
            $notes = [];
            foreach ($stmt->fetchAll() as $row) {
                $notes[$row['restaurant_id']] = $row;
            }
            foreach ($restaurants as &$r) {
                $r['note_moyenne'] = isset($notes[$r['id']]) ? (float)$notes[$r['id']]['moyenne'] : 0;
                $r['note_total']   = isset($notes[$r['id']]) ? (int)$notes[$r['id']]['total']   : 0;
            }
        } catch (\Exception $e) {
            foreach ($restaurants as &$r) {
                $r['note_moyenne'] = 0;
                $r['note_total']   = 0;
            }
        }
        return $restaurants;
    }
}
