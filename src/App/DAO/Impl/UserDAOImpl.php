<?php

namespace App\DAO\Impl;

use App\Config\Database;
use App\Interfaces\UserDAOInterface;
use PDO;
use PDOException;

class UserDAOImpl implements UserDAOInterface
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find user by school ID
     */
    public function findBySchoolId(string $school_id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE school_id = ?");
            $stmt->execute([$school_id]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Authenticate user with school ID and password
     */
    public function authenticate(string $school_id, string $password): ?array
    {
        $user = $this->findBySchoolId($school_id);
        
        if (!$user) {
            return null;
        }

        // Check if password is hashed (starts with $) or plain text
        if (strpos($user['password'], '$') === 0) {
            return password_verify($password, $user['password']) ? $user : null;
        } else {
            return $password === $user['password'] ? $user : null;
        }
    }
}