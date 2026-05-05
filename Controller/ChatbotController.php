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

        $body    = json_decode(file_get_contents('php://input'), true);
        $message = strtolower(trim($body['message'] ?? ''));
        $userLat = isset($body['lat']) ? (float)$body['lat'] : null;
        $userLng = isset($body['lng']) ? (float)$body['lng'] : null;

        // ── Détection d'intention ─────────────────────────────────────────────
        $intentProche   = $this->detectIntent($message, ['proche', 'près', 'autour', 'zone', 'localisation', 'position', 'nearest', 'nearby']);
        $intentListe    = $this->detectIntent($message, ['liste', 'tous', 'all', 'restaurant', 'partenaire']);
        $intentCuisine  = $this->detectCuisine($message);

        if ($intentProche && $userLat && $userLng) {
            $restaurants = $this->getRestaurantsProches($userLat, $userLng, 5);
            if (empty($restaurants)) {
                echo json_encode(['type' => 'text', 'message' => "Je n'ai trouvé aucun restaurant avec des coordonnées GPS dans notre base. Essayez d'abord d'ajouter les coordonnées des restaurants."]);
            } else {
                echo json_encode([
                    'type'        => 'map',
                    'message'     => "Voici les restaurants les plus proches de votre position actuelle 📍",
                    'restaurants' => $restaurants,
                    'userLat'     => $userLat,
                    'userLng'     => $userLng,
                ]);
            }
        } elseif ($intentProche && (!$userLat || !$userLng)) {
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
            // Réponse générique
            echo json_encode(['type' => 'text', 'message' => $this->genericResponse($message)]);
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
        // Formule Haversine en SQL pour calculer la distance
        $sql = "
            SELECT *,
                (6371 * ACOS(
                    COS(RADIANS(:lat1)) * COS(RADIANS(latitude)) *
                    COS(RADIANS(longitude) - RADIANS(:lng)) +
                    SIN(RADIANS(:lat2)) * SIN(RADIANS(latitude))
                )) AS distance_km
            FROM restaurant
            WHERE latitude IS NOT NULL AND longitude IS NOT NULL
            ORDER BY distance_km ASC
            LIMIT :limit
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lat1',  $lat);
        $stmt->bindValue(':lat2',  $lat);
        $stmt->bindValue(':lng',   $lng);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
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
