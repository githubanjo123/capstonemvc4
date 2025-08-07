<?php

namespace App\DAO;

use App\Config\Database;
use PDO;
use PDOException;

class ExamDAO
{
    private $db;
    private $table = 'exams';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all exams
     */
    public function getAllExams()
    {
        try {
            $sql = "SELECT e.*, s.course_code, s.descriptive_title, u.full_name as created_by_name 
                    FROM {$this->table} e 
                    JOIN subjects s ON e.subject_id = s.subject_id 
                    JOIN users u ON e.created_by = u.user_id 
                    ORDER BY e.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get exams by faculty
     */
    public function getExamsByFaculty($faculty_id)
    {
        try {
            $sql = "SELECT e.*, s.course_code, s.descriptive_title 
                    FROM {$this->table} e 
                    JOIN subjects s ON e.subject_id = s.subject_id 
                    WHERE e.created_by = ? 
                    ORDER BY e.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$faculty_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get exams for students by year and section
     */
    public function getExamsForStudents($year_level, $section)
    {
        try {
            $sql = "SELECT e.*, s.course_code, s.descriptive_title, u.full_name as created_by_name 
                    FROM {$this->table} e 
                    JOIN subjects s ON e.subject_id = s.subject_id 
                    JOIN users u ON e.created_by = u.user_id 
                    WHERE e.year_level = ? AND e.section = ? AND e.status = 'active' 
                    ORDER BY e.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$year_level, $section]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get exam by ID
     */
    public function getExamById($exam_id)
    {
        try {
            $sql = "SELECT e.*, s.course_code, s.descriptive_title, u.full_name as created_by_name 
                    FROM {$this->table} e 
                    JOIN subjects s ON e.subject_id = s.subject_id 
                    JOIN users u ON e.created_by = u.user_id 
                    WHERE e.exam_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$exam_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create new exam
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO {$this->table} (title, instructions, subject_id, year_level, section, created_by, time_limit, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['title'],
                $data['instructions'],
                $data['subject_id'],
                $data['year_level'],
                $data['section'],
                $data['created_by'],
                $data['time_limit'] ?? 60,
                $data['status'] ?? 'active'
            ]);

            return $result ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update exam
     */
    public function update($exam_id, $data)
    {
        try {
            $sql = "UPDATE {$this->table} SET 
                    title = ?, instructions = ?, subject_id = ?, year_level = ?, 
                    section = ?, time_limit = ?, status = ?, updated_at = NOW() 
                    WHERE exam_id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['title'],
                $data['instructions'],
                $data['subject_id'],
                $data['year_level'],
                $data['section'],
                $data['time_limit'],
                $data['status'],
                $exam_id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete exam
     */
    public function delete($exam_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE exam_id = ?");
            return $stmt->execute([$exam_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get exam statistics
     */
    public function getExamStats($exam_id)
    {
        try {
            $sql = "SELECT 
                        COUNT(DISTINCT ea.attempt_id) as total_attempts,
                        COUNT(DISTINCT ea.student_id) as unique_students,
                        AVG(ea.score) as average_score,
                        MAX(ea.score) as highest_score,
                        MIN(ea.score) as lowest_score
                    FROM exam_attempts ea 
                    WHERE ea.exam_id = ? AND ea.status = 'graded'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$exam_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
}