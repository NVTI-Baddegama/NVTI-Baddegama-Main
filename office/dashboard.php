<?php
session_start();
include '../include/connection.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: ../log/login.php");
    exit();
}

$staff_name = $_SESSION['staff_name'];
$position = $_SESSION['position'];
$course_no = $_SESSION['course_no'];
$profile_photo = $_SESSION['profile_photo'] ?? null;
$staff_id = $_SESSION['staff_id'];

// Check if this is the first login
$first_login_check = "SELECT login_status FROM staff WHERE staff_id = ?";
$login_stmt = $con->prepare($first_login_check);
$login_stmt->bind_param("s", $staff_id);
$login_stmt->execute();
$login_result = $login_stmt->get_result();
$login_data = $login_result->fetch_assoc();
$show_password_modal = ($login_data['login_status'] == 0);

// Handle search
$search_nic = isset($_GET['search_nic']) ? trim($_GET['search_nic']) : '';
$selected_course_filter = isset($_GET['course_filter']) ? $_GET['course_filter'] : 'all';

// Get students based on position and search criteria
$students = [];
$where_conditions = [];
$params = [];
$param_types = '';

// Base query
if ($position === 'Non-Academic Staff') {
    // Non-Academic Staff can see all students
    $base_query = "SELECT se.*, c.course_name FROM student_enrollments se 
                   LEFT JOIN course c ON se.course_option_one = c.course_name 
                   WHERE 1=1";
} else {
    // Instructors can only see students who applied for their course
    $course_query = "SELECT course_name FROM course WHERE course_no = ?";
    $course_stmt = $con->prepare($course_query);
    $course_stmt->bind_param("s", $course_no);
    $course_stmt->execute();
    $course_result = $course_stmt->get_result();
    
    if ($course_result->num_rows > 0) {
        $course_data = $course_result->fetch_assoc();
        $instructor_course_name = $course_data['course_name'];
        
        $base_query = "SELECT se.*, c.course_name FROM student_enrollments se 
                       LEFT JOIN course c ON se.course_option_one = c.course_name 
                       WHERE (se.course_option_one = ? OR se.course_option_two = ?)";
        $params[] = $instructor_course_name;
        $params[] = $instructor_course_name;
        $param_types .= 'ss';
    } else {
        $base_query = "SELECT se.*, c.course_name FROM student_enrollments se 
                       LEFT JOIN course c ON se.course_option_one = c.course_name 
                       WHERE 1=0"; // No results if course not found
    }
}

// Add search condition
if (!empty($search_nic)) {
    if ($position === 'Non-Academic Staff') {
        $where_conditions[] = "se.nic LIKE ?";
    } else {
        $base_query .= " AND se.nic LIKE ?";
    }
    $params[] = "%$search_nic%";
    $param_types .= 's';
}

// Add course filter for Non-Academic Staff
if ($position === 'Non-Academic Staff' && $selected_course_filter !== 'all') {
    $where_conditions[] = "(se.course_option_one = ? OR se.course_option_two = ?)";
    $params[] = $selected_course_filter;
    $params[] = $selected_course_filter;
    $param_types .= 'ss';
}

// Construct final query
$final_query = $base_query;
if (!empty($where_conditions) && $position === 'Non-Academic Staff') {
    $final_query .= " AND " . implode(" AND ", $where_conditions);
}
$final_query .= " ORDER BY se.application_date DESC";

$students_stmt = $con->prepare($final_query);
if (!empty($params)) {
    $students_stmt->bind_param($param_types, ...$params);
}
$students_stmt->execute();
$students_result = $students_stmt->get_result();

while ($student = $students_result->fetch_assoc()) {
    $students[] = $student;
}

// Get all courses for filter dropdown (Non-Academic Staff only)
$courses = [];
if ($position === 'Non-Academic Staff') {
    $courses_query = "SELECT DISTINCT course_name FROM course WHERE status = 'active' ORDER BY course_name";
    $courses_result = $con->query($courses_query);
    while ($course = $courses_result->fetch_assoc()) {
        $courses[] = $course['course_name'];
    }
}

// Get current staff details for profile modal
$staff_query = "SELECT * FROM staff WHERE staff_id = ?";
$staff_stmt = $con->prepare($staff_query);
$staff_stmt->bind_param("s", $staff_id);
$staff_stmt->execute();
$staff_result = $staff_stmt->get_result();
$current_staff = $staff_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Staff Dashboard - NVTI Baddegama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    
    <style>
        /* Mobile Menu Toggle (complex transition) */
        .mobile-menu-toggle.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }
        .mobile-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }
        .mobile-menu-toggle.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }

        /* Modal Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUpIn {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        .modal {
            animation: fadeIn 0.3s ease;
        }
        
        /* Mobile Modal content: positioned at bottom */
        @media (max-width: 768px) {
            #profileModal.flex .modal-content, 
            #passwordModal.flex .modal-content {
                margin-top: auto; /* Push content to the bottom */
                border-radius: 12px 12px 0 0;
                animation: slideUpIn 0.3s ease;
                max-height: 90vh;
            }
        }
        
        /* Print styles */
        @media print {
            .dashboard-header,
            .controls-section,
            .action-buttons,
            .modal,
            .mobile-menu-toggle,
            .mobile-table-toggle {
                display: none !important;
            }
            .students-table {
                font-size: 10px;
                width: 100%;
            }
            .students-table th,
            .students-table td {
                padding: 0.25rem;
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-relaxed">

    <header class="bg-gradient-to-tr from-[#2c3e50] to-[#3498db] text-white py-2 md:py-4 shadow-xl fixed md:static top-0 left-0 w-full z-20">
        <div class="max-w-6xl mx-auto px-4 md:px-8 flex justify-between items-center gap-4">
            
            <div class="flex items-center gap-2 md:gap-4 w-auto">
                <img src="../images/logo/NVTI_logo.png" alt="NVTI Logo" class="w-10 h-10 md:w-[60px] md:h-[60px] rounded-full">
                <div class="header-text text-left">
                    <h1 class="text-base md:text-2xl mb-0.5 font-semibold whitespace-nowrap">Staff Dashboard</h1>
                    <p class="opacity-90 text-[0.6rem] md:text-sm hidden sm:block">National Vocational Training Institute - Baddegama</p>
                </div>
            </div>
            
            <div class="header-right w-auto">
                <div class="profile-menu flex items-center gap-2 md:gap-4 relative cursor-pointer justify-end" onclick="toggleProfileDropdown()">
                    <?php if (!empty($profile_photo) && file_exists("../uploads/profile_photos/" . $profile_photo)): ?>
                        <img src="../uploads/profile_photos/<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile" class="w-[35px] h-[35px] md:w-[50px] md:h-[50px] rounded-full object-cover border-2 border-white/30">
                    <?php else: ?>
                        <div class="w-[35px] h-[35px] md:w-[50px] md:h-[50px] rounded-full bg-gradient-to-tr from-[#667eea] to-[#764ba2] flex items-center justify-center text-sm md:text-xl font-bold border-2 border-white/30">
                            <?php echo strtoupper(substr($staff_name, 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div class="profile-info hidden sm:block text-right">
                        <strong class="text-sm md:text-base"><?php echo htmlspecialchars($staff_name); ?></strong><br>
                        <small class="text-xs md:text-sm opacity-90"><?php echo htmlspecialchars($position); ?></small>
                    </div>
                    
                    <div class="profile-dropdown absolute top-full right-0 bg-white text-gray-800 min-w-[150px] md:min-w-[200px] rounded-xl shadow-2xl opacity-0 invisible translate-y-2 transition-all duration-300 z-50" id="profileDropdown">
                        <a href="#" onclick="openProfileModal(); return false;" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-t-xl text-center md:text-left">Profile</a>
                        <a href="../admin/lib/logout.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-b-xl text-center md:text-left">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-6xl mx-auto mt-[60px] md:my-8 px-4 md:px-8">
        <?php
        // Alert Messages
        if (isset($_SESSION['success'])) {
            echo '<div class="p-4 mb-4 rounded-lg font-medium bg-green-100 text-green-800 border border-green-300">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="p-4 mb-4 rounded-lg font-medium bg-red-100 text-red-800 border border-red-300">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        
        <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg mb-8 text-center">
            <h2 class="text-gray-800 mb-4 text-xl md:text-3xl font-semibold">Welcome, <?php echo htmlspecialchars($staff_name); ?>!</h2>
            <p class="text-gray-600 mb-2">Position: <strong class="text-gray-700"><?php echo htmlspecialchars($position); ?></strong></p>
            <?php if ($position === 'Instructors' && $course_no): ?>
                <?php
                $course_query = "SELECT course_name FROM course WHERE course_no = ?";
                $course_stmt = $con->prepare($course_query);
                $course_stmt->bind_param("s", $course_no);
                $course_stmt->execute();
                $course_result = $course_stmt->get_result();
                if ($course_result->num_rows > 0) {
                    $course = $course_result->fetch_assoc();
                    echo "<p class='text-gray-600'>Assigned Course: <strong class='text-gray-700'>" . htmlspecialchars($course['course_name']) . "</strong></p>";
                }
                ?>
            <?php endif; ?>
        </div>

        <?php
        // Calculate statistics
        $total_students = count($students);
        $pending_applications = count(array_filter($students, function($s) { return $s['is_processed'] == 0; }));
        $processed_applications = count(array_filter($students, function($s) { return $s['is_processed'] == 1; }));
        ?>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-lg text-center transition-transform duration-200 hover:scale-[1.01] hover:shadow-xl">
                <div class="text-3xl md:text-4xl font-extrabold text-blue-600 mb-1"><?php echo $total_students; ?></div>
                <div class="text-gray-600 text-xs md:text-sm uppercase tracking-wider">Total Students</div>
            </div>
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-lg text-center transition-transform duration-200 hover:scale-[1.01] hover:shadow-xl">
                <div class="text-3xl md:text-4xl font-extrabold text-yellow-600 mb-1"><?php echo $pending_applications; ?></div>
                <div class="text-gray-600 text-xs md:text-sm uppercase tracking-wider">Pending Applications</div>
            </div>
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-lg text-center transition-transform duration-200 hover:scale-[1.01] hover:shadow-xl">
                <div class="text-3xl md:text-4xl font-extrabold text-green-600 mb-1"><?php echo $processed_applications; ?></div>
                <div class="text-gray-600 text-xs md:text-sm uppercase tracking-wider">Processed Applications</div>
            </div>
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-lg text-center transition-transform duration-200 hover:scale-[1.01] hover:shadow-xl hidden lg:block">
                <div class="text-3xl md:text-4xl font-extrabold text-purple-600 mb-1">
                    <?php 
                    $total_courses = $con->query("SELECT COUNT(*) FROM course WHERE status = 'active'")->fetch_row()[0];
                    echo $total_courses;
                    ?>
                </div>
                <div class="text-gray-600 text-xs md:text-sm uppercase tracking-wider">Active Courses</div>
            </div>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-xl shadow-lg mb-8 flex flex-col md:flex-row flex-wrap gap-4 items-end">
            <div class="flex-1 w-full md:min-w-[250px]">
                <label for="search_nic" class="block mb-1 text-gray-700 font-medium">Search by NIC:</label>
                <form method="GET" class="flex flex-col sm:flex-row gap-2 items-end">
                    <input type="text" id="search_nic" name="search_nic" value="<?php echo htmlspecialchars($search_nic); ?>" placeholder="Enter NIC number" class="p-2 border border-gray-300 rounded-lg text-base flex-1 min-h-[44px]">
                    <?php if ($position === 'Non-Academic Staff'): ?>
                        <input type="hidden" name="course_filter" value="<?php echo htmlspecialchars($selected_course_filter); ?>">
                    <?php endif; ?>
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 min-h-[44px]">Search</button>
                    <a href="dashboard.php" class="w-full sm:w-auto px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors duration-200 text-center min-h-[44px] flex items-center justify-center">Clear</a>
                </form>
            </div>
            
            <?php if ($position === 'Non-Academic Staff'): ?>
            <div class="flex-1 w-full md:min-w-[250px]">
                <label for="course_filter" class="block mb-1 text-gray-700 font-medium">Filter by Course:</label>
                <form method="GET" class="flex gap-2 items-end">
                    <select name="course_filter" id="course_filter" onchange="this.form.submit()" class="p-2 border border-gray-300 rounded-lg text-base flex-1 min-h-[44px]">
                        <option value="all" <?php echo $selected_course_filter === 'all' ? 'selected' : ''; ?>>All Courses</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo htmlspecialchars($course); ?>" <?php echo $selected_course_filter === $course ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($course); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="search_nic" value="<?php echo htmlspecialchars($search_nic); ?>">
                </form>
            </div>
            <?php endif; ?>
            
            <div class="export-section w-full md:w-auto flex justify-end">
                <button onclick="exportToPDF()" class="w-full md:w-auto px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 min-h-[44px]">Export to PDF</button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-tr from-[#667eea] to-[#764ba2] text-white p-4 md:p-6 flex justify-between items-center">
                <h3 class="text-xl md:text-2xl font-semibold m-0">Student Applications</h3>
                <button class="mobile-table-toggle md:hidden bg-white/20 text-white border border-white/30 px-3 py-1 rounded-lg cursor-pointer text-xs" onclick="toggleTableView()" id="tableToggleBtn">
                    Switch to Card View
                </button>
            </div>

            <?php if (!empty($students)): ?>
                <div class="overflow-x-auto overflow-y-hidden md:block" id="tableContainer">
                    <table class="min-w-full divide-y divide-gray-200 text-sm md:text-base">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Full Name</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">NIC</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Contact</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">WhatsApp</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Course Option 1</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Course Option 2</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                                <?php if ($position === 'Instructors'): ?>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($students as $student): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?php echo htmlspecialchars($student['full_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600"><?php echo htmlspecialchars($student['nic']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-blue-600"><a href="tel:<?php echo htmlspecialchars($student['contact_no']); ?>" class="hover:underline"><?php echo htmlspecialchars($student['contact_no']); ?></a></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-blue-600">
                                        <?php if ($student['whatsapp_no']): ?>
                                            <a href="https://wa.me/<?php echo htmlspecialchars($student['whatsapp_no']); ?>" target="_blank" class="hover:underline">
                                                <?php echo htmlspecialchars($student['whatsapp_no']); ?>
                                            </a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600"><?php echo htmlspecialchars($student['course_option_one']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600"><?php echo htmlspecialchars($student['course_option_two'] ?: 'N/A'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($student['is_processed']): ?>
                                            <span class="inline-block px-3 py-1 text-xs font-medium uppercase tracking-wider rounded-full bg-green-100 text-green-800 border border-green-300">Processed</span>
                                        <?php else: ?>
                                            <span class="inline-block px-3 py-1 text-xs font-medium uppercase tracking-wider rounded-full bg-yellow-100 text-yellow-800 border border-yellow-300">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($position === 'Instructors'): ?>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex gap-2 flex-wrap min-w-[120px]">
                                                <?php if (!$student['is_processed']): ?>
                                                    <button onclick="handleStudentAction(<?php echo $student['id']; ?>, 'accept')" class="px-3 py-1 text-xs bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors min-h-[36px]">Accept</button>
                                                <?php endif; ?>
                                                <button onclick="handleStudentAction(<?php echo $student['id']; ?>, 'reject')" class="px-3 py-1 text-xs bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors min-h-[36px]">Reject</button>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="p-4 md:hidden" id="cardsContainer" style="display: none;">
                    <?php foreach ($students as $student): ?>
                        <div class="bg-gray-50 rounded-lg mb-4 overflow-hidden shadow-md">
                            <div class="bg-[#667eea] text-white p-4 flex justify-between items-center">
                                <h4 class="m-0 text-lg font-semibold"><?php echo htmlspecialchars($student['full_name']); ?></h4>
                                <span class="inline-block px-3 py-1 text-xs font-medium uppercase tracking-wider rounded-full <?php echo $student['is_processed'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo $student['is_processed'] ? 'Processed' : 'Pending'; ?>
                                </span>
                            </div>
                            <div class="p-4 text-gray-700">
                                <div class="space-y-1 text-sm">
                                    <p><strong>NIC:</strong> <span class="font-mono"><?php echo htmlspecialchars($student['nic']); ?></span></p>
                                    <p><strong>Contact:</strong> <a href="tel:<?php echo htmlspecialchars($student['contact_no']); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($student['contact_no']); ?></a></p>
                                    <?php if ($student['whatsapp_no']): ?>
                                        <p><strong>WhatsApp:</strong> <a href="https://wa.me/<?php echo htmlspecialchars($student['whatsapp_no']); ?>" target="_blank" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($student['whatsapp_no']); ?></a></p>
                                    <?php endif; ?>
                                    <p><strong>Course 1:</strong> <?php echo htmlspecialchars($student['course_option_one']); ?></p>
                                    <?php if ($student['course_option_two']): ?>
                                        <p><strong>Course 2:</strong> <?php echo htmlspecialchars($student['course_option_two']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php if ($position === 'Instructors'): ?>
                                    <div class="flex gap-2 flex-wrap mt-4">
                                        <?php if (!$student['is_processed']): ?>
                                            <button onclick="handleStudentAction(<?php echo $student['id']; ?>, 'accept')" class="flex-1 px-3 py-2 text-sm bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors min-w-[120px]">Accept</button>
                                        <?php endif; ?>
                                        <button onclick="handleStudentAction(<?php echo $student['id']; ?>, 'reject')" class="flex-1 px-3 py-2 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors min-w-[120px]">Reject</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="p-8 text-center text-gray-500">
                    <p class="text-lg">No student applications found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="profileModal" class="modal hidden fixed inset-0 z-[1000] bg-black/50 overflow-y-auto flex items-start justify-center p-4 md:p-0" onclick="closeModalOnOutsideClick(event, 'profileModal')">
        <div class="modal-content bg-white mx-auto p-4 md:p-8 rounded-xl shadow-2xl w-full max-w-lg relative transition-all duration-300 md:mt-10" onclick="event.stopPropagation()">
            <span class="absolute top-4 right-4 text-gray-400 text-3xl font-bold cursor-pointer w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 hover:text-gray-700" onclick="closeProfileModal()">&times;</span>
            <h2 class="text-2xl font-bold text-gray-800 mb-6 pr-10">Update Profile</h2>
            <form id="profileForm" enctype="multipart/form-data" class="space-y-4">
                <div class="form-group">
                    <label for="profile_photo" class="block mb-1 text-gray-700 font-medium">Profile Photo:</label>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="w-full p-2 border border-gray-300 rounded-lg">
                    <?php if (!empty($current_staff['profile_photo'])): ?>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">Current photo:</p>
                            <img src="../uploads/profile_photos/<?php echo htmlspecialchars($current_staff['profile_photo']); ?>" alt="Current Profile" class="max-w-[100px] max-h-[100px] rounded-md mt-1">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="first_name" class="block mb-1 text-gray-700 font-medium">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($current_staff['first_name']); ?>" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="form-group">
                    <label for="last_name" class="block mb-1 text-gray-700 font-medium">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($current_staff['last_name']); ?>" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="form-group">
                    <label for="contact_no" class="block mb-1 text-gray-700 font-medium">Contact Number:</label>
                    <input type="text" id="contact_no" name="contact_no" value="<?php echo htmlspecialchars($current_staff['contact_no']); ?>" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="form-group">
                    <label for="email" class="block mb-1 text-gray-700 font-medium">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($current_staff['email']); ?>" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="form-group">
                    <label for="new_password" class="block mb-1 text-gray-700 font-medium">New Password (leave blank to keep current):</label>
                    <input type="password" id="new_password" name="new_password" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors min-h-[44px]">Update Profile</button>
            </form>
        </div>
    </div>

    <?php if ($show_password_modal): ?>
    <div id="passwordModal" class="modal fixed inset-0 z-[1000] bg-black/50 overflow-y-auto flex items-start justify-center p-4 md:p-0" onclick="closeModalOnOutsideClick(event, 'passwordModal')">
        <div class="modal-content bg-white mx-auto p-4 md:p-8 rounded-xl shadow-2xl w-full max-w-lg relative md:mt-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Welcome! First Time Login</h2>
            <p class="mb-4 text-gray-600">Would you like to change your password or keep the current one?</p>
            <form id="passwordForm" class="space-y-4">
                <div class="form-group">
                    <label for="modal_new_password" class="block mb-1 text-gray-700 font-medium">New Password:</label>
                    <input type="password" id="modal_new_password" name="new_password" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="form-group">
                    <label for="modal_confirm_password" class="block mb-1 text-gray-700 font-medium">Confirm Password:</label>
                    <input type="password" id="modal_confirm_password" name="confirm_password" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex flex-col sm:flex-row gap-2 mt-6">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors min-h-[44px]">Change Password</button>
                    <button type="button" onclick="keepCurrentPassword()" class="flex-1 px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors min-h-[44px]">Keep Current</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Set initial state
        let isMobileView = window.innerWidth <= 768;
        let isCardView = isMobileView; // Default to card view on mobile
        
        // Initial setup for mobile view
        if (isMobileView) {
            const tableContainer = document.getElementById('tableContainer');
            const cardsContainer = document.getElementById('cardsContainer');
            const toggleBtn = document.getElementById('tableToggleBtn');
            
            if (tableContainer) tableContainer.style.display = 'none';
            if (cardsContainer) cardsContainer.style.display = 'block';
            if (toggleBtn) toggleBtn.textContent = 'Switch to Table View';
        }

        // Check screen size on load and resize
        function checkScreenSize() {
            isMobileView = window.innerWidth <= 768;
            const tableToggleBtn = document.getElementById('tableToggleBtn');
            const tableContainer = document.getElementById('tableContainer');
            const cardsContainer = document.getElementById('cardsContainer');
            
            if (isMobileView) {
                if (tableToggleBtn) tableToggleBtn.style.display = 'block';
                // Force initial card view on mobile if table was showing
                if (!isCardView && tableContainer && cardsContainer) {
                    tableContainer.style.display = 'none';
                    cardsContainer.style.display = 'block';
                    isCardView = true;
                    if (tableToggleBtn) toggleBtn.textContent = 'Switch to Table View';
                }
            } else {
                // Desktop view: Always show table, hide mobile elements
                if (tableToggleBtn) tableToggleBtn.style.display = 'none';
                if (tableContainer) tableContainer.style.display = 'block';
                if (cardsContainer) cardsContainer.style.display = 'none';
                isCardView = false;
            }
        }

        // Mobile menu toggle (Not fully implemented in the provided PHP logic)
        function toggleMobileMenu() {
            const toggle = document.getElementById('mobileMenuToggle');
            toggle.classList.toggle('active'); // Applies custom CSS for burger animation
            // Add navigation logic here if you implement a mobile menu
        }

        // Profile dropdown toggle logic
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            // Toggle visibility
            dropdown.classList.toggle('opacity-0');
            dropdown.classList.toggle('invisible');
            
            // Toggle translation (it's always translate-y-2 from top-full)
            dropdown.classList.toggle('translate-y-2');
        }
        
        // Toggle between table and card view
        function toggleTableView() {
            const tableContainer = document.getElementById('tableContainer');
            const cardsContainer = document.getElementById('cardsContainer');
            const toggleBtn = document.getElementById('tableToggleBtn');
            
            isCardView = !isCardView;
            
            if (isCardView) {
                tableContainer.style.display = 'none';
                cardsContainer.style.display = 'block';
                toggleBtn.textContent = 'Switch to Table View';
            } else {
                tableContainer.style.display = 'block';
                cardsContainer.style.display = 'none';
                toggleBtn.textContent = 'Switch to Card View';
            }
        }

        // Profile Modal Functions
        function openProfileModal() {
            const profileModal = document.getElementById('profileModal');
            profileModal.classList.remove('hidden');
            profileModal.classList.add('flex'); // Use flex to center the content
            document.body.style.overflow = 'hidden'; 
            
            // Ensure dropdown is closed when modal opens
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.add('opacity-0', 'invisible'); 
            dropdown.classList.remove('translate-y-2'); // Remove active translation
        }

        function closeProfileModal() {
            const profileModal = document.getElementById('profileModal');
            profileModal.classList.add('hidden');
            profileModal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
        
        function closeModalOnOutsideClick(event, modalId) {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                if(modalId === 'profileModal') {
                    closeProfileModal();
                } 
            }
        }

        // Handle profile form submission
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('profile_update.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeProfileModal();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating profile.');
            });
        });

        // Handle student actions (omitted for brevity, assume linked files are correct)

        // First login password modal functions (omitted for brevity)
        <?php if ($show_password_modal): ?>
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('modal_new_password').value;
            const confirmPassword = document.getElementById('modal_confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            if (newPassword.length < 6) {
                alert('Password must be at least 6 characters long!');
                return;
            }
            
            const formData = new FormData();
            formData.append('new_password', newPassword);
            formData.append('confirm_password', confirmPassword);
            
            fetch('change_password_modal.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    document.getElementById('passwordModal').style.display = 'none';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while changing password.');
            });
        });

        function keepCurrentPassword() {
            if (!confirm('Are you sure you want to keep your current password? You will not see this prompt again.')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('keep_current', '1');
            
            fetch('change_password_modal.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('passwordModal').style.display = 'none';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred.');
            });
        }
        <?php endif; ?>

        function exportToPDF() {
            // PDF Export logic...
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            doc.setFontSize(16);
            doc.text('NVTI Baddegama - Student Applications Report', 20, 20);
            
            doc.setFontSize(10);
            doc.text('Generated on: ' + new Date().toLocaleString(), 20, 30);
            doc.text('Generated by: <?php echo $staff_name; ?> (<?php echo $position; ?>)', 20, 35);
            
            <?php if ($position === 'Non-Academic Staff' && $selected_course_filter !== 'all'): ?>
                doc.text('Filtered by Course: <?php echo htmlspecialchars($selected_course_filter); ?>', 20, 40);
            <?php endif; ?>
            
            const tableData = [];
            <?php foreach ($students as $student): ?>
                tableData.push([
                    '<?php echo htmlspecialchars($student["full_name"]); ?>',
                    '<?php echo htmlspecialchars($student["nic"]); ?>',
                    '<?php echo htmlspecialchars($student["contact_no"]); ?>',
                    '<?php echo htmlspecialchars($student["whatsapp_no"] ?: "N/A"); ?>',
                    '<?php echo htmlspecialchars($student["course_option_one"]); ?>',
                    '<?php echo htmlspecialchars($student["course_option_two"] ?: "N/A"); ?>'
                ]);
            <?php endforeach; ?>
            
            doc.autoTable({
                head: [['Name', 'NIC', 'Contact', 'WhatsApp', 'Course 1', 'Course 2']],
                body: tableData,
                startY: 50,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [52, 152, 219] } 
            });
            
            const filename = 'NVTI_Students_' + new Date().toISOString().split('T')[0] + '.pdf';
            doc.save(filename);
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            const profileDropdown = document.getElementById('profileDropdown');
            
            // Check if the click is outside of the profile menu
            if (!event.target.closest('.profile-menu') && 
                !profileDropdown.contains(event.target)) {
                
                profileDropdown.classList.add('opacity-0', 'invisible');
                profileDropdown.classList.remove('translate-y-2');
            }
        }

        // Initialize on page load
        window.addEventListener('load', function() {
            checkScreenSize();
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            checkScreenSize();
        });
    </script>
</body>
</html>