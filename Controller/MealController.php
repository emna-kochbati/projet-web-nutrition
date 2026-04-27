<?php
require_once 'Model/Meal.php';
require_once 'Model/Restaurant.php';
require_once 'Config/database.php';

class MealController {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // ── GET /Admin/meal ───────────────────────────────────────────────────────
    public function index(): void {
        $search = trim($_GET['search'] ?? '');

        if ($search) {
            $stmt = $this->db->prepare(
                "SELECT m.*, r.nom AS restaurant_nom FROM meal m
                 JOIN restaurant r ON r.id = m.restaurant_id
                 WHERE m.nom LIKE ? ORDER BY m.created_at DESC"
            );
            $stmt->execute(['%'.$search.'%']);
        } else {
            $stmt = $this->db->query(
                "SELECT m.*, r.nom AS restaurant_nom FROM meal m
                 JOIN restaurant r ON r.id = m.restaurant_id
                 ORDER BY m.created_at DESC"
            );
        }
        $meals = $stmt->fetchAll();

        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error']   ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once 'View/back/meal/list.php';
    }

    // ── GET /Admin/meal/create ────────────────────────────────────────────────
    public function create(): void {
        $errors      = [];
        $restaurants = $this->db->query("SELECT * FROM restaurant ORDER BY nom")->fetchAll();
        $meal        = ['restaurant_id' => $_GET['restaurant_id'] ?? ''];
        require_once 'View/back/meal/form.php';
    }

    // ── POST /Admin/meal/store ────────────────────────────────────────────────
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2A35/Admin/meal'); exit;
        }

        $errors = $this->validateMeal($_POST);

        if (empty($errors)) {
            $m = new Meal(
                null,
                (int)$_POST['restaurant_id'],
                htmlspecialchars(trim($_POST['nom'])),
                htmlspecialchars(trim($_POST['description'] ?? '')),
                (float)$_POST['prix'],
                $_POST['categorie'],
                !empty($_POST['calories']) ? (int)$_POST['calories'] : null,
                isset($_POST['disponible']) ? 1 : 0,
                $this->handleImageUpload()
            );

            $this->db->prepare(
                "INSERT INTO meal (restaurant_id, nom, description, prix, categorie, calories, disponible, image)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            )->execute([
                $m->getRestaurantId(), $m->getNom(), $m->getDescription(),
                $m->getPrix(), $m->getCategorie(), $m->getCalories(),
                $m->getDisponible(), $m->getImage()
            ]);

            $_SESSION['success'] = 'Plat ajouté avec succès !';
            $rid = $m->getRestaurantId();
            header('Location: ' . ($rid ? '/2A35/Admin/restaurant/edit/'.$rid : '/2A35/Admin/meal')); exit;
        }

        $meal        = $_POST;
        $restaurants = $this->db->query("SELECT * FROM restaurant ORDER BY nom")->fetchAll();
        require_once 'View/back/meal/form.php';
    }

    // ── GET /Admin/meal/edit/{id} ─────────────────────────────────────────────
    public function edit(string $id): void {
        $meal = $this->findMealOrRedirect((int)$id);
        $errors      = [];
        $restaurants = $this->db->query("SELECT * FROM restaurant ORDER BY nom")->fetchAll();
        require_once 'View/back/meal/form.php';
    }

    // ── POST /Admin/meal/update/{id} ──────────────────────────────────────────
    public function update(string $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2A35/Admin/meal'); exit;
        }

        $meal   = $this->findMealOrRedirect((int)$id);
        $errors = $this->validateMeal($_POST);

        if (empty($errors)) {
            $newImage = $this->handleImageUpload();
            $m = new Meal(
                (int)$id,
                (int)$_POST['restaurant_id'],
                htmlspecialchars(trim($_POST['nom'])),
                htmlspecialchars(trim($_POST['description'] ?? '')),
                (float)$_POST['prix'],
                $_POST['categorie'],
                !empty($_POST['calories']) ? (int)$_POST['calories'] : null,
                isset($_POST['disponible']) ? 1 : 0,
                $newImage ?: $meal['image']
            );

            $this->db->prepare(
                "UPDATE meal SET restaurant_id=?, nom=?, description=?, prix=?, categorie=?, calories=?, disponible=?, image=?
                 WHERE id=?"
            )->execute([
                $m->getRestaurantId(), $m->getNom(), $m->getDescription(),
                $m->getPrix(), $m->getCategorie(), $m->getCalories(),
                $m->getDisponible(), $m->getImage(), $m->getId()
            ]);

            $_SESSION['success'] = 'Plat modifié avec succès !';
            $rid = $m->getRestaurantId();
            header('Location: ' . ($rid ? '/2A35/Admin/restaurant/edit/'.$rid : '/2A35/Admin/meal')); exit;
        }

        $restaurants = $this->db->query("SELECT * FROM restaurant ORDER BY nom")->fetchAll();
        require_once 'View/back/meal/form.php';
    }

    // ── GET /Admin/meal/show/{id} ─────────────────────────────────────────────
    public function show(string $id): void {
        $meal = $this->findMealOrRedirect((int)$id);
        require_once 'View/back/meal/show.php';
    }

    // ── POST /Admin/meal/delete/{id} ──────────────────────────────────────────
    public function delete(string $id): void {
        $stmt = $this->db->prepare("SELECT * FROM meal WHERE id = ?");
        $stmt->execute([(int)$id]);
        $meal = $stmt->fetch();

        if ($meal) {
            if (!empty($meal['image']) && file_exists('assets/uploads/meals/' . $meal['image'])) {
                unlink('assets/uploads/meals/' . $meal['image']);
            }
            $rid = $meal['restaurant_id'] ?? 0;
            $this->db->prepare("DELETE FROM meal WHERE id = ?")->execute([(int)$id]);
            $_SESSION['success'] = 'Plat supprimé avec succès !';
            header('Location: ' . ($rid ? '/2A35/Admin/restaurant/edit/'.$rid : '/2A35/Admin/meal')); exit;
        }

        $_SESSION['error'] = 'Plat introuvable.';
        header('Location: /2A35/Admin/meal'); exit;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Méthodes privées
    // ─────────────────────────────────────────────────────────────────────────

    private function findMealOrRedirect(int $id): array {
        $stmt = $this->db->prepare(
            "SELECT m.*, r.nom AS restaurant_nom FROM meal m
             JOIN restaurant r ON r.id = m.restaurant_id WHERE m.id = ?"
        );
        $stmt->execute([$id]);
        $meal = $stmt->fetch();
        if (!$meal) {
            $_SESSION['error'] = 'Plat introuvable.';
            header('Location: /2A35/Admin/meal'); exit;
        }
        return $meal;
    }

    private function validateMeal(array $post): array {
        $errors = [];
        if (empty(trim($post['nom'] ?? ''))) {
            $errors['nom'] = 'Le nom du plat est obligatoire.';
        } elseif (strlen(trim($post['nom'])) < 2 || strlen(trim($post['nom'])) > 150) {
            $errors['nom'] = 'Le nom doit contenir entre 2 et 150 caractères.';
        }
        if (empty($post['restaurant_id']) || !is_numeric($post['restaurant_id'])) {
            $errors['restaurant_id'] = 'Veuillez sélectionner un restaurant.';
        }
        if (!isset($post['prix']) || !is_numeric($post['prix']) || (float)$post['prix'] < 0) {
            $errors['prix'] = 'Le prix doit être un nombre positif.';
        }
        $categories = ['entree','plat_principal','dessert','boisson','snack'];
        if (empty($post['categorie']) || !in_array($post['categorie'], $categories)) {
            $errors['categorie'] = 'Veuillez sélectionner une catégorie valide.';
        }
        if (!empty($post['calories']) && (!is_numeric($post['calories']) || (int)$post['calories'] < 0)) {
            $errors['calories'] = 'Les calories doivent être un nombre positif.';
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
        $uploadDir = 'assets/uploads/meals/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('meal_') . '.' . strtolower($ext);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
        return $filename;
    }
}
