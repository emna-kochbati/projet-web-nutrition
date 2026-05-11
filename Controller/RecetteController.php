<?php
require_once 'Model/Recette.php';
require_once 'Config/database.php';

class RecetteController {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // GET /Admin/recette
    public function index(): void {
        $search  = trim($_GET['search'] ?? '');

        if ($search) {
            $stmt = $this->db->prepare("SELECT * FROM recette WHERE nom LIKE ? OR categorie LIKE ? ORDER BY created_at DESC");
            $stmt->execute(['%'.$search.'%', '%'.$search.'%']);
        } else {
            $stmt = $this->db->query("SELECT * FROM recette ORDER BY created_at DESC");
        }
        $recettes = $stmt->fetchAll();

        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error']   ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once 'View/back/recette/list.php';
    }

    // GET /Admin/recette/search?q=... (AJAX)
    public function search(): void {
        $q    = trim($_GET['q'] ?? '');
        $stmt = $this->db->prepare("SELECT * FROM recette WHERE nom LIKE ? OR categorie LIKE ? ORDER BY created_at DESC");
        $stmt->execute(['%'.$q.'%', '%'.$q.'%']);
        header('Content-Type: application/json');
        echo json_encode($stmt->fetchAll());
        exit;
    }

    // GET /Admin/recette/create
    public function create(): void {
        $errors  = [];
        $recette = [];
        require_once 'View/back/recette/form.php';
    }

    // POST /Admin/recette/store
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2A35/Admin/recette'); exit;
        }

        $errors = $this->validate($_POST);

        if (empty($errors)) {
            $r = new Recette(
                null,
                htmlspecialchars(trim($_POST['nom'])),
                htmlspecialchars(trim($_POST['description'] ?? '')),
                htmlspecialchars(trim($_POST['ingredients'] ?? '')),
                $_POST['categorie'] ?? null,
                !empty($_POST['temps_preparation']) ? (int)$_POST['temps_preparation'] : null,
                !empty($_POST['calories']) ? (int)$_POST['calories'] : null,
                $this->handleImageUpload()
            );

            $this->db->prepare(
                "INSERT INTO recette (nom, description, ingredients, categorie, temps_preparation, calories, image)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            )->execute([
                $r->getNom(), $r->getDescription(), $r->getIngredients(),
                $r->getCategorie(), $r->getTempsPreparation(), $r->getCalories(), $r->getImage()
            ]);

            $_SESSION['success'] = 'Recette ajoutée avec succès !';
            header('Location: /2A35/Admin/recette'); exit;
        }

        $recette = $_POST;
        require_once 'View/back/recette/form.php';
    }

    // GET /Admin/recette/edit/{id}
    public function edit(string $id): void {
        $recette = $this->findOrRedirect((int)$id);
        $errors  = [];
        require_once 'View/back/recette/form.php';
    }

    // POST /Admin/recette/update/{id}
    public function update(string $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2A35/Admin/recette'); exit;
        }

        $recette = $this->findOrRedirect((int)$id);
        $errors  = $this->validate($_POST);

        if (empty($errors)) {
            $newImage = $this->handleImageUpload();
            $r = new Recette(
                (int)$id,
                htmlspecialchars(trim($_POST['nom'])),
                htmlspecialchars(trim($_POST['description'] ?? '')),
                htmlspecialchars(trim($_POST['ingredients'] ?? '')),
                $_POST['categorie'] ?? null,
                !empty($_POST['temps_preparation']) ? (int)$_POST['temps_preparation'] : null,
                !empty($_POST['calories']) ? (int)$_POST['calories'] : null,
                $newImage ?: $recette['image']
            );

            $this->db->prepare(
                "UPDATE recette SET nom=?, description=?, ingredients=?, categorie=?,
                 temps_preparation=?, calories=?, image=? WHERE id=?"
            )->execute([
                $r->getNom(), $r->getDescription(), $r->getIngredients(),
                $r->getCategorie(), $r->getTempsPreparation(), $r->getCalories(),
                $r->getImage(), $r->getId()
            ]);

            $_SESSION['success'] = 'Recette modifiée avec succès !';
            header('Location: /2A35/Admin/recette'); exit;
        }

        require_once 'View/back/recette/form.php';
    }

    // POST /Admin/recette/delete/{id}
    public function delete(string $id): void {
        $stmt = $this->db->prepare("SELECT * FROM recette WHERE id = ?");
        $stmt->execute([(int)$id]);
        $recette = $stmt->fetch();

        if ($recette) {
            if (!empty($recette['image']) && file_exists('assets/uploads/recettes/' . $recette['image'])) {
                unlink('assets/uploads/recettes/' . $recette['image']);
            }
            $this->db->prepare("DELETE FROM recette WHERE id = ?")->execute([(int)$id]);
            $_SESSION['success'] = 'Recette supprimée avec succès !';
        } else {
            $_SESSION['error'] = 'Recette introuvable.';
        }
        header('Location: /2A35/Admin/recette'); exit;
    }

    // ── Privé ─────────────────────────────────────────────────────────────────

    private function findOrRedirect(int $id): array {
        $stmt = $this->db->prepare("SELECT * FROM recette WHERE id = ?");
        $stmt->execute([$id]);
        $recette = $stmt->fetch();
        if (!$recette) {
            $_SESSION['error'] = 'Recette introuvable.';
            header('Location: /2A35/Admin/recette'); exit;
        }
        return $recette;
    }

    private function validate(array $post): array {
        $errors = [];
        if (empty(trim($post['nom'] ?? ''))) {
            $errors['nom'] = 'Le nom de la recette est obligatoire.';
        } elseif (strlen(trim($post['nom'])) < 2 || strlen(trim($post['nom'])) > 150) {
            $errors['nom'] = 'Le nom doit contenir entre 2 et 150 caractères.';
        }
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($_FILES['image']['type'], $allowed)) {
                $errors['image'] = 'Format image non accepté (JPG, PNG, WEBP).';
            } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $errors['image'] = 'L\'image ne doit pas dépasser 2 Mo.';
            }
        }
        return $errors;
    }

    private function handleImageUpload(): ?string {
        if (empty($_FILES['image']['name'])) return null;
        $uploadDir = 'assets/uploads/recettes/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('recette_') . '.' . strtolower($ext);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
        return $filename;
    }
}
