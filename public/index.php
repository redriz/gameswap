<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\Router;

$router = new Router();
$router->add('/', 'App\Controllers\HomeController', 'index');
$router->add('/games', 'App\Controllers\GameController', 'list');
$router->add('/login', 'App\Controllers\AuthController', 'loginForm');
$router->dispatch();
