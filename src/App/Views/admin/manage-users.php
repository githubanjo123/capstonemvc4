<!-- Top Section - Add User Actions -->
<div class="action-buttons">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h4 class="mb-1">
                <i class="fas fa-user-plus me-2"></i>
                Add New Users
            </h4>
            <p class="text-muted mb-0">Add students and faculty to the system</p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-add-student me-2">
                <i class="fas fa-plus me-2"></i>
                Add Student
            </button>
            <button class="btn btn-add-faculty">
                <i class="fas fa-plus me-2"></i>
                Add Faculty
            </button>
        </div>
    </div>
</div>

<!-- Students Section - Organized by Year & Section -->
<div class="students-section">
    <h5 class="mb-3">
        <i class="fas fa-graduation-cap me-2"></i>
        Students by Year & Section
    </h5>
    
    <!-- Year-Section Tabs -->
    <div class="year-section-tabs">
        <?php 
        $firstSection = true;
        foreach ($yearSections as $yearSection => $count): 
        ?>
            <button class="year-section-tab <?= $firstSection ? 'active' : '' ?>" 
                    data-section="section-<?= str_replace(' ', '-', strtolower($yearSection)) ?>">
                <?= $yearSection ?>
                <span class="student-count"><?= $count ?></span>
            </button>
        <?php 
            $firstSection = false;
        endforeach; 
        ?>
    </div>

    <!-- Student Sections Content -->
    <?php 
    $firstSection = true;
    foreach ($yearSections as $yearSection => $count): 
        $sectionId = 'section-' . str_replace(' ', '-', strtolower($yearSection));
        $sectionStudents = array_filter($students, function($student) use ($yearSection) {
            return ($student['year'] . ' ' . $student['section']) === $yearSection;
        });
    ?>
        <div class="student-section <?= $firstSection ? 'active' : '' ?>" 
             id="<?= $sectionId ?>" 
             style="<?= $firstSection ? 'display: block;' : 'display: none;' ?>">
            
            <!-- Section Header -->
            <div class="section-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="section-title">
                            <i class="fas fa-users me-2"></i>
                            <?= $yearSection ?>
                        </h6>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="section-count"><?= $count ?> students</span>
                    </div>
                </div>
            </div>

            <!-- Student Cards -->
            <?php foreach ($sectionStudents as $student): ?>
                <div class="student-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="student-name">
                                <i class="fas fa-user-graduate me-2"></i>
                                <?= htmlspecialchars($student['name']) ?>
                            </div>
                            <div class="student-email">
                                <i class="fas fa-envelope me-2"></i>
                                <?= htmlspecialchars($student['email']) ?>
                            </div>
                            <div class="student-date">
                                <i class="fas fa-calendar me-2"></i>
                                Added on <?= date('M d, Y', strtotime($student['created_at'])) ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-edit me-2" onclick="editStudent(<?= $student['id'] ?>)">
                                <i class="fas fa-edit me-1"></i>
                                Edit
                            </button>
                            <button class="btn btn-delete" onclick="deleteStudent(<?= $student['id'] ?>)">
                                <i class="fas fa-trash me-1"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php 
        $firstSection = false;
    endforeach; 
    ?>
</div>

<!-- Faculty Section -->
<div class="faculty-section mt-5">
    <h5 class="mb-3">
        <i class="fas fa-chalkboard-teacher me-2"></i>
        Faculty Members
    </h5>
    
    <div class="section-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h6 class="section-title">
                    <i class="fas fa-users me-2"></i>
                    All Faculty
                </h6>
            </div>
            <div class="col-md-6 text-end">
                <span class="section-count"><?= count($faculty) ?> faculty members</span>
            </div>
        </div>
    </div>

    <!-- Faculty Cards -->
    <?php foreach ($faculty as $facultyMember): ?>
        <div class="student-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="student-name">
                        <i class="fas fa-user-tie me-2"></i>
                        <?= htmlspecialchars($facultyMember['name']) ?>
                    </div>
                    <div class="student-email">
                        <i class="fas fa-envelope me-2"></i>
                        <?= htmlspecialchars($facultyMember['email']) ?>
                    </div>
                    <div class="student-date">
                        <i class="fas fa-building me-2"></i>
                        <?= htmlspecialchars($facultyMember['department']) ?> • 
                        Added on <?= date('M d, Y', strtotime($facultyMember['created_at'])) ?>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-edit me-2" onclick="editFaculty(<?= $facultyMember['id'] ?>)">
                        <i class="fas fa-edit me-1"></i>
                        Edit
                    </button>
                    <button class="btn btn-delete" onclick="deleteFaculty(<?= $facultyMember['id'] ?>)">
                        <i class="fas fa-trash me-1"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
// Edit Student Function
function editStudent(studentId) {
    console.log('Edit student:', studentId);
    // TODO: Implement edit functionality
    alert('Edit student functionality coming soon!');
}

// Delete Student Function
function deleteStudent(studentId) {
    if (confirm('Are you sure you want to delete this student?')) {
        console.log('Delete student:', studentId);
        // TODO: Implement delete functionality
        alert('Delete student functionality coming soon!');
    }
}

// Edit Faculty Function
function editFaculty(facultyId) {
    console.log('Edit faculty:', facultyId);
    // TODO: Implement edit functionality
    alert('Edit faculty functionality coming soon!');
}

// Delete Faculty Function
function deleteFaculty(facultyId) {
    if (confirm('Are you sure you want to delete this faculty member?')) {
        console.log('Delete faculty:', facultyId);
        // TODO: Implement delete functionality
        alert('Delete faculty functionality coming soon!');
    }
}
</script>