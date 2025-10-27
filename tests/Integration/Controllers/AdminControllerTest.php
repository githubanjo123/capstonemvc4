<?php

namespace Tests\Integration\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\Admin\AdminController;
use App\Services\Auth\AuthService;
use App\Services\User\UserService;
use Mockery;

class AdminControllerTest extends TestCase
{
    private $mockAuthService;
    private $mockUserService;
    private $adminController;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mocks for dependencies
        $this->mockAuthService = Mockery::mock(AuthService::class);
        $this->mockUserService = Mockery::mock(UserService::class);
        
        // Create AdminController with mocked dependencies
        $this->adminController = new AdminController();
        
        // Use reflection to inject mocked services
        $reflection = new \ReflectionClass($this->adminController);
        
        $authServiceProperty = $reflection->getProperty('authService');
        $authServiceProperty->setAccessible(true);
        $authServiceProperty->setValue($this->adminController, $this->mockAuthService);
        
        $userServiceProperty = $reflection->getProperty('userService');
        $userServiceProperty->setAccessible(true);
        $userServiceProperty->setValue($this->adminController, $this->mockUserService);
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
     * @group admin
     * @group dashboard
     */
    public function it_should_display_admin_dashboard_with_user_data()
    {
        // Arrange (Red Phase - Test First)
        $currentUser = [
            'user_id' => 1,
            'school_id' => 'admin-001',
            'full_name' => 'Admin User',
            'role' => 'admin'
        ];

        $students = [
            [
                'user_id' => 2,
                'school_id' => '2021-0001',
                'full_name' => 'John Doe',
                'role' => 'student',
                'year_level' => '1st',
                'section' => 'A'
            ]
        ];

        $faculty = [
            [
                'user_id' => 3,
                'school_id' => 'faculty-001',
                'full_name' => 'Dr. Smith',
                'role' => 'faculty'
            ]
        ];

        // Mock AuthService getCurrentUser
        $this->mockAuthService->shouldReceive('getCurrentUser')
            ->once()
            ->andReturn($currentUser);

        // Mock UserService methods
        $this->mockUserService->shouldReceive('getUsersByRole')
            ->once()
            ->with('student')
            ->andReturn($students);

        $this->mockUserService->shouldReceive('getUsersByRole')
            ->once()
            ->with('faculty')
            ->andReturn($faculty);

        // Act (Green Phase - Make it pass)
        ob_start();
        $this->adminController->dashboard();
        $output = ob_get_clean();

        // Assert (Refactor Phase - Clean up)
        $this->assertStringContainsString('Admin Dashboard', $output);
        $this->assertStringContainsString('Manage Users', $output);
        $this->assertStringContainsString('John Doe', $output);
        $this->assertStringContainsString('Dr. Smith', $output);
    }

    /**
     * @test
     * @group integration
     * @group admin
     * @group add_student
     */
    public function it_should_handle_successful_student_creation()
    {
        // Arrange (Red Phase)
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['school_id'] = '2021-0002';
        $_POST['full_name'] = 'Jane Smith';
        $_POST['role'] = 'student';
        $_POST['year_level'] = '1st';
        $_POST['section'] = 'A';

        // Mock UserService createUser to return success
        $this->mockUserService->shouldReceive('createUser')
            ->once()
            ->with($_POST)
            ->andReturn([
                'success' => true,
                'message' => 'Student created successfully',
                'user_id' => 2
            ]);

        // Mock redirectToDashboard
        $this->mockUserService->shouldReceive('redirectToDashboard')
            ->once();

        // Act (Green Phase)
        $this->adminController->addStudent();

        // Assert (Refactor Phase)
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group integration
     * @group admin
     * @group add_student
     */
    public function it_should_handle_failed_student_creation()
    {
        // Arrange (Red Phase)
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['school_id'] = '2021-0001';
        $_POST['full_name'] = 'John Doe';
        $_POST['role'] = 'student';

        // Mock UserService createUser to return failure
        $this->mockUserService->shouldReceive('createUser')
            ->once()
            ->with($_POST)
            ->andReturn([
                'success' => false,
                'message' => 'School ID already exists'
            ]);

        // Mock redirectToDashboard
        $this->mockUserService->shouldReceive('redirectToDashboard')
            ->once();

        // Act (Green Phase)
        $this->adminController->addStudent();

        // Assert (Refactor Phase)
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group integration
     * @group admin
     * @group edit_student
     */
    public function it_should_handle_successful_student_update()
    {
        // Arrange (Red Phase)
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['user_id'] = '1';
        $_POST['school_id'] = '2021-0001';
        $_POST['full_name'] = 'John Updated';
        $_POST['role'] = 'student';
        $_POST['year_level'] = '2nd';
        $_POST['section'] = 'B';

        // Mock UserService updateUser to return success
        $this->mockUserService->shouldReceive('updateUser')
            ->once()
            ->with('1', $_POST)
            ->andReturn([
                'success' => true,
                'message' => 'Student updated successfully'
            ]);

        // Mock redirectToDashboard
        $this->mockUserService->shouldReceive('redirectToDashboard')
            ->once();

        // Act (Green Phase)
        $this->adminController->editStudent();

        // Assert (Refactor Phase)
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group integration
     * @group admin
     * @group delete_student
     */
    public function it_should_handle_successful_student_deletion()
    {
        // Arrange (Red Phase)
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['user_id'] = '1';

        // Mock UserService deleteUser to return success
        $this->mockUserService->shouldReceive('deleteUser')
            ->once()
            ->with('1')
            ->andReturn([
                'success' => true,
                'message' => 'Student deleted successfully'
            ]);

        // Mock redirectToDashboard
        $this->mockUserService->shouldReceive('redirectToDashboard')
            ->once();

        // Act (Green Phase)
        $this->adminController->deleteStudent();

        // Assert (Refactor Phase)
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group integration
     * @group admin
     * @group logout
     */
    public function it_should_handle_admin_logout()
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
        $this->adminController->logout();

        // Assert (Refactor Phase)
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group integration
     * @group admin
     * @group logout
     */
    public function it_should_show_confirmation_page_when_admin_logout_not_confirmed()
    {
        // Arrange (Red Phase)
        $_SERVER['REQUEST_METHOD'] = 'GET';
        unset($_GET['confirm']);

        // Act (Green Phase)
        ob_start();
        $this->adminController->logout();
        $output = ob_get_clean();

        // Assert (Refactor Phase)
        $this->assertStringContainsString('Are you sure you want to logout?', $output);
        $this->assertStringContainsString('logout?confirm=true', $output);
    }

    /**
     * @test
     * @group integration
     * @group admin
     * @group year_sections
     */
    public function it_should_get_year_sections_from_students()
    {
        // Arrange (Red Phase)
        $students = [
            [
                'user_id' => 1,
                'school_id' => '2021-0001',
                'full_name' => 'John Doe',
                'role' => 'student',
                'year_level' => '1st',
                'section' => 'A'
            ],
            [
                'user_id' => 2,
                'school_id' => '2021-0002',
                'full_name' => 'Jane Smith',
                'role' => 'student',
                'year_level' => '1st',
                'section' => 'B'
            ],
            [
                'user_id' => 3,
                'school_id' => '2021-0003',
                'full_name' => 'Bob Wilson',
                'role' => 'student',
                'year_level' => '2nd',
                'section' => 'A'
            ]
        ];

        // Mock UserService getUsersByRole
        $this->mockUserService->shouldReceive('getUsersByRole')
            ->once()
            ->with('student')
            ->andReturn($students);

        // Act (Green Phase)
        $result = $this->adminController->getYearSections();

        // Assert (Refactor Phase)
        $expected = [
            '1st' => ['A', 'B'],
            '2nd' => ['A']
        ];
        $this->assertEquals($expected, $result);
    }
}