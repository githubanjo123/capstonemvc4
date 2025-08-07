<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Examination System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: #666;
            font-size: 1rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <h1>Examination System</h1>
                        <p>Please sign in to continue</p>
                    </div>

                    <div id="alert-container"></div>

                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="school_id" class="form-label">School ID</label>
                            <input type="text" class="form-control" id="school_id" name="school_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-login">Sign In</button>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Demo Accounts:<br>
                            Admin: admin123 / password123<br>
                            Faculty: faculty123 / password123<br>
                            Student: student123 / password123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Get the current path and construct the API URL
            const currentPath = window.location.pathname;
            // Remove '/login' from the path to get the base
            const basePath = currentPath.replace('/login', '');
            const apiUrl = basePath + '/api/auth/login';
            
            // Debug: Log the paths
            console.log('Current path:', currentPath);
            console.log('Base path:', basePath);
            console.log('API URL:', apiUrl);
            
            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const alertContainer = document.getElementById('alert-container');
                
                if (data.status === 'success') {
                    // Show success message
                    alertContainer.innerHTML = `
                        <div class="alert alert-success">
                            ${data.message}
                        </div>
                    `;
                    
                    // Redirect based on role
                    setTimeout(() => {
                        switch(data.role) {
                            case 'admin':
                                window.location.href = basePath + '/admin-success';
                                break;
                            case 'faculty':
                                window.location.href = basePath + '/faculty-success';
                                break;
                            case 'student':
                                window.location.href = basePath + '/student-success';
                                break;
                            default:
                                window.location.href = basePath + '/';
                        }
                    }, 1000);
                } else {
                    // Show error message
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger">
                            ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('alert-container').innerHTML = `
                    <div class="alert alert-danger">
                        An error occurred. Please try again.
                    </div>
                `;
            });
        });
    </script>
</body>
</html>