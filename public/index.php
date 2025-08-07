<?php

session_start();

require_once '../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\Auth\AuthController;
use App\Controllers\Admin\AdminController;

// Initialize router
$router = new Router();

// Create controllers
$authController = new AuthController();
$adminController = new AdminController();

// Debug information (remove this later)
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
error_log("Requested path: " . $currentPath);

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

// Success pages for each role
$router->get('/admin-success', function() {
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    echo '<h1>Admin Login Successful!</h1>';
    echo '<p>Welcome Admin! You have successfully logged in.</p>';
    echo '<p><a href="' . $basePath . '/login">Back to Login</a></p>';
});

$router->get('/faculty-success', function() {
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    echo '<h1>Faculty Login Successful!</h1>';
    echo '<p>Welcome Faculty! You have successfully logged in.</p>';
    echo '<p><a href="' . $basePath . '/login">Back to Login</a></p>';
});

$router->get('/student-success', function() {
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    echo '<h1>Student Login Successful!</h1>';
    echo '<p>Welcome Student! You have successfully logged in.</p>';
    echo '<p><a href="' . $basePath . '/login">Back to Login</a></p>';
});

// Admin Dashboard Routes
$router->get('/admin/dashboard', function() use ($adminController) {
    $adminController->dashboard();
});

$router->get('/admin/logout', function() use ($adminController) {
    $adminController->logout();
});

// Handle the request
$router->handleRequest();
?>