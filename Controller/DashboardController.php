<?php
require_once 'Config/database.php';

class DashboardController {
    public function index(): void {
        $db = Database::getConnection();

        // ── Stats Recettes ────────────────────────────────────────────────────
        $totalRecettes = $db->query("SELECT COUNT(*) FROM recette")->fetchColumn();

        $q = $db->query("SELECT categorie, COUNT(*) as total FROM recette GROUP BY categorie ORDER BY total DESC");
        $statsCat = $q->fetchAll();

        $q = $db->query("SELECT difficulte, COUNT(*) as total FROM recette GROUP BY difficulte");
        $statsDiff = $q->fetchAll();

        $q = $db->query("SELECT
            SUM(CASE WHEN calories < 300 THEN 1 ELSE 0 END) as moins300,
            SUM(CASE WHEN calories BETWEEN 300 AND 600 THEN 1 ELSE 0 END) as entre300_600,
            SUM(CASE WHEN calories BETWEEN 601 AND 900 THEN 1 ELSE 0 END) as entre600_900,
            SUM(CASE WHEN calories > 900 THEN 1 ELSE 0 END) as plus900
            FROM recette");
        $statsCal = $q->fetch();

        // ── Stats Ingrédients ─────────────────────────────────────────────────
        $totalIngredients = $db->query("SELECT COUNT(*) FROM ingredient")->fetchColumn();

        $q = $db->query("SELECT type, COUNT(*) as total FROM ingredient GROUP BY type ORDER BY total DESC");
        $statsType = $q->fetchAll();

        $q = $db->query("SELECT AVG(proteines) as moy_prot, AVG(calcium) as moy_cal,
                         AVG(glucides) as moy_gluc, AVG(lipides) as moy_lip FROM ingredient");
        $moyennes = $q->fetch();

        // ── Dernières recettes + ingrédients ─────────────────────────────────
        $q = $db->query("SELECT * FROM recette ORDER BY created_at DESC LIMIT 5");
        $dernieresRecettes = $q->fetchAll();

        $q = $db->query("SELECT * FROM ingredient ORDER BY created_at DESC LIMIT 5");
        $derniersIngredients = $q->fetchAll();

        require_once 'View/back/dashboard.php';
    }
}
