<?php

namespace App\Services\User;

use App\Interfaces\UserServiceInterface;
use App\Interfaces\UserDAOInterface;

class UserServiceWithInterface implements UserServiceInterface
{
    private $userDAO;

    public function __construct(UserDAOInterface $userDAO)
    {
        $this->userDAO = $userDAO;
    }

    /**
     * Create a new user
     */
    public function createUser($data)
    {
        // Same logic as simple approach
        if (empty($data['school_id']) || empty($data['full_name']) || empty($data['role'])) {
            return [
                'success' => false,
                'message' => 'School ID, full name, and role are required.'
            ];
        }

        $existingUser = $this->userDAO->findBySchoolId($data['school_id']);
        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'School ID already exists.'
            ];
        }

        $userId = $this->userDAO->create($data);
        
        if ($userId) {
            return [
                'success' => true,
                'message' => 'User created successfully!',
                'user_id' => $userId
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create user.'
            ];
        }
    }

    /**
     * Update user
     */
    public function updateUser($userId, $data)
    {
        $existingUser = $this->userDAO->findById($userId);
        if (!$existingUser) {
            return [
                'success' => false,
                'message' => 'User not found.'
            ];
        }

        $result = $this->userDAO->update($userId, $data);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'User updated successfully!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update user.'
            ];
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($userId)
    {
        $existingUser = $this->userDAO->findById($userId);
        if (!$existingUser) {
            return [
                'success' => false,
                'message' => 'User not found.'
            ];
        }

        $result = $this->userDAO->delete($userId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'User deleted successfully!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to delete user.'
            ];
        }
    }

    /**
     * Get all users
     */
    public function getAllUsers()
    {
        return $this->userDAO->getAllUsers();
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role)
    {
        return $this->userDAO->getUsersByRole($role);
    }

    /**
     * Get students by year and section
     */
    public function getStudentsByYearSection($yearLevel, $section)
    {
        return $this->userDAO->getStudentsByYearSection($yearLevel, $section);
    }
}