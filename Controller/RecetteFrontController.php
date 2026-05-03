<?php
require_once 'Model/Recette.php';
require_once 'Model/Ingredient.php';

class RecetteFrontController {

    private Recette    $recetteModel;
    private Ingredient $ingredientModel;

    public function __construct() {
        $this->recetteModel    = new Recette();
        $this->ingredientModel = new Ingredient();
    }

    // Liste publique des recettes avec filtres
    public function index(): void {
        $search     = trim($_GET['search']     ?? '');
        $categorie  = trim($_GET['categorie']  ?? '');
        $difficulte = trim($_GET['difficulte'] ?? '');
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $perPage    = 6;

        $total      = $this->recetteModel->countFilter($search, $categorie, $difficulte);
        $totalPages = (int)ceil($total / $perPage);
        $offset     = ($page - 1) * $perPage;

        $recettes = $this->recetteModel->filterPaginated($search, $categorie, $difficulte, $perPage, $offset);
        require_once 'View/front/recette.php';
    }

    // Détail d'une recette
    public function detail(string $id): void {
        $recette = $this->recetteModel->getById((int)$id);
        if (!$recette) {
            header('Location: /2A35/RecetteFront'); exit;
        }
        $ingredients  = $this->ingredientModel->getByRecette((int)$id);
        $valeursNutri = $this->ingredientModel->getValeursNutritionnelles((int)$id);
        require_once 'View/front/recette_detail.php';
    }

    // Endpoint AJAX recherche dynamique frontoffice
    public function ajax(): void {
        header('Content-Type: application/json');
        $search     = trim($_GET['search']     ?? '');
        $categorie  = trim($_GET['categorie']  ?? '');
        $difficulte = trim($_GET['difficulte'] ?? '');
        $recettes   = $this->recetteModel->filter($search, $categorie, $difficulte);
        echo json_encode(array_values($recettes));
        exit;
    }

    // Endpoint AJAX recherche par ingrédients (Speech to Text)
    public function ajaxIngredients(): void {
        header('Content-Type: application/json');
        $body        = json_decode(file_get_contents('php://input'), true);
        $ingredients = $body['ingredients'] ?? [];

        if (empty($ingredients)) {
            echo json_encode([]); exit;
        }

        $recettes = $this->recetteModel->rechercherParIngredients($ingredients);
        echo json_encode(array_values($recettes));
        exit;
    }
}