<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\Router;

$router = new Router();
$router->add('GET', '/', 'App\Controllers\HomeController', 'index');
$router->add('GET', '/games', 'App\Controllers\GameController', 'list');
$router->add('GET', '/login', 'App\Controllers\AuthController', 'loginForm');
$router->add('POST', '/login', 'App\Controllers\AuthController', 'login');
$router->add('GET', '/logout', 'App\Controllers\AuthController', 'logout');
$router->add('GET', '/register', 'App\Controllers\AuthController', 'registerForm');
$router->add('POST', '/register', 'App\Controllers\AuthController', 'register');
$router->dispatch();
