<?php
session_start();
include '../include/connection.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: ../log/login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'];
$position = $_SESSION['position'];

// Verify user is instructor or senior instructor
if (!in_array($position, ['Instructor', 'Senior Instructor'])) {
    $_SESSION['error'] = "Access denied. This page is only for Instructors and Senior Instructors.";
    header("Location: dashboard.php");
    exit();
}

// Get assigned courses with details
$courses_query = "SELECT ic.id, ic.course_no, ic.assigned_date, ic.status,
                         c.course_name, c.nvq_level, c.course_type, c.course_duration, 
                         c.course_fee, c.course_description, c.qualifications
                  FROM instructor_courses ic
                  JOIN course c ON ic.course_no = c.course_no
                  WHERE ic.staff_id = ?
                  ORDER BY ic.assigned_date DESC";

$courses_stmt = $con->prepare($courses_query);
$courses_stmt->bind_param("s", $staff_id);
$courses_stmt->execute();
$courses_result = $courses_stmt->get_result();

$assigned_courses = [];
while ($row = $courses_result->fetch_assoc()) {
    $assigned_courses[] = $row;
}
$courses_stmt->close();

// Handle course filter
$selected_course = isset($_GET['course_no']) ? trim($_GET['course_no']) : '';

// Get students for selected course
$students = [];
$course_name = '';
if (!empty($selected_course)) {
    // Get course name
    $course_name_query = "SELECT course_name FROM course WHERE course_no = ?";
    $cn_stmt = $con->prepare($course_name_query);
    $cn_stmt->bind_param("s", $selected_course);
    $cn_stmt->execute();
    $cn_result = $cn_stmt->get_result();
    if ($cn_result->num_rows > 0) {
        $cn_data = $cn_result->fetch_assoc();
        $course_name = $cn_data['course_name'];
    }
    $cn_stmt->close();
    
    // Get students who applied for this course
    $students_query = "SELECT se.*, c.course_name 
                      FROM student_enrollments se 
                      LEFT JOIN course c ON se.course_option_one = c.course_name 
                      WHERE se.course_option_one = ?
                      ORDER BY se.application_date DESC";
    
    $students_stmt = $con->prepare($students_query);
    $students_stmt->bind_param("s", $course_name);
    $students_stmt->execute();
    $students_result = $students_stmt->get_result();
    
    while ($student = $students_result->fetch_assoc()) {
        $students[] = $student;
    }
    $students_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - NVTI Baddegama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    
    <?php include 'navbar.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        
        <!-- Alert Messages -->
        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">My Assigned Courses</h1>
            <p class="text-gray-600">Manage and view details of courses assigned to you</p>
        </div>
        
        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php if (count($assigned_courses) > 0): ?>
                <?php foreach ($assigned_courses as $course): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4">
                            <h3 class="text-white font-bold text-lg"><?php echo htmlspecialchars($course['course_name']); ?></h3>
                            <p class="text-blue-100 text-sm">Course No: <?php echo htmlspecialchars($course['course_no']); ?></p>
                        </div>
                        <div class="p-4">
                            <div class="space-y-2 mb-4">
                                <p class="text-sm"><strong>NVQ Level:</strong> <?php echo htmlspecialchars($course['nvq_level']); ?></p>
                                <p class="text-sm"><strong>Type:</strong> <?php echo htmlspecialchars($course['course_type']); ?></p>
                                <p class="text-sm"><strong>Duration:</strong> <?php echo htmlspecialchars($course['course_duration']); ?> months</p>
                                <p class="text-sm"><strong>Fee:</strong> Rs. <?php echo htmlspecialchars($course['course_fee']); ?></p>
                                <p class="text-sm"><strong>Assigned:</strong> <?php echo date('Y-m-d', strtotime($course['assigned_date'])); ?></p>
                            </div>
                            <div class="flex gap-2">
                                <a href="?course_no=<?php echo urlencode($course['course_no']); ?>" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center text-sm transition-colors">
                                    <i class="fas fa-users mr-1"></i>View Students
                                </a>
                                <button onclick="viewCourseDetails(<?php echo htmlspecialchars(json_encode($course)); ?>)" 
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm transition-colors">
                                    <i class="fas fa-info-circle mr-1"></i>Details
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-400 text-3xl mr-4"></i>
                        <div>
                            <h3 class="text-lg font-medium text-yellow-800">No Courses Assigned</h3>
                            <p class="text-yellow-700">You don't have any courses assigned yet. Please contact the administrator.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Students Section -->
        <?php if (!empty($selected_course) && !empty($course_name)): ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Students for: <?php echo htmlspecialchars($course_name); ?></h2>
                    <p class="text-gray-600">Course No: <?php echo htmlspecialchars($selected_course); ?></p>
                </div>
                <a href="instructor_courses.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Courses
                </a>
            </div>
            
            <?php if (count($students) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Info</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIC</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($students as $student): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($student['full_name']); ?></div>
                                    <div class="text-sm text-gray-500">ID: <?php echo htmlspecialchars($student['Student_id']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($student['nic']); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="tel:<?php echo htmlspecialchars($student['contact_no']); ?>" class="text-blue-600 hover:text-blue-800">
                                        <?php echo htmlspecialchars($student['contact_no']); ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($student['is_processed']): ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Processed
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <button onclick="viewStudentDetails(<?php echo $student['id']; ?>)" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Students Found</h3>
                    <p class="text-gray-500">No students have applied for this course yet.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Course Details Modal -->
    <div id="courseDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">Course Details & Update</h2>
                    <button onclick="closeCourseDetailsModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                <div id="courseDetailsContent"></div>
            </div>
        </div>
    </div>
    
    <!-- Student Details Modal -->
    <div id="studentDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">Student Details</h2>
                    <button onclick="closeStudentDetailsModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                <div id="studentDetailsContent"></div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script>
        let currentCourse = null;
        
        function viewCourseDetails(course) {
            currentCourse = course;
            const modal = document.getElementById('courseDetailsModal');
            const content = document.getElementById('courseDetailsContent');
            
            content.innerHTML = `
                <div class="space-y-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800">${course.course_name}</h3>
                        <p class="text-sm text-gray-600">Course No: ${course.course_no}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-700">NVQ Level</p>
                            <p class="text-gray-900">${course.nvq_level}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Course Type</p>
                            <p class="text-gray-900">${course.course_type}</p>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration (months)</label>
                        <input type="number" id="edit_duration" value="${course.course_duration}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Course Fee (Rs.)</label>
                        <input type="text" id="edit_fee" value="${course.course_fee}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Qualifications Required</label>
                        <textarea id="edit_qualifications" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">${course.qualifications}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Course Description</label>
                        <textarea id="edit_description" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">${course.course_description}</textarea>
                    </div>
                    
                    <div class="flex gap-3">
                        <button onclick="updateCourseDetails()" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                        <button onclick="closeCourseDetailsModal()" 
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                    
                    <div class="text-xs text-gray-500 bg-yellow-50 p-3 rounded">
                        <i class="fas fa-info-circle mr-1"></i>
                        You can update course details as the assigned instructor. Changes will be reflected immediately.
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }
        
        function closeCourseDetailsModal() {
            document.getElementById('courseDetailsModal').classList.add('hidden');
            currentCourse = null;
        }
        
        async function updateCourseDetails() {
            if (!currentCourse) return;
            
            const duration = document.getElementById('edit_duration').value;
            const fee = document.getElementById('edit_fee').value;
            const qualifications = document.getElementById('edit_qualifications').value;
            const description = document.getElementById('edit_description').value;
            
            if (!duration || !fee || !qualifications || !description) {
                alert('Please fill all fields');
                return;
            }
            
            try {
                // Update each field
                const updates = [
                    { field: 'course_duration', value: duration },
                    { field: 'course_fee', value: fee },
                    { field: 'qualifications', value: qualifications },
                    { field: 'course_description', value: description }
                ];
                
                for (const update of updates) {
                    const formData = new FormData();
                    formData.append('course_no', currentCourse.course_no);
                    formData.append('field', update.field);
                    formData.append('value', update.value);
                    
                    const response = await fetch('update_course.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    if (!result.success) {
                        throw new Error(result.message);
                    }
                }
                
                alert('Course details updated successfully!');
                closeCourseDetailsModal();
                location.reload();
            } catch (error) {
                alert('Error updating course: ' + error.message);
            }
        }
        
        function viewStudentDetails(studentId) {
            const modal = document.getElementById('studentDetailsModal');
            const content = document.getElementById('studentDetailsContent');
            
            content.innerHTML = '<p class="text-center"><i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i></p>';
            modal.classList.remove('hidden');
            
            fetch('get_student_details.php?id=' + studentId)
                .then(response => response.text())
                .then(data => {
                    content.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = '<p class="text-red-600">Error loading student details.</p>';
                });
        }
        
        function closeStudentDetailsModal() {
            document.getElementById('studentDetailsModal').classList.add('hidden');
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            const courseModal = document.getElementById('courseDetailsModal');
            const studentModal = document.getElementById('studentDetailsModal');
            
            if (event.target == courseModal) {
                closeCourseDetailsModal();
            }
            if (event.target == studentModal) {
                closeStudentDetailsModal();
            }
        }
    </script>
</body>
</html>

<?php
$con->close();
?>