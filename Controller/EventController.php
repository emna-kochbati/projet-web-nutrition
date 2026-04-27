<?php
require_once 'Model/Event.php';
require_once 'config.php';

class EventController {
    private PDO $db;

    public function __construct() {
        $this->db = Db::getConnexion();
    }

    public function getAll() {
        $stmt = $this->db->query(
            'SELECT e.*, t.label AS type_label, t.image AS type_image FROM event e JOIN event_type t ON e.id_type = t.id ORDER BY e.date DESC'
        );
        return $stmt->fetchAll();
    }

    public function incrementParticipants($id) {
        $stmt = $this->db->prepare(
            'UPDATE event SET number_of_participants = number_of_participants + 1 WHERE id = :id'
        );
        return $stmt->execute(['id' => (int) $id]);
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 6;
        $result = $this->getPaginated($page, $limit);
        
        $events = $result['data'];
        $totalPages = $result['pages'];
        $currentPage = $result['current_page'];
        
        // Fetch all types for the filter box
        $stmtTypes = $this->db->query('SELECT * FROM event_type ORDER BY label ASC');
        $allTypes = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);
        
        require_once 'View/front/event.php';
    }

    public function search() {
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $typeId = isset($_GET['type']) ? (int)$_GET['type'] : 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 6;
        
        $result = $this->getPaginated($page, $limit, $query, $typeId);
        
        $events = $result['data'];
        $totalPages = $result['pages'];
        $currentPage = $result['current_page'];
        
        // Return only the partial view for AJAX
        require_once 'View/front/partials/event_cards.php';
    }

    private function getPaginated($page = 1, $limit = 6, $query = '', $typeId = 0) {
        $offset = ($page - 1) * $limit;
        $params = [];
        
        $sql = 'SELECT e.*, t.label AS type_label, t.image AS type_image 
                FROM event e 
                JOIN event_type t ON e.id_type = t.id';
        
        $countSql = 'SELECT COUNT(*) FROM event e JOIN event_type t ON e.id_type = t.id';
        
        $conditions = [];
        if ($query !== '') {
            $conditions[] = '(e.name LIKE :q OR e.location LIKE :q OR t.label LIKE :q)';
            $params['q'] = "%$query%";
        }
        
        if ($typeId > 0) {
            $conditions[] = 'e.id_type = :type';
            $params['type'] = $typeId;
        }
        
        if (!empty($conditions)) {
            $where = ' WHERE ' . implode(' AND ', $conditions);
            $sql .= $where;
            $countSql .= $where;
        }
        
        $sql .= ' ORDER BY e.date DESC LIMIT :limit OFFSET :offset';
        
        // Count total
        $stmtCount = $this->db->prepare($countSql);
        $stmtCount->execute($params);
        $total = $stmtCount->fetchColumn();
        
        // Fetch data
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ];
    }

    public function getStatistics() {
        // 1. Events per Type
        $stmtType = $this->db->query(
            'SELECT t.label, COUNT(e.id) as count 
             FROM event_type t 
             LEFT JOIN event e ON t.id = e.id_type 
             GROUP BY t.id'
        );
        $types = $stmtType->fetchAll(PDO::FETCH_ASSOC);

        // 2. Events per Location
        $stmtLoc = $this->db->query(
            'SELECT location, COUNT(id) as count 
             FROM event 
             GROUP BY location 
             ORDER BY count DESC 
             LIMIT 10'
        );
        $locations = $stmtLoc->fetchAll(PDO::FETCH_ASSOC);

        // 3. Participants per Event
        $stmtPart = $this->db->query(
            'SELECT name, number_of_participants 
             FROM event 
             ORDER BY number_of_participants DESC 
             LIMIT 10'
        );
        $participants = $stmtPart->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode([
            'types' => $types,
            'locations' => $locations,
            'participants' => $participants
        ]);
        exit;
    }

    public function register($id) {
        $this->incrementParticipants($id);
        header('Location: /2A35/Event');
        exit;
    }
}
