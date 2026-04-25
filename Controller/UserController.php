<?php

require_once __DIR__ . '/../Config/database.php';

class UserController
{

    /* =========================
       PAGE AUTH
    ========================== */
    public function auth()
    {
        require_once __DIR__ . '/../View/front/pages/auth.php';
    }

    /* =========================
       LOGIN
    ========================== */
    public function login()
    {
        $db = Database::getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email    = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                header("Location: index.php?url=User/auth");
                exit;
            }

            $stmt = $db->prepare("SELECT * FROM user WHERE email=?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {

                // 🔥 BLOQUAGE STATUS
                if (isset($user['status']) && $user['status'] !== 'active') {
                    die("❌ Compte non activé ou bloqué");
                }

                if ($password == $user['password']) {

                    $_SESSION['user'] = [
                        "id"       => $user['id'],
                        "nom"      => $user['nom'],
                        "email"    => $user['email'],
                        "role"     => strtolower($user['role']),
                        "poids"    => $user['poids'],
                        "taille"   => $user['taille'],
                        "age"      => $user['age'] ?? '',
                        "objectif" => $user['objectif']
                    ];

                    if ($_SESSION['user']['role'] === 'admin') {
                        header("Location: index.php?url=Admin/dashboard");
                    } else {
                        header("Location: index.php?url=User/home");
                    }
                    exit;
                }
            }

            header("Location: index.php?url=User/auth");
            exit;
        }
    }

    /* =========================
       REGISTER (AVEC ACTIVATION)
    ========================== */
    public function register()
    {
        $db = Database::getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nom      = trim($_POST['nom'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $age      = trim($_POST['age'] ?? '');
            $poids    = trim($_POST['poids'] ?? '');
            $taille   = trim($_POST['taille'] ?? '');
            $maladie  = trim($_POST['maladie'] ?? '');
            $role     = trim($_POST['role'] ?? 'user');
            $objectif = trim($_POST['objectif'] ?? '');
            $activite = trim($_POST['activite'] ?? '');

            // 🔥 TOKEN ACTIVATION
            $token = bin2hex(random_bytes(32));

            $stmt = $db->prepare("
                INSERT INTO user 
                (nom,email,password,age,poids,taille,maladie,role,objectif,activite,status,activation_token)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
            ");

            $stmt->execute([
                $nom,
                $email,
                $password,
                $age,
                $poids,
                $taille,
                $maladie,
                $role,
                $objectif,
                $activite,
                'inactive',
                $token
            ]);

            // 🔥 lien activation (simulation email)
            $link = "http://localhost/ProjetWeb-User/index.php?url=User/activate&token=$token";

            echo "📧 Activation compte : <br>";
            echo "<a href='$link'>$link</a>";

            exit;
        }
    }

    /* =========================
       ACTIVER COMPTE
    ========================== */
    public function activate()
    {
        $db = Database::getConnection();

        $token = $_GET['token'] ?? null;

        $stmt = $db->prepare("SELECT id FROM user WHERE activation_token=?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {

            $db->prepare("
                UPDATE user 
                SET status='active', activation_token=NULL
                WHERE id=?
            ")->execute([$user['id']]);

            echo "✅ Compte activé";
        } else {
            echo "❌ lien invalide";
        }
    }

    /* =========================
       UPDATE PROFILE
    ========================== */
    public function update()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=User/auth");
            exit;
        }

        $db = Database::getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id       = $_SESSION['user']['id'];
            $nom      = trim($_POST['nom'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $age      = trim($_POST['age'] ?? '');
            $poids    = trim($_POST['poids'] ?? '');
            $taille   = trim($_POST['taille'] ?? '');
            $objectif = trim($_POST['objectif'] ?? '');

            if (!empty($password)) {
                $stmt = $db->prepare("
                    UPDATE user 
                    SET nom=?, email=?, password=?, age=?, poids=?, taille=?, objectif=? 
                    WHERE id=?
                ");
                $stmt->execute([$nom, $email, $password, $age, $poids, $taille, $objectif, $id]);
            } else {
                $stmt = $db->prepare("
                    UPDATE user 
                    SET nom=?, email=?, age=?, poids=?, taille=?, objectif=? 
                    WHERE id=?
                ");
                $stmt->execute([$nom, $email, $age, $poids, $taille, $objectif, $id]);
            }

            $_SESSION['user']['nom'] = $nom;
            $_SESSION['user']['email'] = $email;

            header("Location: index.php?url=User/profile");
            exit;
        }
    }

    /* =========================
       RESET PASSWORD (TEL)
    ========================== */
    public function requestReset()
    {
        $db = Database::getConnection();

        $phone = $_POST['phone'];
        $code = rand(100000, 999999);

        $db->prepare("
            UPDATE user 
            SET reset_code=?, reset_expire=DATE_ADD(NOW(), INTERVAL 10 MINUTE)
            WHERE phone=?
        ")->execute([$code, $phone]);

        echo "📱 Code SMS (simulation): $code";
    }

    public function verifyReset()
    {
        $db = Database::getConnection();

        $code = $_POST['code'];

        $stmt = $db->prepare("
            SELECT * FROM user 
            WHERE reset_code=? AND reset_expire > NOW()
        ");
        $stmt->execute([$code]);

        $user = $stmt->fetch();

        if ($user) {
            echo "OK";
        } else {
            echo "❌ code invalide";
        }
    }

    public function changePassword()
    {
        $db = Database::getConnection();

        $code = $_POST['code'];
        $password = $_POST['password'];

        $db->prepare("
            UPDATE user 
            SET password=?, reset_code=NULL
            WHERE reset_code=?
        ")->execute([$password, $code]);

        echo "✅ password changé";
    }

    /* =========================
       LOGOUT
    ========================== */
    public function logout()
    {
        session_destroy();
        header("Location: index.php?url=User/auth");
        exit;
    }

    /* =========================
       CRUD (VIDE PROF)
    ========================== */
    public function addUser($user) {}
    public function getUser($id) {}
    public function updateUser($user) {}
    public function deleteUser($id) {}
}