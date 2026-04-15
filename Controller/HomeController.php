<?php

class HomeController
{
    public function index()
    {
        require_once __DIR__ . '/../View/front/pages/home.php';
    }

    public function recette()
    {
        require_once __DIR__ . '/../View/front/pages/recette.php';
    }

    public function sport()
    {
        require_once __DIR__ . '/../View/front/pages/sport.php';
    }

    public function evenement()
    {
        require_once __DIR__ . '/../View/front/pages/evenement.php';
    }

    public function partenaire()
    {
        require_once __DIR__ . '/../View/front/pages/partenaire.php';
    }

    public function contact()
    {
        require_once __DIR__ . '/../View/front/pages/contact.php';
    }

    public function about()
    {
        require_once __DIR__ . '/../View/front/pages/about.php';
    }
}