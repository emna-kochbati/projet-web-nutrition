<?php
session_start();

$url = $_GET['url'] ?? 'Home/index';
$url = explode('/', trim($url, '/'));

$controllerName = ucfirst($url[0]) . 'Controller';
$method         = $url[1] ?? 'index';
$params         = array_slice($url, 2);

$paths = [
    __DIR__ . "/Controller/$controllerName.php",
    __DIR__ . "/Controllers/$controllerName.php"
];

$controllerFile = null;
foreach ($paths as $path) {
    if (file_exists($path)) {
        $controllerFile = $path;
        break;
    }
}

if (!$controllerFile) {
    http_response_code(404);
    die("Controller introuvable");
}

require_once $controllerFile;

if (!class_exists($controllerName)) {
    die("Classe introuvable");
}

$controller = new $controllerName();

if (!method_exists($controller, $method)) {
    http_response_code(404);
    die("Méthode introuvable");
}

call_user_func_array([$controller, $method], $params);
