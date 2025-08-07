<?php

namespace App\Services;

use App\DAO\UserDAO;

class AuthService
{
    private $userDAO;

    public function __construct()
    {
        $this->userDAO = new UserDAO();
    }

    /**
     * Authenticate user login
     */
    public function login($school_id, $password)
    {
        // Validate inputs
        if (empty($school_id) || empty($password)) {
            return [
                'success' => false,
                'message' => 'School ID and password are required.'
            ];
        }

        // Sanitize inputs
        $school_id = trim($school_id);
        $password = trim($password);

        // Authenticate user
        $user = $this->userDAO->authenticate($school_id, $password);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid School ID or password.'
            ];
        }

        // Start session and store user data
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['school_id'] = $user['school_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['year_level'] = $user['year_level'];
        $_SESSION['section'] = $user['section'];

        return [
            'success' => true,
            'message' => 'Login successful!',
            'user' => [
                'user_id' => $user['user_id'],
                'school_id' => $user['school_id'],
                'full_name' => $user['full_name'],
                'role' => $user['role'],
                'year_level' => $user['year_level'],
                'section' => $user['section']
            ]
        ];
    }

    /**
     * Logout user
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear all session data
        session_unset();
        session_destroy();

        return [
            'success' => true,
            'message' => 'Logged out successfully.'
        ];
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user data
     */
    public function getCurrentUser()
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'user_id' => $_SESSION['user_id'],
            'school_id' => $_SESSION['school_id'],
            'full_name' => $_SESSION['full_name'],
            'role' => $_SESSION['role'],
            'year_level' => $_SESSION['year_level'] ?? null,
            'section' => $_SESSION['section'] ?? null
        ];
    }

    /**
     * Check if user has required role
     */
    public function hasRole($requiredRole)
    {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === $requiredRole;
    }

    /**
     * Require authentication middleware
     */
    public function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            return [
                'success' => false,
                'message' => 'Authentication required.',
                'redirect' => '/login'
            ];
        }

        return ['success' => true];
    }

    /**
     * Require specific role middleware
     */
    public function requireRole($requiredRole)
    {
        $authResult = $this->requireAuth();
        if (!$authResult['success']) {
            return $authResult;
        }

        if (!$this->hasRole($requiredRole)) {
            return [
                'success' => false,
                'message' => 'Insufficient permissions.',
                'redirect' => '/dashboard'
            ];
        }

        return ['success' => true];
    }
}