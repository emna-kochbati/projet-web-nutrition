<?php
require_once 'Config/database.php';

class MapController {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // GET /Map — page carte
    public function index(): void {
        // Créer les colonnes automatiquement si elles n'existent pas
        $this->ensureGpsColumns();
        $restaurants = $this->db->query("SELECT * FROM restaurant ORDER BY nom")->fetchAll();
        require_once 'View/front/map.php';
    }

    // Crée latitude/longitude si elles n'existent pas
    private function ensureGpsColumns(): void {
        $lat = $this->db->query("SHOW COLUMNS FROM restaurant LIKE 'latitude'")->fetch();
        if (!$lat) {
            $this->db->exec("ALTER TABLE restaurant ADD COLUMN latitude DECIMAL(10,7) DEFAULT NULL");
        }
        $lng = $this->db->query("SHOW COLUMNS FROM restaurant LIKE 'longitude'")->fetch();
        if (!$lng) {
            $this->db->exec("ALTER TABLE restaurant ADD COLUMN longitude DECIMAL(10,7) DEFAULT NULL");
        }
    }

    // POST /Map/saveCoords — sauvegarde les coordonnées géocodées
    public function saveCoords(): void {
        header('Content-Type: application/json');
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $id   = (int)($body['id']  ?? 0);
        $lat  = (float)($body['lat'] ?? 0);
        $lng  = (float)($body['lng'] ?? 0);

        if (!$id || !$lat || !$lng) {
            echo json_encode(['ok' => false]);
            exit;
        }

        // Créer les colonnes si elles n'existent pas
        $this->ensureGpsColumns();

        $stmt = $this->db->prepare("UPDATE restaurant SET latitude=?, longitude=? WHERE id=?");
        $stmt->execute([$lat, $lng, $id]);
        echo json_encode(['ok' => true]);
        exit;
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
