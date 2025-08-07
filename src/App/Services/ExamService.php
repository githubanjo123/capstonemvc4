<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;

class ExamService
{
    private $examModel;
    private $questionModel;
    private $attemptModel;
    private $answerModel;

    public function __construct()
    {
        $this->examModel = new Exam();
        $this->questionModel = new Question();
        $this->attemptModel = new ExamAttempt();
        $this->answerModel = new StudentAnswer();
    }

    /**
     * Create new exam with questions
     */
    public function createExam($examData, $questions)
    {
        try {
            // Create exam
            $exam_id = $this->examModel->create($examData);
            if (!$exam_id) {
                return [
                    'success' => false,
                    'message' => 'Failed to create exam.'
                ];
            }

            // Add questions
            foreach ($questions as $index => $question) {
                $question['exam_id'] = $exam_id;
                $question['question_order'] = $index + 1;
                
                if (!$this->questionModel->create($question)) {
                    return [
                        'success' => false,
                        'message' => 'Failed to add question ' . ($index + 1)
                    ];
                }
            }

            return [
                'success' => true,
                'message' => 'Exam created successfully!',
                'exam_id' => $exam_id
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error creating exam: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Start exam for student
     */
    public function startExam($exam_id, $student_id)
    {
        // Check if student can take exam
        if (!$this->attemptModel->canTakeExam($exam_id, $student_id)) {
            return [
                'success' => false,
                'message' => 'You have already completed this exam.'
            ];
        }

        // Start attempt
        $attempt_id = $this->attemptModel->startAttempt($exam_id, $student_id);
        if (!$attempt_id) {
            return [
                'success' => false,
                'message' => 'Failed to start exam.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Exam started successfully!',
            'attempt_id' => $attempt_id
        ];
    }

    /**
     * Submit exam answers
     */
    public function submitExam($attempt_id, $answers)
    {
        try {
            // Save all answers
            foreach ($answers as $question_id => $answer) {
                $this->answerModel->saveAnswer($attempt_id, $question_id, $answer);
            }

            // Submit attempt
            if (!$this->attemptModel->submitAttempt($attempt_id)) {
                return [
                    'success' => false,
                    'message' => 'Failed to submit exam.'
                ];
            }

            // Auto-grade the exam
            $gradingResult = $this->gradeExam($attempt_id);

            return [
                'success' => true,
                'message' => 'Exam submitted successfully!',
                'score' => $gradingResult['score'],
                'total_points' => $gradingResult['total_points']
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error submitting exam: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Grade exam automatically
     */
    public function gradeExam($attempt_id)
    {
        $answers = $this->answerModel->getAnswersByAttempt($attempt_id);
        $total_score = 0;
        $total_points = 0;

        foreach ($answers as $answer) {
            $question = $this->questionModel->getQuestionById($answer['question_id']);
            if (!$question) continue;

            $total_points += $question['points'];
            $points_earned = 0;
            $is_correct = false;

            switch ($question['question_type']) {
                case 'multiple_choice':
                case 'true_false':
                    if (strtolower(trim($answer['student_answer'])) === strtolower(trim($question['correct_answer']))) {
                        $points_earned = $question['points'];
                        $is_correct = true;
                    }
                    break;

                case 'essay':
                    // Use AI essay checker
                    $essayScore = $this->gradeEssay($answer['student_answer'], $question['correct_answer']);
                    $points_earned = ($essayScore / 100) * $question['points'];
                    $is_correct = $essayScore >= 60; // Consider 60% as passing
                    break;
            }

            // Update answer with grading
            $this->answerModel->gradeAnswer($attempt_id, $answer['question_id'], $is_correct, $points_earned);
            $total_score += $points_earned;
        }

        // Update attempt with final score
        $this->attemptModel->gradeAttempt($attempt_id, $total_score, $total_points);

        return [
            'score' => $total_score,
            'total_points' => $total_points,
            'percentage' => $total_points > 0 ? round(($total_score / $total_points) * 100, 2) : 0
        ];
    }

    /**
     * AI Essay Checker (Mock implementation)
     */
    private function gradeEssay($studentAnswer, $referenceAnswer)
    {
        // This is a mock implementation
        // In a real system, you would integrate with an AI service like OpenAI, GPT, etc.
        
        if (empty($studentAnswer)) {
            return 0;
        }

        // Simple scoring based on length and keyword matching
        $score = 0;
        
        // Length factor (0-30 points)
        $length = strlen($studentAnswer);
        if ($length >= 100) {
            $score += 30;
        } elseif ($length >= 50) {
            $score += 20;
        } elseif ($length >= 25) {
            $score += 10;
        }

        // Keyword matching (0-40 points)
        $keywords = explode(' ', strtolower($referenceAnswer));
        $studentWords = explode(' ', strtolower($studentAnswer));
        $matchedKeywords = 0;
        
        foreach ($keywords as $keyword) {
            if (strlen($keyword) > 3 && in_array($keyword, $studentWords)) {
                $matchedKeywords++;
            }
        }
        
        if (count($keywords) > 0) {
            $score += ($matchedKeywords / count($keywords)) * 40;
        }

        // Grammar and structure (0-30 points)
        $score += 20; // Mock score for basic structure

        return min(100, max(0, round($score)));
    }

    /**
     * Get exam results for faculty
     */
    public function getExamResults($exam_id)
    {
        $attempts = $this->attemptModel->getAttemptsByExam($exam_id);
        $statistics = $this->attemptModel->getExamStatistics($exam_id);

        return [
            'attempts' => $attempts,
            'statistics' => $statistics
        ];
    }

    /**
     * Get student's exam results
     */
    public function getStudentResults($student_id)
    {
        return $this->attemptModel->getAttemptsByStudent($student_id);
    }

    /**
     * Get exam with questions for taking
     */
    public function getExamForStudent($exam_id)
    {
        $exam = $this->examModel->getExamById($exam_id);
        if (!$exam) {
            return null;
        }

        $questions = $this->questionModel->getQuestionsByExam($exam_id);
        $exam['questions'] = $questions;

        return $exam;
    }
}