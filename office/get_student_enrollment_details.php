<?php
// Database Connection
require '../include/connection.php';

// Get and validate student enrollment ID from URL
$student_id = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $student_id = (int)$_GET['id'];
} else {
    echo '<p class="text-red-600">Invalid Student ID</p>';
    exit;
}

// Get student enrollment details
$sql = "SELECT * FROM student_enrollments WHERE id = ?";
$stmt = $con->prepare($sql);

if ($stmt === false) {
    echo '<p class="text-red-600">Database error</p>';
    exit;
}

$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $student = $result->fetch_assoc();
    
    // Display student details in a formatted way
    ?>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($student['full_name']); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Student ID</label>
                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($student['Student_id']); ?></p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">NIC</label>
                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($student['nic']); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($student['dob']); ?></p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700">Address</label>
            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($student['address']); ?></p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                <p class="mt-1 text-sm text-gray-900">
                    <a href="tel:<?php echo htmlspecialchars($student['contact_no']); ?>" class="text-blue-600 hover:text-blue-800">
                        <?php echo htmlspecialchars($student['contact_no']); ?>
                    </a>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">WhatsApp Number</label>
                <p class="mt-1 text-sm text-gray-900">
                    <?php if ($student['whatsapp_no']): ?>
                        <a href="https://wa.me/<?php echo htmlspecialchars($student['whatsapp_no']); ?>" target="_blank" class="text-green-600 hover:text-green-800">
                            <i class="fab fa-whatsapp mr-1"></i><?php echo htmlspecialchars($student['whatsapp_no']); ?>
                        </a>
                    <?php else: ?>
                        <span class="text-gray-500">Not provided</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        
        <div class="border-t pt-4">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Academic Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">O/L Pass Status</label>
                    <p class="mt-1 text-sm text-gray-900">
                        <?php if ($student['ol_pass_status'] == 'Yes'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Passed
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Not Passed
                            </span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <?php if ($student['ol_pass_status'] == 'Yes'): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700">O/L Grades</label>
                    <div class="mt-1 text-sm text-gray-900 space-y-1">
                        <?php if ($student['ol_english_grade']): ?>
                            <p>English: <span class="font-medium"><?php echo htmlspecialchars($student['ol_english_grade']); ?></span></p>
                        <?php endif; ?>
                        <?php if ($student['ol_maths_grade']): ?>
                            <p>Mathematics: <span class="font-medium"><?php echo htmlspecialchars($student['ol_maths_grade']); ?></span></p>
                        <?php endif; ?>
                        <?php if ($student['ol_science_grade']): ?>
                            <p>Science: <span class="font-medium"><?php echo htmlspecialchars($student['ol_science_grade']); ?></span></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($student['al_category']): ?>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">A/L Category</label>
                <p class="mt-1 text-sm text-gray-900 capitalize"><?php echo htmlspecialchars($student['al_category']); ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="border-t pt-4">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Course Preferences</h3>
            
            <div class="space-y-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">First Choice</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <?php echo htmlspecialchars($student['course_option_one']); ?>
                        </span>
                    </p>
                </div>
                
                <?php if ($student['course_option_two']): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Second Choice</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <?php echo htmlspecialchars($student['course_option_two']); ?>
                        </span>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="border-t pt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Application Date</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo date('F j, Y, g:i a', strtotime($student['application_date'])); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Application Status</label>
                    <p class="mt-1">
                        <?php if ($student['is_processed']): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Processed
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    echo '<p class="text-red-600">Student not found</p>';
}

$stmt->close();
$con->close();
?>