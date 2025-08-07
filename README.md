# Examination System with AI Essay Checker

A comprehensive web-based examination system built with PHP using MVC architecture. The system supports three user roles (Admin, Faculty, Student) and includes automated scoring with AI essay checking capabilities.

## 🚀 Features

### Admin Features
- **User Management**: Add, edit, and delete students and faculty
- **Subject Management**: Create and manage subjects, assign faculty to subjects
- **Result Viewing**: View all exam results across the system
- **Report Generation**: Generate comprehensive reports

### Faculty Features
- **Exam Creation**: Create exams with multiple question types
- **Question Management**: Add, edit, and delete questions (Multiple Choice, True/False, Essay)
- **Time Limit Management**: Set and modify exam time limits
- **Result Viewing**: View detailed results for their exams

### Student Features
- **Exam Taking**: Take exams with real-time timer
- **Multiple Question Types**: Support for Multiple Choice, True/False, and Essay questions
- **Auto-Scoring**: Automatic scoring for MCQ and T/F questions
- **AI Essay Checking**: Automated essay grading with AI analysis
- **Result Viewing**: View personal exam results and scores

## 🛠 Technical Features

- **MVC Architecture**: Clean separation of concerns
- **Role-Based Access Control**: Secure access based on user roles
- **Session Management**: Secure session handling
- **Database Integration**: MySQL database with proper relationships
- **Responsive Design**: Bootstrap-based responsive UI
- **Real-time Timer**: JavaScript-based exam timer
- **AI Essay Grading**: Mock AI implementation for essay scoring

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for autoloading)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd examination-system
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Database Setup
1. Create a MySQL database named `capstone2`
2. Import the database schema:
```bash
mysql -u root -p capstone2 < capstone2.sql
```

### 4. Configuration
1. Update database credentials in `src/App/Config/Database.php`:
```php
private $host = '127.0.0.1';
private $database = 'capstone2';
private $username = 'your_username';
private $password = 'your_password';
```

### 5. Web Server Configuration
Configure your web server to point to the `public/` directory as the document root.

#### Apache (.htaccess already included)
The `.htaccess` file in the `public/` directory handles URL rewriting.

#### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/examination-system/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 👥 Default Users

The system comes with pre-configured users for testing:

### Admin
- **School ID**: ADMIN001
- **Password**: password123
- **Role**: Admin

### Faculty
- **School ID**: FAC001
- **Password**: password123
- **Role**: Faculty

### Students
- **School ID**: 2020-001, 2020-002, 2021-001, 2021-002
- **Password**: password123
- **Role**: Student

## 🏗 System Architecture

### Directory Structure
```
examination-system/
├── public/                 # Web root directory
│   ├── index.php          # Main entry point
│   └── .htaccess          # URL rewriting
├── src/                   # Application source code
│   └── App/
│       ├── Config/        # Configuration files
│       ├── Controllers/   # Controller classes
│       ├── Core/          # Core framework classes
│       ├── Models/        # Data models
│       ├── Services/      # Business logic services
│       └── Views/         # View templates
├── vendor/                # Composer dependencies
├── tests/                 # Unit tests
├── capstone2.sql          # Database schema
└── composer.json          # Composer configuration
```

### MVC Implementation

#### Models (Data Access Layer)
- `User.php` - User management
- `Exam.php` - Exam management
- `Question.php` - Question management
- `Subject.php` - Subject management
- `ExamAttempt.php` - Exam attempt tracking
- `StudentAnswer.php` - Student answer management

#### Services (Business Logic Layer)
- `AuthService.php` - Authentication and authorization
- `ExamService.php` - Exam creation, grading, and AI essay checking

#### Controllers (Request Handling)
- `AuthController.php` - Login/logout handling
- `AdminController.php` - Admin-specific actions
- `FacultyController.php` - Faculty-specific actions
- `StudentController.php` - Student-specific actions

#### Views (Presentation Layer)
- Layout templates with Bootstrap styling
- Role-specific dashboards
- Exam interfaces with real-time timer

## 🔐 Security Features

- **Session-based Authentication**: Secure session management
- **Role-based Access Control**: Proper authorization checks
- **Input Validation**: Sanitized user inputs
- **SQL Injection Prevention**: Prepared statements
- **CSRF Protection**: Form token validation (recommended enhancement)

## 🤖 AI Essay Checker

The system includes a mock AI essay checker that evaluates essays based on:
- **Length Analysis**: Evaluates response length
- **Keyword Matching**: Checks for relevant keywords
- **Structure Scoring**: Basic grammar and structure assessment

### Implementation Details
```php
private function gradeEssay($studentAnswer, $referenceAnswer)
{
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
    // ... keyword matching logic

    // Grammar and structure (0-30 points)
    $score += 20; // Mock score

    return min(100, max(0, round($score)));
}
```

## 📊 Database Schema

The system uses a well-structured MySQL database with the following main tables:

- `users` - User accounts and roles
- `subjects` - Course subjects
- `subject_assignments` - Faculty-subject assignments
- `exams` - Exam definitions
- `questions` - Exam questions with multiple types
- `exam_attempts` - Student exam attempts
- `student_answers` - Individual student answers

## 🧪 Testing

Run the database test to verify connectivity:
```bash
php test_db.php
```

## 🚀 Usage

1. **Access the System**: Navigate to your web server URL
2. **Login**: Use the default credentials provided above
3. **Role-based Access**: Each role will see their specific dashboard
4. **Create Exams** (Faculty): Create exams with various question types
5. **Take Exams** (Students): Take exams with real-time timer
6. **View Results**: Check scores and performance analytics

## 🔧 Customization

### Adding New Question Types
1. Update the `questions` table schema
2. Modify `Question.php` model
3. Update exam creation forms
4. Extend the grading logic in `ExamService.php`

### Integrating Real AI Services
Replace the mock AI implementation in `ExamService.php` with actual AI service calls:
```php
// Example OpenAI integration
private function gradeEssayWithAI($studentAnswer, $referenceAnswer)
{
    $client = new OpenAI\Client('your-api-key');
    $response = $client->chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'Grade this essay...'],
            ['role' => 'user', 'content' => $studentAnswer]
        ]
    ]);
    return $response->choices[0]->message->content;
}
```

## 📝 License

This project is licensed under the MIT License.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📞 Support

For support and questions, please open an issue in the repository.

---

**Note**: This is a comprehensive examination system with AI essay checking capabilities. The AI implementation is currently a mock version - for production use, integrate with actual AI services like OpenAI, GPT, or similar APIs.