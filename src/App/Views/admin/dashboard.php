<?php $layout = 'app'; $pageTitle = 'Admin Dashboard'; ?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Users</h5>
                        <h2 class="text-primary"><?= $stats['total_users'] ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
                <a href="/admin/users" class="btn btn-outline-primary btn-sm">Manage Users</a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Subjects</h5>
                        <h2 class="text-success"><?= $stats['total_subjects'] ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-book fa-2x text-success"></i>
                    </div>
                </div>
                <a href="/admin/subjects" class="btn btn-outline-success btn-sm">Manage Subjects</a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Exams</h5>
                        <h2 class="text-info"><?= $stats['total_exams'] ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-2x text-info"></i>
                    </div>
                </div>
                <a href="/admin/results" class="btn btn-outline-info btn-sm">View Results</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/admin/users/add" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Add New User
                    </a>
                    <a href="/admin/subjects/add" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Add New Subject
                    </a>
                    <a href="/admin/reports" class="btn btn-info">
                        <i class="fas fa-chart-bar me-2"></i>Generate Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <p class="mb-1"><strong>Role:</strong></p>
                        <p class="mb-1"><strong>Name:</strong></p>
                        <p class="mb-1"><strong>School ID:</strong></p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1 text-capitalize"><?= htmlspecialchars($user['role']) ?></p>
                        <p class="mb-1"><?= htmlspecialchars($user['full_name']) ?></p>
                        <p class="mb-1"><?= htmlspecialchars($user['school_id']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>