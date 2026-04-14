<?php
require_once 'Model/Restaurant.php';

class HomeController {
    public function index(): void {
        $restaurants = (new Restaurant())->getAll();
        require_once 'View/front/home.php';
    }
}
