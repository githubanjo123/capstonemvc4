<?php

// Test script to verify DAO structure
require_once 'vendor/autoload.php';

echo "🧪 Testing DAO Structure...\n\n";

try {
    // Test DAO classes
    echo "1. Testing DAO Classes:\n";
    
    $userDAO = new \App\DAO\UserDAO();
    echo "✅ UserDAO loaded successfully\n";
    
    $examDAO = new \App\DAO\ExamDAO();
    echo "✅ ExamDAO loaded successfully\n";
    
    $questionDAO = new \App\DAO\QuestionDAO();
    echo "✅ QuestionDAO loaded successfully\n";
    
    $subjectDAO = new \App\DAO\SubjectDAO();
    echo "✅ SubjectDAO loaded successfully\n";
    
    $examAttemptDAO = new \App\DAO\ExamAttemptDAO();
    echo "✅ ExamAttemptDAO loaded successfully\n";
    
    $studentAnswerDAO = new \App\DAO\StudentAnswerDAO();
    echo "✅ StudentAnswerDAO loaded successfully\n\n";
    
    // Test Service classes
    echo "2. Testing Service Classes:\n";
    
    $authService = new \App\Services\AuthService();
    echo "✅ AuthService loaded successfully\n";
    
    $examService = new \App\Services\ExamService();
    echo "✅ ExamService loaded successfully\n\n";
    
    // Test Controller classes
    echo "3. Testing Controller Classes:\n";
    
    $authController = new \App\Controllers\Auth\AuthController();
    echo "✅ AuthController loaded successfully\n";
    
    $adminController = new \App\Controllers\Admin\AdminController();
    echo "✅ AdminController loaded successfully\n";
    
    $facultyController = new \App\Controllers\Faculty\FacultyController();
    echo "✅ FacultyController loaded successfully\n";
    
    $studentController = new \App\Controllers\Student\StudentController();
    echo "✅ StudentController loaded successfully\n\n";
    
    // Test Core classes
    echo "4. Testing Core Classes:\n";
    
    $router = new \App\Core\Router();
    echo "✅ Router loaded successfully\n";
    
    $view = new \App\Core\View();
    echo "✅ View loaded successfully\n\n";
    
    echo "🎉 All classes loaded successfully! DAO structure is working correctly.\n";
    echo "📁 New organized structure:\n";
    echo "   - DAO classes in src/App/DAO/\n";
    echo "   - Controllers organized in src/App/Controllers/[Auth|Admin|Faculty|Student]/\n";
    echo "   - Services in src/App/Services/\n";
    echo "   - Core framework in src/App/Core/\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}