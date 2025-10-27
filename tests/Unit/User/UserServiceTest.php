<?php

namespace Tests\Unit\User;

use PHPUnit\Framework\TestCase;
use App\Services\User\UserService;
use App\Interfaces\UserServiceInterface;
use App\Interfaces\UserDAOInterface;
use Mockery;

class UserServiceTest extends TestCase
{
    private $mockUserDAO;
    private $userService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock for UserDAO
        $this->mockUserDAO = Mockery::mock(UserDAOInterface::class);
        
        // Create UserService with mocked dependencies
        $this->userService = new UserService($this->mockUserDAO);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     * @group user
     * @group create
     */
    public function it_should_create_user_successfully_with_valid_data()
    {
        // Arrange (Red Phase - Test First)
        $userData = [
            'school_id' => '2021-0001',
            'full_name' => 'John Doe',
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A',
            'password' => 'password123'
        ];

        $expectedUserId = 1;

        // Mock the create method to return success
        $this->mockUserDAO->shouldReceive('create')
            ->once()
            ->with($userData)
            ->andReturn($expectedUserId);

        // Mock findBySchoolId to return null (user doesn't exist)
        $this->mockUserDAO->shouldReceive('findBySchoolId')
            ->once()
            ->with('2021-0001')
            ->andReturn(null);

        // Act (Green Phase - Make it pass)
        $result = $this->userService->createUser($userData);

        // Assert (Refactor Phase - Clean up)
        $this->assertTrue($result['success']);
        $this->assertEquals('User created successfully', $result['message']);
        $this->assertEquals($expectedUserId, $result['user_id']);
    }

    /**
     * @test
     * @group user
     * @group create
     */
    public function it_should_fail_when_creating_user_with_existing_school_id()
    {
        // Arrange (Red Phase)
        $userData = [
            'school_id' => '2021-0001',
            'full_name' => 'John Doe',
            'role' => 'student'
        ];

        $existingUser = [
            'user_id' => 1,
            'school_id' => '2021-0001',
            'full_name' => 'Existing User'
        ];

        // Mock findBySchoolId to return existing user
        $this->mockUserDAO->shouldReceive('findBySchoolId')
            ->once()
            ->with('2021-0001')
            ->andReturn($existingUser);

        // Act (Green Phase)
        $result = $this->userService->createUser($userData);

        // Assert (Refactor Phase)
        $this->assertFalse($result['success']);
        $this->assertEquals('School ID already exists', $result['message']);
    }

    /**
     * @test
     * @group user
     * @group create
     */
    public function it_should_fail_when_creating_user_with_missing_required_fields()
    {
        // Arrange (Red Phase)
        $userData = [
            'school_id' => '',
            'full_name' => '',
            'role' => 'student'
        ];

        // Act (Green Phase)
        $result = $this->userService->createUser($userData);

        // Assert (Refactor Phase)
        $this->assertFalse($result['success']);
        $this->assertEquals('School ID and full name are required', $result['message']);
    }

    /**
     * @test
     * @group user
     * @group update
     */
    public function it_should_update_user_successfully_with_valid_data()
    {
        // Arrange (Red Phase)
        $userId = 1;
        $userData = [
            'school_id' => '2021-0001',
            'full_name' => 'John Updated',
            'role' => 'student',
            'year_level' => '2nd',
            'section' => 'B'
        ];

        $existingUser = [
            'user_id' => 1,
            'school_id' => '2021-0001',
            'full_name' => 'John Doe'
        ];

        // Mock findById to return existing user
        $this->mockUserDAO->shouldReceive('findById')
            ->once()
            ->with($userId)
            ->andReturn($existingUser);

        // Mock update to return success
        $this->mockUserDAO->shouldReceive('update')
            ->once()
            ->with($userId, $userData)
            ->andReturn(true);

        // Act (Green Phase)
        $result = $this->userService->updateUser($userId, $userData);

        // Assert (Refactor Phase)
        $this->assertTrue($result['success']);
        $this->assertEquals('User updated successfully', $result['message']);
    }

    /**
     * @test
     * @group user
     * @group update
     */
    public function it_should_fail_when_updating_nonexistent_user()
    {
        // Arrange (Red Phase)
        $userId = 999;
        $userData = [
            'school_id' => '2021-0001',
            'full_name' => 'John Updated'
        ];

        // Mock findById to return null (user doesn't exist)
        $this->mockUserDAO->shouldReceive('findById')
            ->once()
            ->with($userId)
            ->andReturn(null);

        // Act (Green Phase)
        $result = $this->userService->updateUser($userId, $userData);

        // Assert (Refactor Phase)
        $this->assertFalse($result['success']);
        $this->assertEquals('User not found', $result['message']);
    }

    /**
     * @test
     * @group user
     * @group delete
     */
    public function it_should_delete_user_successfully()
    {
        // Arrange (Red Phase)
        $userId = 1;

        $existingUser = [
            'user_id' => 1,
            'school_id' => '2021-0001',
            'full_name' => 'John Doe'
        ];

        // Mock findById to return existing user
        $this->mockUserDAO->shouldReceive('findById')
            ->once()
            ->with($userId)
            ->andReturn($existingUser);

        // Mock delete to return success
        $this->mockUserDAO->shouldReceive('delete')
            ->once()
            ->with($userId)
            ->andReturn(true);

        // Act (Green Phase)
        $result = $this->userService->deleteUser($userId);

        // Assert (Refactor Phase)
        $this->assertTrue($result['success']);
        $this->assertEquals('User deleted successfully', $result['message']);
    }

    /**
     * @test
     * @group user
     * @group delete
     */
    public function it_should_fail_when_deleting_nonexistent_user()
    {
        // Arrange (Red Phase)
        $userId = 999;

        // Mock findById to return null (user doesn't exist)
        $this->mockUserDAO->shouldReceive('findById')
            ->once()
            ->with($userId)
            ->andReturn(null);

        // Act (Green Phase)
        $result = $this->userService->deleteUser($userId);

        // Assert (Refactor Phase)
        $this->assertFalse($result['success']);
        $this->assertEquals('User not found', $result['message']);
    }

    /**
     * @test
     * @group user
     * @group get
     */
    public function it_should_get_all_users()
    {
        // Arrange (Red Phase)
        $expectedUsers = [
            [
                'user_id' => 1,
                'school_id' => '2021-0001',
                'full_name' => 'John Doe',
                'role' => 'student'
            ],
            [
                'user_id' => 2,
                'school_id' => '2021-0002',
                'full_name' => 'Jane Smith',
                'role' => 'faculty'
            ]
        ];

        // Mock getAllUsers to return users
        $this->mockUserDAO->shouldReceive('getAllUsers')
            ->once()
            ->andReturn($expectedUsers);

        // Act (Green Phase)
        $result = $this->userService->getAllUsers();

        // Assert (Refactor Phase)
        $this->assertEquals($expectedUsers, $result);
    }

    /**
     * @test
     * @group user
     * @group get
     */
    public function it_should_get_users_by_role()
    {
        // Arrange (Red Phase)
        $role = 'student';
        $expectedUsers = [
            [
                'user_id' => 1,
                'school_id' => '2021-0001',
                'full_name' => 'John Doe',
                'role' => 'student'
            ],
            [
                'user_id' => 2,
                'school_id' => '2021-0002',
                'full_name' => 'Jane Smith',
                'role' => 'student'
            ]
        ];

        // Mock getUsersByRole to return users
        $this->mockUserDAO->shouldReceive('getUsersByRole')
            ->once()
            ->with($role)
            ->andReturn($expectedUsers);

        // Act (Green Phase)
        $result = $this->userService->getUsersByRole($role);

        // Assert (Refactor Phase)
        $this->assertEquals($expectedUsers, $result);
    }

    /**
     * @test
     * @group user
     * @group get
     */
    public function it_should_get_students_by_year_and_section()
    {
        // Arrange (Red Phase)
        $yearLevel = '1st';
        $section = 'A';
        $expectedStudents = [
            [
                'user_id' => 1,
                'school_id' => '2021-0001',
                'full_name' => 'John Doe',
                'role' => 'student',
                'year_level' => '1st',
                'section' => 'A'
            ]
        ];

        // Mock getStudentsByYearSection to return students
        $this->mockUserDAO->shouldReceive('getStudentsByYearSection')
            ->once()
            ->with($yearLevel, $section)
            ->andReturn($expectedStudents);

        // Act (Green Phase)
        $result = $this->userService->getStudentsByYearSection($yearLevel, $section);

        // Assert (Refactor Phase)
        $this->assertEquals($expectedStudents, $result);
    }
}