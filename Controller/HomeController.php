<?php

class HomeController {

    public function index() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once __DIR__ . '/../View/front/pages/home.php';
    }
}