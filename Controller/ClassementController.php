<?php
require_once 'Config/database.php';

class ClassementController {

    private PDO $db;

    // Seuil calories pour considérer un plat "healthy"
    const SEUIL_HEALTHY = 500;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // GET /Admin/classement
    public function index(): void {
        $classement = $this->calculerClassement();
        require_once 'View/back/classement/index.php';
    }

    // ── Logique métier ────────────────────────────────────────────────────────
    private function calculerClassement(): array {
        // Récupérer tous les restaurants avec leurs stats de meals
        $sql = "
            SELECT
                r.id,
                r.nom,
                r.type_cuisine,
                r.image,
                COUNT(m.id)                                         AS total_meals,
                COALESCE(AVG(m.calories), 0)                        AS avg_calories,
                SUM(CASE WHEN m.calories IS NOT NULL
                         AND m.calories < :seuil THEN 1 ELSE 0 END) AS nb_healthy,
                SUM(CASE WHEN m.calories IS NOT NULL THEN 1 ELSE 0 END) AS meals_avec_calories
            FROM restaurant r
            LEFT JOIN meal m ON m.restaurant_id = r.id AND m.disponible = 1
            GROUP BY r.id, r.nom, r.type_cuisine, r.image
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':seuil', self::SEUIL_HEALTHY, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $classement = [];
        foreach ($rows as $r) {
            $totalMeals       = (int)$r['total_meals'];
            $avgCal           = (float)$r['avg_calories'];
            $nbHealthy        = (int)$r['nb_healthy'];
            $mealsAvecCal     = (int)$r['meals_avec_calories'];

            // Pourcentage de plats healthy
            $pctHealthy = $mealsAvecCal > 0
                ? round($nbHealthy / $mealsAvecCal * 100)
                : 0;

            // Score healthy :
            // - Base 100
            // - On retire avg_calories / 10 (plus c'est calorique, moins bon le score)
            // - On ajoute pctHealthy (bonus pour les plats < 500 kcal)
            // - Si aucun meal → score neutre 50
            if ($totalMeals === 0) {
                $score = 50;
                $label = 'Non évalué';
                $color = '#9e9e9e';
            } else {
                $score = round(100 - ($avgCal / 10) + $pctHealthy);
                $score = max(0, min(200, $score)); // borner entre 0 et 200

                if ($score >= 120) {
                    $label = '🥗 Très healthy';
                    $color = '#2e7d32';
                } elseif ($score >= 80) {
                    $label = '✅ Healthy';
                    $color = '#558b2f';
                } elseif ($score >= 50) {
                    $label = '⚠️ Modéré';
                    $color = '#f57f17';
                } else {
                    $label = '🔴 Calorique';
                    $color = '#c62828';
                }
            }

            $classement[] = [
                'id'           => $r['id'],
                'nom'          => $r['nom'],
                'type_cuisine' => $r['type_cuisine'],
                'image'        => $r['image'],
                'total_meals'  => $totalMeals,
                'avg_calories' => $avgCal > 0 ? round($avgCal) : null,
                'nb_healthy'   => $nbHealthy,
                'pct_healthy'  => $pctHealthy,
                'score'        => $score,
                'label'        => $label,
                'color'        => $color,
            ];
        }

        // Trier par score décroissant (meilleur en premier)
        usort($classement, fn($a, $b) => $b['score'] <=> $a['score']);

        // Ajouter le rang
        foreach ($classement as $i => &$item) {
            $item['rang'] = $i + 1;
        }

        return $classement;
    }
}
