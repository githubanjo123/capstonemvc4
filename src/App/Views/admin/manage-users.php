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
            <button class="btn btn-add-student me-2" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                <i class="fas fa-plus me-2"></i>
                Add Student
            </button>
            <button class="btn btn-add-faculty" data-bs-toggle="modal" data-bs-target="#addFacultyModal">
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
            return ($student['year_level'] . ' ' . $student['section']) === $yearSection;
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
                                <?= htmlspecialchars($student['full_name']) ?>
                            </div>
                            <div class="student-email">
                                <i class="fas fa-id-card me-2"></i>
                                <?= htmlspecialchars($student['school_id']) ?>
                            </div>
                            <div class="student-date">
                                <i class="fas fa-calendar me-2"></i>
                                Added on <?= date('M d, Y', strtotime($student['created_at'])) ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-edit me-2" onclick="editStudent(<?= $student['user_id'] ?>)">
                                <i class="fas fa-edit me-1"></i>
                                Edit
                            </button>
                            <button class="btn btn-delete" onclick="deleteStudent(<?= $student['user_id'] ?>)">
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
                        <?= htmlspecialchars($facultyMember['full_name']) ?>
                    </div>
                    <div class="student-email">
                        <i class="fas fa-id-card me-2"></i>
                        <?= htmlspecialchars($facultyMember['school_id']) ?>
                    </div>
                    <div class="student-date">
                        <i class="fas fa-calendar me-2"></i>
                        Added on <?= date('M d, Y', strtotime($facultyMember['created_at'])) ?>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-edit me-2" onclick="editFaculty(<?= $facultyMember['user_id'] ?>)">
                        <i class="fas fa-edit me-1"></i>
                        Edit
                    </button>
                    <button class="btn btn-delete" onclick="deleteFaculty(<?= $facultyMember['user_id'] ?>)">
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

    // Add Student Modal
    function showAddStudentModal() {
        $('#addStudentModal').modal('show');
    }

    // Handle Add Student Form - Proper MVC approach
    document.getElementById('addStudentForm').addEventListener('submit', function(e) {
        // Let the form submit normally - Controller will handle everything
        // No JavaScript needed for form submission
    });
</script>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>
                    Add New Student
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addStudentForm" action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/admin/users/add-student" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_id" class="form-label">School ID *</label>
                                <input type="text" class="form-control" id="school_id" name="school_id" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="year_level" class="form-label">Year Level *</label>
                                <select class="form-control" id="year_level" name="year_level" required>
                                    <option value="">Select Year Level</option>
                                    <option value="1st">1st Year</option>
                                    <option value="2nd">2nd Year</option>
                                    <option value="3rd">3rd Year</option>
                                    <option value="4th">4th Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="section" class="form-label">Section *</label>
                                <select class="form-control" id="section" name="section" required>
                                    <option value="">Select Section</option>
                                    <option value="A">Section A</option>
                                    <option value="B">Section B</option>
                                    <option value="C">Section C</option>
                                    <option value="D">Section D</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank for default password">
                        <small class="text-muted">Default password will be: School ID + Full Name</small>
                    </div>

                    <input type="hidden" name="role" value="student">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Cancel
                </button>
                <button type="submit" form="addStudentForm" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    Add Student
                </button>
            </div>
        </div>
    </div>
</div>