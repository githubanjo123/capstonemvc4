<?php $layout = 'app'; $pageTitle = 'Exam Session'; ?>

<div class="row">
    <div class="col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><?= htmlspecialchars($exam['title']) ?></h5>
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-2"><?= htmlspecialchars($exam['descriptive_title']) ?></span>
                    <div id="timer" class="badge bg-warning text-dark"></div>
                </div>
            </div>
            <div class="card-body">
                <form id="examForm" method="POST" action="/student/exam/<?= $exam['exam_id'] ?>/submit">
                    <input type="hidden" name="attempt_id" value="<?= $attempt_id ?>">
                    
                    <?php foreach ($exam['questions'] as $index => $question): ?>
                        <div class="question-container mb-4" id="question-<?= $question['question_id'] ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">Question <?= $index + 1 ?></h6>
                                <span class="badge bg-secondary"><?= ucfirst(str_replace('_', ' ', $question['question_type'])) ?></span>
                            </div>
                            
                            <p class="mb-3"><?= htmlspecialchars($question['question_text']) ?></p>
                            
                            <?php if ($question['question_type'] === 'multiple_choice'): ?>
                                <div class="options">
                                    <?php 
                                    $options = ['a', 'b', 'c', 'd'];
                                    foreach ($options as $option):
                                        $optionValue = $question['option_' . $option];
                                        if (!empty($optionValue)):
                                    ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" 
                                                   name="answer_<?= $question['question_id'] ?>" 
                                                   id="q<?= $question['question_id'] ?>_<?= $option ?>" 
                                                   value="<?= htmlspecialchars($optionValue) ?>">
                                            <label class="form-check-label" for="q<?= $question['question_id'] ?>_<?= $option ?>">
                                                <?= htmlspecialchars($optionValue) ?>
                                            </label>
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                                
                            <?php elseif ($question['question_type'] === 'true_false'): ?>
                                <div class="options">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="answer_<?= $question['question_id'] ?>" 
                                               id="q<?= $question['question_id'] ?>_true" 
                                               value="True">
                                        <label class="form-check-label" for="q<?= $question['question_id'] ?>_true">
                                            True
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="answer_<?= $question['question_id'] ?>" 
                                               id="q<?= $question['question_id'] ?>_false" 
                                               value="False">
                                        <label class="form-check-label" for="q<?= $question['question_id'] ?>_false">
                                            False
                                        </label>
                                    </div>
                                    
                            <?php elseif ($question['question_type'] === 'essay'): ?>
                                <div class="form-group">
                                    <textarea class="form-control" 
                                              name="answer_<?= $question['question_id'] ?>" 
                                              rows="6" 
                                              placeholder="Write your answer here..."></textarea>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                            <i class="fas fa-check me-2"></i>Submit Exam
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card sticky-top" style="top: 1rem;">
            <div class="card-header">
                <h6 class="card-title mb-0">Exam Progress</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Time Remaining:</strong>
                    <div id="timer-display" class="h4 text-warning"></div>
                </div>
                
                <div class="mb-3">
                    <strong>Questions:</strong>
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        <?php foreach ($exam['questions'] as $index => $question): ?>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary question-nav" 
                                    data-question="<?= $question['question_id'] ?>">
                                <?= $index + 1 ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Click on question numbers to navigate
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Timer functionality
let timeLeft = <?= $exam['time_limit'] * 60 ?>; // Convert to seconds
const timerDisplay = document.getElementById('timer-display');
const timer = document.getElementById('timer');
const submitBtn = document.getElementById('submitBtn');

function updateTimer() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    timerDisplay.textContent = timeString;
    timer.textContent = timeString;
    
    if (timeLeft <= 300) { // 5 minutes warning
        timer.classList.remove('bg-warning');
        timer.classList.add('bg-danger');
    }
    
    if (timeLeft <= 0) {
        alert('Time is up! Your exam will be submitted automatically.');
        document.getElementById('examForm').submit();
        return;
    }
    
    timeLeft--;
    setTimeout(updateTimer, 1000);
}

// Question navigation
document.querySelectorAll('.question-nav').forEach(btn => {
    btn.addEventListener('click', function() {
        const questionId = this.dataset.question;
        document.getElementById('question-' + questionId).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Form submission confirmation
document.getElementById('examForm').addEventListener('submit', function(e) {
    if (!confirm('Are you sure you want to submit your exam? You cannot change your answers after submission.')) {
        e.preventDefault();
    }
});

// Start timer
updateTimer();

// Prevent accidental navigation
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = 'Are you sure you want to leave? Your progress will be lost.';
});
</script>