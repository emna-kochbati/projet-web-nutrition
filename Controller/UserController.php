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
                $_SESSION['error'] = "Veuillez remplir tous les champs.";
                header("Location: index.php?url=User/auth");
                exit;
            }

            $stmt = $db->prepare("SELECT * FROM user WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {

                // BLOQUAGE STATUS
                if (isset($user['status']) && $user['status'] !== 'active') {
                    $_SESSION['error'] = "Compte non activé. Vérifiez votre email.";
                    header("Location: index.php?url=User/auth");
                    exit;
                }

                // Vérification password (hash ou plain pour compatibilité)
                $valid = password_verify($password, $user['password'])
                      || $password === $user['password'];

                if ($valid) {
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

            $_SESSION['error'] = "Email ou mot de passe incorrect.";
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

            // Vérifier email unique
            $check = $db->prepare("SELECT id FROM user WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $_SESSION['error'] = "Cet email est déjà utilisé.";
                header("Location: index.php?url=User/auth");
                exit;
            }

            // Hash du password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Token d'activation
            $token = bin2hex(random_bytes(32));

            $stmt = $db->prepare("
                INSERT INTO user 
                (nom, email, password, age, poids, taille, maladie, role, objectif, activite, status, activation_token)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'inactive', ?)
            ");

            $stmt->execute([
                $nom, $email, $hashedPassword,
                $age, $poids, $taille, $maladie,
                $role, $objectif, $activite, $token
            ]);

            // Lien d'activation
            $link = "http://localhost/ProjetWeb-User/index.php?url=User/activate&token=" . $token;

            // Afficher la page email simulé
            $_SESSION['activation_link'] = $link;
            $_SESSION['activation_email'] = $email;
            header("Location: index.php?url=User/registerSuccess");
            exit;
        }
    }

    /* =========================
       PAGE CONFIRMATION REGISTER
    ========================== */
    public function registerSuccess()
    {
        require_once __DIR__ . '/../View/front/pages/register_success.php';
    }

    /* =========================
       ACTIVER COMPTE
    ========================== */
    public function activate()
    {
        $db = Database::getConnection();

        $token = $_GET['token'] ?? null;

        if (!$token) {
            require_once __DIR__ . '/../View/front/pages/activate_error.php';
            exit;
        }

        $stmt = $db->prepare("SELECT id, nom FROM user WHERE activation_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            $db->prepare("
                UPDATE user 
                SET status = 'active', activation_token = NULL
                WHERE id = ?
            ")->execute([$user['id']]);

            $_SESSION['activated_name'] = $user['nom'];
            require_once __DIR__ . '/../View/front/pages/activate_success.php';
        } else {
            require_once __DIR__ . '/../View/front/pages/activate_error.php';
        }
    }

    /* =========================
       RESET PASSWORD — ÉTAPE 1
       Demande par numéro de tél
    ========================== */
    public function resetPassword()
    {
        require_once __DIR__ . '/../View/front/pages/reset_password.php';
    }

    public function requestReset()
    {
        $db = Database::getConnection();

        $phone = trim($_POST['phone'] ?? '');

        if (empty($phone)) {
            $_SESSION['reset_error'] = "Numéro de téléphone requis.";
            header("Location: index.php?url=User/resetPassword");
            exit;
        }

        // Vérifier que ce numéro existe
        $stmt = $db->prepare("SELECT id FROM user WHERE phone = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['reset_error'] = "Aucun compte lié à ce numéro.";
            header("Location: index.php?url=User/resetPassword");
            exit;
        }

        $code = rand(100000, 999999);

        $db->prepare("
            UPDATE user 
            SET reset_code = ?, reset_expire = DATE_ADD(NOW(), INTERVAL 10 MINUTE)
            WHERE phone = ?
        ")->execute([$code, $phone]);

        // Simulation SMS : stocker en session pour afficher à l'utilisateur
        $_SESSION['reset_phone']   = $phone;
        $_SESSION['reset_sms_sim'] = $code;   // simulation uniquement
        $_SESSION['reset_step']    = 'verify';

        header("Location: index.php?url=User/resetPassword");
        exit;
    }

    /* =========================
       RESET PASSWORD — ÉTAPE 2
       Vérification code SMS
    ========================== */
    public function verifyReset()
    {
        $db = Database::getConnection();

        $code = trim($_POST['code'] ?? '');

        if (empty($code)) {
            $_SESSION['reset_error'] = "Code requis.";
            header("Location: index.php?url=User/resetPassword");
            exit;
        }

        $stmt = $db->prepare("
            SELECT id FROM user 
            WHERE reset_code = ? AND reset_expire > NOW()
        ");
        $stmt->execute([$code]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['reset_code']    = $code;
            $_SESSION['reset_step']    = 'newpwd';
            $_SESSION['reset_user_id'] = $user['id'];
            header("Location: index.php?url=User/resetPassword");
        } else {
            $_SESSION['reset_error'] = "Code invalide ou expiré.";
            header("Location: index.php?url=User/resetPassword");
        }
        exit;
    }

    /* =========================
       RESET PASSWORD — ÉTAPE 3
       Nouveau mot de passe
    ========================== */
    public function changePassword()
    {
        $db = Database::getConnection();

        $code     = $_SESSION['reset_code']    ?? null;
        $userId   = $_SESSION['reset_user_id'] ?? null;
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['confirm']   ?? '');

        if (!$code || !$userId) {
            header("Location: index.php?url=User/resetPassword");
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['reset_error'] = "Le mot de passe doit contenir au moins 8 caractères.";
            header("Location: index.php?url=User/resetPassword");
            exit;
        }

        if ($password !== $confirm) {
            $_SESSION['reset_error'] = "Les mots de passe ne correspondent pas.";
            header("Location: index.php?url=User/resetPassword");
            exit;
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $db->prepare("
            UPDATE user 
            SET password = ?, reset_code = NULL, reset_expire = NULL
            WHERE id = ?
        ")->execute([$hashed, $userId]);

        // Nettoyer session reset
        unset($_SESSION['reset_code'], $_SESSION['reset_user_id'],
              $_SESSION['reset_step'], $_SESSION['reset_phone'],
              $_SESSION['reset_sms_sim']);

        $_SESSION['success'] = "Mot de passe modifié avec succès ! Connectez-vous.";
        header("Location: index.php?url=User/auth");
        exit;
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
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("
                    UPDATE user 
                    SET nom=?, email=?, password=?, age=?, poids=?, taille=?, objectif=? 
                    WHERE id=?
                ");
                $stmt->execute([$nom, $email, $hashed, $age, $poids, $taille, $objectif, $id]);
            } else {
                $stmt = $db->prepare("
                    UPDATE user 
                    SET nom=?, email=?, age=?, poids=?, taille=?, objectif=? 
                    WHERE id=?
                ");
                $stmt->execute([$nom, $email, $age, $poids, $taille, $objectif, $id]);
            }

            $_SESSION['user']['nom']   = $nom;
            $_SESSION['user']['email'] = $email;

            header("Location: index.php?url=User/profile");
            exit;
        }
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
       HOME
    ========================== */
    public function home()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=User/auth");
            exit;
        }
        require_once __DIR__ . '/../View/front/pages/home.php';
    }

    /* =========================
       PROFILE
    ========================== */
    public function profile()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=User/auth");
            exit;
        }
        require_once __DIR__ . '/../View/front/pages/profile.php';
    }

    /* =========================
       CRUD
    ========================== */
    public function addUser($user)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO user (nom, email, password, poids, taille, objectif)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user->getNom(),
            $user->getEmail(),
            password_hash($user->getPassword(), PASSWORD_DEFAULT),
            $user->getPoids(),
            $user->getTaille(),
            $user->getObjectif()
        ]);
        return $db->lastInsertId();
    }

    public function getUser($id)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM user WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($user)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE user SET nom=?, email=?, poids=?, taille=?, objectif=? WHERE id=?
        ");
        $stmt->execute([
            $user->getNom(),
            $user->getEmail(),
            $user->getPoids(),
            $user->getTaille(),
            $user->getObjectif(),
            $user->getId()
        ]);
    }

    public function deleteUser($id)
    {
        $db = Database::getConnection();
        $db->prepare("DELETE FROM user WHERE id = ?")->execute([$id]);
    }
}