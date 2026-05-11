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
        $search     = trim($_GET['search'] ?? '');
        $categorie  = trim($_GET['categorie'] ?? '');
        $difficulte = trim($_GET['difficulte'] ?? '');
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $perPage    = 10;

        [$where, $bind] = $this->recetteFilterBindings($search, $categorie, $difficulte);

        $sqlCount = 'SELECT COUNT(*) FROM recette' . $where;
        $stmt = $this->db->prepare($sqlCount);
        $stmt->execute($bind);
        $total      = (int)$stmt->fetchColumn();
        $totalPages = max(1, (int)ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $sqlStats = 'SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN difficulte = \'facile\' THEN 1 ELSE 0 END) AS faciles,
                COALESCE(AVG(calories), 0) AS moy_kcal,
                COALESCE(AVG(duree), 0) AS moy_duree
            FROM recette' . $where;
        $stmt = $this->db->prepare($sqlStats);
        $stmt->execute($bind);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total' => 0, 'faciles' => 0, 'moy_kcal' => 0, 'moy_duree' => 0,
        ];

        $sqlList = 'SELECT * FROM recette' . $where . ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sqlList);
        foreach ($bind as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $recettes = $stmt->fetchAll();

        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error']   ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once 'View/back/recette/list.php';
    }

    // GET /Admin/recette/ajax?search=&categorie=&difficulte= (AJAX, même filtres que la liste)
    public function ajax(): void {
        $search     = trim($_GET['search'] ?? '');
        $categorie  = trim($_GET['categorie'] ?? '');
        $difficulte = trim($_GET['difficulte'] ?? '');

        [$where, $bind] = $this->recetteFilterBindings($search, $categorie, $difficulte);
        $sql = 'SELECT * FROM recette' . $where . ' ORDER BY created_at DESC LIMIT 200';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($bind);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($stmt->fetchAll());
        exit;
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
            $duree    = isset($_POST['duree']) && $_POST['duree'] !== '' ? (int)$_POST['duree'] : null;
            $calories = isset($_POST['calories']) && $_POST['calories'] !== '' ? (int)$_POST['calories'] : null;

            $r = new Recette(
                null,
                htmlspecialchars(trim($_POST['nom'])),
                htmlspecialchars(trim($_POST['description'] ?? '')),
                isset($_POST['categorie']) ? htmlspecialchars(trim((string)$_POST['categorie'])) : null,
                $duree,
                isset($_POST['difficulte']) ? htmlspecialchars(trim((string)$_POST['difficulte'])) : null,
                $calories,
                $this->handleImageUpload(),
                null
            );

            $this->db->prepare(
                "INSERT INTO recette (nom, description, categorie, duree, difficulte, calories, image)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            )->execute([
                $r->getNom(),
                $r->getDescription(),
                $r->getCategorie(),
                $r->getDuree(),
                $r->getDifficulte(),
                $r->getCalories(),
                $r->getImage(),
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
            $duree    = isset($_POST['duree']) && $_POST['duree'] !== '' ? (int)$_POST['duree'] : null;
            $calories = isset($_POST['calories']) && $_POST['calories'] !== '' ? (int)$_POST['calories'] : null;

            $r = new Recette(
                (int)$id,
                htmlspecialchars(trim($_POST['nom'])),
                htmlspecialchars(trim($_POST['description'] ?? '')),
                isset($_POST['categorie']) ? htmlspecialchars(trim((string)$_POST['categorie'])) : null,
                $duree,
                isset($_POST['difficulte']) ? htmlspecialchars(trim((string)$_POST['difficulte'])) : null,
                $calories,
                $newImage ?: $recette['image'],
                null
            );

            $this->db->prepare(
                "UPDATE recette SET nom=?, description=?, categorie=?, duree=?, difficulte=?, calories=?, image=? WHERE id=?"
            )->execute([
                $r->getNom(),
                $r->getDescription(),
                $r->getCategorie(),
                $r->getDuree(),
                $r->getDifficulte(),
                $r->getCalories(),
                $r->getImage(),
                $r->getId(),
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

    /**
     * Filtres liste / pagination / AJAX (valeurs GET whitelistées pour l’SQL).
     *
     * @return array{0: string, 1: array<string, mixed>} [ WHERE clause, bind params ]
     */
    private function recetteFilterBindings(string $search, string $categorie, string $difficulte): array {
        $conditions = [];
        $bind       = [];

        if ($search !== '') {
            $conditions[]              = '(nom LIKE :f_search_nom OR categorie LIKE :f_search_cat)';
            $bind[':f_search_nom']     = '%' . $search . '%';
            $bind[':f_search_cat']     = '%' . $search . '%';
        }

        $allowedCat = ['petit-dejeuner', 'dejeuner', 'diner', 'collation', 'dessert', 'vegetarien', 'regime', 'sportif'];
        if ($categorie !== '' && in_array($categorie, $allowedCat, true)) {
            $conditions[]         = 'categorie = :f_cat';
            $bind[':f_cat']       = $categorie;
        }

        $allowedDif = ['facile', 'moyen', 'difficile'];
        if ($difficulte !== '' && in_array($difficulte, $allowedDif, true)) {
            $conditions[]          = 'difficulte = :f_dif';
            $bind[':f_dif']        = $difficulte;
        }

        $where = $conditions !== [] ? (' WHERE ' . implode(' AND ', $conditions)) : '';
        return [$where, $bind];
    }

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

        $categories = ['petit-dejeuner','dejeuner','diner','collation','dessert','vegetarien','regime','sportif'];
        $difficultes = ['facile','moyen','difficile'];

        if (empty(trim($post['nom'] ?? ''))) {
            $errors['nom'] = 'Le nom de la recette est obligatoire.';
        } elseif (strlen(trim($post['nom'])) < 2 || strlen(trim($post['nom'])) > 150) {
            $errors['nom'] = 'Le nom doit contenir entre 2 et 150 caractères.';
        }

        $cat = trim($post['categorie'] ?? '');
        if ($cat === '' || !in_array($cat, $categories, true)) {
            $errors['categorie'] = 'Veuillez sélectionner une catégorie valide.';
        }

        $dif = trim($post['difficulte'] ?? '');
        if ($dif === '' || !in_array($dif, $difficultes, true)) {
            $errors['difficulte'] = 'Veuillez sélectionner une difficulté valide.';
        }

        if (!isset($post['duree']) || $post['duree'] === '' || !is_numeric($post['duree'])) {
            $errors['duree'] = 'La durée est obligatoire (en minutes).';
        } else {
            $d = (int)$post['duree'];
            if ($d < 1 || $d > 1440) {
                $errors['duree'] = 'La durée doit être entre 1 et 1440 minutes.';
            }
        }

        if (!isset($post['calories']) || $post['calories'] === '' || !is_numeric($post['calories'])) {
            $errors['calories'] = 'Les calories sont obligatoires.';
        } else {
            $c = (int)$post['calories'];
            if ($c < 0 || $c > 99999) {
                $errors['calories'] = 'Les calories doivent être entre 0 et 99999.';
            }
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
