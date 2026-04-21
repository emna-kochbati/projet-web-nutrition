<?php
require_once 'Model/Restaurant.php';
require_once 'Model/Meal.php';

class RestaurantController {

    private Restaurant $restaurantModel;
    private Meal       $mealModel;

    public function __construct() {
        $this->restaurantModel = new Restaurant();
        $this->mealModel       = new Meal();
    }

    // ── GET /Admin/restaurant ─────────────────────────────────────────────────
    public function index(): void {
        $search      = trim($_GET['search'] ?? '');
        $restaurants = $search
            ? $this->restaurantModel->search($search)
            : $this->restaurantModel->getAll();

        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error']   ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once 'View/back/restaurant/list.php';
    }

    // ── GET /Admin/restaurant/create ──────────────────────────────────────────
    public function create(): void {
        $errors = [];
        $meals  = [];
        require_once 'View/back/restaurant/form.php';
    }

    // ── POST /Admin/restaurant/store ──────────────────────────────────────────
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2A35/Admin/restaurant'); exit;
        }

        $errors = $this->validateRestaurant($_POST);

        if (empty($errors)) {
            $data          = $this->sanitizeRestaurant($_POST);
            $data['image'] = $this->handleImageUpload('restaurants');
            $restaurantId  = $this->restaurantModel->create($data);

            // Sauvegarder les plats soumis
            $this->saveMeals($restaurantId, $_POST['meals'] ?? []);

            $_SESSION['success'] = 'Restaurant ajouté avec succès !';
            header('Location: /2A35/Admin/restaurant'); exit;
        }

        $restaurant = $_POST;
        $meals      = [];
        require_once 'View/back/restaurant/form.php';
    }

    // ── GET /Admin/restaurant/edit/{id} ───────────────────────────────────────
    public function edit(string $id): void {
        $restaurant = $this->restaurantModel->getById((int)$id);
        if (!$restaurant) {
            $_SESSION['error'] = 'Restaurant introuvable.';
            header('Location: /2A35/Admin/restaurant'); exit;
        }
        $errors = [];
        $meals  = $this->mealModel->getByRestaurant((int)$id);
        require_once 'View/back/restaurant/form.php';
    }

    // ── POST /Admin/restaurant/update/{id} ────────────────────────────────────
    public function update(string $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2A35/Admin/restaurant'); exit;
        }

        $restaurant = $this->restaurantModel->getById((int)$id);
        if (!$restaurant) {
            $_SESSION['error'] = 'Restaurant introuvable.';
            header('Location: /2A35/Admin/restaurant'); exit;
        }

        $errors = $this->validateRestaurant($_POST);

        if (empty($errors)) {
            $data          = $this->sanitizeRestaurant($_POST);
            $newImage      = $this->handleImageUpload('restaurants');
            $data['image'] = $newImage ?: $restaurant['image'];
            $this->restaurantModel->update((int)$id, $data);

            // Mettre à jour les plats
            $this->saveMeals((int)$id, $_POST['meals'] ?? []);

            $_SESSION['success'] = 'Restaurant modifié avec succès !';
            header('Location: /2A35/Admin/restaurant'); exit;
        }

        $meals = $this->mealModel->getByRestaurant((int)$id);
        require_once 'View/back/restaurant/form.php';
    }

    // ── GET /Admin/restaurant/show/{id} ───────────────────────────────────────
    public function show(string $id): void {
        $restaurant = $this->restaurantModel->getById((int)$id);
        if (!$restaurant) {
            $_SESSION['error'] = 'Restaurant introuvable.';
            header('Location: /2A35/Admin/restaurant'); exit;
        }
        $meals = $this->mealModel->getByRestaurant((int)$id);
        require_once 'View/back/restaurant/show.php';
    }

    // ── POST /Admin/restaurant/delete/{id} ────────────────────────────────────
    public function delete(string $id): void {
        $restaurant = $this->restaurantModel->getById((int)$id);
        if ($restaurant) {
            if ($restaurant['image'] && file_exists('assets/uploads/restaurants/' . $restaurant['image'])) {
                unlink('assets/uploads/restaurants/' . $restaurant['image']);
            }
            $this->restaurantModel->delete((int)$id);
            $_SESSION['success'] = 'Restaurant supprimé avec succès !';
        } else {
            $_SESSION['error'] = 'Restaurant introuvable.';
        }
        header('Location: /2A35/Admin/restaurant'); exit;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Méthodes privées
    // ─────────────────────────────────────────────────────────────────────────

    private function validateRestaurant(array $post): array {
        $errors = [];

        if (empty(trim($post['nom'] ?? ''))) {
            $errors['nom'] = 'Le nom du restaurant est obligatoire.';
        } elseif (strlen(trim($post['nom'])) < 2 || strlen(trim($post['nom'])) > 150) {
            $errors['nom'] = 'Le nom doit contenir entre 2 et 150 caractères.';
        }

        if (empty(trim($post['adresse'] ?? ''))) {
            $errors['adresse'] = 'L\'adresse est obligatoire.';
        }

        $types = ['tunisienne','italienne','japonaise','americaine','indienne','mexicaine','française','autre'];
        if (empty($post['type_cuisine']) || !in_array($post['type_cuisine'], $types)) {
            $errors['type_cuisine'] = 'Veuillez sélectionner un type de cuisine valide.';
        }

        if (!empty($post['email']) && !filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'adresse email n\'est pas valide.';
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

    private function sanitizeRestaurant(array $post): array {
        return [
            'nom'          => htmlspecialchars(trim($post['nom'])),
            'description'  => htmlspecialchars(trim($post['description'] ?? '')),
            'adresse'      => htmlspecialchars(trim($post['adresse'])),
            'telephone'    => htmlspecialchars(trim($post['telephone'] ?? '')),
            'email'        => trim($post['email'] ?? ''),
            'type_cuisine' => $post['type_cuisine'],
        ];
    }

    private function handleImageUpload(string $folder): ?string {
        if (empty($_FILES['image']['name'])) return null;
        $uploadDir = "assets/uploads/$folder/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid($folder . '_') . '.' . strtolower($ext);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
        return $filename;
    }

    private function saveMeals(int $restaurantId, array $mealsPost): void {
        if (empty($mealsPost)) return;

        // Récupérer les IDs existants pour ce restaurant
        $existing = $this->mealModel->getByRestaurant($restaurantId);
        $existingIds = array_column($existing, 'id');
        $submittedIds = [];

        foreach ($mealsPost as $m) {
            if (empty(trim($m['nom'] ?? ''))) continue;

            $data = [
                'restaurant_id' => $restaurantId,
                'nom'           => htmlspecialchars(trim($m['nom'])),
                'description'   => '',
                'prix'          => is_numeric($m['prix'] ?? '') ? (float)$m['prix'] : 0,
                'categorie'     => $m['categorie'] ?? 'plat_principal',
                'calories'      => !empty($m['calories']) ? (int)$m['calories'] : null,
                'disponible'    => isset($m['disponible']) ? 1 : 0,
                'image'         => null,
            ];

            if (!empty($m['id']) && in_array((int)$m['id'], $existingIds)) {
                // Mise à jour
                $existingMeal = $this->mealModel->getById((int)$m['id']);
                $data['image'] = $existingMeal['image'] ?? null;
                $this->mealModel->update((int)$m['id'], $data);
                $submittedIds[] = (int)$m['id'];
            } else {
                // Création
                $newId = $this->mealModel->create($data);
                $submittedIds[] = $newId;
            }
        }

        // Supprimer les plats retirés du formulaire
        foreach ($existingIds as $eid) {
            if (!in_array($eid, $submittedIds)) {
                $this->mealModel->delete($eid);
            }
        }
    }
}
