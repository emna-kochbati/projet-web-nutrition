<?php
require_once 'Config/database.php';

class ChatbotController {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // POST /Chatbot/ask  — reçoit { message, lat, lng }
    public function ask(): void {
        header('Content-Type: application/json');

        try {
            $body    = json_decode(file_get_contents('php://input'), true) ?? [];
            $message = strtolower(trim($body['message'] ?? ''));
            $userLat = isset($body['lat']) && is_numeric($body['lat']) ? (float)$body['lat'] : null;
            $userLng = isset($body['lng']) && is_numeric($body['lng']) ? (float)$body['lng'] : null;

            // ── Détection d'intention ─────────────────────────────────────────
            $intentProche  = $this->detectIntent($message, ['proche', 'près', 'autour', 'zone', 'localisation', 'position', 'nearest', 'nearby']);
            $intentListe   = $this->detectIntent($message, ['liste', 'tous', 'all', 'restaurant', 'partenaire']);
            $intentCuisine = $this->detectCuisine($message);

            if ($intentProche && $userLat && $userLng) {
                $restaurants = $this->getRestaurantsProches($userLat, $userLng, 5);
                if (empty($restaurants)) {
                    echo json_encode(['type' => 'text', 'message' => "Aucun restaurant avec coordonnées GPS trouvé. Ajoutez d'abord les coordonnées dans la base."]);
                } else {
                    echo json_encode([
                        'type'        => 'map',
                        'message'     => "Voici les restaurants les plus proches de votre position 📍",
                        'restaurants' => $restaurants,
                        'userLat'     => $userLat,
                        'userLng'     => $userLng,
                    ]);
                }
            } elseif ($intentProche) {
                echo json_encode(['type' => 'request_location', 'message' => "Pour trouver les restaurants proches, j'ai besoin de votre position. Cliquez sur 📍 pour partager votre localisation."]);
            } elseif ($intentCuisine) {
                $restaurants = $this->getRestaurantsByCuisine($intentCuisine);
                echo json_encode([
                    'type'        => 'list',
                    'message'     => "Voici les restaurants de cuisine **" . ucfirst($intentCuisine) . "** 🍽️",
                    'restaurants' => $restaurants,
                ]);
            } elseif ($intentListe) {
                $restaurants = $this->getAllRestaurants();
                echo json_encode([
                    'type'        => 'list',
                    'message'     => "Voici tous nos restaurants partenaires 🍴",
                    'restaurants' => $restaurants,
                ]);
            } else {
                echo json_encode(['type' => 'text', 'message' => $this->genericResponse($message)]);
            }

        } catch (\Throwable $e) {
            echo json_encode(['type' => 'text', 'message' => 'Erreur serveur : ' . $e->getMessage()]);
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function detectIntent(string $msg, array $keywords): bool {
        foreach ($keywords as $kw) {
            if (str_contains($msg, $kw)) return true;
        }
        return false;
    }

    private function detectCuisine(string $msg): ?string {
        $cuisines = ['tunisienne','italienne','japonaise','americaine','indienne','mexicaine','française','autre'];
        foreach ($cuisines as $c) {
            if (str_contains($msg, $c) || str_contains($msg, substr($c, 0, 5))) return $c;
        }
        return null;
    }

    private function getRestaurantsProches(float $lat, float $lng, int $limit = 5): array {
        // Vérifier si les colonnes latitude/longitude existent
        $check = $this->db->query("SHOW COLUMNS FROM restaurant LIKE 'latitude'")->fetch();
        if (!$check) {
            throw new \RuntimeException("Les colonnes latitude/longitude n'existent pas encore. Exécutez ce SQL dans phpMyAdmin : ALTER TABLE restaurant ADD COLUMN latitude DECIMAL(10,7) DEFAULT NULL, ADD COLUMN longitude DECIMAL(10,7) DEFAULT NULL;");
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
            HAVING distance_km <= 50
            ORDER BY distance_km ASC
            LIMIT :lim
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lat1', $lat, PDO::PARAM_STR);
        $stmt->bindValue(':lat2', $lat, PDO::PARAM_STR);
        $stmt->bindValue(':lng',  $lng, PDO::PARAM_STR);
        $stmt->bindValue(':lim',  $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getRestaurantsByCuisine(string $cuisine): array {
        $stmt = $this->db->prepare("SELECT * FROM restaurant WHERE type_cuisine = ? ORDER BY nom");
        $stmt->execute([$cuisine]);
        return $stmt->fetchAll();
    }

    private function getAllRestaurants(): array {
        return $this->db->query("SELECT * FROM restaurant ORDER BY nom")->fetchAll();
    }

    private function genericResponse(string $msg): string {
        $responses = [
            'bonjour|salut|hello|bonsoir' => "Bonjour ! 👋 Je suis votre assistant EcoNutri. Je peux vous aider à :\n• Trouver les restaurants **proches de vous** 📍\n• Lister tous nos **restaurants partenaires** 🍴\n• Filtrer par **type de cuisine** 🍽️",
            'merci|thanks'                => "Avec plaisir ! 😊 N'hésitez pas si vous avez d'autres questions.",
            'aide|help|comment'           => "Je peux vous aider à :\n• **Restaurants proches** : écrivez \"restaurants près de moi\"\n• **Par cuisine** : écrivez \"restaurant tunisien\"\n• **Tous les restaurants** : écrivez \"liste des restaurants\"",
        ];
        foreach ($responses as $pattern => $reply) {
            foreach (explode('|', $pattern) as $kw) {
                if (str_contains($msg, $kw)) return $reply;
            }
        }
        return "Je n'ai pas bien compris votre demande 🤔\nEssayez :\n• \"restaurants proches de moi\"\n• \"liste des restaurants\"\n• \"restaurant tunisien\"";
    }
}
