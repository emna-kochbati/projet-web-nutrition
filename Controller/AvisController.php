<?php
require_once 'Config/database.php';

class AvisController {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->ensureTable();
    }

    // POST /Avis/noter  — { restaurant_id, note }
    public function noter(): void {
        header('Content-Type: application/json');

        $body          = json_decode(file_get_contents('php://input'), true) ?? [];
        $restaurantId  = (int)($body['restaurant_id'] ?? 0);
        $note          = (int)($body['note'] ?? 0);
        $ip            = $_SERVER['REMOTE_ADDR'] ?? '';

        if (!$restaurantId || $note < 1 || $note > 5) {
            echo json_encode(['ok' => false, 'message' => 'Données invalides']);
            exit;
        }

        // Un vote par IP par restaurant
        $check = $this->db->prepare("SELECT id FROM avis WHERE restaurant_id = ? AND ip = ?");
        $check->execute([$restaurantId, $ip]);
        if ($check->fetch()) {
            // Mettre à jour le vote existant
            $upd = $this->db->prepare("UPDATE avis SET note = ? WHERE restaurant_id = ? AND ip = ?");
            $upd->execute([$note, $restaurantId, $ip]);
        } else {
            $ins = $this->db->prepare("INSERT INTO avis (restaurant_id, note, ip) VALUES (?, ?, ?)");
            $ins->execute([$restaurantId, $note, $ip]);
        }

        // Retourner la nouvelle moyenne
        $stats = $this->getStats($restaurantId);
        echo json_encode(['ok' => true, 'stats' => $stats]);
        exit;
    }

    // GET /Avis/stats?restaurant_id=...
    public function stats(): void {
        header('Content-Type: application/json');
        $id = (int)($_GET['restaurant_id'] ?? 0);
        echo json_encode($this->getStats($id));
        exit;
    }

    public function getStats(int $restaurantId): array {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total, ROUND(AVG(note), 1) as moyenne FROM avis WHERE restaurant_id = ?"
        );
        $stmt->execute([$restaurantId]);
        $row = $stmt->fetch();
        return [
            'total'   => (int)$row['total'],
            'moyenne' => $row['moyenne'] ? (float)$row['moyenne'] : 0,
        ];
    }

    // Créer la table si elle n'existe pas
    private function ensureTable(): void {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS `avis` (
                `id`            INT(11)    NOT NULL AUTO_INCREMENT,
                `restaurant_id` INT(11)    NOT NULL,
                `note`          TINYINT(1) NOT NULL,
                `ip`            VARCHAR(45) DEFAULT NULL,
                `created_at`    TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}
