<?php
require_once 'Model/Event.php';
require_once 'Model/EventType.php';

class EventController {
    public function index() {
        $eventModel = new Event();
        $events = $eventModel->getAll();
        require_once 'View/back/event/index.php';
    }

    public function create() {
        $typeModel = new EventType();
        $types = $typeModel->getAll();
        require_once 'View/back/event/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventModel = new Event();
            $eventModel->create($_POST['name'], $_POST['id_type'], $_POST['date'], $_POST['location'], $_POST['number_of_participants']);
            header('Location: /2A35/back/Event');
            exit;
        }
    }

    public function edit($id) {
        $eventModel = new Event();
        $event = $eventModel->getById($id);
        $typeModel = new EventType();
        $types = $typeModel->getAll();
        require_once 'View/back/event/edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventModel = new Event();
            $eventModel->update($id, $_POST['name'], $_POST['id_type'], $_POST['date'], $_POST['location'], $_POST['number_of_participants']);
            header('Location: /2A35/back/Event');
            exit;
        }
    }

    public function delete($id) {
        $eventModel = new Event();
        $eventModel->delete($id);
        header('Location: /2A35/back/Event');
        exit;
    }
}
