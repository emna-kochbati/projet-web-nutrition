<?php
session_start();

/* ======================
   URL PARSING
====================== */
$url = isset($_GET['url']) ? explode('/', trim($_GET['url'], '/')) : [];

/* ======================
   CONTROLLER DEFAULT
====================== */
$controllerName = ucfirst($url[0] ?? 'Home') . 'Controller';
$method = $url[1] ?? 'index';
$params = array_slice($url, 2);

/* ======================
   CONTROLLER PATH
====================== */
$controllerFile = __DIR__ . "/Controller/$controllerName.php";

if (!file_exists($controllerFile)) {
    die("❌ Controller not found: " . $controllerName);
}

require_once $controllerFile;

/* ======================
   CLASS CHECK
====================== */
if (!class_exists($controllerName)) {
    die("❌ Class not found: " . $controllerName);
}

$controller = new $controllerName();

/* ======================
   METHOD CHECK + FALLBACK
====================== */
if (!method_exists($controller, $method)) {

    // fallback propre vers index
    $method = "index";

    if (!method_exists($controller, $method)) {
        die("❌ Method not found: index in " . $controllerName);
    }
}

/* ======================
   EXECUTE CONTROLLER
====================== */
call_user_func_array([$controller, $method], $params);