<?php

// Test script to verify login functionality with interface-based structure
require_once 'vendor/autoload.php';

echo "🧪 Testing Login Functionality...\n\n";

try {
    // Test interfaces
    echo "1. Testing Interfaces:\n";
    
    $userDAOInterface = new ReflectionClass(\App\Interfaces\UserDAOInterface::class);
    echo "✅ UserDAOInterface loaded successfully\n";
    
    $authServiceInterface = new ReflectionClass(\App\Interfaces\AuthServiceInterface::class);
    echo "✅ AuthServiceInterface loaded successfully\n\n";
    
    // Test implementations
    echo "2. Testing Implementations:\n";
    
    $userDAOImpl = new \App\DAO\Impl\UserDAOImpl();
    echo "✅ UserDAOImpl loaded successfully\n";
    
    $authServiceImpl = new \App\Services\Impl\AuthServiceImpl($userDAOImpl);
    echo "✅ AuthServiceImpl loaded successfully\n\n";
    
    // Test container
    echo "3. Testing Dependency Injection Container:\n";
    
    $container = \App\Core\Container::getInstance();
    echo "✅ Container loaded successfully\n";
    
    $authService = $container->get(\App\Interfaces\AuthServiceInterface::class);
    echo "✅ AuthService resolved from container successfully\n\n";
    
    // Test controller
    echo "4. Testing Controller:\n";
    
    $authController = new \App\Controllers\Auth\AuthController($authService);
    echo "✅ AuthController loaded successfully\n\n";
    
    echo "🎉 All login components loaded successfully!\n";
    echo "📁 New Interface-Based Structure:\n";
    echo "   - Interfaces in src/App/Interfaces/\n";
    echo "   - Implementations in src/App/DAO/Impl/ and src/App/Services/Impl/\n";
    echo "   - Controllers in src/App/Controllers/Auth/\n";
    echo "   - Dependency injection via Container\n";
    echo "   - Focused only on login functionality\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}