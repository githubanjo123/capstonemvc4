<?php

namespace Tests\Unit\Auth;

use PHPUnit\Framework\TestCase;
use App\Services\Auth\AuthService;
use App\DAO\Auth\UserDAO;
use App\Interfaces\UserDAOInterface;
use Mockery;

class AuthServiceTest extends TestCase
{
    private $mockUserDAO;
    private $authService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock for UserDAO
        $this->mockUserDAO = Mockery::mock(UserDAOInterface::class);
        
        // Create AuthService with mocked dependencies
        $this->authService = new AuthService($this->mockUserDAO);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     * @group auth
     * @group login
     */
    public function it_should_return_success_when_valid_credentials_provided()
    {
        // Arrange (Red Phase - Test First)
        $schoolId = '2021-0001';
        $password = 'password123';
        
        $expectedUser = [
            'user_id' => 1,
            'school_id' => '2021-0001',
            'full_name' => 'John Doe',
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A'
        ];

        // Mock the authenticate method to return success
        $this->mockUserDAO->shouldReceive('authenticate')
            ->once()
            ->with($schoolId, $password)
            ->andReturn([
                'success' => true,
                'user' => $expectedUser
            ]);

        // Act (Green Phase - Make it pass)
        $result = $this->authService->login($schoolId, $password);

        // Assert (Refactor Phase - Clean up)
        $this->assertTrue($result['success']);
        $this->assertEquals('Login successful', $result['message']);
        $this->assertEquals($expectedUser, $result['user']);
        $this->assertArrayHasKey('user', $result);
    }

    /**
     * @test
     * @group auth
     * @group login
     */
    public function it_should_return_failure_when_invalid_credentials_provided()
    {
        // Arrange (Red Phase)
        $schoolId = '2021-0001';
        $password = 'wrongpassword';

        // Mock the authenticate method to return failure
        $this->mockUserDAO->shouldReceive('authenticate')
            ->once()
            ->with($schoolId, $password)
            ->andReturn([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);

        // Act (Green Phase)
        $result = $this->authService->login($schoolId, $password);

        // Assert (Refactor Phase)
        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid credentials', $result['message']);
        $this->assertArrayNotHasKey('user', $result);
    }

    /**
     * @test
     * @group auth
     * @group login
     */
    public function it_should_return_failure_when_empty_credentials_provided()
    {
        // Arrange (Red Phase)
        $schoolId = '';
        $password = '';

        // Act (Green Phase)
        $result = $this->authService->login($schoolId, $password);

        // Assert (Refactor Phase)
        $this->assertFalse($result['success']);
        $this->assertEquals('School ID and password are required', $result['message']);
    }

    /**
     * @test
     * @group auth
     * @group logout
     */
    public function it_should_destroy_session_on_logout()
    {
        // Arrange (Red Phase)
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'student';

        // Act (Green Phase)
        $result = $this->authService->logout();

        // Assert (Refactor Phase)
        $this->assertTrue($result['success']);
        $this->assertEquals('Logout successful', $result['message']);
        $this->assertEmpty($_SESSION);
    }

    /**
     * @test
     * @group auth
     * @group current_user
     */
    public function it_should_return_current_user_when_session_exists()
    {
        // Arrange (Red Phase)
        $expectedUser = [
            'user_id' => 1,
            'school_id' => '2021-0001',
            'full_name' => 'John Doe',
            'role' => 'student'
        ];
        $_SESSION['user_id'] = 1;
        $_SESSION['user'] = $expectedUser;

        // Mock findById to return user
        $this->mockUserDAO->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($expectedUser);

        // Act (Green Phase)
        $result = $this->authService->getCurrentUser();

        // Assert (Refactor Phase)
        $this->assertEquals($expectedUser, $result);
    }

    /**
     * @test
     * @group auth
     * @group current_user
     */
    public function it_should_return_null_when_no_session_exists()
    {
        // Arrange (Red Phase)
        unset($_SESSION['user_id']);
        unset($_SESSION['user']);

        // Act (Green Phase)
        $result = $this->authService->getCurrentUser();

        // Assert (Refactor Phase)
        $this->assertNull($result);
    }
}