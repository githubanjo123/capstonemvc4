<?php

namespace App\DAO;

use App\Config\Database;
use PDO;
use PDOException;

class ExamAttemptDAO
{
    private $db;
    private $table = 'exam_attempts';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Start exam attempt
     */
    public function startAttempt($exam_id, $student_id)
    {
        try {
            // Check if student already has an attempt for this exam
            $existing = $this->getAttemptByStudent($exam_id, $student_id);
            if ($existing) {
                return $existing['attempt_id'];
            }

            $sql = "INSERT INTO {$this->table} (exam_id, student_id, status) VALUES (?, ?, 'in_progress')";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$exam_id, $student_id]);

            return $result ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Submit exam attempt
     */
    public function submitAttempt($attempt_id)
    {
        try {
            $sql = "UPDATE {$this->table} SET submitted_at = NOW(), status = 'submitted' WHERE attempt_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$attempt_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Grade exam attempt
     */
    public function gradeAttempt($attempt_id, $score, $total_points)
    {
        try {
            $sql = "UPDATE {$this->table} SET score = ?, total_points = ?, status = 'graded' WHERE attempt_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$score, $total_points, $attempt_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get attempt by ID
     */
    public function getAttemptById($attempt_id)
    {
        try {
            $sql = "SELECT ea.*, e.title as exam_title, e.time_limit, u.full_name as student_name, u.school_id 
                    FROM {$this->table} ea 
                    JOIN exams e ON ea.exam_id = e.exam_id 
                    JOIN users u ON ea.student_id = u.user_id 
                    WHERE ea.attempt_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$attempt_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get attempt by student and exam
     */
    public function getAttemptByStudent($exam_id, $student_id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE exam_id = ? AND student_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$exam_id, $student_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get all attempts for an exam
     */
    public function getAttemptsByExam($exam_id)
    {
        try {
            $sql = "SELECT ea.*, u.full_name as student_name, u.school_id 
                    FROM {$this->table} ea 
                    JOIN users u ON ea.student_id = u.user_id 
                    WHERE ea.exam_id = ? 
                    ORDER BY ea.started_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$exam_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get attempts by student
     */
    public function getAttemptsByStudent($student_id)
    {
        try {
            $sql = "SELECT ea.*, e.title as exam_title, e.time_limit 
                    FROM {$this->table} ea 
                    JOIN exams e ON ea.exam_id = e.exam_id 
                    WHERE ea.student_id = ? 
                    ORDER BY ea.started_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$student_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Check if student can take exam
     */
    public function canTakeExam($exam_id, $student_id)
    {
        try {
            $attempt = $this->getAttemptByStudent($exam_id, $student_id);
            if (!$attempt) {
                return true; // No previous attempt
            }
            
            // Allow retake if previous attempt was not submitted
            return $attempt['status'] === 'in_progress';
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get exam statistics
     */
    public function getExamStatistics($exam_id)
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_attempts,
                        COUNT(CASE WHEN status = 'graded' THEN 1 END) as graded_attempts,
                        COUNT(CASE WHEN status = 'submitted' THEN 1 END) as submitted_attempts,
                        COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress_attempts,
                        AVG(CASE WHEN status = 'graded' THEN score END) as average_score,
                        MAX(CASE WHEN status = 'graded' THEN score END) as highest_score,
                        MIN(CASE WHEN status = 'graded' THEN score END) as lowest_score
                    FROM {$this->table} 
                    WHERE exam_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$exam_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
}