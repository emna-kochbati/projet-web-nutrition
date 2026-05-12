<?php   

class HomeController {

    public function index() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
    }

        require_once 'View/front/home.php';
    }
}