<?php
require_once 'Model/Event.php';
require_once 'Model/EventType.php';
require_once 'Controller/back/EventTypeController.php';
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

    public function getById($id) {
        $stmt = $this->db->prepare(
            'SELECT e.*, t.label AS type_label FROM event e JOIN event_type t ON e.id_type = t.id WHERE e.id = :id'
        );
        $stmt->execute(['id' => (int) $id]);
        return $stmt->fetch();
    }

    public function createEvent($name, $id_type, $date, $location, $number_of_participants) {
        $stmt = $this->db->prepare(
            'INSERT INTO event (name, id_type, date, location, number_of_participants) VALUES (:name, :id_type, :date, :location, :number_of_participants)'
        );
        return $stmt->execute([
            'name' => $name,
            'id_type' => (int) $id_type,
            'date' => $date,
            'location' => $location,
            'number_of_participants' => (int) $number_of_participants
        ]);
    }

    public function updateEvent($id, $name, $id_type, $date, $location, $number_of_participants) {
        $stmt = $this->db->prepare(
            'UPDATE event SET name = :name, id_type = :id_type, date = :date, location = :location, number_of_participants = :number_of_participants WHERE id = :id'
        );
        return $stmt->execute([
            'id' => (int) $id,
            'name' => $name,
            'id_type' => (int) $id_type,
            'date' => $date,
            'location' => $location,
            'number_of_participants' => (int) $number_of_participants
        ]);
    }

    public function deleteEvent($id) {
        $stmt = $this->db->prepare('DELETE FROM event WHERE id = :id');
        return $stmt->execute(['id' => (int) $id]);
    }

    public function incrementParticipants($id) {
        $stmt = $this->db->prepare(
            'UPDATE event SET number_of_participants = number_of_participants + 1 WHERE id = :id'
        );
        return $stmt->execute(['id' => (int) $id]);
    }

    public function index() {
        $events = $this->getAll();
        require_once 'View/back/event/index.php';
    }

    public function create() {
        $typeController = new EventTypeController();
        $types = $typeController->getAll();
        require_once 'View/back/event/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createEvent($_POST['name'], $_POST['id_type'], $_POST['date'], $_POST['location'], $_POST['number_of_participants']);
            header('Location: /2A35/Admin/evenement');
            exit;
        }
    }

    public function edit($id) {
        $event = $this->getById($id);
        $typeController = new EventTypeController();
        $types = $typeController->getAll();
        require_once 'View/back/event/edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateEvent($id, $_POST['name'], $_POST['id_type'], $_POST['date'], $_POST['location'], $_POST['number_of_participants']);
            header('Location: /2A35/Admin/evenement');
            exit;
        }
    }

    public function delete($id) {
        $this->deleteEvent($id);
        header('Location: /2A35/Admin/evenement');
        exit;
    }
}


