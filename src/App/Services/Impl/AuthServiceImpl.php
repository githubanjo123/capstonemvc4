<?php

namespace App\Services\Impl;

use App\Interfaces\AuthServiceInterface;
use App\Interfaces\UserDAOInterface;

class AuthServiceImpl implements AuthServiceInterface
{
    private $userDAO;

    public function __construct(UserDAOInterface $userDAO)
    {
        $this->userDAO = $userDAO;
    }

    /**
     * Login user with school ID and password
     */
    public function login(string $school_id, string $password): array
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

        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Store user data in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['school_id'] = $user['school_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['year_level'] = $user['year_level'] ?? null;
        $_SESSION['section'] = $user['section'] ?? null;

        return [
            'success' => true,
            'message' => 'Login successful!',
            'user' => [
                'user_id' => $user['user_id'],
                'school_id' => $user['school_id'],
                'full_name' => $user['full_name'],
                'role' => $user['role'],
                'year_level' => $user['year_level'] ?? null,
                'section' => $user['section'] ?? null
            ]
        ];
    }

    /**
     * Logout current user
     */
    public function logout(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Destroy session
        session_destroy();

        return [
            'success' => true,
            'message' => 'Logged out successfully.'
        ];
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user data
     */
    public function getCurrentUser(): ?array
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
     * Require authentication
     */
    public function requireAuth(): array
    {
        if (!$this->isAuthenticated()) {
            return [
                'success' => false,
                'message' => 'Authentication required.',
                'redirect' => '/login'
            ];
        }

        return [
            'success' => true,
            'message' => 'User is authenticated.'
        ];
    }

    /**
     * Require specific role
     */
    public function requireRole(string $requiredRole): array
    {
        $authResult = $this->requireAuth();
        if (!$authResult['success']) {
            return $authResult;
        }

        $user = $this->getCurrentUser();
        if ($user['role'] !== $requiredRole) {
            return [
                'success' => false,
                'message' => 'Insufficient permissions.',
                'redirect' => '/login'
            ];
        }

        return [
            'success' => true,
            'message' => 'User has required role.'
        ];
    }
}