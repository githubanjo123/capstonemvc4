<?php

namespace App\Controllers\Student;

use App\Services\AuthService;
use App\Services\ExamService;
use App\DAO\ExamDAO;
use App\Core\View;

class StudentController
{
    private $authService;
    private $examService;
    private $examDAO;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->examService = new ExamService();
        $this->examDAO = new ExamDAO();
    }

    /**
     * Student dashboard
     */
    public function dashboard()
    {
        $authResult = $this->authService->requireRole('student');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $exams = $this->examDAO->getExamsForStudents($user['year_level'], $user['section']);

        $view = new View();
        $view->display('student.dashboard', [
            'user' => $user,
            'exams' => $exams
        ]);
    }

    /**
     * Take exam
     */
    public function takeExam($exam_id)
    {
        $authResult = $this->authService->requireRole('student');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $exam = $this->examService->getExamForStudent($exam_id);

        if (!$exam) {
            $this->redirect('/student/dashboard?error=Exam not found');
            return;
        }

        // Check if exam is for student's year and section
        if ($exam['year_level'] != $user['year_level'] || $exam['section'] != $user['section']) {
            $this->redirect('/student/dashboard?error=Access denied');
            return;
        }

        // Check if exam is active
        if ($exam['status'] !== 'active') {
            $this->redirect('/student/dashboard?error=Exam is not available');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Start exam attempt
            $startResult = $this->examService->startExam($exam_id, $user['user_id']);
            if (!$startResult['success']) {
                $this->redirect('/student/dashboard?error=' . $startResult['message']);
                return;
            }

            $attempt_id = $startResult['attempt_id'];
            $this->redirect("/student/exam/$exam_id/take?attempt_id=$attempt_id");
        } else {
            $view = new View();
            $view->display('student.take_exam', [
                'exam' => $exam,
                'user' => $user
            ]);
        }
    }

    /**
     * Start exam session
     */
    public function startExamSession($exam_id)
    {
        $authResult = $this->authService->requireRole('student');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $attempt_id = $_GET['attempt_id'] ?? null;

        if (!$attempt_id) {
            $this->redirect('/student/dashboard?error=Invalid exam session');
            return;
        }

        $exam = $this->examService->getExamForStudent($exam_id);
        if (!$exam) {
            $this->redirect('/student/dashboard?error=Exam not found');
            return;
        }

        $view = new View();
        $view->display('student.exam_session', [
            'exam' => $exam,
            'user' => $user,
            'attempt_id' => $attempt_id
        ]);
    }

    /**
     * Submit exam answers
     */
    public function submitExam($exam_id)
    {
        $authResult = $this->authService->requireRole('student');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $attempt_id = $_POST['attempt_id'] ?? null;

        if (!$attempt_id) {
            $this->redirect('/student/dashboard?error=Invalid exam session');
            return;
        }

        // Collect answers
        $answers = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'answer_') === 0) {
                $question_id = substr($key, 7); // Remove 'answer_' prefix
                $answers[$question_id] = $value;
            }
        }

        // Submit exam
        $result = $this->examService->submitExam($attempt_id, $answers);

        if ($result['success']) {
            $this->redirect("/student/results?success=Exam submitted successfully! Score: {$result['score']}/{$result['total_points']}");
        } else {
            $this->redirect("/student/dashboard?error=" . $result['message']);
        }
    }

    /**
     * View results
     */
    public function viewResults()
    {
        $authResult = $this->authService->requireRole('student');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $results = $this->examService->getStudentResults($user['user_id']);

        $view = new View();
        $view->display('student.results', [
            'user' => $user,
            'results' => $results
        ]);
    }

    /**
     * View specific exam result
     */
    public function viewExamResult($exam_id)
    {
        $authResult = $this->authService->requireRole('student');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $results = $this->examService->getStudentResults($user['user_id']);
        
        // Find the specific exam result
        $examResult = null;
        foreach ($results as $result) {
            if ($result['exam_id'] == $exam_id) {
                $examResult = $result;
                break;
            }
        }

        if (!$examResult) {
            $this->redirect('/student/results?error=Result not found');
            return;
        }

        $view = new View();
        $view->display('student.exam_result', [
            'user' => $user,
            'result' => $examResult
        ]);
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