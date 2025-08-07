<?php

session_start();

require_once '../vendor/autoload.php';

use App\Core\Router;
use App\Core\Container;
use App\Interfaces\AuthServiceInterface;
use App\Controllers\Auth\AuthController;

// Initialize router
$router = new Router();

// Get container instance
$container = Container::getInstance();

// Get auth service from container
$authService = $container->get(AuthServiceInterface::class);

// Create auth controller with dependency injection
$authController = new AuthController($authService);

// Root route - redirect to login
$router->get('/', function() {
    header('Location: /login');
    exit;
});

// Login page
$router->get('/login', function() use ($authController) {
    $authController->showLogin();
});

// API routes for authentication
$router->post('/api/auth/login', function() use ($authController) {
    $authController->login();
});

$router->post('/api/auth/logout', function() use ($authController) {
    $authController->logout();
});

// Handle the request
$router->dispatch();
?>