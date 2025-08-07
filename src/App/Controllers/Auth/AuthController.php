<?php

namespace App\Controllers\Auth;

use App\Services\Auth\AuthService;
use App\Core\View;

class AuthController
{
    private $authService;
    private $view;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->view = new View();
    }

    /**
     * Show login page
     */
    public function showLogin()
    {
        // If user is already logged in, redirect to appropriate dashboard
        if ($this->authService->isAuthenticated()) {
            $user = $this->authService->getCurrentUser();
            $this->redirectToDashboard($user['role']);
            return;
        }

        $this->view->display('auth.login');
    }

    /**
     * Handle login request
     */
    public function login()
    {
        // Set headers for JSON response
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');

        // Only accept POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid request method.'
            ]);
            return;
        }

        try {
            // Get POST data
            $school_id = $_POST['school_id'] ?? '';
            $password = $_POST['password'] ?? '';

            // Use AuthService for login
            $result = $this->authService->login($school_id, $password);

            if ($result['success']) {
                // Return successful login response
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'message' => $result['message'],
                    'role' => $result['user']['role'],
                    'user' => $result['user']
                ]);
            } else {
                // Return error message if credentials are incorrect
                http_response_code(401);
                echo json_encode([
                    'status' => 'fail',
                    'message' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            // Handle any unexpected errors
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'An error occurred during login.'
            ]);
        }
    }

    /**
     * Handle logout request
     */
    public function logout()
    {
        header('Content-Type: application/json');

        $result = $this->authService->logout();

        echo json_encode([
            'status' => 'success',
            'message' => $result['message']
        ]);
    }

    /**
     * Redirect to appropriate dashboard based on role
     */
    private function redirectToDashboard($role)
    {
        switch ($role) {
            case 'admin':
                header('Location: /admin/dashboard');
                break;
            case 'faculty':
                header('Location: /faculty/dashboard');
                break;
            case 'student':
                header('Location: /student/dashboard');
                break;
            default:
                header('Location: /login');
        }
        exit;
    }
}