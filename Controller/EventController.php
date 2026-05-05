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

    public function decrementAvailablePlaces($id) {
        $stmt = $this->db->prepare(
            'UPDATE event SET number_of_participants = number_of_participants - 1 WHERE id = :id'
        );
        return $stmt->execute(['id' => (int) $id]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare(
            'SELECT e.*, t.label AS type_label, t.image AS type_image 
             FROM event e 
             JOIN event_type t ON e.id_type = t.id 
             WHERE e.id = :id'
        );
        $stmt->execute(['id' => (int) $id]);
        return $stmt->fetch();
    }

    public function show($id) {
        $event = $this->getById($id);
        if (!$event) {
            header('Location: /2A35/Event');
            exit;
        }
        require_once 'View/front/event_details.php';
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
        $event = $this->getById($id);
        if ($event) {
            // LOGIQUE MÉTIER : Vérifier si l'inscription est possible via les méthodes du contrôleur
            if ($this->hasAvailablePlaces($event) && !$this->isPast($event)) {
                $this->decrementAvailablePlaces($id);
            }
        }
        
        header('Location: /2A35/Event');
        exit;
    }

    /* --- LOGIQUE MÉTIER (Déplacée du Modèle) --- */

    public function hasAvailablePlaces(array $event): bool {
        return (int)$event['number_of_participants'] > 0;
    }

    public function isPast(array $event): bool {
        return strtotime($event['date']) < strtotime('today');
    }

    public function getStatusLabel(array $event): string {
        if ($this->isPast($event)) {
            return "Terminé";
        }
        if (!$this->hasAvailablePlaces($event)) {
            return "Complet";
        }
        if ((int)$event['number_of_participants'] <= 5) {
            return "Dernières places !";
        }
        return "Ouvert";
    }

    public function getStatusColor(array $event): string {
        if ($this->isPast($event)) return "secondary";
        if (!$this->hasAvailablePlaces($event)) return "danger";
        if ((int)$event['number_of_participants'] <= 5) return "warning";
        return "success";
    }

    private function callGemini($prompt) {
        $apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
        if (empty($apiKey) || $apiKey === 'YOUR_API_KEY_HERE') {
            return "Please provide a valid Gemini API Key in config.php";
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $apiKey;
        
        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return "CURL Error: " . $curlError;
        }

        $result = json_decode($response, true);
        
        if (isset($result['error'])) {
            return "Gemini API Error: " . ($result['error']['message'] ?? 'Unknown error') . " (Raw: " . substr($response, 0, 100) . ")";
        }

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        }

        return "AI Error: Response format unexpected. Raw Response: " . substr($response, 0, 200);
    }

    public function generateDescription($id = null) {
        $id = $id ?? ($_GET['id'] ?? 0);
        $event = $this->getById($id);
        
        if (!$event) {
            echo "Event not found.";
            exit;
        }

        $prompt = "Rédige une description accrocheuse et professionnelle de 3 phrases pour un événement nommé '" . $event['name'] . "' de type '" . $event['type_label'] . "' se déroulant à " . $event['location'] . ". Le ton doit être enthousiaste et inciter les gens à participer.";
        
        echo $this->callGemini($prompt);
        exit;
    }

    public function generateFaq($id = null) {
        $id = $id ?? ($_GET['id'] ?? 0);
        $event = $this->getById($id);
        
        if (!$event) {
            echo "Event not found.";
            exit;
        }

        $prompt = "Génère 3 questions fréquemment posées avec leurs réponses pour un événement nommé '" . $event['name'] . "' à " . $event['location'] . ". 
                   Retourne le résultat strictement sous forme d'un tableau JSON d'objets avec les clés 'q' and 'a'. 
                   Exemple: [{\"q\": \"Question ?\", \"a\": \"Réponse.\"}]
                   Les questions et réponses doivent être en français.";
        
        $rawResponse = $this->callGemini($prompt);
        
        // Clean the response in case AI adds markdown code blocks
        $json = preg_replace('/^```json\s*|\s*```$/i', '', trim($rawResponse));
        
        header('Content-Type: application/json');
        echo $json;
        exit;
    }
}
