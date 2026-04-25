<?php
session_start();

$url = isset($_GET['url']) ? explode('/', trim($_GET['url'], '/')) : [];

/* CONTROLLER */
$controllerName = ucfirst($url[0] ?? 'User') . 'Controller';
$method = $url[1] ?? 'auth';
$params = array_slice($url, 2);

/* FILE */
$controllerFile = __DIR__ . "/Controller/$controllerName.php";

if (!file_exists($controllerFile)) {
    die("Controller introuvable: $controllerName");
}

require_once $controllerFile;

if (!class_exists($controllerName)) {
    die("Class introuvable: $controllerName");
}

$controller = new $controllerName();

/* SECURITY CHECK */
if (!method_exists($controller, $method)) {
    $method = 'auth'; // fallback sécurisé
}

/* EXECUTE */
call_user_func_array([$controller, $method], $params);