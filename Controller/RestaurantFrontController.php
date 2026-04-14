<?php
require_once 'Model/Restaurant.php';
require_once 'Model/Meal.php';

class RestaurantFrontController {

    private Restaurant $restaurantModel;
    private Meal       $mealModel;

    public function __construct() {
        $this->restaurantModel = new Restaurant();
        $this->mealModel       = new Meal();
    }

    // GET /Restaurant
    public function index(): void {
        $search = trim($_GET['search'] ?? '');
        $type   = trim($_GET['type']   ?? '');

        if ($search) {
            $restaurants = $this->restaurantModel->search($search);
        } else {
            $restaurants = $this->restaurantModel->getAll();
        }

        // Filtre par type de cuisine côté PHP si besoin
        if ($type) {
            $restaurants = array_filter($restaurants, fn($r) => $r['type_cuisine'] === $type);
            $restaurants = array_values($restaurants);
        }

        require_once 'View/front/restaurant.php';
    }

    // GET /Restaurant/show/{id}
    public function show(string $id): void {
        $restaurant = $this->restaurantModel->getById((int)$id);
        if (!$restaurant) {
            require_once 'View/front/404.php';
            return;
        }
        $meals = $this->mealModel->getByRestaurant((int)$id);
        require_once 'View/front/restaurant_show.php';
    }
}
