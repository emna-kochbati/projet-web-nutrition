<?php
require_once 'Config/database.php';
require_once 'Model/Ingredient.php';

class IngredientController {

    private Ingredient $ingredientModel;

    public function __construct() {
        $this->ingredientModel = new Ingredient();
    }

    // ── Liste tous les ingrédients ────────────────────────────────────────────
    public function index(): void {
        $search      = trim($_GET['search'] ?? '');
        $type        = trim($_GET['type']   ?? '');
        $ingredients = $this->ingredientModel->filter($search, $type);
        $stats       = $this->ingredientModel->countByType();
        $success     = $_SESSION['success'] ?? null;
        $error       = $_SESSION['error']   ?? null;
        unset($_SESSION['success'], $_SESSION['error']);
        require_once 'View/back/ingredient/list.php';
    }

    // ── Afficher formulaire ajout ─────────────────────────────────────────────
    public function create(): void {
        $ingredient = [];
        $errors     = [];
        require_once 'View/back/ingredient/form.php';
    }

    // ── Ajouter un ingrédient ─────────────────────────────────────────────────
    public function store(): void {
        $errors = $this->valider($_POST);
        if (empty($errors)) {
            $ingredient = new Ingredient(
                null,
                htmlspecialchars(trim($_POST['nom'])),
                $_POST['type'],
                $this->uploadImage()
            );
            $this->addIngredient($ingredient);
            $_SESSION['success'] = 'Ingrédient ajouté avec succès !';
            header('Location: /2A35/Admin/ingredient'); exit;
        }
        $ingredient = $_POST;
        require_once 'View/back/ingredient/form.php';
    }

    // ── Afficher formulaire modification ──────────────────────────────────────
    public function edit(string $id): void {
        $ingredient = $this->showIngredient((int)$id);
        if (!$ingredient) {
            $_SESSION['error'] = 'Ingrédient introuvable.';
            header('Location: /2A35/Admin/ingredient'); exit;
        }
        $errors = [];
        require_once 'View/back/ingredient/form.php';
    }

    // ── Modifier un ingrédient ────────────────────────────────────────────────
    public function update(string $id): void {
        $ingredient = $this->showIngredient((int)$id);
        if (!$ingredient) {
            $_SESSION['error'] = 'Ingrédient introuvable.';
            header('Location: /2A35/Admin/ingredient'); exit;
        }
        $errors = $this->valider($_POST);
        if (empty($errors)) {
            $nouvelleImage = $this->uploadImage();
            $obj = new Ingredient(
                (int)$id,
                htmlspecialchars(trim($_POST['nom'])),
                $_POST['type'],
                $nouvelleImage ?: $ingredient['image']
            );
            $this->updateIngredient($obj, (int)$id);
            $_SESSION['success'] = 'Ingrédient modifié avec succès !';
            header('Location: /2A35/Admin/ingredient'); exit;
        }
        require_once 'View/back/ingredient/form.php';
    }

    // ── Détail ingrédient ─────────────────────────────────────────────────────
    public function show(string $id): void {
        $ingredient = $this->showIngredient((int)$id);
        if (!$ingredient) {
            $_SESSION['error'] = 'Ingrédient introuvable.';
            header('Location: /2A35/Admin/ingredient'); exit;
        }
        require_once 'View/back/ingredient/show.php';
    }

    // ── Supprimer un ingrédient ───────────────────────────────────────────────
    public function delete(string $id): void {
        $ingredient = $this->showIngredient((int)$id);
        if ($ingredient) {
            if ($ingredient['image'] && file_exists('assets/uploads/ingredients/' . $ingredient['image'])) {
                unlink('assets/uploads/ingredients/' . $ingredient['image']);
            }
            $this->deleteIngredient((int)$id);
            $_SESSION['success'] = 'Ingrédient supprimé.';
        } else {
            $_SESSION['error'] = 'Ingrédient introuvable.';
        }
        header('Location: /2A35/Admin/ingredient'); exit;
    }

    // =========================================================================
    // Méthodes PDO (structure comme l'exemple de la prof)
    // =========================================================================

    public function listIngredients(): array {
        $sql = "SELECT * FROM ingredient ORDER BY nom ASC";
        $db  = Database::getConnection();
        try {
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function addIngredient(Ingredient $ingredient): void {
        $sql = "INSERT INTO ingredient (nom, type, image) VALUES (:nom, :type, :image)";
        $db  = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom'   => $ingredient->getNom(),
                'type'  => $ingredient->getType(),
                'image' => $ingredient->getImage(),
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function updateIngredient(Ingredient $ingredient, int $id): void {
        $sql = "UPDATE ingredient SET nom=:nom, type=:type, image=:image WHERE id=:id";
        $db  = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id'    => $id,
                'nom'   => $ingredient->getNom(),
                'type'  => $ingredient->getType(),
                'image' => $ingredient->getImage(),
            ]);
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function deleteIngredient(int $id): void {
        $sql = "DELETE FROM ingredient WHERE id = :id";
        $db  = Database::getConnection();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function showIngredient(int $id): array|false {
        $sql   = "SELECT * FROM ingredient WHERE id = :id";
        $db    = Database::getConnection();
        $query = $db->prepare($sql);
        try {
            $query->execute([':id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // Méthodes privées utilitaires
    // =========================================================================

    private function valider(array $post): array {
        $errors = [];
        $nom = trim($post['nom'] ?? '');
        if ($nom === '')            $errors['nom'] = 'Le nom est obligatoire.';
        elseif (strlen($nom) < 2)   $errors['nom'] = 'Le nom doit contenir au moins 2 caractères.';
        elseif (strlen($nom) > 150) $errors['nom'] = 'Maximum 150 caractères.';
        elseif (preg_match('/\d/', $nom)) $errors['nom'] = 'Le nom ne doit pas contenir de chiffres.';

        $types = ['legume','fruit','produit-laitier','epice','viande','cereale','autre'];
        if (empty($post['type']) || !in_array($post['type'], $types, true))
            $errors['type'] = 'Veuillez sélectionner un type valide.';

        if (!empty($_FILES['image']['name'])) {
            $allowed = ['image/jpeg','image/png','image/webp'];
            if (!in_array($_FILES['image']['type'], $allowed))
                $errors['image'] = 'Format non accepté (JPG, PNG, WEBP).';
            elseif ($_FILES['image']['size'] > 2 * 1024 * 1024)
                $errors['image'] = "L'image ne doit pas dépasser 2 Mo.";
        }
        return $errors;
    }

    private function uploadImage(): ?string {
        if (empty($_FILES['image']['name'])) return null;
        $dir = 'assets/uploads/ingredients/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext  = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $name = uniqid('ing_') . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $dir . $name);
        return $name;
    }
}
