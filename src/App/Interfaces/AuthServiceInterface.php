<?php

namespace App\Interfaces;

interface AuthServiceInterface
{
    /**
     * Login user with school ID and password
     */
    public function login(string $school_id, string $password): array;

    /**
     * Logout current user
     */
    public function logout(): array;

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool;

    /**
     * Get current user data
     */
    public function getCurrentUser(): ?array;

    /**
     * Require authentication
     */
    public function requireAuth(): array;

    /**
     * Require specific role
     */
    public function requireRole(string $requiredRole): array;
}