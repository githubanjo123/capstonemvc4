<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Examination System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --info-color: #36b9cc;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
        }

        .welcome-text {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .logout-btn {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: white;
            color: var(--primary-color);
        }

        .nav-tabs {
            border-bottom: 2px solid #e3e6f0;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--secondary-color);
            font-weight: 600;
            padding: 1rem 1.5rem;
            margin-right: 0.5rem;
            border-radius: 0.5rem 0.5rem 0 0;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background-color: white;
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
        }

        .nav-tabs .nav-link:hover {
            background-color: #f8f9fc;
            color: var(--primary-color);
        }

        .tab-content {
            background: white;
            border-radius: 0 0 0.5rem 0.5rem;
            padding: 2rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .action-buttons {
            margin-bottom: 2rem;
        }

        .btn-add-student {
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-student:hover {
            background: #224abe;
            color: white;
            transform: translateY(-2px);
        }

        .btn-add-faculty {
            background: transparent;
            border: 2px solid var(--secondary-color);
            color: var(--secondary-color);
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-faculty:hover {
            background: var(--secondary-color);
            color: white;
        }

        .year-section-tabs {
            margin-bottom: 2rem;
        }

        .year-section-tab {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            color: var(--secondary-color);
            padding: 0.5rem 1rem;
            margin-right: 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .year-section-tab.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .year-section-tab:hover {
            background: var(--primary-color);
            color: white;
        }

        .student-count {
            background: var(--success-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            margin-left: 0.5rem;
        }

        .student-card {
            background: white;
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .student-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .student-name {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .student-email {
            color: var(--secondary-color);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .student-date {
            color: var(--secondary-color);
            font-size: 0.8rem;
        }

        .btn-edit {
            background: var(--warning-color);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .btn-edit:hover {
            background: #e0a800;
            color: white;
        }

        .btn-delete {
            background: var(--danger-color);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .btn-delete:hover {
            background: #be2617;
            color: white;
        }

        .section-header {
            background: #f8f9fc;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
        }

        .section-title {
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }

        .section-count {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-1">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Admin Dashboard
                    </h1>
                    <p class="welcome-text mb-0">
                        Welcome back, <?= htmlspecialchars($admin['name'] ?? 'Admin') ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/admin/logout" class="btn logout-btn">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                    <i class="fas fa-users me-2"></i>
                    Manage Users
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab">
                    <i class="fas fa-book me-2"></i>
                    Manage Subjects
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="assignments-tab" data-bs-toggle="tab" data-bs-target="#assignments" type="button" role="tab">
                    <i class="fas fa-link me-2"></i>
                    Subject Assignments
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">
                    <i class="fas fa-chart-bar me-2"></i>
                    Reports
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="adminTabContent">
            <!-- Tab 1: Manage Users -->
            <div class="tab-pane fade show active" id="users" role="tabpanel">
                <?php include 'manage-users.php'; ?>
            </div>

            <!-- Tab 2: Manage Subjects -->
            <div class="tab-pane fade" id="subjects" role="tabpanel">
                <div class="text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h4>Manage Subjects</h4>
                    <p class="text-muted">Subject management functionality coming soon...</p>
                </div>
            </div>

            <!-- Tab 3: Subject Assignments -->
            <div class="tab-pane fade" id="assignments" role="tabpanel">
                <div class="text-center py-5">
                    <i class="fas fa-link fa-3x text-muted mb-3"></i>
                    <h4>Subject Assignments</h4>
                    <p class="text-muted">Assignment functionality coming soon...</p>
                </div>
            </div>

            <!-- Tab 4: Reports -->
            <div class="tab-pane fade" id="reports" role="tabpanel">
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <h4>Reports & Analytics</h4>
                    <p class="text-muted">Reporting functionality coming soon...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Year-Section Tab Switching
        document.addEventListener('DOMContentLoaded', function() {
            const yearSectionTabs = document.querySelectorAll('.year-section-tab');
            const studentSections = document.querySelectorAll('.student-section');

            yearSectionTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetSection = this.getAttribute('data-section');
                    
                    // Update active tab
                    yearSectionTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show/hide sections
                    studentSections.forEach(section => {
                        if (section.id === targetSection) {
                            section.style.display = 'block';
                        } else {
                            section.style.display = 'none';
                        }
                    });
                });
            });

            // Show first section by default
            if (yearSectionTabs.length > 0) {
                yearSectionTabs[0].click();
            }
        });
    </script>
</body>
</html>