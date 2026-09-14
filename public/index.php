<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\Router;

$router = new Router();
$router->add('GET', '/', 'App\Controllers\HomeController', 'index');
$router->add('GET', '/login', 'App\Controllers\AuthController', 'loginForm');
$router->add('POST', '/login', 'App\Controllers\AuthController', 'login');
$router->add('GET', '/logout', 'App\Controllers\AuthController', 'logout');
$router->add('GET', '/register', 'App\Controllers\AuthController', 'registerForm');
$router->add('POST', '/register', 'App\Controllers\AuthController', 'register');
$router->add('GET', '/games', 'App\Controllers\GameController', 'list');
$router->add('GET', '/game', 'App\Controllers\GameController', 'show');
$router->add('POST', '/library/buy', 'App\Controllers\LibraryController', 'buy');
$router->add('GET', '/library', 'App\Controllers\LibraryController', 'index');
$router->add('GET', '/listings/create', 'App\Controllers\ListingController', 'createForm');
$router->add('POST', '/listings/create', 'App\Controllers\ListingController', 'create');
$router->add('GET', '/marketplace', 'App\Controllers\ListingController', 'marketplace');
$router->add('POST', '/listings/buy', 'App\Controllers\ListingController', 'buy');
$router->dispatch();
