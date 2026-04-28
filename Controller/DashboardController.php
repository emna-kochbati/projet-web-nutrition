<?php
require_once 'Config/database.php';

class DashboardController {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function index(): void {
        // Stats restaurants
        $totalRestaurants  = $this->db->query("SELECT COUNT(*) FROM restaurant")->fetchColumn();
        $byTypeCuisine     = $this->db->query("SELECT type_cuisine, COUNT(*) as total FROM restaurant GROUP BY type_cuisine ORDER BY total DESC")->fetchAll();
        $lastRestaurants   = $this->db->query("SELECT * FROM restaurant ORDER BY created_at DESC LIMIT 5")->fetchAll();

        // Stats meals
        $totalMeals        = $this->db->query("SELECT COUNT(*) FROM meal")->fetchColumn();
        $totalDisponibles  = $this->db->query("SELECT COUNT(*) FROM meal WHERE disponible = 1")->fetchColumn();
        $totalIndisponibles= $this->db->query("SELECT COUNT(*) FROM meal WHERE disponible = 0")->fetchColumn();
        $byCategorie       = $this->db->query("SELECT categorie, COUNT(*) as total FROM meal GROUP BY categorie ORDER BY total DESC")->fetchAll();
        $lastMeals         = $this->db->query("SELECT m.*, r.nom AS restaurant_nom FROM meal m JOIN restaurant r ON r.id = m.restaurant_id ORDER BY m.created_at DESC LIMIT 5")->fetchAll();

        require_once 'View/back/dashboard.php';
    }
}
