<?php

namespace App\DAO;

use App\Config\Database;
use PDO;
use PDOException;

class StudentAnswerDAO
{
    private $db;
    private $table = 'student_answers';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Save student answer
     */
    public function saveAnswer($attempt_id, $question_id, $student_answer)
    {
        try {
            // Check if answer already exists
            $existing = $this->getAnswer($attempt_id, $question_id);
            if ($existing) {
                // Update existing answer
                $sql = "UPDATE {$this->table} SET student_answer = ?, answered_at = NOW() WHERE attempt_id = ? AND question_id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$student_answer, $attempt_id, $question_id]);
            } else {
                // Insert new answer
                $sql = "INSERT INTO {$this->table} (attempt_id, question_id, student_answer) VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$attempt_id, $question_id, $student_answer]);
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get answer by attempt and question
     */
    public function getAnswer($attempt_id, $question_id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE attempt_id = ? AND question_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$attempt_id, $question_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get all answers for an attempt
     */
    public function getAnswersByAttempt($attempt_id)
    {
        try {
            $sql = "SELECT sa.*, q.question_text, q.question_type, q.correct_answer, q.points 
                    FROM {$this->table} sa 
                    JOIN questions q ON sa.question_id = q.question_id 
                    WHERE sa.attempt_id = ? 
                    ORDER BY q.question_order ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$attempt_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Grade answer
     */
    public function gradeAnswer($attempt_id, $question_id, $is_correct, $points_earned)
    {
        try {
            $sql = "UPDATE {$this->table} SET is_correct = ?, points_earned = ? WHERE attempt_id = ? AND question_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$is_correct, $points_earned, $attempt_id, $question_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get answer statistics for a question
     */
    public function getQuestionStatistics($question_id)
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_answers,
                        COUNT(CASE WHEN is_correct = 1 THEN 1 END) as correct_answers,
                        AVG(points_earned) as average_points
                    FROM {$this->table} 
                    WHERE question_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$question_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get total score for an attempt
     */
    public function getAttemptScore($attempt_id)
    {
        try {
            $sql = "SELECT SUM(points_earned) as total_score FROM {$this->table} WHERE attempt_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$attempt_id]);
            $result = $stmt->fetch();
            return $result['total_score'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Delete all answers for an attempt
     */
    public function deleteAnswersByAttempt($attempt_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE attempt_id = ?");
            return $stmt->execute([$attempt_id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}