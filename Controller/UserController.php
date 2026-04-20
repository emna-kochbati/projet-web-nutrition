<?php


require_once __DIR__ . '/../Config/database.php';

class UserController
{
    // =========================
    // INDEX → AUTH
    // =========================
    public function index()
    {
        header("Location: index.php?url=User/auth");
        exit;
    }

    // =========================
    // AUTH PAGE
    // =========================
    public function auth()
    {
        require_once __DIR__ . '/../View/front/pages/auth.php';
    }

    // =========================
    // LOGIN
    // =========================
    public function login()
    {
        $db = Database::getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = $_POST['email'];
            $password = $_POST['password'];

            $stmt = $db->prepare("SELECT * FROM user WHERE email=?");
            $stmt->execute([$email]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $password === $user['password']) {

                // ✅ SESSION OK MAINTENANT
                $_SESSION['user'] = $user;

                header("Location: index.php?url=User/home");
                exit;
            }

            echo "❌ Login incorrect";
        }
    }

    // =========================
    // HOME
    // =========================
    public function home()
    {
        require_once __DIR__ . '/../View/front/pages/home.php';
    }

    // =========================
    // DASHBOARD
    // =========================
    public function dashboard()
    {
        // 🔥 FIX IMPORTANT
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=User/auth");
            exit;
        }

        require_once __DIR__ . '/../View/front/pages/dashboard.php';
    }

    // =========================
    // PROFILE
    // =========================
    public function profile()
    {
        // 🔥 FIX IMPORTANT
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=User/auth");
            exit;
        }

        require_once __DIR__ . '/../View/front/pages/profile.php';
    }

    // =========================
    // SHOW USER (PROF STYLE)
    // =========================
    public function showUser($user)
    {
        echo "
        <table border='2'>
            <tr>
                <th>ID</th>
                <th>NOM</th>
                <th>EMAIL</th>
                <th>PASSWORD</th>
                <th>POIDS</th>
                <th>TAILLE</th>
                <th>OBJECTIF</th>
            </tr>
            <tr>
                <td>".$user->getId()."</td>
                <td>".$user->getNom()."</td>
                <td>".$user->getEmail()."</td>
                <td>".$user->getPassword()."</td>
                <td>".$user->getPoids()."</td>
                <td>".$user->getTaille()."</td>
                <td>".$user->getObjectif()."</td>
            </tr>
        </table>
        ";
    }

    // =========================
    // CRUD (VIDE PROF)
    // =========================
    public function addUser($user) {}
    public function getUser($id) {}
    public function updateUser($user) {}
    public function deleteUser($id) {}

    // =========================
    // LOGOUT
    // =========================
    public function logout()
    {
        session_destroy();
        header("Location: index.php?url=User/auth");
        exit;
    }
}
?>