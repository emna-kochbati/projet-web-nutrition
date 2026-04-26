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
            'SELECT e.*, t.label AS type_label FROM event e JOIN event_type t ON e.id_type = t.id ORDER BY e.date DESC'
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
        $events = $this->getAll();
        require_once 'View/front/event.php';
    }

    public function register($id) {
        $this->incrementParticipants($id);
        header('Location: /2A35/Event');
        exit;
    }
}
