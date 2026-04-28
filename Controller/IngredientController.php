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
        $page        = max(1, (int)($_GET['page'] ?? 1));
        $perPage     = 6;
        $total       = $this->ingredientModel->countFilter($search, $type);
        $totalPages  = (int)ceil($total / $perPage);
        $offset      = ($page - 1) * $perPage;
        $ingredients = $this->ingredientModel->filterPaginated($search, $type, $perPage, $offset);
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
            // Convertir virgule → point pour les décimales
            $toFloat = fn($v) => (float)str_replace(',', '.', $v ?? '0');

            $ingredient = new Ingredient(
                null,
                htmlspecialchars(trim($_POST['nom'])),
                $_POST['type'],
                $this->uploadImage(),
                $toFloat($_POST['proteines'] ?? 0),
                $toFloat($_POST['calcium']   ?? 0),
                $toFloat($_POST['glucides']  ?? 0),
                $toFloat($_POST['lipides']   ?? 0)
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
            // Convertir virgule → point pour les décimales
            $toFloat = fn($v) => (float)str_replace(',', '.', $v ?? '0');

            $obj = new Ingredient(
                (int)$id,
                htmlspecialchars(trim($_POST['nom'])),
                $_POST['type'],
                $nouvelleImage ?: $ingredient['image'],
                $toFloat($_POST['proteines'] ?? 0),
                $toFloat($_POST['calcium']   ?? 0),
                $toFloat($_POST['glucides']  ?? 0),
                $toFloat($_POST['lipides']   ?? 0)
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

    // ── Statistiques ──────────────────────────────────────────────────────────
    public function stats(): void {
        $db = Database::getConnection();

        $q = $db->query("SELECT type, COUNT(*) as total FROM ingredient GROUP BY type ORDER BY total DESC");
        $statsType = $q->fetchAll();

        $q = $db->query("SELECT COUNT(*) as total FROM ingredient");
        $totalIngredients = $q->fetch()['total'];

        $q = $db->query("SELECT AVG(proteines) as moy_prot, AVG(calcium) as moy_cal,
                         AVG(glucides) as moy_gluc, AVG(lipides) as moy_lip FROM ingredient");
        $moyennes = $q->fetch();

        require_once 'View/back/ingredient/stats.php';
    }

    // ── Endpoint AJAX recherche dynamique ─────────────────────────────────────
    public function ajax(): void {        header('Content-Type: application/json');
        $search = trim($_GET['search'] ?? '');
        $type   = trim($_GET['type']   ?? '');
        $ingredients = $this->ingredientModel->filter($search, $type);
        echo json_encode($ingredients);
        exit;
    }

    public function addIngredient(Ingredient $ingredient): void {
        $sql = "INSERT INTO ingredient (nom, type, image, proteines, calcium, glucides, lipides)
                VALUES (:nom, :type, :image, :proteines, :calcium, :glucides, :lipides)";
        $db  = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom'       => $ingredient->getNom(),
                'type'      => $ingredient->getType(),
                'image'     => $ingredient->getImage(),
                'proteines' => $ingredient->getProteines(),
                'calcium'   => $ingredient->getCalcium(),
                'glucides'  => $ingredient->getGlucides(),
                'lipides'   => $ingredient->getLipides(),
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function updateIngredient(Ingredient $ingredient, int $id): void {
        $sql = "UPDATE ingredient SET nom=:nom, type=:type, image=:image,
                proteines=:proteines, calcium=:calcium, glucides=:glucides, lipides=:lipides
                WHERE id=:id";
        $db  = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id'        => $id,
                'nom'       => $ingredient->getNom(),
                'type'      => $ingredient->getType(),
                'image'     => $ingredient->getImage(),
                'proteines' => $ingredient->getProteines(),
                'calcium'   => $ingredient->getCalcium(),
                'glucides'  => $ingredient->getGlucides(),
                'lipides'   => $ingredient->getLipides(),
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
