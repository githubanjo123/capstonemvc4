<?php

namespace App\DAO;

use App\Config\Database;
use PDO;
use PDOException;

class SubjectDAO
{
    private $db;
    private $table = 'subjects';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all subjects
     */
    public function getAllSubjects()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY course_code ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get subject by ID
     */
    public function getSubjectById($subject_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE subject_id = ?");
            $stmt->execute([$subject_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create new subject
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO {$this->table} (course_code, descriptive_title) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['course_code'],
                $data['descriptive_title']
            ]);

            return $result ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update subject
     */
    public function update($subject_id, $data)
    {
        try {
            $sql = "UPDATE {$this->table} SET course_code = ?, descriptive_title = ? WHERE subject_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['course_code'],
                $data['descriptive_title'],
                $subject_id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete subject
     */
    public function delete($subject_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE subject_id = ?");
            return $stmt->execute([$subject_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get subjects assigned to faculty
     */
    public function getSubjectsByFaculty($faculty_id)
    {
        try {
            $sql = "SELECT s.*, sa.year_level, sa.section 
                    FROM {$this->table} s 
                    JOIN subject_assignments sa ON s.subject_id = sa.subject_id 
                    WHERE sa.faculty_id = ? 
                    ORDER BY s.course_code ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$faculty_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Assign subject to faculty
     */
    public function assignToFaculty($faculty_id, $subject_id, $year_level, $section)
    {
        try {
            $sql = "INSERT INTO subject_assignments (faculty_id, subject_id, year_level, section) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$faculty_id, $subject_id, $year_level, $section]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Remove subject assignment from faculty
     */
    public function removeFacultyAssignment($faculty_id, $subject_id, $year_level, $section)
    {
        try {
            $sql = "DELETE FROM subject_assignments WHERE faculty_id = ? AND subject_id = ? AND year_level = ? AND section = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$faculty_id, $subject_id, $year_level, $section]);
        } catch (PDOException $e) {
            return false;
        }
    }
}