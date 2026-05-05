<?php
require_once 'Config/database.php';

class MapController {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // GET /Map — page carte
    public function index(): void {
        $restaurants = $this->db->query("SELECT * FROM restaurant ORDER BY nom")->fetchAll();
        require_once 'View/front/map.php';
    }

    // GET /Map/restaurants — API JSON pour tous les restaurants
    public function restaurants(): void {
        header('Content-Type: application/json');
        $restaurants = $this->db->query("SELECT * FROM restaurant ORDER BY nom")->fetchAll();
        echo json_encode(array_values($restaurants));
        exit;
    }

    // GET /Map/nearby?lat=...&lng=...&radius=... — API JSON restaurants proches
    public function nearby(): void {
        header('Content-Type: application/json');

        $lat    = isset($_GET['lat'])    ? (float)$_GET['lat']    : null;
        $lng    = isset($_GET['lng'])    ? (float)$_GET['lng']    : null;
        $radius = isset($_GET['radius']) ? (float)$_GET['radius'] : 10; // km

        if (!$lat || !$lng) {
            echo json_encode(['error' => 'Paramètres lat/lng manquants']);
            exit;
        }

        $sql = "
            SELECT *,
                ROUND(6371 * ACOS(LEAST(1, GREATEST(-1,
                    COS(RADIANS(:lat1)) * COS(RADIANS(latitude)) *
                    COS(RADIANS(longitude) - RADIANS(:lng)) +
                    SIN(RADIANS(:lat2)) * SIN(RADIANS(latitude))
                ))), 2) AS distance_km
            FROM restaurant
            WHERE latitude IS NOT NULL AND longitude IS NOT NULL
            HAVING distance_km <= :radius
            ORDER BY distance_km ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lat1',   $lat,    PDO::PARAM_STR);
        $stmt->bindValue(':lat2',   $lat,    PDO::PARAM_STR);
        $stmt->bindValue(':lng',    $lng,    PDO::PARAM_STR);
        $stmt->bindValue(':radius', $radius, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll();

        echo json_encode(array_values($results));
        exit;
    }
}
