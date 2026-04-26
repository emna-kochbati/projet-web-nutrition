<?php
require_once 'Model/EventType.php';
require_once 'config.php';

class EventTypeController {
    private PDO $db;

    public function __construct() {
        $this->db = Db::getConnexion();
    }

    public function getAll() {
        $req = $this->db->query('SELECT * FROM event_type');
        return $req->fetchAll();
    }

    public function getById($id) {
        $req = $this->db->prepare('SELECT * FROM event_type WHERE id = :id');
        $req->execute(['id' => $id]);
        return $req->fetch();
    }

    public function createEventType($label) {
        $req = $this->db->prepare('INSERT INTO event_type (label) VALUES (:label)');
        $req->execute(['label' => $label]);
    }

    public function updateEventType($id, $label) {
        $req = $this->db->prepare('UPDATE event_type SET label = :label WHERE id = :id');
        $req->execute(['id' => $id, 'label' => $label]);
    }

    public function deleteEventType($id) {
        $req = $this->db->prepare('DELETE FROM event_type WHERE id = :id');
        $req->execute(['id' => $id]);
    }

    public function index() {
        $types = $this->getAll();
        require_once 'View/back/event_type/index.php';
    }

    public function create() {
        require_once 'View/back/event_type/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createEventType($_POST['label']);
            header('Location: /2A35/back/EventType');
            exit;
        }
    }

    public function edit($id) {
        $type = $this->getById($id);
        require_once 'View/back/event_type/edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateEventType($id, $_POST['label']);
            header('Location: /2A35/back/EventType');
            exit;
        }
    }

    public function delete($id) {
        try {
            $this->deleteEventType($id);
        } catch (Exception $e) {
            // Handle foreign key error if needed, for now just redirect
        }
        header('Location: /2A35/back/EventType');
        exit;
    }
}

