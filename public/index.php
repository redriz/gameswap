<?php

require_once __DIR__ . '/../app/Controllers/HomeController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routes = [
    '/' => ['HomeController', 'index'],
    '/games' => ['GameController', 'list'],
    '/login' => ['AuthController', 'loginForm'],
];

if (array_key_exists($uri, $routes)) {
    [$controllerName, $methodName] = $routes[$uri];
    $controller = new $controllerName();
    $controller->$methodName();
} else {
    http_response_code(404);
    echo "Página não encontrada.";
}
