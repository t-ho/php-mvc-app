<?php

require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../routes/web.php';

$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (isset($routes[$method][$url])) {
    list($controller, $action) = explode('@', $routes[$method][$url]);

    $controllerInstance = new $controller();
    $controllerInstance->$action();
} else {
    http_response_code(404);
}
