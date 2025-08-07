<?php

namespace App\DAO;

use App\Config\Database;
use PDO;
use PDOException;

class QuestionDAO
{
    private $db;
    private $table = 'questions';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get questions by exam ID
     */
    public function getQuestionsByExam($exam_id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE exam_id = ? ORDER BY question_order ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$exam_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get question by ID
     */
    public function getQuestionById($question_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE question_id = ?");
            $stmt->execute([$question_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create new question
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO {$this->table} (exam_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer, points, question_order) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['exam_id'],
                $data['question_text'],
                $data['question_type'],
                $data['option_a'] ?? null,
                $data['option_b'] ?? null,
                $data['option_c'] ?? null,
                $data['option_d'] ?? null,
                $data['correct_answer'],
                $data['points'] ?? 1,
                $data['question_order'] ?? 1
            ]);

            return $result ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update question
     */
    public function update($question_id, $data)
    {
        try {
            $sql = "UPDATE {$this->table} SET 
                    question_text = ?, question_type = ?, option_a = ?, option_b = ?, 
                    option_c = ?, option_d = ?, correct_answer = ?, points = ?, question_order = ? 
                    WHERE question_id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['question_text'],
                $data['question_type'],
                $data['option_a'] ?? null,
                $data['option_b'] ?? null,
                $data['option_c'] ?? null,
                $data['option_d'] ?? null,
                $data['correct_answer'],
                $data['points'],
                $data['question_order'],
                $question_id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete question
     */
    public function delete($question_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE question_id = ?");
            return $stmt->execute([$question_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get questions count by exam
     */
    public function getQuestionsCountByExam($exam_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE exam_id = ?");
            $stmt->execute([$exam_id]);
            $result = $stmt->fetch();
            return $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Get total points by exam
     */
    public function getTotalPointsByExam($exam_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT SUM(points) as total FROM {$this->table} WHERE exam_id = ?");
            $stmt->execute([$exam_id]);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Get questions by type for an exam
     */
    public function getQuestionsByType($exam_id, $question_type)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE exam_id = ? AND question_type = ? ORDER BY question_order ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$exam_id, $question_type]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}