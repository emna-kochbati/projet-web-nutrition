<?php

session_start(); // 🔥 IMPORTANT

require_once __DIR__ . '/../Model/User.php';

class UserController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // 🔐 PAGE LOGIN
    public function auth()
    {
        require_once __DIR__ . '/../View/front/pages/auth.php';
    }

    // 🔐 LOGIN ACTION
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->login($email, $password);

            if ($user) {

                $_SESSION['user'] = $user;

                // ✅ REDIRECTION PROPRE (PROJET ACTUEL)
                header("Location: /ProjetWeb-User/index.php?url=User/dashboard");
                exit;
            }

            echo "❌ Email ou mot de passe incorrect";
        }
    }

    // 📝 REGISTER
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->userModel->create($_POST);

            header("Location: /ProjetWeb-User/index.php?url=User/auth");
            exit;
        }
    }

    // 📊 DASHBOARD
    public function dashboard()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: /ProjetWeb-User/index.php?url=User/auth");
            exit;
        }

        require_once __DIR__ . '/../View/front/pages/dashboard.php';
    }

    // 👤 PROFILE
    public function profile()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: /ProjetWeb-User/index.php?url=User/auth");
            exit;
        }

        require_once __DIR__ . '/../View/front/pages/profile.php';
    }

    // 🚪 LOGOUT
    public function logout()
    {
        session_destroy();

        header("Location: /ProjetWeb-User/index.php?url=User/auth");
        exit;
    }
}