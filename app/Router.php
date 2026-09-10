<?php

namespace App;

class Router
{
    private array $routes = [];

    public function add(string $uri, string $controller, string $method): void
    {
        $this->routes[$uri] = [$controller, $method];
    }

    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (array_key_exists($uri, $this->routes)) {
            [$controllerName, $methodName] = $this->routes[$uri];
            $controller = new $controllerName();
            $controller->$methodName();
        } else {
            http_response_code(404);
            echo "Página não encontrada.";
        }
    }
}
