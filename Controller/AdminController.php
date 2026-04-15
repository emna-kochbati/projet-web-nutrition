<?php

require_once __DIR__ . '/../Model/User.php';

class AdminController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /* ================= DASHBOARD ================= */
    public function dashboard()
    {
        require_once __DIR__ . '/../View/back/pages/dashboard.php';
    }

    /* ================= LIST USERS ================= */
    public function users()
    {
        $users = $this->userModel->getAll();
        require_once __DIR__ . '/../View/back/pages/users.php';
    }

    /* ================= ADD USER ================= */
    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // sécurité basique (trim)
            $data = [
                'nom' => trim($_POST['nom']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'poids' => $_POST['poids'] ?? null,
                'taille' => $_POST['taille'] ?? null,
                'objectif' => $_POST['objectif'] ?? 'Autre'
            ];

            $this->userModel->create($data);
        }

        header("Location: /ProjetWeb-User/Admin/users");
        exit;
    }

    /* ================= VIEW USER (AJAX MODAL) ================= */
    public function viewUser($id)
    {
        $user = $this->userModel->getById($id);

        if (!$user) {
            http_response_code(404);
            echo json_encode(["error" => "User not found"]);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode($user);
        exit;
    }

    /* ================= UPDATE USER ================= */
    public function updateUser($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'nom' => trim($_POST['nom']),
                'email' => trim($_POST['email']),
                'poids' => $_POST['poids'],
                'taille' => $_POST['taille'],
                'objectif' => $_POST['objectif']
            ];

            $this->userModel->update($id, $data);
        }

        header("Location: /ProjetWeb-User/Admin/users");
        exit;
    }

    /* ================= DELETE USER ================= */
    public function deleteUser($id)
    {
        if ($id) {
            $this->userModel->delete($id);
        }

        header("Location: /ProjetWeb-User/Admin/users");
        exit;
    }
}