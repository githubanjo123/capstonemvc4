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
        $this->authService->logout();
        header('Location: /login');
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