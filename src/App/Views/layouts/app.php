<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Examination System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin: 0.25rem 0;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .card-header {
            background-color: white;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">Examination System</h4>
                        <small class="text-white-50"><?= $user['role'] ?? 'User' ?></small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <?php if (isset($user) && $user['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/dashboard">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/users">
                                    <i class="fas fa-users me-2"></i>Manage Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/subjects">
                                    <i class="fas fa-book me-2"></i>Manage Subjects
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/results">
                                    <i class="fas fa-chart-bar me-2"></i>View Results
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/reports">
                                    <i class="fas fa-file-alt me-2"></i>Generate Reports
                                </a>
                            </li>
                        <?php elseif (isset($user) && $user['role'] === 'faculty'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/faculty/dashboard">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/faculty/exams">
                                    <i class="fas fa-file-alt me-2"></i>My Exams
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/faculty/exams/create">
                                    <i class="fas fa-plus me-2"></i>Create Exam
                                </a>
                            </li>
                        <?php elseif (isset($user) && $user['role'] === 'student'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/student/dashboard">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/student/results">
                                    <i class="fas fa-chart-line me-2"></i>My Results
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item mt-3">
                            <a class="nav-link text-danger" href="/api/auth/logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= $pageTitle ?? 'Dashboard' ?></h1>
                    <?php if (isset($user)): ?>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <div class="btn-group me-2">
                                <span class="btn btn-outline-secondary">
                                    <i class="fas fa-user me-1"></i><?= htmlspecialchars($user['full_name']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Flash messages -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_GET['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Page content -->
                <?= $content ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>