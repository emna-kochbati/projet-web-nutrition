<?php
// 1. Grab the URL parts
$url = isset($_GET['url']) ? explode('/', rtrim($_GET['url'], '/')) : ['Home'];

if (strtolower($url[0]) === 'back') {
    $controllerName = isset($url[1]) ? ucfirst($url[1]) . 'Controller' : 'DashboardController';
    $controllerFile = "Controller/back/" . $controllerName . ".php";
    $method = isset($url[2]) ? $url[2] : 'index';
    $params = array_slice($url, 3);
} else {
    $controllerName = ucfirst($url[0]) . 'Controller';
    $controllerFile = "Controller/" . $controllerName . ".php";
    $method = isset($url[1]) ? $url[1] : 'index';
    $params = array_slice($url, 2);
}

// 3. Check if the file exists before loading
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();

    // 4. Check if the method exists (e.g., 'show', 'add', 'edit')
    if (method_exists($controller, $method)) {
        // Pass the rest of the URL parts as parameters (like an ID)
        call_user_func_array([$controller, $method], $params);
    } else {
        echo "404 - Method '$method' not found in $controllerName";
    }
} else {
    echo "404 - Controller $controllerName not found in $controllerFile";
}