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
        $search = trim($_GET['search'] ?? '');
        $type   = trim($_GET['type']   ?? '');

        if ($search) {
            $stmt = $this->db->prepare("SELECT * FROM restaurant WHERE nom LIKE ? OR adresse LIKE ? ORDER BY created_at DESC");
            $stmt->execute(['%'.$search.'%', '%'.$search.'%']);
            $restaurants = $stmt->fetchAll();
        } else {
            $restaurants = $this->db->query("SELECT * FROM restaurant ORDER BY created_at DESC")->fetchAll();
        }

        if ($type) {
            $restaurants = array_values(array_filter($restaurants, fn($r) => $r['type_cuisine'] === $type));
        }

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

        require_once 'View/front/restaurant_show.php';
    }
}
