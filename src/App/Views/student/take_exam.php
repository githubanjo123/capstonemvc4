<?php $layout = 'app'; $pageTitle = 'Take Exam'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Exam Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4><?= htmlspecialchars($exam['title']) ?></h4>
                        <p class="text-muted"><?= htmlspecialchars($exam['descriptive_title']) ?></p>
                        
                        <div class="mb-3">
                            <strong>Instructions:</strong>
                            <p><?= nl2br(htmlspecialchars($exam['instructions'] ?? 'No specific instructions.')) ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Time Limit:</strong> <?= $exam['time_limit'] ?> minutes
                        </div>
                        
                        <div class="mb-3">
                            <strong>Subject:</strong> <?= htmlspecialchars($exam['descriptive_title']) ?>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Created by:</strong> <?= htmlspecialchars($exam['created_by_name']) ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Important Information</h6>
                            <ul class="mb-0">
                                <li>You cannot pause the exam once started</li>
                                <li>Make sure you have a stable internet connection</li>
                                <li>Do not refresh the page during the exam</li>
                                <li>Submit your answers before time runs out</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Before You Start</h6>
                            <ul class="mb-0">
                                <li>Ensure you're in a quiet environment</li>
                                <li>Have all necessary materials ready</li>
                                <li>Close other applications/tabs</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <form method="POST" action="/student/exam/<?= $exam['exam_id'] ?>/take">
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-play me-2"></i>Start Exam
                        </button>
                        <a href="/student/dashboard" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>