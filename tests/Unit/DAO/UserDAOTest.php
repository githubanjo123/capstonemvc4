<?php

namespace Tests\Unit\DAO;

use PHPUnit\Framework\TestCase;
use App\DAO\Auth\UserDAO;
use App\Config\Database;
use PDO;
use PDOStatement;
use Mockery;

class UserDAOTest extends TestCase
{
    private $mockPDO;
    private $mockStatement;
    private $userDAO;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock PDO
        $this->mockPDO = Mockery::mock(PDO::class);
        $this->mockStatement = Mockery::mock(PDOStatement::class);
        
        // Create UserDAO with mocked PDO
        $this->userDAO = new UserDAO();
        
        // Use reflection to inject mocked PDO
        $reflection = new \ReflectionClass($this->userDAO);
        $dbProperty = $reflection->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($this->userDAO, $this->mockPDO);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     * @group dao
     * @group user
     * @group find
     */
    public function it_should_find_user_by_school_id()
    {
        // Arrange (Red Phase - Test First)
        $schoolId = '2021-0001';
        $expectedUser = [
            'user_id' => 1,
            'school_id' => '2021-0001',
            'full_name' => 'John Doe',
            'role' => 'student'
        ];

        // Mock PDO prepare and execute
        $this->mockPDO->shouldReceive('prepare')
            ->once()
            ->with('SELECT * FROM users WHERE school_id = ?')
            ->andReturn($this->mockStatement);

        $this->mockStatement->shouldReceive('execute')
            ->once()
            ->with([$schoolId])
            ->andReturn(true);

        $this->mockStatement->shouldReceive('fetch')
            ->once()
            ->with(PDO::FETCH_ASSOC)
            ->andReturn($expectedUser);

        // Act (Green Phase - Make it pass)
        $result = $this->userDAO->findBySchoolId($schoolId);

        // Assert (Refactor Phase - Clean up)
        $this->assertEquals($expectedUser, $result);
    }

    /**
     * @test
     * @group dao
     * @group user
     * @group find
     */
    public function it_should_return_null_when_user_not_found_by_school_id()
    {
        // Arrange (Red Phase)
        $schoolId = 'nonexistent';

        // Mock PDO prepare and execute
        $this->mockPDO->shouldReceive('prepare')
            ->once()
            ->with('SELECT * FROM users WHERE school_id = ?')
            ->andReturn($this->mockStatement);

        $this->mockStatement->shouldReceive('execute')
            ->once()
            ->with([$schoolId])
            ->andReturn(true);

        $this->mockStatement->shouldReceive('fetch')
            ->once()
            ->with(PDO::FETCH_ASSOC)
            ->andReturn(false);

        // Act (Green Phase)
        $result = $this->userDAO->findBySchoolId($schoolId);

        // Assert (Refactor Phase)
        $this->assertNull($result);
    }

    /**
     * @test
     * @group dao
     * @group user
     * @group find
     */
    public function it_should_find_user_by_id()
    {
        // Arrange (Red Phase)
        $userId = 1;
        $expectedUser = [
            'user_id' => 1,
            'school_id' => '2021-0001',
            'full_name' => 'John Doe',
            'role' => 'student'
        ];

        // Mock PDO prepare and execute
        $this->mockPDO->shouldReceive('prepare')
            ->once()
            ->with('SELECT * FROM users WHERE user_id = ?')
            ->andReturn($this->mockStatement);

        $this->mockStatement->shouldReceive('execute')
            ->once()
            ->with([$userId])
            ->andReturn(true);

        $this->mockStatement->shouldReceive('fetch')
            ->once()
            ->with(PDO::FETCH_ASSOC)
            ->andReturn($expectedUser);

        // Act (Green Phase)
        $result = $this->userDAO->findById($userId);

        // Assert (Refactor Phase)
        $this->assertEquals($expectedUser, $result);
    }

    /**
     * @test
     * @group dao
     * @group user
     * @group create
     */
    public function it_should_create_user_successfully()
    {
        // Arrange (Red Phase)
        $userData = [
            'school_id' => '2021-0001',
            'full_name' => 'John Doe',
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A',
            'password' => 'hashedpassword'
        ];

        $expectedUserId = 1;

        // Mock PDO prepare and execute
        $this->mockPDO->shouldReceive('prepare')
            ->once()
            ->with('INSERT INTO users (school_id, full_name, role, year_level, section, password) VALUES (?, ?, ?, ?, ?, ?)')
            ->andReturn($this->mockStatement);

        $this->mockStatement->shouldReceive('execute')
            ->once()
            ->with([
                '2021-0001',
                'John Doe',
                'student',
                '1st',
                'A',
                'hashedpassword'
            ])
            ->andReturn(true);

        $this->mockPDO->shouldReceive('lastInsertId')
            ->once()
            ->andReturn($expectedUserId);

        // Act (Green Phase)
        $result = $this->userDAO->create($userData);

        // Assert (Refactor Phase)
        $this->assertEquals($expectedUserId, $result);
    }

    /**
     * @test
     * @group dao
     * @group user
     * @group update
     */
    public function it_should_update_user_successfully()
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

        // Mock PDO prepare and execute
        $this->mockPDO->shouldReceive('prepare')
            ->once()
            ->with('UPDATE users SET school_id = ?, full_name = ?, role = ?, year_level = ?, section = ? WHERE user_id = ?')
            ->andReturn($this->mockStatement);

        $this->mockStatement->shouldReceive('execute')
            ->once()
            ->with([
                '2021-0001',
                'John Updated',
                'student',
                '2nd',
                'B',
                1
            ])
            ->andReturn(true);

        $this->mockStatement->shouldReceive('rowCount')
            ->once()
            ->andReturn(1);

        // Act (Green Phase)
        $result = $this->userDAO->update($userId, $userData);

        // Assert (Refactor Phase)
        $this->assertTrue($result);
    }

    /**
     * @test
     * @group dao
     * @group user
     * @group delete
     */
    public function it_should_delete_user_successfully()
    {
        // Arrange (Red Phase)
        $userId = 1;

        // Mock PDO prepare and execute
        $this->mockPDO->shouldReceive('prepare')
            ->once()
            ->with('DELETE FROM users WHERE user_id = ?')
            ->andReturn($this->mockStatement);

        $this->mockStatement->shouldReceive('execute')
            ->once()
            ->with([$userId])
            ->andReturn(true);

        $this->mockStatement->shouldReceive('rowCount')
            ->once()
            ->andReturn(1);

        // Act (Green Phase)
        $result = $this->userDAO->delete($userId);

        // Assert (Refactor Phase)
        $this->assertTrue($result);
    }

    /**
     * @test
     * @group dao
     * @group user
     * @group authenticate
     */
    public function it_should_authenticate_user_with_valid_credentials()
    {
        // Arrange (Red Phase)
        $schoolId = '2021-0001';
        $password = 'password123';
        
        $expectedUser = [
            'user_id' => 1,
            'school_id' => '2021-0001',
            'full_name' => 'John Doe',
            'role' => 'student',
            'password' => password_hash('password123', PASSWORD_DEFAULT)
        ];

        // Mock PDO prepare and execute
        $this->mockPDO->shouldReceive('prepare')
            ->once()
            ->with('SELECT * FROM users WHERE school_id = ?')
            ->andReturn($this->mockStatement);

        $this->mockStatement->shouldReceive('execute')
            ->once()
            ->with([$schoolId])
            ->andReturn(true);

        $this->mockStatement->shouldReceive('fetch')
            ->once()
            ->with(PDO::FETCH_ASSOC)
            ->andReturn($expectedUser);

        // Act (Green Phase)
        $result = $this->userDAO->authenticate($schoolId, $password);

        // Assert (Refactor Phase)
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals($expectedUser['user_id'], $result['user']['user_id']);
    }

    /**
     * @test
     * @group dao
     * @group user
     * @group authenticate
     */
    public function it_should_fail_authentication_with_invalid_password()
    {
        // Arrange (Red Phase)
        $schoolId = '2021-0001';
        $password = 'wrongpassword';
        
        $user = [
            'user_id' => 1,
            'school_id' => '2021-0001',
            'full_name' => 'John Doe',
            'role' => 'student',
            'password' => password_hash('correctpassword', PASSWORD_DEFAULT)
        ];

        // Mock PDO prepare and execute
        $this->mockPDO->shouldReceive('prepare')
            ->once()
            ->with('SELECT * FROM users WHERE school_id = ?')
            ->andReturn($this->mockStatement);

        $this->mockStatement->shouldReceive('execute')
            ->once()
            ->with([$schoolId])
            ->andReturn(true);

        $this->mockStatement->shouldReceive('fetch')
            ->once()
            ->with(PDO::FETCH_ASSOC)
            ->andReturn($user);

        // Act (Green Phase)
        $result = $this->userDAO->authenticate($schoolId, $password);

        // Assert (Refactor Phase)
        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid credentials', $result['message']);
    }

    /**
     * @test
     * @group dao
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

        // Mock PDO prepare and execute
        $this->mockPDO->shouldReceive('prepare')
            ->once()
            ->with('SELECT * FROM users ORDER BY user_id DESC')
            ->andReturn($this->mockStatement);

        $this->mockStatement->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->mockStatement->shouldReceive('fetchAll')
            ->once()
            ->with(PDO::FETCH_ASSOC)
            ->andReturn($expectedUsers);

        // Act (Green Phase)
        $result = $this->userDAO->getAllUsers();

        // Assert (Refactor Phase)
        $this->assertEquals($expectedUsers, $result);
    }

    /**
     * @test
     * @group dao
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

        // Mock PDO prepare and execute
        $this->mockPDO->shouldReceive('prepare')
            ->once()
            ->with('SELECT * FROM users WHERE role = ? ORDER BY user_id DESC')
            ->andReturn($this->mockStatement);

        $this->mockStatement->shouldReceive('execute')
            ->once()
            ->with([$role])
            ->andReturn(true);

        $this->mockStatement->shouldReceive('fetchAll')
            ->once()
            ->with(PDO::FETCH_ASSOC)
            ->andReturn($expectedUsers);

        // Act (Green Phase)
        $result = $this->userDAO->getUsersByRole($role);

        // Assert (Refactor Phase)
        $this->assertEquals($expectedUsers, $result);
    }

    /**
     * @test
     * @group dao
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

        // Mock PDO prepare and execute
        $this->mockPDO->shouldReceive('prepare')
            ->once()
            ->with('SELECT * FROM users WHERE role = "student" AND year_level = ? AND section = ? ORDER BY full_name')
            ->andReturn($this->mockStatement);

        $this->mockStatement->shouldReceive('execute')
            ->once()
            ->with([$yearLevel, $section])
            ->andReturn(true);

        $this->mockStatement->shouldReceive('fetchAll')
            ->once()
            ->with(PDO::FETCH_ASSOC)
            ->andReturn($expectedStudents);

        // Act (Green Phase)
        $result = $this->userDAO->getStudentsByYearSection($yearLevel, $section);

        // Assert (Refactor Phase)
        $this->assertEquals($expectedStudents, $result);
    }
}