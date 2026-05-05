<?php

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/Email.php';
require_once __DIR__ . '/../Config/sms.php';

class UserController
{
    /* =========================
       AUTH PAGE
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

            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = "Veuillez remplir tous les champs.";
                header("Location: index.php?url=User/auth");
                exit;
            }

            $stmt = $db->prepare("SELECT * FROM user WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {

                // ❗ sécurité status
                if (!isset($user['status']) || $user['status'] !== 'active') {
                    $_SESSION['error'] = "Compte non activé.";
                    header("Location: index.php?url=User/auth");
                    exit;
                }

                // ✔ password sécurisé uniquement
                if (password_verify($password, $user['password'])) {

                    $_SESSION['user'] = $user;

                    if (strtolower($user['role']) === 'admin') {
                        header("Location: index.php?url=Admin/dashboard");
                    } else {
                        header("Location: index.php?url=User/home");
                    }
                    exit;
                }
            }

            $_SESSION['error'] = "Email ou mot de passe incorrect.";
            header("Location: index.php?url=User/auth");
            exit;
        }
    }

    /* =========================
       REGISTER + EMAIL
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
        $objectif = trim($_POST['objectif'] ?? '');
        $activite = trim($_POST['activite'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');

        // check email exist
        $check = $db->prepare("SELECT id FROM user WHERE email=?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $_SESSION['error'] = "Email déjà utilisé.";
            header("Location: index.php?url=User/auth");
            exit;
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));

        // ✅ FIX IMPORTANT ICI
        $stmt = $db->prepare("
            INSERT INTO user 
            (nom,email,password,age,poids,taille,maladie,role,objectif,activite,phone,status,activation_token)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $nom,
            $email,
            $hashed,
            $age,
            $poids,
            $taille,
            $maladie,
            'user',
            $objectif,
            $activite,
            $phone,
            'inactive',
            $token
        ]);

        // lien activation
        $link = "http://localhost/ProjetWeb-User/index.php?url=User/activate&token=".$token;

        // email
        $ok = Email::sendActivation($email, $nom, $link);

        $_SESSION['activation_link'] = $link;
        $_SESSION['activation_email'] = $email;
        $_SESSION['email_fallback'] = !$ok;

        header("Location: index.php?url=User/registerSuccess");
        exit;
    }
}

    /* =========================
       REGISTER SUCCESS
    ========================== */
    public function registerSuccess()
    {
        require_once __DIR__ . '/../View/front/pages/register_success.php';
    }

    /* =========================
       ACTIVATE ACCOUNT
    ========================== */
    public function activate()
    {
        $db = Database::getConnection();

        $token = $_GET['token'] ?? null;

        if (!$token) {
            require_once __DIR__ . '/../View/front/pages/activate_error.php';
            exit;
        }

        $stmt = $db->prepare("SELECT id, nom FROM user WHERE activation_token=?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            $db->prepare("UPDATE user SET status='active', activation_token=NULL WHERE id=?")
               ->execute([$user['id']]);

            $_SESSION['activated_name'] = $user['nom'];

            require_once __DIR__ . '/../View/front/pages/activate_success.php';
        } else {
            require_once __DIR__ . '/../View/front/pages/activate_error.php';
        }
    }

    /* =========================
       RESET PASSWORD SMS
    ========================== */
    public function resetPassword()
    {
        if (!isset($_GET['continue'])) {
            unset($_SESSION['reset_step'], $_SESSION['reset_user_id'], $_SESSION['reset_error']);
        }

        require_once __DIR__ . '/../View/front/pages/reset_password.php';
    }

    public function requestReset()
    {
        $db = Database::getConnection();

        $phone = trim($_POST['phone'] ?? '');

        $phoneClean = preg_replace('/[^0-9]/', '', $phone);
        $phoneClean = preg_replace('/^216/', '', $phoneClean);

        $stmt = $db->prepare("SELECT id FROM user WHERE phone LIKE ?");
        $stmt->execute(["%$phoneClean%"]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['reset_error'] = "Numéro non trouvé.";
            header("Location: index.php?url=User/resetPassword");
            exit;
        }

        $code = rand(100000, 999999);

        $db->prepare("
            UPDATE user 
            SET reset_code=?, reset_expire=DATE_ADD(NOW(),INTERVAL 10 MINUTE) 
            WHERE id=?
        ")->execute([$code, $user['id']]);

        SMS::send("+216".$phoneClean, "Code: ".$code);

        $_SESSION['reset_step'] = 'verify';

        header("Location: index.php?url=User/resetPassword&continue=1");
        exit;
    }

    public function verifyReset()
    {
        $db = Database::getConnection();

        $code = $_POST['code'] ?? '';

        $stmt = $db->prepare("SELECT id FROM user WHERE reset_code=? AND reset_expire>NOW()");
        $stmt->execute([$code]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['reset_user_id'] = $user['id'];
            $_SESSION['reset_step'] = 'newpwd';
        } else {
            $_SESSION['reset_error'] = "Code invalide";
        }

        header("Location: index.php?url=User/resetPassword&continue=1");
        exit;
    }

    public function changePassword()
    {
        $db = Database::getConnection();

        $id = $_SESSION['reset_user_id'] ?? null;
        $pass = $_POST['password'] ?? '';
        $conf = $_POST['confirm'] ?? '';

        if (!$id || $pass !== $conf) {
            $_SESSION['reset_error'] = "Erreur";
            header("Location: index.php?url=User/resetPassword");
            exit;
        }

        $hashed = password_hash($pass, PASSWORD_DEFAULT);

        $db->prepare("UPDATE user SET password=?, reset_code=NULL WHERE id=?")
           ->execute([$hashed, $id]);

        session_destroy();

        header("Location: index.php?url=User/auth");
        exit;
    }

    /* =========================
       PAGES
    ========================== */
    public function home()
    {
        require_once __DIR__ . '/../View/front/pages/home.php';
    }

    public function dashboard()
    {
        require_once __DIR__ . '/../View/front/pages/dashboard.php';
    }

    public function profile()
    {
        require_once __DIR__ . '/../View/front/pages/profile.php';
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php?url=User/auth");
        exit;
    }
}