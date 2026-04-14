<?php
require_once 'Model/Event.php';

class EventController {
    public function index() {
        $eventModel = new Event();
        $events = $eventModel->getAll();
        
        require_once 'View/front/event.php';
    }
    public function register($id) {
        $eventModel = new Event();
        $eventModel->incrementParticipants($id);
        header('Location: /2A35/Event');
        exit;
    }
}
