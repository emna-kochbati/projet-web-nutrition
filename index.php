<?php
session_start();

$url = $_GET['url'] ?? 'User/auth';
$url = explode('/', trim($url, '/'));

$controllerPart = $url[0] ?? 'User';
$method = $url[1] ?? 'auth';
$params = array_slice($url, 2);

/* NORMALISATION */
$controllerName = ucfirst($controllerPart) . 'Controller';

/* IMPORTANT : dossier correct */
$controllerFile = __DIR__ . "/Controller/$controllerName.php";

/* DEBUG PROPRE */
if (!file_exists($controllerFile)) {
    http_response_code(404);
    die(json_encode([
        "success" => false,
        "message" => "Controller introuvable",
        "debug" => $controllerName
    ]));
}

require_once $controllerFile;

if (!class_exists($controllerName)) {
    http_response_code(500);
    die(json_encode([
        "success" => false,
        "message" => "Classe introuvable",
        "debug" => $controllerName
    ]));
}

$controller = new $controllerName();

if (!method_exists($controller, $method)) {
    $method = 'auth';
}

call_user_func_array([$controller, $method], $params);