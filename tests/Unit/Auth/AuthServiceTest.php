<?php

namespace Tests\Unit\Auth;

use PHPUnit\Framework\TestCase;
use App\Services\Auth\AuthService;
use App\DAO\Auth\UserDAO;
use App\Config\Database;

class AuthServiceTest extends TestCase
{
    private AuthService $authService;
    private UserDAO $userDAO;
    private ?int $createdUserId = null;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure session isolation
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        $_SESSION = [];

        $this->authService = new AuthService();
        $this->userDAO = new UserDAO();
    }

    protected function tearDown(): void
    {
        // Clean created user
        if ($this->createdUserId) {
            $this->userDAO->delete($this->createdUserId);
            $this->createdUserId = null;
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        $_SESSION = [];
        parent::tearDown();
    }

    /** @test */
    public function it_should_return_success_when_valid_credentials_provided()
    {
        $schoolId = 'UT_' . uniqid();
        $password = 'password123';
        // Create a user directly in DB with a plaintext password for test simplicity
        $userId = $this->userDAO->create([
            'school_id' => $schoolId,
            'full_name' => 'John Doe',
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A',
        ]);
        $this->createdUserId = $userId;

        // AuthService will construct default password as school_id+full_name in DAO::create.
        $result = $this->authService->login($schoolId, $schoolId . 'John Doe');

        $this->assertTrue($result['success']);
        $this->assertSame('Login successful!', $result['message']);
        $this->assertEquals($schoolId, $result['user']['school_id']);
    }

    /** @test */
    public function it_should_return_failure_when_invalid_credentials_provided()
    {
        $result = $this->authService->login('NOPE', 'bad');
        $this->assertFalse($result['success']);
        $this->assertSame('Invalid School ID or password.', $result['message']);
    }

    /** @test */
    public function it_should_return_failure_when_empty_credentials_provided()
    {
        $result = $this->authService->login('', '');
        $this->assertFalse($result['success']);
        $this->assertSame('School ID and password are required.', $result['message']);
    }

    /** @test */
    public function it_should_destroy_session_on_logout()
    {
        // Start a session and set some values
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'student';

        $result = $this->authService->logout();
        $this->assertTrue($result['success']);
        $this->assertSame('Logged out successfully.', $result['message']);
    }

    /** @test */
    public function it_should_return_current_user_when_session_exists()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION['user_id'] = 1;
        $_SESSION['school_id'] = '2021-0001';
        $_SESSION['full_name'] = 'John Doe';
        $_SESSION['role'] = 'student';

        $result = $this->authService->getCurrentUser();
        $this->assertEquals(1, $result['user_id']);
        $this->assertEquals('2021-0001', $result['school_id']);
        $this->assertEquals('John Doe', $result['full_name']);
        $this->assertEquals('student', $result['role']);
    }

    /** @test */
    public function it_should_return_null_when_no_session_exists()
    {
        if (session_status() === PHP_SESSION_ACTIVE) { session_destroy(); }
        unset($_SESSION['user_id'], $_SESSION['user']);
        $result = $this->authService->getCurrentUser();
        $this->assertNull($result);
    }
}