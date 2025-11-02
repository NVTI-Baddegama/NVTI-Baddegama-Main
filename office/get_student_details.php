<?php
session_start();
include '../include/connection.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    http_response_code(401);
    echo '<p class="text-red-600">Unauthorized access.</p>';
    exit();
}

$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($student_id <= 0) {
    echo '<p class="text-red-600">Invalid student ID.</p>';
    exit();
}

// Get student details
$query = "SELECT * FROM student_enrollments WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<p class="text-red-600">Student not found.</p>';
    exit();
}

$student = $result->fetch_assoc();
?>

<div class="space-y-6">
    <!-- Personal Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-user mr-2 text-blue-500"></i>
            Personal Information
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Full Name</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['full_name'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Student ID</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['Student_id'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">NIC Number</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['nic'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Date of Birth</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['date_of_birth'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Gender</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['gender'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Civil Status</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['civil_status'] ?? 'Not provided'); ?></p>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-phone mr-2 text-green-500"></i>
            Contact Information
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Contact Number</label>
                <p class="text-gray-900 font-medium">
                    <?php if (!empty($student['contact_no'])): ?>
                        <a href="tel:<?php echo htmlspecialchars($student['contact_no']); ?>" class="text-blue-600 hover:text-blue-800">
                            <?php echo htmlspecialchars($student['contact_no']); ?>
                        </a>
                    <?php else: ?>
                        Not provided
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">WhatsApp Number</label>
                <p class="text-gray-900 font-medium">
                    <?php if (!empty($student['whatsapp_no'])): ?>
                        <a href="https://wa.me/<?php echo htmlspecialchars($student['whatsapp_no']); ?>" target="_blank" class="text-green-600 hover:text-green-800">
                            <i class="fab fa-whatsapp mr-1"></i><?php echo htmlspecialchars($student['whatsapp_no']); ?>
                        </a>
                    <?php else: ?>
                        Not provided
                    <?php endif; ?>
                </p>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-600">Email Address</label>
                <p class="text-gray-900 font-medium">
                    <?php if (!empty($student['email'])): ?>
                        <a href="mailto:<?php echo htmlspecialchars($student['email']); ?>" class="text-blue-600 hover:text-blue-800">
                            <?php echo htmlspecialchars($student['email']); ?>
                        </a>
                    <?php else: ?>
                        Not provided
                    <?php endif; ?>
                </p>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-600">Address</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['address'] ?? 'Not provided'); ?></p>
            </div>
        </div>
    </div>

    <!-- Educational Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-graduation-cap mr-2 text-purple-500"></i>
            Educational Information
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Educational Qualifications</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['educational_qualifications'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">School/Institution</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['school_institution'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Year of Completion</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['year_of_completion'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Previous Experience</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['previous_experience'] ?? 'Not provided'); ?></p>
            </div>
        </div>
    </div>

    <!-- Course Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-book mr-2 text-indigo-500"></i>
            Course Information
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Course Option 1</label>
                <p class="text-gray-900 font-medium">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <?php echo htmlspecialchars($student['course_option_one'] ?? 'Not provided'); ?>
                    </span>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Course Option 2</label>
                <p class="text-gray-900 font-medium">
                    <?php if (!empty($student['course_option_two'])): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <?php echo htmlspecialchars($student['course_option_two']); ?>
                        </span>
                    <?php else: ?>
                        Not provided
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Reason for Course Selection</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['reason_for_selection'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Career Goals</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['career_goals'] ?? 'Not provided'); ?></p>
            </div>
        </div>
    </div>

    <!-- Guardian Information -->
    <?php if (!empty($student['guardian_name']) || !empty($student['guardian_contact']) || !empty($student['guardian_relationship'])): ?>
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-users mr-2 text-orange-500"></i>
            Guardian Information
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Guardian Name</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['guardian_name'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Guardian Contact</label>
                <p class="text-gray-900 font-medium">
                    <?php if (!empty($student['guardian_contact'])): ?>
                        <a href="tel:<?php echo htmlspecialchars($student['guardian_contact']); ?>" class="text-blue-600 hover:text-blue-800">
                            <?php echo htmlspecialchars($student['guardian_contact']); ?>
                        </a>
                    <?php else: ?>
                        Not provided
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Relationship</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['guardian_relationship'] ?? 'Not provided'); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Application Status -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
            Application Status
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Application Date</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['application_date'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Status</label>
                <p class="text-gray-900 font-medium">
                    <?php if ($student['is_processed']): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>Processed
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>Pending
                        </span>
                    <?php endif; ?>
                </p>
            </div>
            <?php if (!empty($student['remarks'])): ?>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-600">Remarks</label>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['remarks']); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Additional Information -->
    <?php if (!empty($student['additional_info']) || !empty($student['special_requirements'])): ?>
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-plus-circle mr-2 text-teal-500"></i>
            Additional Information
        </h3>
        <div class="space-y-3">
            <?php if (!empty($student['additional_info'])): ?>
            <div>
                <label class="block text-sm font-medium text-gray-600">Additional Information</label>
                <p class="text-gray-900 font-medium"><?php echo nl2br(htmlspecialchars($student['additional_info'])); ?></p>
            </div>
            <?php endif; ?>
            <?php if (!empty($student['special_requirements'])): ?>
            <div>
                <label class="block text-sm font-medium text-gray-600">Special Requirements</label>
                <p class="text-gray-900 font-medium"><?php echo nl2br(htmlspecialchars($student['special_requirements'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="mt-6 pt-4 border-t border-gray-200">
    <button onclick="closeStudentDetailsModal()" class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md transition-colors">
        Close
    </button>
</div>