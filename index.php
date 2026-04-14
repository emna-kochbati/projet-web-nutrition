<?php
session_start();

// 1. Grab the URL parts
$url = isset($_GET['url']) ? explode('/', rtrim($_GET['url'], '/')) : ['Home'];

// 2. Handle /Admin prefix — strip it and shift the array
$offset = 0;
if (strtolower($url[0]) === 'admin') {
    $offset = 1;
}

// 3. Format the Controller name
// Front routes use a "Front" suffix to separate from back controllers
$segment = $url[$offset] ?? 'Home';
$isAdmin = $offset === 1;

if (!$isAdmin && in_array(strtolower($segment), ['restaurant'])) {
    $controllerName = ucfirst($segment) . 'FrontController';
} else {
    $controllerName = ucfirst($segment) . 'Controller';
}
$controllerFile = "Controller/" . $controllerName . ".php";

// 4. Check if the file exists before loading
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();

    // 5. Method (e.g., index, create, store, edit, update, delete, show)
    $method = $url[$offset + 1] ?? 'index';

    if (method_exists($controller, $method)) {
        $params = array_slice($url, $offset + 2);
        call_user_func_array([$controller, $method], $params);
    } else {
        echo "404 - Method '$method' not found in $controllerName";
    }
} else {
    echo "404 - Controller $controllerName not found";
}