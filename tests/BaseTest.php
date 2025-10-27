<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Config\Database;
use PDO;
use Exception;

abstract class BaseTest extends TestCase
{
    protected PDO $db;
    protected array $testData = [];

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize database connection
        $this->db = Database::getConnection();
        
        // Start transaction for test isolation
        $this->db->beginTransaction();
        
        // Clear any existing test data
        $this->cleanupTestData();
    }

    protected function tearDown(): void
    {
        // Rollback transaction to undo any changes
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        
        // Clean up any remaining test data
        $this->cleanupTestData();
        
        parent::tearDown();
    }

    /**
     * Clean up test data from database
     */
    protected function cleanupTestData(): void
    {
        try {
            // Delete test users (those with TEST_ prefix in school_id)
            $stmt = $this->db->prepare("DELETE FROM users WHERE school_id LIKE 'TEST_%'");
            $stmt->execute();
            
            // Delete test users with other test prefixes
            $testPrefixes = [
                'DUPLICATE_TEST_',
                'FIND_TEST_',
                'SCHOOL_FIND_',
                'UPDATE_TEST_',
                'DELETE_TEST_',
                'AUTH_TEST_',
                'AUTH_FAIL_',
                'STU_YEAR1_',
                'STU_YEAR2_',
                'FAC_TEST_',
                'ADMIN_TEST_'
            ];
            
            foreach ($testPrefixes as $prefix) {
                $stmt = $this->db->prepare("DELETE FROM users WHERE school_id LIKE ?");
                $stmt->execute([$prefix . '%']);
            }
            
        } catch (Exception $e) {
            // Ignore cleanup errors in tests
        }
    }

    /**
     * Create a test user in the database
     */
    protected function createTestUser(array $userData): int
    {
        $sql = "INSERT INTO users (school_id, full_name, password, role, year_level, section) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $userData['school_id'],
            $userData['full_name'],
            $userData['password'],
            $userData['role'],
            $userData['year_level'] ?? null,
            $userData['section'] ?? null
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Delete a test user from the database
     */
    protected function deleteTestUser(int $userId): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
            return $stmt->execute([$userId]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Find a user by ID
     */
    protected function findUserById(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Find a user by school ID
     */
    protected function findUserBySchoolId(string $schoolId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE school_id = ?");
        $stmt->execute([$schoolId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Get all users
     */
    protected function getAllUsers(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY user_id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get users by role
     */
    protected function getUsersByRole(string $role): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ? ORDER BY user_id DESC");
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get students by year and section
     */
    protected function getStudentsByYearSection(string $yearLevel, string $section): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users 
             WHERE role = 'student' AND year_level = ? AND section = ? 
             ORDER BY full_name"
        );
        $stmt->execute([$yearLevel, $section]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Authenticate a user
     */
    protected function authenticateUser(string $schoolId, string $password): array
    {
        $user = $this->findUserBySchoolId($schoolId);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }
        
        if (password_verify($password, $user['password'])) {
            return [
                'success' => true,
                'user' => $user
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Invalid credentials'
        ];
    }

    /**
     * Update a user
     */
    protected function updateUser(int $userId, array $userData): bool
    {
        try {
            $sql = "UPDATE users SET 
                    school_id = ?, 
                    full_name = ?, 
                    role = ?, 
                    year_level = ?, 
                    section = ? 
                    WHERE user_id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $userData['school_id'],
                $userData['full_name'],
                $userData['role'],
                $userData['year_level'] ?? null,
                $userData['section'] ?? null,
                $userId
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete a user
     */
    protected function deleteUser(int $userId): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
            return $stmt->execute([$userId]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Generate a unique test school ID
     */
    protected function generateTestSchoolId(string $prefix = 'TEST'): string
    {
        return $prefix . '_' . uniqid() . '_' . time();
    }

    /**
     * Create a test student
     */
    protected function createTestStudent(string $schoolId = null): int
    {
        $schoolId = $schoolId ?: $this->generateTestSchoolId('STU');
        
        return $this->createTestUser([
            'school_id' => $schoolId,
            'full_name' => 'Test Student',
            'password' => password_hash('testpass', PASSWORD_DEFAULT),
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A'
        ]);
    }

    /**
     * Create a test faculty member
     */
    protected function createTestFaculty(string $schoolId = null): int
    {
        $schoolId = $schoolId ?: $this->generateTestSchoolId('FAC');
        
        return $this->createTestUser([
            'school_id' => $schoolId,
            'full_name' => 'Test Faculty',
            'password' => password_hash('testpass', PASSWORD_DEFAULT),
            'role' => 'faculty'
        ]);
    }

    /**
     * Create a test admin
     */
    protected function createTestAdmin(string $schoolId = null): int
    {
        $schoolId = $schoolId ?: $this->generateTestSchoolId('ADMIN');
        
        return $this->createTestUser([
            'school_id' => $schoolId,
            'full_name' => 'Test Admin',
            'password' => password_hash('testpass', PASSWORD_DEFAULT),
            'role' => 'admin'
        ]);
    }

    /**
     * Assert that a user exists in the database
     */
    protected function assertUserExists(int $userId, string $message = ''): void
    {
        $user = $this->findUserById($userId);
        $this->assertNotNull($user, $message ?: "User with ID $userId should exist");
    }

    /**
     * Assert that a user does not exist in the database
     */
    protected function assertUserNotExists(int $userId, string $message = ''): void
    {
        $user = $this->findUserById($userId);
        $this->assertNull($user, $message ?: "User with ID $userId should not exist");
    }

    /**
     * Assert that a school ID exists in the database
     */
    protected function assertSchoolIdExists(string $schoolId, string $message = ''): void
    {
        $user = $this->findUserBySchoolId($schoolId);
        $this->assertNotNull($user, $message ?: "User with school ID $schoolId should exist");
    }

    /**
     * Assert that a school ID does not exist in the database
     */
    protected function assertSchoolIdNotExists(string $schoolId, string $message = ''): void
    {
        $user = $this->findUserBySchoolId($schoolId);
        $this->assertNull($user, $message ?: "User with school ID $schoolId should not exist");
    }
}