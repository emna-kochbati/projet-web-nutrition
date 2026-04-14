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

        $recettes = $this->recetteModel->getAll();

        // Filtrage
        if ($search !== '') {
            $recettes = array_filter($recettes, fn($r) =>
                stripos($r['nom'], $search) !== false
            );
        }
        if ($categorie !== '') {
            $recettes = array_filter($recettes, fn($r) => $r['categorie'] === $categorie);
        }
        if ($difficulte !== '') {
            $recettes = array_filter($recettes, fn($r) => $r['difficulte'] === $difficulte);
        }

        $recettes = array_values($recettes);
        require_once 'View/front/recette.php';
    }

    // Détail d'une recette
    public function detail(string $id): void {
        $recette = $this->recetteModel->getById((int)$id);
        if (!$recette) {
            header('Location: /2A35/Recette'); exit;
        }
        $ingredients = $this->ingredientModel->getByRecette((int)$id);
        require_once 'View/front/recette_detail.php';
    }
}
