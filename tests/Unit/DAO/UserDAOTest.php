<?php

namespace Tests\Unit\DAO;

use Tests\BaseTest;
use App\DAO\Auth\UserDAO;
use App\Models\User;
use Exception;

class UserDAOTest extends BaseTest
{
    private UserDAO $userDAO;
    private $testUserId = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userDAO = new UserDAO();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clean up test user if created
        if ($this->testUserId !== null) {
            try {
                $this->userDAO->delete($this->testUserId);
            } catch (Exception $e) {
                // Ignore cleanup errors
            }
            $this->testUserId = null;
        }
    }

    // ===== CREATE TESTS =====

    /**
     * @test
     * @group dao
     * @group create
     */
    public function testCreate_WithValidStudentUser_ShouldSucceed(): void
    {
        // Arrange (Red Phase - Test First)
        $userData = [
            'school_id' => 'TEST_STU_' . uniqid(),
            'full_name' => 'Test Student',
            'password' => password_hash('testpass', PASSWORD_DEFAULT),
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A'
        ];

        // Act (Green Phase - Make it pass)
        $result = $this->userDAO->create($userData);

        // Assert (Refactor Phase - Clean up)
        $this->assertIsInt($result, "Should return user ID after creation");
        $this->assertGreaterThan(0, $result, "User ID should be positive");
        
        // Store for cleanup
        $this->testUserId = $result;
        
        // Verify user was actually created
        $retrievedUser = $this->userDAO->findById($result);
        $this->assertNotNull($retrievedUser, "Created user should be retrievable");
        $this->assertEquals($userData['school_id'], $retrievedUser['school_id']);
        $this->assertEquals($userData['full_name'], $retrievedUser['full_name']);
        $this->assertEquals($userData['role'], $retrievedUser['role']);
    }

    /**
     * @test
     * @group dao
     * @group create
     */
    public function testCreate_WithValidFacultyUser_ShouldSucceed(): void
    {
        // Arrange (Red Phase)
        $userData = [
            'school_id' => 'TEST_FAC_' . uniqid(),
            'full_name' => 'Test Faculty',
            'password' => password_hash('testpass', PASSWORD_DEFAULT),
            'role' => 'faculty'
        ];

        // Act (Green Phase)
        $result = $this->userDAO->create($userData);

        // Assert (Refactor Phase)
        $this->assertIsInt($result, "Should return user ID after creation");
        $this->assertGreaterThan(0, $result, "User ID should be positive");
        
        // Store for cleanup
        $this->testUserId = $result;
        
        // Verify faculty-specific fields
        $retrievedUser = $this->userDAO->findById($result);
        $this->assertEquals('faculty', $retrievedUser['role']);
        $this->assertNull($retrievedUser['year_level'] ?? null, "Faculty should not have year level");
        $this->assertNull($retrievedUser['section'] ?? null, "Faculty should not have section");
    }

    /**
     * @test
     * @group dao
     * @group create
     */
    public function testCreate_WithDuplicateSchoolId_ShouldFail(): void
    {
        // Arrange - Create first user
        $userData1 = [
            'school_id' => 'DUPLICATE_TEST_' . uniqid(),
            'full_name' => 'First User',
            'password' => password_hash('pass1', PASSWORD_DEFAULT),
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A'
        ];
        
        $userId1 = $this->userDAO->create($userData1);
        $this->testUserId = $userId1; // Store for cleanup
        
        // Create second user with same school ID
        $userData2 = [
            'school_id' => $userData1['school_id'], // Same school ID
            'full_name' => 'Second User',
            'password' => password_hash('pass2', PASSWORD_DEFAULT),
            'role' => 'student',
            'year_level' => '2nd',
            'section' => 'B'
        ];

        // Act (Green Phase)
        $result = $this->userDAO->create($userData2);

        // Assert (Refactor Phase)
        $this->assertFalse($result, "Should fail to create user with duplicate school ID");
    }

    // ===== FIND TESTS =====

    /**
     * @test
     * @group dao
     * @group find
     */
    public function testFindById_WithExistingUser_ShouldReturnUser(): void
    {
        // Arrange - Create a test user
        $userData = [
            'school_id' => 'FIND_TEST_' . uniqid(),
            'full_name' => 'Find Test User',
            'password' => password_hash('findpass', PASSWORD_DEFAULT),
            'role' => 'student',
            'year_level' => '2nd',
            'section' => 'B'
        ];
        
        $userId = $this->userDAO->create($userData);
        $this->testUserId = $userId;

        // Act (Green Phase)
        $foundUser = $this->userDAO->findById($userId);

        // Assert (Refactor Phase)
        $this->assertNotNull($foundUser, "Should find existing user");
        $this->assertEquals($userId, $foundUser['user_id']);
        $this->assertEquals($userData['school_id'], $foundUser['school_id']);
        $this->assertEquals($userData['full_name'], $foundUser['full_name']);
        $this->assertEquals($userData['role'], $foundUser['role']);
        $this->assertEquals($userData['year_level'], $foundUser['year_level']);
        $this->assertEquals($userData['section'], $foundUser['section']);
    }

    /**
     * @test
     * @group dao
     * @group find
     */
    public function testFindById_WithNonExistentUser_ShouldReturnNull(): void
    {
        // Act (Green Phase)
        $foundUser = $this->userDAO->findById(999999);

        // Assert (Refactor Phase)
        $this->assertNull($foundUser, "Should return null for non-existent user");
    }

    /**
     * @test
     * @group dao
     * @group find
     */
    public function testFindBySchoolId_WithExistingUser_ShouldReturnUser(): void
    {
        // Arrange (Red Phase)
        $schoolId = 'SCHOOL_FIND_' . uniqid();
        $userData = [
            'school_id' => $schoolId,
            'full_name' => 'School Find Test',
            'password' => password_hash('schoolpass', PASSWORD_DEFAULT),
            'role' => 'faculty'
        ];
        
        $userId = $this->userDAO->create($userData);
        $this->testUserId = $userId;

        // Act (Green Phase)
        $foundUser = $this->userDAO->findBySchoolId($schoolId);

        // Assert (Refactor Phase)
        $this->assertNotNull($foundUser, "Should find user by school ID");
        $this->assertEquals($schoolId, $foundUser['school_id']);
        $this->assertEquals($userData['full_name'], $foundUser['full_name']);
    }

    /**
     * @test
     * @group dao
     * @group find
     */
    public function testFindBySchoolId_WithNonExistentSchoolId_ShouldReturnNull(): void
    {
        // Act (Green Phase)
        $foundUser = $this->userDAO->findBySchoolId('NONEXISTENT_SCHOOL_ID');

        // Assert (Refactor Phase)
        $this->assertNull($foundUser, "Should return null for non-existent school ID");
    }

    /**
     * @test
     * @group dao
     * @group find
     */
    public function testGetAllUsers_ShouldReturnArrayOfUsers(): void
    {
        // Act (Green Phase)
        $users = $this->userDAO->getAllUsers();

        // Assert (Refactor Phase)
        $this->assertIsArray($users, "Should return array");
        
        if (count($users) > 0) {
            $this->assertIsArray($users[0], "Should return user arrays");
            $this->assertArrayHasKey('user_id', $users[0], "Should have user_id key");
            $this->assertArrayHasKey('school_id', $users[0], "Should have school_id key");
        }
    }

    /**
     * @test
     * @group dao
     * @group find
     */
    public function testGetUsersByRole_WithSpecificRole_ShouldReturnFilteredUsers(): void
    {
        // Act (Green Phase)
        $students = $this->userDAO->getUsersByRole('student');
        $faculty = $this->userDAO->getUsersByRole('faculty');
        $admins = $this->userDAO->getUsersByRole('admin');

        // Assert (Refactor Phase)
        $this->assertIsArray($students, "Should return array for students");
        $this->assertIsArray($faculty, "Should return array for faculty");
        $this->assertIsArray($admins, "Should return array for admins");
        
        // Verify role filtering if users exist
        if (count($students) > 0) {
            foreach ($students as $student) {
                $this->assertEquals('student', $student['role'], "All returned users should be students");
            }
        }
        
        if (count($faculty) > 0) {
            foreach ($faculty as $facultyMember) {
                $this->assertEquals('faculty', $facultyMember['role'], "All returned users should be faculty");
            }
        }
    }

    // ===== UPDATE TESTS =====

    /**
     * @test
     * @group dao
     * @group update
     */
    public function testUpdate_WithValidChanges_ShouldSucceed(): void
    {
        // Arrange - Create user to update
        $userData = [
            'school_id' => 'UPDATE_TEST_' . uniqid(),
            'full_name' => 'Original Name',
            'password' => password_hash('originalpass', PASSWORD_DEFAULT),
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A'
        ];
        
        $userId = $this->userDAO->create($userData);
        $this->testUserId = $userId;
        
        // Modify user data
        $updateData = [
            'user_id' => $userId,
            'full_name' => 'Updated Name',
            'year_level' => '2nd',
            'section' => 'B'
        ];

        // Act (Green Phase)
        $result = $this->userDAO->update($userId, $updateData);

        // Assert (Refactor Phase)
        $this->assertTrue($result, "Should successfully update user");
        
        // Verify changes were persisted
        $updatedUser = $this->userDAO->findById($userId);
        $this->assertEquals('Updated Name', $updatedUser['full_name']);
        $this->assertEquals('2nd', $updatedUser['year_level']);
        $this->assertEquals('B', $updatedUser['section']);
    }

    /**
     * @test
     * @group dao
     * @group update
     */
    public function testUpdate_WithNonExistentUser_ShouldFail(): void
    {
        // Arrange (Red Phase)
        $updateData = [
            'user_id' => 999999,
            'school_id' => 'NONEXISTENT',
            'full_name' => 'Non Existent',
            'role' => 'student'
        ];

        // Act (Green Phase)
        $result = $this->userDAO->update(999999, $updateData);

        // Assert (Refactor Phase)
        $this->assertFalse($result, "Should fail to update non-existent user");
    }

    // ===== DELETE TESTS =====

    /**
     * @test
     * @group dao
     * @group delete
     */
    public function testDelete_WithExistingUser_ShouldSucceed(): void
    {
        // Arrange - Create user to delete
        $userData = [
            'school_id' => 'DELETE_TEST_' . uniqid(),
            'full_name' => 'Delete Test User',
            'password' => password_hash('deletepass', PASSWORD_DEFAULT),
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A'
        ];
        
        $userId = $this->userDAO->create($userData);

        // Act (Green Phase)
        $result = $this->userDAO->delete($userId);

        // Assert (Refactor Phase)
        $this->assertTrue($result, "Should successfully delete user");
        
        // Verify user was actually deleted
        $deletedUser = $this->userDAO->findById($userId);
        $this->assertNull($deletedUser, "Deleted user should not be found");
        
        // Don't set testUserId since we already deleted
    }

    // ===== AUTHENTICATION TESTS =====

    /**
     * @test
     * @group dao
     * @group authenticate
     */
    public function testAuthenticate_WithValidCredentials_ShouldSucceed(): void
    {
        // Arrange (Red Phase)
        $password = 'testpass123';
        $userData = [
            'school_id' => 'AUTH_TEST_' . uniqid(),
            'full_name' => 'Auth Test User',
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A'
        ];
        
        $userId = $this->userDAO->create($userData);
        $this->testUserId = $userId;

        // Act (Green Phase)
        $result = $this->userDAO->authenticate($userData['school_id'], $password);

        // Assert (Refactor Phase)
        $this->assertTrue($result['success'], "Should authenticate with valid credentials");
        $this->assertArrayHasKey('user', $result, "Should return user data");
        $this->assertEquals($userData['school_id'], $result['user']['school_id']);
        $this->assertEquals($userData['full_name'], $result['user']['full_name']);
    }

    /**
     * @test
     * @group dao
     * @group authenticate
     */
    public function testAuthenticate_WithInvalidPassword_ShouldFail(): void
    {
        // Arrange (Red Phase)
        $userData = [
            'school_id' => 'AUTH_FAIL_' . uniqid(),
            'full_name' => 'Auth Fail User',
            'password' => password_hash('correctpass', PASSWORD_DEFAULT),
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A'
        ];
        
        $userId = $this->userDAO->create($userData);
        $this->testUserId = $userId;

        // Act (Green Phase)
        $result = $this->userDAO->authenticate($userData['school_id'], 'wrongpass');

        // Assert (Refactor Phase)
        $this->assertFalse($result['success'], "Should fail with invalid password");
        $this->assertEquals('Invalid credentials', $result['message']);
    }

    /**
     * @test
     * @group dao
     * @group authenticate
     */
    public function testAuthenticate_WithNonExistentUser_ShouldFail(): void
    {
        // Act (Green Phase)
        $result = $this->userDAO->authenticate('NONEXISTENT_USER', 'anypass');

        // Assert (Refactor Phase)
        $this->assertFalse($result['success'], "Should fail with non-existent user");
        $this->assertEquals('Invalid credentials', $result['message']);
    }

    // ===== STUDENT-SPECIFIC TESTS =====

    /**
     * @test
     * @group dao
     * @group students
     */
    public function testGetStudentsByYearSection_WithValidCriteria_ShouldReturnStudents(): void
    {
        // Arrange - Create test students
        $student1Data = [
            'school_id' => 'STU_YEAR1_' . uniqid(),
            'full_name' => 'Year 1 Student A',
            'password' => password_hash('pass1', PASSWORD_DEFAULT),
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A'
        ];
        
        $student2Data = [
            'school_id' => 'STU_YEAR1_' . uniqid(),
            'full_name' => 'Year 1 Student B',
            'password' => password_hash('pass2', PASSWORD_DEFAULT),
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A'
        ];
        
        $userId1 = $this->userDAO->create($student1Data);
        $userId2 = $this->userDAO->create($student2Data);
        $this->testUserId = $userId1; // Store one for cleanup

        // Act (Green Phase)
        $students = $this->userDAO->getStudentsByYearSection('1st', 'A');

        // Assert (Refactor Phase)
        $this->assertIsArray($students, "Should return array of students");
        
        // Should find at least our test students
        $foundTestStudents = 0;
        foreach ($students as $student) {
            if ($student['school_id'] === $student1Data['school_id'] || 
                $student['school_id'] === $student2Data['school_id']) {
                $foundTestStudents++;
            }
            $this->assertEquals('student', $student['role'], "All should be students");
            $this->assertEquals('1st', $student['year_level'], "All should be 1st year");
            $this->assertEquals('A', $student['section'], "All should be section A");
        }
        
        $this->assertGreaterThanOrEqual(2, $foundTestStudents, "Should find our test students");
    }
}