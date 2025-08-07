<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\View;
use App\Services\AuthService;

// Start session
session_start();

// Create router instance
$router = new Router();
$authService = new AuthService();

// Define routes
$router->get('/', function() {
    // Redirect to login page
    header("Location: login_mvc.php");
    exit;
});

$router->get('/login', function() {
    // If user is already logged in, redirect to appropriate dashboard
    if (isset($_SESSION['user_id'])) {
        $role = $_SESSION['role'] ?? 'student';
        switch ($role) {
            case 'admin':
                header("Location: /admin/dashboard");
                break;
            case 'faculty':
                header("Location: /faculty/dashboard");
                break;
            case 'student':
                header("Location: /student/dashboard");
                break;
            default:
                header("Location: /student/dashboard");
        }
        exit;
    }
    
    // Show login page
    $view = new View();
    $view->display('auth.login', [
        'title' => 'Login - Examination System'
    ]);
});

// Admin routes
$router->get('/admin/dashboard', function() {
    $controller = new \App\Controllers\Admin\AdminController();
    $controller->dashboard();
});

$router->get('/admin/users', function() {
    $controller = new \App\Controllers\Admin\AdminController();
    $controller->manageUsers();
});

$router->any('/admin/users/add', function() {
    $controller = new \App\Controllers\Admin\AdminController();
    $controller->addUser();
});

$router->any('/admin/users/edit/{id}', function($id) {
    $controller = new \App\Controllers\Admin\AdminController();
    $controller->editUser($id);
});

$router->get('/admin/users/delete/{id}', function($id) {
    $controller = new \App\Controllers\Admin\AdminController();
    $controller->deleteUser($id);
});

$router->get('/admin/subjects', function() {
    $controller = new \App\Controllers\Admin\AdminController();
    $controller->manageSubjects();
});

$router->any('/admin/subjects/add', function() {
    $controller = new \App\Controllers\Admin\AdminController();
    $controller->addSubject();
});

$router->any('/admin/subjects/edit/{id}', function($id) {
    $controller = new \App\Controllers\Admin\AdminController();
    $controller->editSubject($id);
});

$router->get('/admin/subjects/delete/{id}', function($id) {
    $controller = new \App\Controllers\Admin\AdminController();
    $controller->deleteSubject($id);
});

$router->get('/admin/results', function() {
    $controller = new \App\Controllers\Admin\AdminController();
    $controller->viewResults();
});

$router->get('/admin/reports', function() {
    $controller = new \App\Controllers\Admin\AdminController();
    $controller->generateReports();
});

// Faculty routes
$router->get('/faculty/dashboard', function() {
    $controller = new \App\Controllers\Faculty\FacultyController();
    $controller->dashboard();
});

$router->get('/faculty/exams', function() {
    $controller = new \App\Controllers\Faculty\FacultyController();
    $controller->listExams();
});

$router->any('/faculty/exams/create', function() {
    $controller = new \App\Controllers\Faculty\FacultyController();
    $controller->createExam();
});

$router->any('/faculty/exams/edit/{id}', function($id) {
    $controller = new \App\Controllers\Faculty\FacultyController();
    $controller->editExam($id);
});

$router->get('/faculty/exams/delete/{id}', function($id) {
    $controller = new \App\Controllers\Faculty\FacultyController();
    $controller->deleteExam($id);
});

$router->any('/faculty/exams/{exam_id}/questions/add', function($exam_id) {
    $controller = new \App\Controllers\Faculty\FacultyController();
    $controller->addQuestion($exam_id);
});

$router->any('/faculty/questions/edit/{id}', function($id) {
    $controller = new \App\Controllers\Faculty\FacultyController();
    $controller->editQuestion($id);
});

$router->get('/faculty/questions/delete/{id}', function($id) {
    $controller = new \App\Controllers\Faculty\FacultyController();
    $controller->deleteQuestion($id);
});

$router->get('/faculty/exams/{id}/results', function($id) {
    $controller = new \App\Controllers\Faculty\FacultyController();
    $controller->viewResults($id);
});

// Student routes
$router->get('/student/dashboard', function() {
    $controller = new \App\Controllers\Student\StudentController();
    $controller->dashboard();
});

$router->any('/student/exam/{id}/take', function($id) {
    $controller = new \App\Controllers\Student\StudentController();
    $controller->takeExam($id);
});

$router->get('/student/exam/{id}/session', function($id) {
    $controller = new \App\Controllers\Student\StudentController();
    $controller->startExamSession($id);
});

$router->post('/student/exam/{id}/submit', function($id) {
    $controller = new \App\Controllers\Student\StudentController();
    $controller->submitExam($id);
});

$router->get('/student/results', function() {
    $controller = new \App\Controllers\Student\StudentController();
    $controller->viewResults();
});

$router->get('/student/exam/{id}/result', function($id) {
    $controller = new \App\Controllers\Student\StudentController();
    $controller->viewExamResult($id);
});

// API routes
$router->post('/api/auth/login', function() {
    $controller = new \App\Controllers\Auth\AuthController();
    $controller->login();
});

$router->post('/api/auth/logout', function() {
    $controller = new \App\Controllers\Auth\AuthController();
    $controller->logout();
});

// Handle the request
$router->handleRequest();
?>