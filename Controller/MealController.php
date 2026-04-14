<?php
require_once 'Model/Meal.php';
require_once 'Model/Restaurant.php';

class MealController {

    private Meal       $mealModel;
    private Restaurant $restaurantModel;

    public function __construct() {
        $this->mealModel       = new Meal();
        $this->restaurantModel = new Restaurant();
    }

    // ── GET /Admin/meal ───────────────────────────────────────────────────────
    public function index(): void {
        $search = trim($_GET['search'] ?? '');
        $meals  = $search ? $this->mealModel->search($search) : $this->mealModel->getAll();

        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error']   ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once 'View/back/meal/list.php';
    }

    // ── GET /Admin/meal/create ────────────────────────────────────────────────
    public function create(): void {
        $errors      = [];
        $restaurants = $this->restaurantModel->getAll();
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
            $data          = $this->sanitizeMeal($_POST);
            $data['image'] = $this->handleImageUpload();
            $this->mealModel->create($data);
            $_SESSION['success'] = 'Plat ajouté avec succès !';
            $rid = $data['restaurant_id'] ?? 0;
            header('Location: ' . ($rid ? '/2A35/Admin/restaurant/edit/'.$rid : '/2A35/Admin/meal')); exit;
        }

        $meal        = $_POST;
        $restaurants = $this->restaurantModel->getAll();
        require_once 'View/back/meal/form.php';
    }

    // ── GET /Admin/meal/edit/{id} ─────────────────────────────────────────────
    public function edit(string $id): void {
        $meal = $this->mealModel->getById((int)$id);
        if (!$meal) {
            $_SESSION['error'] = 'Plat introuvable.';
            header('Location: /2A35/Admin/meal'); exit;
        }
        $errors      = [];
        $restaurants = $this->restaurantModel->getAll();
        require_once 'View/back/meal/form.php';
    }

    // ── POST /Admin/meal/update/{id} ──────────────────────────────────────────
    public function update(string $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2A35/Admin/meal'); exit;
        }

        $meal = $this->mealModel->getById((int)$id);
        if (!$meal) {
            $_SESSION['error'] = 'Plat introuvable.';
            header('Location: /2A35/Admin/meal'); exit;
        }

        $errors = $this->validateMeal($_POST);

        if (empty($errors)) {
            $data          = $this->sanitizeMeal($_POST);
            $newImage      = $this->handleImageUpload();
            $data['image'] = $newImage ?: $meal['image'];
            $this->mealModel->update((int)$id, $data);
            $_SESSION['success'] = 'Plat modifié avec succès !';
            $rid = $data['restaurant_id'] ?? 0;
            header('Location: ' . ($rid ? '/2A35/Admin/restaurant/edit/'.$rid : '/2A35/Admin/meal')); exit;
        }

        $restaurants = $this->restaurantModel->getAll();
        require_once 'View/back/meal/form.php';
    }

    // ── GET /Admin/meal/show/{id} ─────────────────────────────────────────────
    public function show(string $id): void {
        $meal = $this->mealModel->getById((int)$id);
        if (!$meal) {
            $_SESSION['error'] = 'Plat introuvable.';
            header('Location: /2A35/Admin/meal'); exit;
        }
        require_once 'View/back/meal/show.php';
    }

    // ── POST /Admin/meal/delete/{id} ──────────────────────────────────────────
    public function delete(string $id): void {
        $meal = $this->mealModel->getById((int)$id);
        if ($meal) {
            if ($meal['image'] && file_exists('assets/uploads/meals/' . $meal['image'])) {
                unlink('assets/uploads/meals/' . $meal['image']);
            }
            $rid = $meal['restaurant_id'] ?? 0;
            $this->mealModel->delete((int)$id);
            $_SESSION['success'] = 'Plat supprimé avec succès !';
            header('Location: ' . ($rid ? '/2A35/Admin/restaurant/edit/'.$rid : '/2A35/Admin/meal')); exit;
        } else {
            $_SESSION['error'] = 'Plat introuvable.';
        }
        header('Location: /2A35/Admin/meal'); exit;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Méthodes privées
    // ─────────────────────────────────────────────────────────────────────────

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

    private function sanitizeMeal(array $post): array {
        return [
            'restaurant_id' => (int)$post['restaurant_id'],
            'nom'           => htmlspecialchars(trim($post['nom'])),
            'description'   => htmlspecialchars(trim($post['description'] ?? '')),
            'prix'          => (float)$post['prix'],
            'categorie'     => $post['categorie'],
            'calories'      => !empty($post['calories']) ? (int)$post['calories'] : null,
            'disponible'    => isset($post['disponible']) ? 1 : 0,
        ];
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
