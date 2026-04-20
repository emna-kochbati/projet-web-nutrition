<?php

require_once __DIR__ . '/../Config/database.php';

class AdminController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ================= DASHBOARD ================= */
    public function dashboard()
    {
        require_once __DIR__ . '/../View/back/pages/dashboard.php';
    }

    /* ================= USERS LIST ================= */
    public function users()
    {
        $stmt = $this->db->query("SELECT * FROM user ORDER BY id DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../View/back/pages/users.php';
    }

    /* ================= ADD USER ================= */
    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $stmt = $this->db->prepare("
                INSERT INTO user (nom, email, password, poids, taille, objectif)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $_POST['nom'],
                $_POST['email'],
                $_POST['password'],
                $_POST['poids'],
                $_POST['taille'],
                $_POST['objectif']
            ]);
        }

        header("Location: /ProjetWeb-User/index.php?url=Admin/users");
        exit;
    }

    /* ================= SHOW USER (STYLE PROF COMME BOOK) ================= */
    public function showUser($user)
    {
        echo "
        <table border='2'>
            <tr>
                <th>ID</th>
                <th>NOM</th>
                <th>EMAIL</th>
                <th>POIDS</th>
                <th>TAILLE</th>
                <th>OBJECTIF</th>
            </tr>
            <tr>
                <td>".$user['id']."</td>
                <td>".$user['nom']."</td>
                <td>".$user['email']."</td>
                <td>".$user['poids']."</td>
                <td>".$user['taille']."</td>
                <td>".$user['objectif']."</td>
            </tr>
        </table>
        ";
    }

    /* ================= VIEW USER ================= */
    public function viewUser($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE id=?");
        $stmt->execute([$id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $this->showUser($user);
        } else {
            echo "User not found";
        }
    }

    /* ================= UPDATE USER ================= */
    public function updateUser($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $stmt = $this->db->prepare("
                UPDATE user 
                SET nom=?, email=?, password=?, poids=?, taille=?, objectif=?
                WHERE id=?
            ");

            $stmt->execute([
                $_POST['nom'],
                $_POST['email'],
                $_POST['password'],
                $_POST['poids'],
                $_POST['taille'],
                $_POST['objectif'],
                $id
            ]);
        }

        header("Location: /ProjetWeb-User/index.php?url=Admin/users");
        exit;
    }

    /* ================= DELETE USER ================= */
    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM user WHERE id=?");
        $stmt->execute([$id]);

        header("Location: /ProjetWeb-User/index.php?url=Admin/users");
        exit;
    }
}