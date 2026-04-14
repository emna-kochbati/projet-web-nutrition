<?php
require_once 'Model/Recette.php';
require_once 'Model/Ingredient.php';

class RecetteController {

    private Recette    $model;
    private Ingredient $ingredientModel;

    public function __construct() {
        $this->model           = new Recette();
        $this->ingredientModel = new Ingredient();
    }

    // Liste
    public function index(): void {
        $search   = trim($_GET['search'] ?? '');
        $recettes = $search ? $this->model->search($search) : $this->model->getAll();
        $success  = $_SESSION['success'] ?? null;
        $error    = $_SESSION['error']   ?? null;
        unset($_SESSION['success'], $_SESSION['error']);
        require_once 'View/back/recette/list.php';
    }

    // Formulaire ajout
    public function create(): void {
        $recette     = [];
        $ingredients = [];
        $errors      = [];
        require_once 'View/back/recette/form.php';
    }

    // Traiter ajout
    public function store(): void {
        $errors = $this->valider($_POST);
        if (empty($errors)) {
            $data          = $this->nettoyer($_POST);
            $data['image'] = $this->uploadImage();
            $recetteId     = $this->model->create($data);
            $this->sauvegarderIngredients($recetteId, $_POST);
            $_SESSION['success'] = 'Recette ajoutée avec succès !';
            header('Location: /2A35/Admin/recette'); exit;
        }
        $recette     = $_POST;
        $ingredients = $this->rebuildIngredients($_POST);
        require_once 'View/back/recette/form.php';
    }

    // Formulaire modification
    public function edit(string $id): void {
        $recette = $this->model->getById((int)$id);
        if (!$recette) {
            $_SESSION['error'] = 'Recette introuvable.';
            header('Location: /2A35/Admin/recette'); exit;
        }
        $ingredients = $this->ingredientModel->getByRecette((int)$id);
        $errors      = [];
        require_once 'View/back/recette/form.php';
    }

    // Traiter modification
    public function update(string $id): void {
        $recette = $this->model->getById((int)$id);
        if (!$recette) {
            $_SESSION['error'] = 'Recette introuvable.';
            header('Location: /2A35/Admin/recette'); exit;
        }
        $errors = $this->valider($_POST);
        if (empty($errors)) {
            $data          = $this->nettoyer($_POST);
            $nouvelleImage = $this->uploadImage();
            $data['image'] = $nouvelleImage ?: $recette['image'];
            $this->model->update((int)$id, $data);
            $this->ingredientModel->deleteByRecette((int)$id);
            $this->sauvegarderIngredients((int)$id, $_POST);
            $_SESSION['success'] = 'Recette modifiée avec succès !';
            header('Location: /2A35/Admin/recette'); exit;
        }
        $ingredients = $this->rebuildIngredients($_POST);
        require_once 'View/back/recette/form.php';
    }

    // Détail
    public function show(string $id): void {
        $recette = $this->model->getById((int)$id);
        if (!$recette) {
            $_SESSION['error'] = 'Recette introuvable.';
            header('Location: /2A35/Admin/recette'); exit;
        }
        $ingredients = $this->ingredientModel->getByRecette((int)$id);
        require_once 'View/back/recette/show.php';
    }

    // Supprimer
    public function delete(string $id): void {
        $recette = $this->model->getById((int)$id);
        if ($recette) {
            if ($recette['image'] && file_exists('assets/uploads/recettes/' . $recette['image'])) {
                unlink('assets/uploads/recettes/' . $recette['image']);
            }
            $this->model->delete((int)$id);
            $_SESSION['success'] = 'Recette supprimée avec succès !';
        } else {
            $_SESSION['error'] = 'Recette introuvable.';
        }
        header('Location: /2A35/Admin/recette'); exit;
    }

    // ── Validation PHP côté serveur ───────────────────────────────────────────
    private function valider(array $post): array {
        $errors = [];

        $nom = trim($post['nom'] ?? '');
        if ($nom === '')            $errors['nom'] = 'Le nom est obligatoire.';
        elseif (strlen($nom) < 3)   $errors['nom'] = 'Le nom doit contenir au moins 3 caractères.';
        elseif (strlen($nom) > 150) $errors['nom'] = 'Le nom ne peut pas dépasser 150 caractères.';
        elseif (preg_match('/\d/', $nom)) $errors['nom'] = 'Le nom ne doit pas contenir de chiffres.';
        elseif (!preg_match('/^[\p{L}\s\-\'\,\.]+$/u', $nom)) $errors['nom'] = 'Le nom ne doit contenir que des lettres.';

        $cats = ['petit-dejeuner','dejeuner','diner','collation','dessert','vegetarien','regime','sportif'];
        if (empty($post['categorie']) || !in_array($post['categorie'], $cats, true))
            $errors['categorie'] = 'Veuillez sélectionner une catégorie valide.';

        $diffs = ['facile','moyen','difficile'];
        if (empty($post['difficulte']) || !in_array($post['difficulte'], $diffs, true))
            $errors['difficulte'] = 'Veuillez sélectionner un niveau de difficulté.';

        $duree = $post['duree'] ?? '';
        if (!ctype_digit((string)$duree) || (int)$duree < 1)
            $errors['duree'] = 'La durée doit être un entier positif (en minutes).';
        elseif ((int)$duree > 1440)
            $errors['duree'] = 'La durée ne peut pas dépasser 1440 minutes.';

        $cal = $post['calories'] ?? '';
        if (!is_numeric($cal) || (int)$cal < 0)
            $errors['calories'] = 'Les calories doivent être un nombre positif ou nul.';
        elseif ((int)$cal > 10000)
            $errors['calories'] = 'Valeur calorique incorrecte (max 10 000 kcal).';

        if (!empty($_FILES['image']['name'])) {
            $allowed = ['image/jpeg','image/png','image/webp'];
            if (!in_array($_FILES['image']['type'], $allowed))
                $errors['image'] = 'Format non accepté (JPG, PNG, WEBP uniquement).';
            elseif ($_FILES['image']['size'] > 2 * 1024 * 1024)
                $errors['image'] = "L'image ne doit pas dépasser 2 Mo.";
        }

        // Validation ingrédients
        $noms = $post['ing_nom'] ?? [];
        foreach ($noms as $i => $nom) {
            if (empty(trim($nom))) continue;
            $qte   = $post['ing_quantite'][$i] ?? '';
            $unite = trim($post['ing_unite'][$i] ?? '');
            if (!is_numeric($qte) || (float)$qte <= 0)
                $errors["ing_quantite_$i"] = "Quantité invalide pour l'ingrédient " . ($i+1) . ".";
            if ($unite === '')
                $errors["ing_unite_$i"] = "Unité manquante pour l'ingrédient " . ($i+1) . ".";
        }

        return $errors;
    }

    private function nettoyer(array $post): array {
        return [
            'nom'        => htmlspecialchars(trim($post['nom'])),
            'categorie'  => $post['categorie'],
            'duree'      => (int)$post['duree'],
            'difficulte' => $post['difficulte'],
            'calories'   => (int)$post['calories'],
        ];
    }

    private function uploadImage(): ?string {
        if (empty($_FILES['image']['name'])) return null;
        $dir = 'assets/uploads/recettes/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext  = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $name = uniqid('rec_') . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $dir . $name);
        return $name;
    }

    private function sauvegarderIngredients(int $recetteId, array $post): void {
        $noms = $post['ing_nom'] ?? [];
        foreach ($noms as $i => $nom) {
            if (empty(trim($nom))) continue;
            $this->ingredientModel->create([
                'recette_id' => $recetteId,
                'nom'        => htmlspecialchars(trim($nom)),
                'quantite'   => (float)($post['ing_quantite'][$i] ?? 0),
                'unite'      => htmlspecialchars(trim($post['ing_unite'][$i] ?? '')),
            ]);
        }
    }

    private function rebuildIngredients(array $post): array {
        $result = [];
        $noms   = $post['ing_nom'] ?? [];
        foreach ($noms as $i => $nom) {
            $result[] = [
                'nom'      => $nom,
                'quantite' => $post['ing_quantite'][$i] ?? '',
                'unite'    => $post['ing_unite'][$i]    ?? '',
            ];
        }
        return $result;
    }
}
