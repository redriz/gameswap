<?php

namespace App;

class Router
{
    private array $routes = [];

    public function add(string $httpMethod, string $uri, string $controller, string $method): void
    {
        $key = strtoupper($httpMethod) . ' ' . $uri;
        $this->routes[$key] = [$controller, $method];
    }

    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $key = strtoupper($httpMethod) . ' ' . $uri;

        if (array_key_exists($key, $this->routes)) {
            [$controllerName, $methodName] = $this->routes[$key];
            $controller = new $controllerName();
            $controller->$methodName();
        } else {
            http_response_code(404);
            echo "Página não encontrada.";
        }
    }
}
