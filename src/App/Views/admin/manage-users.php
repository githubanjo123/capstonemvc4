<!-- Top Section - Add User Actions -->
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h4 class="text-xl font-semibold text-grey-800 mb-1">
                <i class="fas fa-user-plus mr-2 text-primary-600"></i>
                Add New Users
            </h4>
            <p class="text-grey-600">Add students and faculty to the system</p>
        </div>
        <div class="flex space-x-3">
            <button class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:-translate-y-1" onclick="showAddStudentModal()">
                <i class="fas fa-plus mr-2"></i>
                Add Student
            </button>
            <button class="bg-transparent border-2 border-grey-500 text-grey-600 hover:bg-grey-500 hover:text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300" onclick="showAddFacultyModal()">
                <i class="fas fa-plus mr-2"></i>
                Add Faculty
            </button>
        </div>
    </div>
</div>

<!-- Students Section - Organized by Year & Section -->
<div class="mb-8">
    <h5 class="text-lg font-semibold text-grey-800 mb-4">
        <i class="fas fa-graduation-cap mr-2 text-primary-600"></i>
        Students by Year & Section
    </h5>
    
    <!-- Year-Section Tabs -->
    <div class="flex flex-wrap gap-2 mb-6">
        <?php 
        $firstSection = true;
        foreach ($yearSections as $yearSection => $count): 
        ?>
            <button class="year-section-tab <?= $firstSection ? 'active' : '' ?> bg-grey-100 border border-grey-300 text-grey-600 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300 hover:bg-primary-600 hover:text-white hover:border-primary-600" 
                    data-section="section-<?= str_replace(' ', '-', strtolower($yearSection)) ?>">
                <?= $yearSection ?>
                <span class="bg-green-500 text-white rounded-full w-5 h-5 inline-flex items-center justify-center text-xs font-bold ml-2"><?= $count ?></span>
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
        <div class="student-section <?= $firstSection ? 'active' : '' ?> <?= !$firstSection ? 'hidden' : '' ?>" 
             id="<?= $sectionId ?>">
            
            <!-- Section Header -->
            <div class="bg-grey-100 p-4 rounded-lg mb-4 border-l-4 border-primary-600">
                <div class="flex justify-between items-center">
                    <div>
                        <h6 class="text-lg font-bold text-primary-600">
                            <i class="fas fa-users mr-2"></i>
                            <?= $yearSection ?>
                        </h6>
                    </div>
                    <div>
                        <span class="text-grey-600 text-sm"><?= $count ?> students</span>
                    </div>
                </div>
            </div>

            <!-- Student Cards -->
            <?php foreach ($sectionStudents as $student): ?>
                <div class="bg-white border border-grey-200 rounded-lg p-6 mb-4 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <div class="text-lg font-bold text-primary-600 mb-2">
                                <i class="fas fa-user-graduate mr-2"></i>
                                <?= htmlspecialchars($student['full_name']) ?>
                            </div>
                            <div class="text-grey-600 text-sm mb-2">
                                <i class="fas fa-id-card mr-2"></i>
                                <?= htmlspecialchars($student['school_id']) ?>
                            </div>
                            <div class="text-grey-500 text-xs">
                                <i class="fas fa-calendar mr-2"></i>
                                Added on <?= date('M d, Y', strtotime($student['created_at'])) ?>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm transition-all duration-300" onclick="editStudent(<?= $student['user_id'] ?>)">
                                <i class="fas fa-edit mr-1"></i>
                                Edit
                            </button>
                            <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition-all duration-300" onclick="deleteStudent(<?= $student['user_id'] ?>)">
                                <i class="fas fa-trash mr-1"></i>
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
<div class="mt-12">
    <h5 class="text-lg font-semibold text-grey-800 mb-4">
        <i class="fas fa-chalkboard-teacher mr-2 text-primary-600"></i>
        Faculty Members
    </h5>
    
    <div class="bg-grey-100 p-4 rounded-lg mb-4 border-l-4 border-primary-600">
        <div class="flex justify-between items-center">
            <div>
                <h6 class="text-lg font-bold text-primary-600">
                    <i class="fas fa-users mr-2"></i>
                    All Faculty
                </h6>
            </div>
            <div>
                <span class="text-grey-600 text-sm"><?= count($faculty) ?> faculty members</span>
            </div>
        </div>
    </div>

    <!-- Faculty Cards -->
    <?php foreach ($faculty as $facultyMember): ?>
        <div class="bg-white border border-grey-200 rounded-lg p-6 mb-4 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
            <div class="flex justify-between items-center">
                <div class="flex-1">
                    <div class="text-lg font-bold text-primary-600 mb-2">
                        <i class="fas fa-user-tie mr-2"></i>
                        <?= htmlspecialchars($facultyMember['full_name']) ?>
                    </div>
                    <div class="text-grey-600 text-sm mb-2">
                        <i class="fas fa-id-card mr-2"></i>
                        <?= htmlspecialchars($facultyMember['school_id']) ?>
                    </div>
                    <div class="text-grey-500 text-xs">
                        <i class="fas fa-calendar mr-2"></i>
                        Added on <?= date('M d, Y', strtotime($facultyMember['created_at'])) ?>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm transition-all duration-300" onclick="editFaculty(<?= $facultyMember['user_id'] ?>)">
                        <i class="fas fa-edit mr-1"></i>
                        Edit
                    </button>
                    <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition-all duration-300" onclick="deleteFaculty(<?= $facultyMember['user_id'] ?>)">
                        <i class="fas fa-trash mr-1"></i>
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

// Add Student Modal
function showAddStudentModal() {
    // TODO: Implement modal
    alert('Add student modal coming soon!');
}

// Add Faculty Modal
function showAddFacultyModal() {
    // TODO: Implement modal
    alert('Add faculty modal coming soon!');
}

// Year-Section Tab Switching
document.addEventListener('DOMContentLoaded', function() {
    const yearSectionTabs = document.querySelectorAll('.year-section-tab');
    const studentSections = document.querySelectorAll('.student-section');

    yearSectionTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetSection = this.getAttribute('data-section');
            
            // Update active tab
            yearSectionTabs.forEach(t => {
                t.classList.remove('active', 'bg-primary-600', 'text-white', 'border-primary-600');
                t.classList.add('bg-grey-100', 'text-grey-600', 'border-grey-300');
            });
            this.classList.add('active', 'bg-primary-600', 'text-white', 'border-primary-600');
            this.classList.remove('bg-grey-100', 'text-grey-600', 'border-grey-300');
            
            // Show/hide sections
            studentSections.forEach(section => {
                if (section.id === targetSection) {
                    section.classList.remove('hidden');
                } else {
                    section.classList.add('hidden');
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