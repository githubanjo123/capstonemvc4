<?php $layout = 'app'; $pageTitle = 'Student Dashboard'; ?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Available Exams</h5>
                        <h2 class="text-primary"><?= count($exams) ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-2x text-primary"></i>
                    </div>
                </div>
                <span class="btn btn-outline-primary btn-sm">View All Exams</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Year Level</h5>
                        <h2 class="text-success"><?= $user['year_level'] ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-graduation-cap fa-2x text-success"></i>
                    </div>
                </div>
                <span class="btn btn-outline-success btn-sm">Section <?= $user['section'] ?></span>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Completed Exams</h5>
                        <h2 class="text-info">0</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x text-info"></i>
                    </div>
                </div>
                <a href="/student/results" class="btn btn-outline-info btn-sm">View Results</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Available Exams</h5>
            </div>
            <div class="card-body">
                <?php if (empty($exams)): ?>
                    <p class="text-muted">No exams available for your year level and section.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Subject</th>
                                    <th>Duration</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($exams as $exam): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($exam['title']) ?></td>
                                        <td><?= htmlspecialchars($exam['descriptive_title']) ?></td>
                                        <td><?= $exam['time_limit'] ?> minutes</td>
                                        <td><?= htmlspecialchars($exam['created_by_name']) ?></td>
                                        <td>
                                            <a href="/student/exam/<?= $exam['exam_id'] ?>/take" class="btn btn-sm btn-primary">
                                                <i class="fas fa-play me-1"></i>Take Exam
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
                <h5 class="card-title mb-0">Student Information</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
                <p class="mb-1"><strong>School ID:</strong> <?= htmlspecialchars($user['school_id']) ?></p>
                <p class="mb-1"><strong>Year Level:</strong> <?= $user['year_level'] ?></p>
                <p class="mb-0"><strong>Section:</strong> <?= $user['section'] ?></p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/student/results" class="btn btn-outline-primary">
                        <i class="fas fa-chart-line me-2"></i>View My Results
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>