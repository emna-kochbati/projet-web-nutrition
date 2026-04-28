<?php
require_once 'Config/database.php';
require_once 'Model/Recette.php';
require_once 'Model/Ingredient.php';

class RecetteController {

    private Recette    $recetteModel;
    private Ingredient $ingredientModel;

    public function __construct() {
        $this->recetteModel    = new Recette();
        $this->ingredientModel = new Ingredient();
    }

    // ── Liste toutes les recettes ─────────────────────────────────────────────
    public function index(): void {
        $search     = trim($_GET['search']     ?? '');
        $categorie  = trim($_GET['categorie']  ?? '');
        $difficulte = trim($_GET['difficulte'] ?? '');
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $perPage    = 5;

        $total    = $this->recetteModel->countFilter($search, $categorie, $difficulte);
        $totalPages = (int)ceil($total / $perPage);
        $offset   = ($page - 1) * $perPage;

        $recettes = $this->recetteModel->filterPaginated($search, $categorie, $difficulte, $perPage, $offset);
        $success  = $_SESSION['success'] ?? null;
        $error    = $_SESSION['error']   ?? null;
        unset($_SESSION['success'], $_SESSION['error']);
        require_once 'View/back/recette/list.php';
    }

    // ── Afficher formulaire ajout ─────────────────────────────────────────────
    public function create(): void {
        $recette     = [];
        $ingredients = [];
        $errors      = [];
        require_once 'View/back/recette/form.php';
    }

    // ── Ajouter une recette ───────────────────────────────────────────────────
    public function store(): void {
        $errors = $this->valider($_POST);
        if (empty($errors)) {
            // Créer l'objet Recette avec les getters/setters
            $recette = new Recette(
                null,
                htmlspecialchars(trim($_POST['nom'])),
                htmlspecialchars(trim($_POST['description'] ?? '')),
                $_POST['categorie'],
                (int)$_POST['duree'],
                $_POST['difficulte'],
                (int)$_POST['calories'],
                $this->uploadImage()
            );
            $recetteId = $this->addRecette($recette);
            $this->sauvegarderIngredients($recetteId, $_POST);
            $_SESSION['success'] = 'Recette ajoutée avec succès !';
            header('Location: /2A35/Admin/recette'); exit;
        }
        $recette     = $_POST;
        $ingredients = $this->rebuildIngredients($_POST);
        require_once 'View/back/recette/form.php';
    }

    // ── Afficher formulaire modification ──────────────────────────────────────
    public function edit(string $id): void {
        $recette = $this->showRecette((int)$id);
        if (!$recette) {
            $_SESSION['error'] = 'Recette introuvable.';
            header('Location: /2A35/Admin/recette'); exit;
        }
        $ingredients = $this->ingredientModel->getByRecette((int)$id);
        $errors      = [];
        require_once 'View/back/recette/form.php';
    }

    // ── Modifier une recette ──────────────────────────────────────────────────
    public function update(string $id): void {
        $recette = $this->showRecette((int)$id);
        if (!$recette) {
            $_SESSION['error'] = 'Recette introuvable.';
            header('Location: /2A35/Admin/recette'); exit;
        }
        $errors = $this->valider($_POST);
        if (empty($errors)) {
            $nouvelleImage = $this->uploadImage();
            $obj = new Recette(
                (int)$id,
                htmlspecialchars(trim($_POST['nom'])),
                htmlspecialchars(trim($_POST['description'] ?? '')),
                $_POST['categorie'],
                (int)$_POST['duree'],
                $_POST['difficulte'],
                (int)$_POST['calories'],
                $nouvelleImage ?: $recette['image']
            );
            $this->updateRecette($obj, (int)$id);
            $this->ingredientModel->deleteByRecette((int)$id);
            $this->sauvegarderIngredients((int)$id, $_POST);
            $_SESSION['success'] = 'Recette modifiée avec succès !';
            header('Location: /2A35/Admin/recette'); exit;
        }
        $ingredients = $this->rebuildIngredients($_POST);
        require_once 'View/back/recette/form.php';
    }

    // ── Détail recette ────────────────────────────────────────────────────────
    public function show(string $id): void {
        $recette = $this->showRecette((int)$id);
        if (!$recette) {
            $_SESSION['error'] = 'Recette introuvable.';
            header('Location: /2A35/Admin/recette'); exit;
        }
        $ingredients        = $this->ingredientModel->getByRecette((int)$id);
        $valeursNutri       = $this->ingredientModel->getValeursNutritionnelles((int)$id);
        require_once 'View/back/recette/show.php';
    }

    // ── Supprimer une recette ─────────────────────────────────────────────────
    public function delete(string $id): void {
        $recette = $this->showRecette((int)$id);
        if ($recette) {
            if ($recette['image'] && file_exists('assets/uploads/recettes/' . $recette['image'])) {
                unlink('assets/uploads/recettes/' . $recette['image']);
            }
            $this->deleteRecette((int)$id);
            $_SESSION['success'] = 'Recette supprimée avec succès !';
        } else {
            $_SESSION['error'] = 'Recette introuvable.';
        }
        header('Location: /2A35/Admin/recette'); exit;
    }

    // =========================================================================
    // Méthodes PDO (structure comme l'exemple de la prof)
    // =========================================================================

    public function addRecette(Recette $recette): int {
        $sql = "INSERT INTO recette (nom, description, categorie, duree, difficulte, calories, image)
                VALUES (:nom, :description, :categorie, :duree, :difficulte, :calories, :image)";
        $db  = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom'         => $recette->getNom(),
                'description' => $recette->getDescription(),
                'categorie'   => $recette->getCategorie(),
                'duree'       => $recette->getDuree(),
                'difficulte'  => $recette->getDifficulte(),
                'calories'    => $recette->getCalories(),
                'image'       => $recette->getImage(),
            ]);
            return (int)$db->lastInsertId();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return 0;
        }
    }

    public function updateRecette(Recette $recette, int $id): void {
        $sql = "UPDATE recette SET nom=:nom, description=:description, categorie=:categorie,
                duree=:duree, difficulte=:difficulte, calories=:calories, image=:image WHERE id=:id";
        $db  = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id'          => $id,
                'nom'         => $recette->getNom(),
                'description' => $recette->getDescription(),
                'categorie'   => $recette->getCategorie(),
                'duree'       => $recette->getDuree(),
                'difficulte'  => $recette->getDifficulte(),
                'calories'    => $recette->getCalories(),
                'image'       => $recette->getImage(),
            ]);
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function deleteRecette(int $id): void {
        $sql = "DELETE FROM recette WHERE id = :id";
        $db  = Database::getConnection();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function showRecette(int $id): array|false {
        $sql = "SELECT * FROM recette WHERE id = :id";
        $db  = Database::getConnection();
        $query = $db->prepare($sql);
        try {
            $query->execute([':id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function listRecettes(): array {
        $sql = "SELECT * FROM recette ORDER BY created_at DESC";
        $db  = Database::getConnection();
        try {
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // ── Statistiques des recettes ─────────────────────────────────────────────
    public function stats(): void {
        $db = Database::getConnection();

        // Stats par catégorie
        $q = $db->query("SELECT categorie, COUNT(*) as total FROM recette GROUP BY categorie ORDER BY total DESC");
        $statsCat = $q->fetchAll();

        // Stats par difficulté
        $q = $db->query("SELECT difficulte, COUNT(*) as total FROM recette GROUP BY difficulte");
        $statsDiff = $q->fetchAll();

        // Stats calories
        $q = $db->query("SELECT 
            SUM(CASE WHEN calories < 300 THEN 1 ELSE 0 END) as moins300,
            SUM(CASE WHEN calories BETWEEN 300 AND 600 THEN 1 ELSE 0 END) as entre300_600,
            SUM(CASE WHEN calories BETWEEN 601 AND 900 THEN 1 ELSE 0 END) as entre600_900,
            SUM(CASE WHEN calories > 900 THEN 1 ELSE 0 END) as plus900
            FROM recette");
        $statsCal = $q->fetch();

        $totalRecettes = array_sum(array_column($statsDiff, 'total'));

        require_once 'View/back/recette/stats.php';
    }

    // ── Endpoint AJAX recherche dynamique ─────────────────────────────────────
    public function ajax(): void {
        header('Content-Type: application/json');
        $search     = trim($_GET['search']     ?? '');
        $categorie  = trim($_GET['categorie']  ?? '');
        $difficulte = trim($_GET['difficulte'] ?? '');
        $recettes   = $this->recetteModel->filter($search, $categorie, $difficulte);
        echo json_encode($recettes);
        exit;
    }

    // =========================================================================
    // Méthodes privées utilitaires
    // =========================================================================

    private function valider(array $post): array {
        $errors = [];
        $nom = trim($post['nom'] ?? '');
        if ($nom === '')            $errors['nom'] = 'Le nom est obligatoire.';
        elseif (strlen($nom) < 3)   $errors['nom'] = 'Le nom doit contenir au moins 3 caractères.';
        elseif (strlen($nom) > 150) $errors['nom'] = 'Le nom ne peut pas dépasser 150 caractères.';
        elseif (preg_match('/\d/', $nom)) $errors['nom'] = 'Le nom ne doit pas contenir de chiffres.';

        $desc = trim($post['description'] ?? '');
        if ($desc === '')           $errors['description'] = 'La description est obligatoire.';
        elseif (strlen($desc) < 10) $errors['description'] = 'La description doit contenir au moins 10 caractères.';

        $cats = ['petit-dejeuner','dejeuner','diner','collation','dessert','vegetarien','regime','sportif'];
        if (empty($post['categorie']) || !in_array($post['categorie'], $cats, true))
            $errors['categorie'] = 'Veuillez sélectionner une catégorie valide.';

        $diffs = ['facile','moyen','difficile'];
        if (empty($post['difficulte']) || !in_array($post['difficulte'], $diffs, true))
            $errors['difficulte'] = 'Veuillez sélectionner un niveau de difficulté.';

        $duree = $post['duree'] ?? '';
        if (!ctype_digit((string)$duree) || (int)$duree < 1)
            $errors['duree'] = 'La durée doit être un entier positif.';
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
                $errors['image'] = 'Format non accepté (JPG, PNG, WEBP).';
            elseif ($_FILES['image']['size'] > 2 * 1024 * 1024)
                $errors['image'] = "L'image ne doit pas dépasser 2 Mo.";
        }
        return $errors;
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
        $ids    = $post['ing_id']       ?? [];
        $qtes   = $post['ing_quantite'] ?? [];
        $unites = $post['ing_unite']    ?? [];
        foreach ($ids as $i => $ingId) {
            if (empty($ingId)) continue;
            $qte   = (float)($qtes[$i] ?? 0);
            $unite = trim($unites[$i] ?? '');
            if ($qte <= 0 || $unite === '') continue;
            $this->ingredientModel->addToRecette($recetteId, (int)$ingId, $qte, $unite);
        }
    }

    private function rebuildIngredients(array $post): array {
        $result = [];
        foreach ($post['ing_id'] ?? [] as $i => $id) {
            $result[] = [
                'ingredient_id' => $id,
                'nom'           => $post['ing_nom'][$i]      ?? '',
                'quantite'      => $post['ing_quantite'][$i] ?? '',
                'unite'         => $post['ing_unite'][$i]    ?? '',
            ];
        }
        return $result;
    }
}
