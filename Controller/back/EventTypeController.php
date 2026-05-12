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

    public function createEventType($label, $image) {
        $req = $this->db->prepare('INSERT INTO event_type (label, image) VALUES (:label, :image)');
        $req->execute(['label' => $label, 'image' => $image]);
    }

    public function updateEventType($id, $label, $image = null) {
        if ($image) {
            $req = $this->db->prepare('UPDATE event_type SET label = :label, image = :image WHERE id = :id');
            $req->execute(['id' => $id, 'label' => $label, 'image' => $image]);
        } else {
            $req = $this->db->prepare('UPDATE event_type SET label = :label WHERE id = :id');
            $req->execute(['id' => $id, 'label' => $label]);
        }
    }

    public function deleteEventType($id) {
        $type = $this->getById($id);
        if ($type && !empty($type['image']) && file_exists($type['image'])) {
            unlink($type['image']);
        }
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

    private function handleImageUpload($file) {
        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'assets/img/';
            $fileName = time() . '_' . basename($file['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return $targetPath;
            }
        }
        return null;
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imagePath = $this->handleImageUpload($_FILES['image']);
            $this->createEventType($_POST['label'], $imagePath);
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
            $imagePath = $this->handleImageUpload($_FILES['image']);
            
            if ($imagePath) {
                $oldType = $this->getById($id);
                if ($oldType && !empty($oldType['image']) && file_exists($oldType['image'])) {
                    unlink($oldType['image']);
                }
            }
            
            $this->updateEventType($id, $_POST['label'], $imagePath);
            header('Location: /2A35/back/EventType');
            exit;
        }
    }

    public function delete($id) {
        try {
            $this->deleteEventType($id);
        } catch (Exception $e) {
        }
        header('Location: /2A35/back/EventType');
        exit;
    }
}

