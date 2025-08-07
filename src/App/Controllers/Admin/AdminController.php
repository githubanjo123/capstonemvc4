<?php

namespace App\Controllers\Admin;

use App\Services\AuthService;
use App\DAO\UserDAO;
use App\DAO\SubjectDAO;
use App\DAO\ExamDAO;
use App\Core\View;

class AdminController
{
    private $authService;
    private $userDAO;
    private $subjectDAO;
    private $examDAO;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userDAO = new UserDAO();
        $this->subjectDAO = new SubjectDAO();
        $this->examDAO = new ExamDAO();
    }

    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        $authResult = $this->authService->requireRole('admin');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $totalUsers = count($this->userDAO->getAllUsers());
        $totalSubjects = count($this->subjectDAO->getAllSubjects());
        $totalExams = count($this->examDAO->getAllExams());

        $view = new View();
        $view->display('admin.dashboard', [
            'user' => $user,
            'stats' => [
                'total_users' => $totalUsers,
                'total_subjects' => $totalSubjects,
                'total_exams' => $totalExams
            ]
        ]);
    }

    /**
     * Manage users
     */
    public function manageUsers()
    {
        $authResult = $this->authService->requireRole('admin');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $users = $this->userDAO->getAllUsers();
        $view = new View();
        $view->display('admin.users', [
            'users' => $users
        ]);
    }

    /**
     * Add user
     */
    public function addUser()
    {
        $authResult = $this->authService->requireRole('admin');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'school_id' => $_POST['school_id'] ?? '',
                'full_name' => $_POST['full_name'] ?? '',
                'role' => $_POST['role'] ?? 'student',
                'year_level' => $_POST['year_level'] ?? null,
                'section' => $_POST['section'] ?? null
            ];

            $result = $this->userDAO->create($data);
            if ($result) {
                $this->redirect('/admin/users?success=User added successfully');
            } else {
                $this->redirect('/admin/users?error=Failed to add user');
            }
        } else {
            $view = new View();
            $view->display('admin.add_user');
        }
    }

    /**
     * Edit user
     */
    public function editUser($user_id)
    {
        $authResult = $this->authService->requireRole('admin');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'school_id' => $_POST['school_id'] ?? '',
                'full_name' => $_POST['full_name'] ?? '',
                'role' => $_POST['role'] ?? 'student',
                'year_level' => $_POST['year_level'] ?? null,
                'section' => $_POST['section'] ?? null
            ];

            $result = $this->userDAO->update($user_id, $data);
            if ($result) {
                $this->redirect('/admin/users?success=User updated successfully');
            } else {
                $this->redirect('/admin/users?error=Failed to update user');
            }
        } else {
            $user = $this->userDAO->findById($user_id);
            if (!$user) {
                $this->redirect('/admin/users?error=User not found');
                return;
            }

            $view = new View();
            $view->display('admin.edit_user', [
                'user' => $user
            ]);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($user_id)
    {
        $authResult = $this->authService->requireRole('admin');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $result = $this->userDAO->delete($user_id);
        if ($result) {
            $this->redirect('/admin/users?success=User deleted successfully');
        } else {
            $this->redirect('/admin/users?error=Failed to delete user');
        }
    }

    /**
     * Manage subjects
     */
    public function manageSubjects()
    {
        $authResult = $this->authService->requireRole('admin');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $subjects = $this->subjectDAO->getAllSubjects();
        $view = new View();
        $view->display('admin.subjects', [
            'subjects' => $subjects
        ]);
    }

    /**
     * Add subject
     */
    public function addSubject()
    {
        $authResult = $this->authService->requireRole('admin');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'course_code' => $_POST['course_code'] ?? '',
                'descriptive_title' => $_POST['descriptive_title'] ?? ''
            ];

            $result = $this->subjectDAO->create($data);
            if ($result) {
                $this->redirect('/admin/subjects?success=Subject added successfully');
            } else {
                $this->redirect('/admin/subjects?error=Failed to add subject');
            }
        } else {
            $view = new View();
            $view->display('admin.add_subject');
        }
    }

    /**
     * Edit subject
     */
    public function editSubject($subject_id)
    {
        $authResult = $this->authService->requireRole('admin');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'course_code' => $_POST['course_code'] ?? '',
                'descriptive_title' => $_POST['descriptive_title'] ?? ''
            ];

            $result = $this->subjectDAO->update($subject_id, $data);
            if ($result) {
                $this->redirect('/admin/subjects?success=Subject updated successfully');
            } else {
                $this->redirect('/admin/subjects?error=Failed to update subject');
            }
        } else {
            $subject = $this->subjectDAO->getSubjectById($subject_id);
            if (!$subject) {
                $this->redirect('/admin/subjects?error=Subject not found');
                return;
            }

            $view = new View();
            $view->display('admin.edit_subject', [
                'subject' => $subject
            ]);
        }
    }

    /**
     * Delete subject
     */
    public function deleteSubject($subject_id)
    {
        $authResult = $this->authService->requireRole('admin');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $result = $this->subjectDAO->delete($subject_id);
        if ($result) {
            $this->redirect('/admin/subjects?success=Subject deleted successfully');
        } else {
            $this->redirect('/admin/subjects?error=Failed to delete subject');
        }
    }

    /**
     * View results
     */
    public function viewResults()
    {
        $authResult = $this->authService->requireRole('admin');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $exams = $this->examDAO->getAllExams();
        $view = new View();
        $view->display('admin.results', [
            'exams' => $exams
        ]);
    }

    /**
     * Generate reports
     */
    public function generateReports()
    {
        $authResult = $this->authService->requireRole('admin');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $view = new View();
        $view->display('admin.reports');
    }

    /**
     * Redirect helper
     */
    private function redirect($url)
    {
        header("Location: $url");
        exit;
    }
}