<?php $layout = 'app'; $pageTitle = 'Faculty Dashboard'; ?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">My Exams</h5>
                        <h2 class="text-primary"><?= count($exams) ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-2x text-primary"></i>
                    </div>
                </div>
                <a href="/faculty/exams" class="btn btn-outline-primary btn-sm">View All Exams</a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Assigned Subjects</h5>
                        <h2 class="text-success"><?= count($subjects) ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-book fa-2x text-success"></i>
                    </div>
                </div>
                <span class="btn btn-outline-success btn-sm">View Subjects</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Active Exams</h5>
                        <h2 class="text-info"><?= count(array_filter($exams, function($exam) { return $exam['status'] === 'active'; })) ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-play-circle fa-2x text-info"></i>
                    </div>
                </div>
                <a href="/faculty/exams/create" class="btn btn-outline-info btn-sm">Create New Exam</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Exams</h5>
                <a href="/faculty/exams" class="btn btn-primary btn-sm">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($exams)): ?>
                    <p class="text-muted">No exams created yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($exams, 0, 5) as $exam): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($exam['title']) ?></td>
                                        <td><?= htmlspecialchars($exam['descriptive_title']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $exam['status'] === 'active' ? 'success' : ($exam['status'] === 'inactive' ? 'warning' : 'secondary') ?>">
                                                <?= ucfirst($exam['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($exam['created_at'])) ?></td>
                                        <td>
                                            <a href="/faculty/exams/edit/<?= $exam['exam_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/faculty/exams/<?= $exam['exam_id'] ?>/results" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/faculty/exams/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create New Exam
                    </a>
                    <a href="/faculty/exams" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>Manage Exams
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Faculty Information</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
                <p class="mb-1"><strong>School ID:</strong> <?= htmlspecialchars($user['school_id']) ?></p>
                <p class="mb-0"><strong>Role:</strong> <?= ucfirst($user['role']) ?></p>
            </div>
        </div>
    </div>
</div>