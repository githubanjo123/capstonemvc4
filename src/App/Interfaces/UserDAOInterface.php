<?php

namespace App\Interfaces;

interface UserDAOInterface
{
    /**
     * Find user by school ID
     */
    public function findBySchoolId(string $school_id): ?array;

    /**
     * Authenticate user with school ID and password
     */
    public function authenticate(string $school_id, string $password): ?array;
}