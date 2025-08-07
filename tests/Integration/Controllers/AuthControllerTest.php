<?php

namespace Tests\Integration\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\Auth\AuthController;
use App\Services\Auth\AuthService;
use App\DAO\Auth\UserDAO;
use Mockery;

class AuthControllerTest extends TestCase
{
    private $mockAuthService;
    private $authController;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock for AuthService
        $this->mockAuthService = Mockery::mock(AuthService::class);
        
        // Create AuthController with mocked dependencies
        $this->authController = new AuthController();
        
        // Use reflection to inject mocked AuthService
        $reflection = new \ReflectionClass($this->authController);
        $authServiceProperty = $reflection->getProperty('authService');
        $authServiceProperty->setAccessible(true);
        $authServiceProperty->setValue($this->authController, $this->mockAuthService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
        
        // Clean up session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * @test
     * @group integration
     * @group auth
     * @group login
     */
    public function it_should_handle_successful_login_request()
    {
        // Arrange (Red Phase - Test First)
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['school_id'] = '2021-0001';
        $_POST['password'] = 'password123';
        
        $expectedUser = [
            'user_id' => 1,
            'school_id' => '2021-0001',
            'full_name' => 'John Doe',
            'role' => 'student'
        ];

        // Mock AuthService login to return success
        $this->mockAuthService->shouldReceive('login')
            ->once()
            ->with('2021-0001', 'password123')
            ->andReturn([
                'success' => true,
                'message' => 'Login successful',
                'user' => $expectedUser
            ]);

        // Mock redirectToDashboard
        $this->mockAuthService->shouldReceive('redirectToDashboard')
            ->once()
            ->with('student');

        // Act (Green Phase - Make it pass)
        $this->authController->login();

        // Assert (Refactor Phase - Clean up)
        // The method should not throw any exceptions
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group integration
     * @group auth
     * @group login
     */
    public function it_should_handle_failed_login_request()
    {
        // Arrange (Red Phase)
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['school_id'] = '2021-0001';
        $_POST['password'] = 'wrongpassword';

        // Mock AuthService login to return failure
        $this->mockAuthService->shouldReceive('login')
            ->once()
            ->with('2021-0001', 'wrongpassword')
            ->andReturn([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);

        // Mock showLoginError
        $this->mockAuthService->shouldReceive('showLoginError')
            ->once()
            ->with('Invalid credentials');

        // Act (Green Phase)
        $this->authController->login();

        // Assert (Refactor Phase)
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group integration
     * @group auth
     * @group login
     */
    public function it_should_handle_invalid_request_method()
    {
        // Arrange (Red Phase)
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // Mock showLoginError
        $this->mockAuthService->shouldReceive('showLoginError')
            ->once()
            ->with('Invalid request method.');

        // Act (Green Phase)
        $this->authController->login();

        // Assert (Refactor Phase)
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group integration
     * @group auth
     * @group logout
     */
    public function it_should_handle_logout_request()
    {
        // Arrange (Red Phase)
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['confirm'] = 'true';

        // Mock AuthService logout to return success
        $this->mockAuthService->shouldReceive('logout')
            ->once()
            ->andReturn([
                'success' => true,
                'message' => 'Logout successful'
            ]);

        // Mock redirect to login
        $this->mockAuthService->shouldReceive('redirectToLogin')
            ->once();

        // Act (Green Phase)
        $this->authController->logout();

        // Assert (Refactor Phase)
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group integration
     * @group auth
     * @group logout
     */
    public function it_should_show_confirmation_page_when_logout_not_confirmed()
    {
        // Arrange (Red Phase)
        $_SERVER['REQUEST_METHOD'] = 'GET';
        unset($_GET['confirm']);

        // Act (Green Phase)
        ob_start();
        $this->authController->logout();
        $output = ob_get_clean();

        // Assert (Refactor Phase)
        $this->assertStringContainsString('Are you sure you want to logout?', $output);
        $this->assertStringContainsString('logout?confirm=true', $output);
    }

    /**
     * @test
     * @group integration
     * @group auth
     * @group show_login
     */
    public function it_should_display_login_page()
    {
        // Arrange (Red Phase)
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // Act (Green Phase)
        ob_start();
        $this->authController->showLogin();
        $output = ob_get_clean();

        // Assert (Refactor Phase)
        $this->assertStringContainsString('Login', $output);
        $this->assertStringContainsString('School ID', $output);
        $this->assertStringContainsString('Password', $output);
    }

    /**
     * @test
     * @group integration
     * @group auth
     * @group show_login
     */
    public function it_should_display_login_page_with_error_message()
    {
        // Arrange (Red Phase)
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SESSION['error'] = 'Invalid credentials';

        // Act (Green Phase)
        ob_start();
        $this->authController->showLogin();
        $output = ob_get_clean();

        // Assert (Refactor Phase)
        $this->assertStringContainsString('Invalid credentials', $output);
    }
}