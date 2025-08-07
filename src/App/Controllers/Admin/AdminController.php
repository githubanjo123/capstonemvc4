<?php

namespace App\Controllers\Admin;

use App\Services\Auth\AuthService;
use App\Core\View;

class AdminController
{
    private $authService;
    private $view;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->view = new View();
        
        // Ensure user is authenticated and is admin
        $this->authService->requireAuth();
        $this->authService->requireRole('admin');
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $currentUser = $this->authService->getCurrentUser();
        
        // Get sample data for demonstration
        $students = $this->getSampleStudents();
        $faculty = $this->getSampleFaculty();
        
        $data = [
            'admin' => $currentUser,
            'students' => $students,
            'faculty' => $faculty,
            'yearSections' => $this->getYearSections($students)
        ];
        
        $this->view->display('admin.dashboard', $data);
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        // Check if user confirmed logout
        if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
            $this->authService->logout();
            
            // Get the base path for correct redirect
            $scriptName = $_SERVER['SCRIPT_NAME'];
            $basePath = dirname($scriptName);
            $loginUrl = $basePath . '/login';
            
            header('Location: ' . $loginUrl);
            exit;
        } else {
            // Show confirmation page
            $this->showLogoutConfirmation();
        }
    }

    /**
     * Show logout confirmation page
     */
    private function showLogoutConfirmation()
    {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = dirname($scriptName);
        $logoutUrl = $basePath . '/admin/logout?confirm=true';
        $dashboardUrl = $basePath . '/admin/dashboard';
        
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Confirm Logout - Admin Dashboard</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-header bg-warning text-white">
                                <h4 class="mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Confirm Logout
                                </h4>
                            </div>
                            <div class="card-body text-center">
                                <i class="fas fa-sign-out-alt fa-3x text-warning mb-3"></i>
                                <h5>Are you sure you want to logout?</h5>
                                <p class="text-muted">You will be redirected to the login page.</p>
                                
                                <div class="mt-4">
                                    <a href="' . $logoutUrl . '" class="btn btn-warning me-2">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Yes, Logout
                                    </a>
                                    <a href="' . $dashboardUrl . '" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>';
        exit;
    }

    /**
     * Get sample students data
     */
    private function getSampleStudents()
    {
        return [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john.doe@student.edu',
                'school_id' => '2021-001',
                'year' => '1st',
                'section' => 'A',
                'created_at' => '2024-01-15'
            ],
            [
                'id' => 2,
                'name' => 'Jane Smith',
                'email' => 'jane.smith@student.edu',
                'school_id' => '2021-002',
                'year' => '1st',
                'section' => 'A',
                'created_at' => '2024-01-16'
            ],
            [
                'id' => 3,
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@student.edu',
                'school_id' => '2020-001',
                'year' => '2nd',
                'section' => 'A',
                'created_at' => '2024-01-10'
            ],
            [
                'id' => 4,
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@student.edu',
                'school_id' => '2020-002',
                'year' => '2nd',
                'section' => 'B',
                'created_at' => '2024-01-12'
            ],
            [
                'id' => 5,
                'name' => 'David Brown',
                'email' => 'david.brown@student.edu',
                'school_id' => '2021-003',
                'year' => '1st',
                'section' => 'B',
                'created_at' => '2024-01-18'
            ]
        ];
    }

    /**
     * Get sample faculty data
     */
    private function getSampleFaculty()
    {
        return [
            [
                'id' => 1,
                'name' => 'Dr. Robert Chen',
                'email' => 'robert.chen@faculty.edu',
                'department' => 'Computer Science',
                'created_at' => '2024-01-05'
            ],
            [
                'id' => 2,
                'name' => 'Prof. Maria Garcia',
                'email' => 'maria.garcia@faculty.edu',
                'department' => 'Mathematics',
                'created_at' => '2024-01-08'
            ]
        ];
    }

    /**
     * Get year-section combinations with counts
     */
    private function getYearSections($students)
    {
        $yearSections = [];
        
        foreach ($students as $student) {
            $key = $student['year'] . ' ' . $student['section'];
            if (!isset($yearSections[$key])) {
                $yearSections[$key] = 0;
            }
            $yearSections[$key]++;
        }
        
        return $yearSections;
    }
}