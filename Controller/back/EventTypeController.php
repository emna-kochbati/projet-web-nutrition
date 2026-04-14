<?php
require_once 'Model/EventType.php';

class EventTypeController {
    public function index() {
        $typeModel = new EventType();
        $types = $typeModel->getAll();
        require_once 'View/back/event_type/index.php';
    }

    public function create() {
        require_once 'View/back/event_type/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $typeModel = new EventType();
            $typeModel->create($_POST['label']);
            header('Location: /2A35/back/EventType');
            exit;
        }
    }

    public function edit($id) {
        $typeModel = new EventType();
        $type = $typeModel->getById($id);
        require_once 'View/back/event_type/edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $typeModel = new EventType();
            $typeModel->update($id, $_POST['label']);
            header('Location: /2A35/back/EventType');
            exit;
        }
    }

    public function delete($id) {
        $typeModel = new EventType();
        try {
            $typeModel->delete($id);
        } catch (Exception $e) {
            // Handle foreign key error if needed, for now just redirect
        }
        header('Location: /2A35/back/EventType');
        exit;
    }
}
