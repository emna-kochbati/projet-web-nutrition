<?php
require_once 'Model/Recette.php';

class RecetteController {

    private Recette $recetteModel;

    public function __construct() {
        $this->recetteModel = new Recette();
    }

    // GET /Admin/recette
    public function index(): void {
        $search  = trim($_GET['search'] ?? '');
        $recettes = $search
            ? $this->recetteModel->search($search)
            : $this->recetteModel->getAll();

        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error']   ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once 'View/back/recette/list.php';
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
            $data          = $this->sanitize($_POST);
            $data['image'] = $this->handleImageUpload();
            $this->recetteModel->create($data);
            $_SESSION['success'] = 'Recette ajoutée avec succès !';
            header('Location: /2A35/Admin/recette'); exit;
        }

        $recette = $_POST;
        require_once 'View/back/recette/form.php';
    }

    // GET /Admin/recette/edit/{id}
    public function edit(string $id): void {
        $recette = $this->recetteModel->getById((int)$id);
        if (!$recette) {
            $_SESSION['error'] = 'Recette introuvable.';
            header('Location: /2A35/Admin/recette'); exit;
        }
        $errors = [];
        require_once 'View/back/recette/form.php';
    }

    // POST /Admin/recette/update/{id}
    public function update(string $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2A35/Admin/recette'); exit;
        }

        $recette = $this->recetteModel->getById((int)$id);
        if (!$recette) {
            $_SESSION['error'] = 'Recette introuvable.';
            header('Location: /2A35/Admin/recette'); exit;
        }

        $errors = $this->validate($_POST);

        if (empty($errors)) {
            $data          = $this->sanitize($_POST);
            $newImage      = $this->handleImageUpload();
            $data['image'] = $newImage ?: $recette['image'];
            $this->recetteModel->update((int)$id, $data);
            $_SESSION['success'] = 'Recette modifiée avec succès !';
            header('Location: /2A35/Admin/recette'); exit;
        }

        require_once 'View/back/recette/form.php';
    }

    // POST /Admin/recette/delete/{id}
    public function delete(string $id): void {
        $recette = $this->recetteModel->getById((int)$id);
        if ($recette) {
            if (!empty($recette['image']) && file_exists('assets/uploads/recettes/' . $recette['image'])) {
                unlink('assets/uploads/recettes/' . $recette['image']);
            }
            $this->recetteModel->delete((int)$id);
            $_SESSION['success'] = 'Recette supprimée avec succès !';
        } else {
            $_SESSION['error'] = 'Recette introuvable.';
        }
        header('Location: /2A35/Admin/recette'); exit;
    }

    // ── Privé ─────────────────────────────────────────────────────────────────

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

    private function sanitize(array $post): array {
        return [
            'nom'               => htmlspecialchars(trim($post['nom'])),
            'description'       => htmlspecialchars(trim($post['description'] ?? '')),
            'ingredients'       => htmlspecialchars(trim($post['ingredients'] ?? '')),
            'categorie'         => $post['categorie'] ?? null,
            'temps_preparation' => $post['temps_preparation'] ?? null,
            'calories'          => $post['calories'] ?? null,
        ];
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
