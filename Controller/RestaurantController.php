<?php
require_once 'Model/Restaurant.php';
require_once 'Model/Meal.php';
require_once 'Config/database.php';

class RestaurantController {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // ── GET /Admin/restaurant ─────────────────────────────────────────────────
    public function index(): void {
        $search  = trim($_GET['search'] ?? '');
        $type    = trim($_GET['type']   ?? '');
        $perPage = 5;
        $page    = max(1, (int)($_GET['page'] ?? 1));

        $where  = [];
        $params = [];
        if ($search) { $where[] = "(nom LIKE ? OR adresse LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($type)   { $where[] = "type_cuisine = ?"; $params[] = $type; }
        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM restaurant $whereSQL");
        $stmtCount->execute($params);
        $total      = (int)$stmtCount->fetchColumn();
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        $stmt = $this->db->prepare("SELECT * FROM restaurant $whereSQL ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);
        $restaurants = $stmt->fetchAll();

        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error']   ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once 'View/back/restaurant/list.php';
    }

    // ── GET /Admin/restaurant/search?q=...&type=... (AJAX) ───────────────────
    public function search(): void {
        $q    = trim($_GET['q']    ?? '');
        $type = trim($_GET['type'] ?? '');

        if ($q) {
            $stmt = $this->db->prepare(
                "SELECT * FROM restaurant WHERE (nom LIKE ? OR adresse LIKE ?) ORDER BY created_at DESC"
            );
            $stmt->execute(['%'.$q.'%', '%'.$q.'%']);
        } else {
            $stmt = $this->db->query("SELECT * FROM restaurant ORDER BY created_at DESC");
        }
        $results = $stmt->fetchAll();

        if ($type) {
            $results = array_values(array_filter($results, fn($r) => $r['type_cuisine'] === $type));
        }

        header('Content-Type: application/json');
        echo json_encode(array_values($results));
        exit;
    }

    // ── GET /Admin/restaurant/create ──────────────────────────────────────────
    public function create(): void {
        $errors     = [];
        $restaurant = [];
        $meals      = [];
        require_once 'View/back/restaurant/form.php';
    }

    // ── POST /Admin/restaurant/store ──────────────────────────────────────────
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2A35/Admin/restaurant'); exit;
        }

        $errors = $this->validateRestaurant($_POST);

        if (empty($errors)) {
            $r = new Restaurant(
                null,
                htmlspecialchars(trim($_POST['nom'])),
                htmlspecialchars(trim($_POST['description'] ?? '')),
                htmlspecialchars(trim($_POST['adresse'])),
                htmlspecialchars(trim($_POST['telephone'] ?? '')),
                trim($_POST['email'] ?? ''),
                $_POST['type_cuisine'],
                $this->handleImageUpload('restaurants')
            );

            $stmt = $this->db->prepare(
                "INSERT INTO restaurant (nom, description, adresse, telephone, email, type_cuisine, image)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $r->getNom(), $r->getDescription(), $r->getAdresse(),
                $r->getTelephone(), $r->getEmail(), $r->getTypeCuisine(), $r->getImage()
            ]);
            $restaurantId = (int)$this->db->lastInsertId();

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
        $restaurant = $this->findRestaurantOrRedirect((int)$id);
        $errors     = [];
        $meals      = $this->getMealsByRestaurant((int)$id);
        require_once 'View/back/restaurant/form.php';
    }

    // ── POST /Admin/restaurant/update/{id} ────────────────────────────────────
    public function update(string $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2A35/Admin/restaurant'); exit;
        }

        $restaurant = $this->findRestaurantOrRedirect((int)$id);
        $errors     = $this->validateRestaurant($_POST);

        if (empty($errors)) {
            $newImage = $this->handleImageUpload('restaurants');

            $r = new Restaurant(
                (int)$id,
                htmlspecialchars(trim($_POST['nom'])),
                htmlspecialchars(trim($_POST['description'] ?? '')),
                htmlspecialchars(trim($_POST['adresse'])),
                htmlspecialchars(trim($_POST['telephone'] ?? '')),
                trim($_POST['email'] ?? ''),
                $_POST['type_cuisine'],
                $newImage ?: $restaurant['image']
            );

            $stmt = $this->db->prepare(
                "UPDATE restaurant SET nom=?, description=?, adresse=?, telephone=?, email=?, type_cuisine=?, image=?
                 WHERE id=?"
            );
            $stmt->execute([
                $r->getNom(), $r->getDescription(), $r->getAdresse(),
                $r->getTelephone(), $r->getEmail(), $r->getTypeCuisine(),
                $r->getImage(), $r->getId()
            ]);

            $this->saveMeals((int)$id, $_POST['meals'] ?? []);

            $_SESSION['success'] = 'Restaurant modifié avec succès !';
            header('Location: /2A35/Admin/restaurant'); exit;
        }

        $meals = $this->getMealsByRestaurant((int)$id);
        require_once 'View/back/restaurant/form.php';
    }

    // ── GET /Admin/restaurant/show/{id} ───────────────────────────────────────
    public function show(string $id): void {
        $restaurant = $this->findRestaurantOrRedirect((int)$id);
        $meals      = $this->getMealsByRestaurant((int)$id);
        require_once 'View/back/restaurant/show.php';
    }

    // ── POST /Admin/restaurant/delete/{id} ────────────────────────────────────
    public function delete(string $id): void {
        $stmt = $this->db->prepare("SELECT * FROM restaurant WHERE id = ?");
        $stmt->execute([(int)$id]);
        $restaurant = $stmt->fetch();

        if ($restaurant) {
            if (!empty($restaurant['image']) && file_exists('assets/uploads/restaurants/' . $restaurant['image'])) {
                unlink('assets/uploads/restaurants/' . $restaurant['image']);
            }
            $this->db->prepare("DELETE FROM restaurant WHERE id = ?")->execute([(int)$id]);
            $_SESSION['success'] = 'Restaurant supprimé avec succès !';
        } else {
            $_SESSION['error'] = 'Restaurant introuvable.';
        }
        header('Location: /2A35/Admin/restaurant'); exit;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Méthodes privées
    // ─────────────────────────────────────────────────────────────────────────

    private function findRestaurantOrRedirect(int $id): array {
        $stmt = $this->db->prepare("SELECT * FROM restaurant WHERE id = ?");
        $stmt->execute([$id]);
        $restaurant = $stmt->fetch();
        if (!$restaurant) {
            $_SESSION['error'] = 'Restaurant introuvable.';
            header('Location: /2A35/Admin/restaurant'); exit;
        }
        return $restaurant;
    }

    private function getMealsByRestaurant(int $restaurantId): array {
        $stmt = $this->db->prepare("SELECT * FROM meal WHERE restaurant_id = ? ORDER BY categorie, nom");
        $stmt->execute([$restaurantId]);
        return $stmt->fetchAll();
    }

    private function saveMeals(int $restaurantId, array $mealsPost): void {
        if (empty($mealsPost)) return;

        $stmt     = $this->db->prepare("SELECT id FROM meal WHERE restaurant_id = ?");
        $stmt->execute([$restaurantId]);
        $existingIds  = array_column($stmt->fetchAll(), 'id');
        $submittedIds = [];

        foreach ($mealsPost as $m) {
            if (empty(trim($m['nom'] ?? ''))) continue;

            $meal = new Meal(
                !empty($m['id']) ? (int)$m['id'] : null,
                $restaurantId,
                htmlspecialchars(trim($m['nom'])),
                null,
                is_numeric($m['prix'] ?? '') ? (float)$m['prix'] : 0,
                $m['categorie'] ?? 'plat_principal',
                !empty($m['calories']) ? (int)$m['calories'] : null,
                isset($m['disponible']) ? 1 : 0
            );

            if ($meal->getId() && in_array($meal->getId(), $existingIds)) {
                // Récupérer l'image existante
                $s = $this->db->prepare("SELECT image FROM meal WHERE id = ?");
                $s->execute([$meal->getId()]);
                $meal->setImage($s->fetchColumn() ?: null);

                $this->db->prepare(
                    "UPDATE meal SET nom=?, description=?, prix=?, categorie=?, calories=?, disponible=?, image=?
                     WHERE id=?"
                )->execute([
                    $meal->getNom(), $meal->getDescription(), $meal->getPrix(),
                    $meal->getCategorie(), $meal->getCalories(), $meal->getDisponible(),
                    $meal->getImage(), $meal->getId()
                ]);
                $submittedIds[] = $meal->getId();
            } else {
                $this->db->prepare(
                    "INSERT INTO meal (restaurant_id, nom, description, prix, categorie, calories, disponible, image)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                )->execute([
                    $meal->getRestaurantId(), $meal->getNom(), $meal->getDescription(),
                    $meal->getPrix(), $meal->getCategorie(), $meal->getCalories(),
                    $meal->getDisponible(), $meal->getImage()
                ]);
                $submittedIds[] = (int)$this->db->lastInsertId();
            }
        }

        // Supprimer les plats retirés
        foreach ($existingIds as $eid) {
            if (!in_array($eid, $submittedIds)) {
                $this->db->prepare("DELETE FROM meal WHERE id = ?")->execute([$eid]);
            }
        }
    }

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

    private function handleImageUpload(string $folder): ?string {
        if (empty($_FILES['image']['name'])) return null;
        $uploadDir = "assets/uploads/$folder/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid($folder . '_') . '.' . strtolower($ext);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
        return $filename;
    }
}
