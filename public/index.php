<?php

require_once __DIR__ . '/../vendor/autoload.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routes = [
    '/' => ['App\Controllers\HomeController', 'index'],
    '/games' => ['App\Controllers\GameController', 'list'],
    '/login' => ['App\Controllers\AuthController', 'loginForm'],
];

if (array_key_exists($uri, $routes)) {
    [$controllerName, $methodName] = $routes[$uri];
    $controller = new $controllerName();
    $controller->$methodName();
} else {
    http_response_code(404);
    echo "Página não encontrada.";
}
