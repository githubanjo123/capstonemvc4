<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\ExamService;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Core\View;

class FacultyController
{
    private $authService;
    private $examService;
    private $examModel;
    private $questionModel;
    private $subjectModel;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->examService = new ExamService();
        $this->examModel = new Exam();
        $this->questionModel = new Question();
        $this->subjectModel = new Subject();
    }

    /**
     * Faculty dashboard
     */
    public function dashboard()
    {
        $authResult = $this->authService->requireRole('faculty');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $exams = $this->examModel->getExamsByFaculty($user['user_id']);
        $subjects = $this->subjectModel->getSubjectsByFaculty($user['user_id']);

        $view = new View();
        $view->display('faculty.dashboard', [
            'user' => $user,
            'exams' => $exams,
            'subjects' => $subjects
        ]);
    }

    /**
     * Create exam
     */
    public function createExam()
    {
        $authResult = $this->authService->requireRole('faculty');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->authService->getCurrentUser();
            
            $examData = [
                'title' => $_POST['title'] ?? '',
                'instructions' => $_POST['instructions'] ?? '',
                'subject_id' => $_POST['subject_id'] ?? '',
                'year_level' => $_POST['year_level'] ?? '',
                'section' => $_POST['section'] ?? '',
                'created_by' => $user['user_id'],
                'time_limit' => $_POST['time_limit'] ?? 60
            ];

            $questions = [];
            if (isset($_POST['questions']) && is_array($_POST['questions'])) {
                foreach ($_POST['questions'] as $question) {
                    if (!empty($question['question_text'])) {
                        $questions[] = [
                            'question_text' => $question['question_text'],
                            'question_type' => $question['question_type'],
                            'option_a' => $question['option_a'] ?? null,
                            'option_b' => $question['option_b'] ?? null,
                            'option_c' => $question['option_c'] ?? null,
                            'option_d' => $question['option_d'] ?? null,
                            'correct_answer' => $question['correct_answer'],
                            'points' => $question['points'] ?? 1
                        ];
                    }
                }
            }

            $result = $this->examService->createExam($examData, $questions);
            if ($result['success']) {
                $this->redirect('/faculty/exams?success=Exam created successfully');
            } else {
                $this->redirect('/faculty/exams?error=' . $result['message']);
            }
        } else {
            $user = $this->authService->getCurrentUser();
            $subjects = $this->subjectModel->getSubjectsByFaculty($user['user_id']);
            
            $view = new View();
            $view->display('faculty.create_exam', [
                'subjects' => $subjects
            ]);
        }
    }

    /**
     * List exams
     */
    public function listExams()
    {
        $authResult = $this->authService->requireRole('faculty');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $exams = $this->examModel->getExamsByFaculty($user['user_id']);

        $view = new View();
        $view->display('faculty.exams', [
            'exams' => $exams
        ]);
    }

    /**
     * Edit exam
     */
    public function editExam($exam_id)
    {
        $authResult = $this->authService->requireRole('faculty');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $exam = $this->examModel->getExamById($exam_id);
        
        // Check if faculty owns this exam
        if (!$exam || $exam['created_by'] != $user['user_id']) {
            $this->redirect('/faculty/exams?error=Exam not found');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $examData = [
                'title' => $_POST['title'] ?? '',
                'instructions' => $_POST['instructions'] ?? '',
                'subject_id' => $_POST['subject_id'] ?? '',
                'year_level' => $_POST['year_level'] ?? '',
                'section' => $_POST['section'] ?? '',
                'time_limit' => $_POST['time_limit'] ?? 60,
                'status' => $_POST['status'] ?? 'active'
            ];

            $result = $this->examModel->update($exam_id, $examData);
            if ($result) {
                $this->redirect('/faculty/exams?success=Exam updated successfully');
            } else {
                $this->redirect('/faculty/exams?error=Failed to update exam');
            }
        } else {
            $questions = $this->questionModel->getQuestionsByExam($exam_id);
            $subjects = $this->subjectModel->getSubjectsByFaculty($user['user_id']);

            $view = new View();
            $view->display('faculty.edit_exam', [
                'exam' => $exam,
                'questions' => $questions,
                'subjects' => $subjects
            ]);
        }
    }

    /**
     * Delete exam
     */
    public function deleteExam($exam_id)
    {
        $authResult = $this->authService->requireRole('faculty');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $exam = $this->examModel->getExamById($exam_id);
        
        // Check if faculty owns this exam
        if (!$exam || $exam['created_by'] != $user['user_id']) {
            $this->redirect('/faculty/exams?error=Exam not found');
            return;
        }

        $result = $this->examModel->delete($exam_id);
        if ($result) {
            $this->redirect('/faculty/exams?success=Exam deleted successfully');
        } else {
            $this->redirect('/faculty/exams?error=Failed to delete exam');
        }
    }

    /**
     * Add question to exam
     */
    public function addQuestion($exam_id)
    {
        $authResult = $this->authService->requireRole('faculty');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $exam = $this->examModel->getExamById($exam_id);
        
        // Check if faculty owns this exam
        if (!$exam || $exam['created_by'] != $user['user_id']) {
            $this->redirect('/faculty/exams?error=Exam not found');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $questionData = [
                'exam_id' => $exam_id,
                'question_text' => $_POST['question_text'] ?? '',
                'question_type' => $_POST['question_type'] ?? 'multiple_choice',
                'option_a' => $_POST['option_a'] ?? null,
                'option_b' => $_POST['option_b'] ?? null,
                'option_c' => $_POST['option_c'] ?? null,
                'option_d' => $_POST['option_d'] ?? null,
                'correct_answer' => $_POST['correct_answer'] ?? '',
                'points' => $_POST['points'] ?? 1
            ];

            $result = $this->questionModel->create($questionData);
            if ($result) {
                $this->redirect("/faculty/exams/$exam_id/edit?success=Question added successfully");
            } else {
                $this->redirect("/faculty/exams/$exam_id/edit?error=Failed to add question");
            }
        } else {
            $view = new View();
            $view->display('faculty.add_question', [
                'exam' => $exam
            ]);
        }
    }

    /**
     * Edit question
     */
    public function editQuestion($question_id)
    {
        $authResult = $this->authService->requireRole('faculty');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $question = $this->questionModel->getQuestionById($question_id);
        if (!$question) {
            $this->redirect('/faculty/exams?error=Question not found');
            return;
        }

        $user = $this->authService->getCurrentUser();
        $exam = $this->examModel->getExamById($question['exam_id']);
        
        // Check if faculty owns this exam
        if (!$exam || $exam['created_by'] != $user['user_id']) {
            $this->redirect('/faculty/exams?error=Access denied');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $questionData = [
                'question_text' => $_POST['question_text'] ?? '',
                'question_type' => $_POST['question_type'] ?? 'multiple_choice',
                'option_a' => $_POST['option_a'] ?? null,
                'option_b' => $_POST['option_b'] ?? null,
                'option_c' => $_POST['option_c'] ?? null,
                'option_d' => $_POST['option_d'] ?? null,
                'correct_answer' => $_POST['correct_answer'] ?? '',
                'points' => $_POST['points'] ?? 1
            ];

            $result = $this->questionModel->update($question_id, $questionData);
            if ($result) {
                $this->redirect("/faculty/exams/{$question['exam_id']}/edit?success=Question updated successfully");
            } else {
                $this->redirect("/faculty/exams/{$question['exam_id']}/edit?error=Failed to update question");
            }
        } else {
            $view = new View();
            $view->display('faculty.edit_question', [
                'question' => $question,
                'exam' => $exam
            ]);
        }
    }

    /**
     * Delete question
     */
    public function deleteQuestion($question_id)
    {
        $authResult = $this->authService->requireRole('faculty');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $question = $this->questionModel->getQuestionById($question_id);
        if (!$question) {
            $this->redirect('/faculty/exams?error=Question not found');
            return;
        }

        $user = $this->authService->getCurrentUser();
        $exam = $this->examModel->getExamById($question['exam_id']);
        
        // Check if faculty owns this exam
        if (!$exam || $exam['created_by'] != $user['user_id']) {
            $this->redirect('/faculty/exams?error=Access denied');
            return;
        }

        $result = $this->questionModel->delete($question_id);
        if ($result) {
            $this->redirect("/faculty/exams/{$question['exam_id']}/edit?success=Question deleted successfully");
        } else {
            $this->redirect("/faculty/exams/{$question['exam_id']}/edit?error=Failed to delete question");
        }
    }

    /**
     * View exam results
     */
    public function viewResults($exam_id)
    {
        $authResult = $this->authService->requireRole('faculty');
        if (!$authResult['success']) {
            $this->redirect($authResult['redirect']);
            return;
        }

        $user = $this->authService->getCurrentUser();
        $exam = $this->examModel->getExamById($exam_id);
        
        // Check if faculty owns this exam
        if (!$exam || $exam['created_by'] != $user['user_id']) {
            $this->redirect('/faculty/exams?error=Exam not found');
            return;
        }

        $results = $this->examService->getExamResults($exam_id);

        $view = new View();
        $view->display('faculty.results', [
            'exam' => $exam,
            'results' => $results
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