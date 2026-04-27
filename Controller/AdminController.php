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

    /* ================= USERS LIST (avec filter + pagination) ================= */
    public function users()
    {
        $perPage = 8;
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $offset  = ($page - 1) * $perPage;

        // Filtres GET
        $search   = trim($_GET['search']   ?? '');
        $status   = trim($_GET['status']   ?? '');
        $objectif = trim($_GET['objectif'] ?? '');

        // Construction WHERE dynamique
        $where  = "WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $where   .= " AND (nom LIKE ? OR email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status !== '') {
            $where   .= " AND status = ?";
            $params[] = $status;
        }
        if ($objectif !== '') {
            $where   .= " AND objectif = ?";
            $params[] = $objectif;
        }

        // Total pour pagination
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM user $where");
        $countStmt->execute($params);
        $total     = (int)$countStmt->fetchColumn();
        $totalPages = max(1, ceil($total / $perPage));

        // Données page courante
        $stmt = $this->db->prepare("SELECT * FROM user $where ORDER BY id DESC LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Stats globales pour les cards
        $stats = $this->getStats();

        require_once __DIR__ . '/../View/back/pages/users.php';
    }

    /* ================= AJAX SEARCH ================= */
    public function searchUsers()
    {
        header('Content-Type: application/json');

        $perPage  = 8;
        $page     = max(1, (int)($_GET['page']     ?? 1));
        $offset   = ($page - 1) * $perPage;
        $search   = trim($_GET['search']   ?? '');
        $status   = trim($_GET['status']   ?? '');
        $objectif = trim($_GET['objectif'] ?? '');

        $where  = "WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $where   .= " AND (nom LIKE ? OR email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status !== '') {
            $where   .= " AND status = ?";
            $params[] = $status;
        }
        if ($objectif !== '') {
            $where   .= " AND objectif = ?";
            $params[] = $objectif;
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM user $where");
        $countStmt->execute($params);
        $total      = (int)$countStmt->fetchColumn();
        $totalPages = max(1, ceil($total / $perPage));

        $stmt = $this->db->prepare("SELECT * FROM user $where ORDER BY id DESC LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'users'      => $users,
            'total'      => $total,
            'page'       => $page,
            'totalPages' => $totalPages
        ]);
        exit;
    }

    /* ================= STATS (pour dashboard + cards) ================= */
    public function getStats(): array
    {
        return [
            'total'    => (int)$this->db->query("SELECT COUNT(*) FROM user")->fetchColumn(),
            'active'   => (int)$this->db->query("SELECT COUNT(*) FROM user WHERE status='active'")->fetchColumn(),
            'inactive' => (int)$this->db->query("SELECT COUNT(*) FROM user WHERE status='inactive'")->fetchColumn(),
            'banned'   => (int)$this->db->query("SELECT COUNT(*) FROM user WHERE status='banned'")->fetchColumn(),

            // Répartition objectifs
            'objectifs' => $this->db->query("
                SELECT objectif, COUNT(*) as cnt 
                FROM user 
                GROUP BY objectif
            ")->fetchAll(PDO::FETCH_ASSOC),

            // Nouveaux 7 derniers jours (si colonne created_at existe)
            'newThisWeek' => (int)$this->db->query("
                SELECT COUNT(*) FROM user 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ")->fetchColumn(),
        ];
    }

    /* ================= AJAX STATS (pour graphes dynamiques) ================= */
    public function statsJson()
    {
        header('Content-Type: application/json');
        echo json_encode($this->getStats());
        exit;
    }

    /* ================= ADD USER ================= */
    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Vérifier email unique
            $check = $this->db->prepare("SELECT id FROM user WHERE email = ?");
            $check->execute([$_POST['email']]);
            if ($check->fetch()) {
                $_SESSION['admin_error'] = "Email déjà utilisé.";
                header("Location: /ProjetWeb-User/index.php?url=Admin/users");
                exit;
            }

            $stmt = $this->db->prepare("
                INSERT INTO user (nom, email, password, poids, taille, objectif, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                trim($_POST['nom']),
                trim($_POST['email']),
                password_hash(trim($_POST['password']), PASSWORD_DEFAULT),
                trim($_POST['poids']),
                trim($_POST['taille']),
                trim($_POST['objectif']),
                trim($_POST['status'] ?? 'inactive'),
            ]);

            $_SESSION['admin_success'] = "Utilisateur ajouté avec succès.";
        }

        header("Location: /ProjetWeb-User/index.php?url=Admin/users");
        exit;
    }

    /* ================= SHOW USER (style prof) ================= */
    public function showUser($user)
    {
        echo "
        <table border='2'>
            <tr>
                <th>ID</th><th>NOM</th><th>EMAIL</th>
                <th>POIDS</th><th>TAILLE</th><th>OBJECTIF</th><th>STATUS</th>
            </tr>
            <tr>
                <td>".$user['id']."</td>
                <td>".$user['nom']."</td>
                <td>".$user['email']."</td>
                <td>".$user['poids']."</td>
                <td>".$user['taille']."</td>
                <td>".$user['objectif']."</td>
                <td>".$user['status']."</td>
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

        if ($user) $this->showUser($user);
        else echo "Utilisateur introuvable.";
    }

    /* ================= UPDATE USER ================= */
    public function updateUser($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Mise à jour avec ou sans nouveau mot de passe
            if (!empty($_POST['password'])) {
                $stmt = $this->db->prepare("
                    UPDATE user 
                    SET nom=?, email=?, password=?, poids=?, taille=?, objectif=?, status=?
                    WHERE id=?
                ");
                $stmt->execute([
                    trim($_POST['nom']),
                    trim($_POST['email']),
                    password_hash(trim($_POST['password']), PASSWORD_DEFAULT),
                    trim($_POST['poids']),
                    trim($_POST['taille']),
                    trim($_POST['objectif']),
                    trim($_POST['status']),
                    $id
                ]);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE user 
                    SET nom=?, email=?, poids=?, taille=?, objectif=?, status=?
                    WHERE id=?
                ");
                $stmt->execute([
                    trim($_POST['nom']),
                    trim($_POST['email']),
                    trim($_POST['poids']),
                    trim($_POST['taille']),
                    trim($_POST['objectif']),
                    trim($_POST['status']),
                    $id
                ]);
            }

            $_SESSION['admin_success'] = "Utilisateur mis à jour.";
        }

        header("Location: /ProjetWeb-User/index.php?url=Admin/users");
        exit;
    }

    /* ================= DELETE USER ================= */
    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM user WHERE id=?");
        $stmt->execute([$id]);

        $_SESSION['admin_success'] = "Utilisateur supprimé.";
        header("Location: /ProjetWeb-User/index.php?url=Admin/users");
        exit;
    }

    /* ================= TOGGLE STATUS (AJAX) ================= */
    public function toggleStatus()
    {
        header('Content-Type: application/json');

        $id     = (int)($_POST['id']     ?? 0);
        $status = trim($_POST['status'] ?? '');

        $allowed = ['active', 'inactive', 'banned'];
        if (!$id || !in_array($status, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Données invalides.']);
            exit;
        }

        $this->db->prepare("UPDATE user SET status=? WHERE id=?")
                 ->execute([$status, $id]);

        echo json_encode(['success' => true, 'status' => $status]);
        exit;
    }
}