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
$profile_photo = $_SESSION['profile_photo'] ?? null;
$staff_id = $_SESSION['staff_id'];
$staff_type = $_SESSION['staff_type'] ?? '';

// Check if this is the first login
$first_login_check = "SELECT login_status FROM staff WHERE staff_id = ?";
$login_stmt = $con->prepare($first_login_check);
$login_stmt->bind_param("s", $staff_id);
$login_stmt->execute();
$login_result = $login_stmt->get_result();
$login_data = $login_result->fetch_assoc();
$show_password_modal = ($login_data['login_status'] == 0);

// Get all courses assigned to this instructor from instructor_courses table
$assigned_courses = [];
if ($position === 'Instructor' || $position === 'Senior Instructor') {
    $course_query = "SELECT ic.course_no, c.course_name, ic.assigned_date, ic.status
                     FROM instructor_courses ic
                     JOIN course c ON ic.course_no = c.course_no
                     WHERE ic.staff_id = ? AND ic.status = 'active'
                     ORDER BY ic.assigned_date DESC";
    $course_stmt = $con->prepare($course_query);
    $course_stmt->bind_param("s", $staff_id);
    $course_stmt->execute();
    $course_result = $course_stmt->get_result();
    
    while ($course_info = $course_result->fetch_assoc()) {
        $assigned_courses[] = $course_info;
    }
    $course_stmt->close();
}

// Get current active tab (default to first course)
$active_course_no = isset($_GET['course']) ? $_GET['course'] : ($assigned_courses[0]['course_no'] ?? '');
$active_course_name = '';

// Find active course name
foreach ($assigned_courses as $course) {
    if ($course['course_no'] === $active_course_no) {
        $active_course_name = $course['course_name'];
        break;
    }
}

// Handle search
$search_nic = isset($_GET['search_nic']) ? trim($_GET['search_nic']) : '';

// Get students for the active course
$students = [];
if (!empty($active_course_name)) {
    $base_query = "SELECT se.*, c.course_name FROM student_enrollments se 
                   LEFT JOIN course c ON se.course_option_one = c.course_name 
                   WHERE se.course_option_one = ?";
    
    $params = [$active_course_name];
    $param_types = 's';
    
    if (!empty($search_nic)) {
        $base_query .= " AND se.nic LIKE ?";
        $params[] = "%$search_nic%";
        $param_types .= 's';
    }
    
    $base_query .= " ORDER BY se.application_date DESC";
    
    $students_stmt = $con->prepare($base_query);
    $students_stmt->bind_param($param_types, ...$params);
    $students_stmt->execute();
    $students_result = $students_stmt->get_result();
    
    while ($student = $students_result->fetch_assoc()) {
        $students[] = $student;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NVTI Baddegama - Staff Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 2% auto;
            padding: 20px;
            border-radius: 8px;
            width: 95%;
            max-width: 600px;
            max-height: 95vh;
            overflow-y: auto;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        
        /* Tab Styles */
        .tab-button {
            position: relative;
            padding: 12px 24px;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .tab-button:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }
        .tab-button.active {
            border-bottom-color: #2563eb;
            color: #2563eb;
            font-weight: 600;
        }
        
        @media print {
            .no-print { display: none !important; }
        }
        
        @media (max-width: 768px) {
            .mobile-stack > * {
                display: block !important;
                width: 100% !important;
                margin-bottom: 0.5rem;
            }
            
            .tab-button {
                padding: 8px 12px;
                font-size: 0.875rem;
            }
        }
        
        @media (max-width: 640px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .mobile-card {
                display: block !important;
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
                padding: 1rem;
                margin-bottom: 1rem;
                background: white;
            }
            
            .desktop-table {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>
    
    <!-- Main Content -->
    <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
        
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
        
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6 sm:mb-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Welcome, <?php echo htmlspecialchars($staff_name); ?>!</h1>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4">
                        <p class="text-gray-600 text-sm sm:text-base">
                            <span class="font-semibold">Position:</span> <?php echo htmlspecialchars($position); ?>
                        </p>
                        <p class="text-gray-600 text-sm sm:text-base">
                            <span class="font-semibold">Staff Type:</span> <?php echo htmlspecialchars($staff_type); ?>
                        </p>
                    </div>
                    
                    <?php if (count($assigned_courses) > 0): ?>
                        <p class="text-gray-600 mb-2 text-sm sm:text-base mt-2">
                            <span class="font-semibold">Assigned Courses:</span> <?php echo count($assigned_courses); ?>
                        </p>
                    <?php elseif ($position === 'Instructor' || $position === 'Senior Instructor'): ?>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-4">
                            <p class="text-yellow-800 text-sm sm:text-base">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                No course assigned to your profile. Please contact the administrator.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($position === 'Instructor' || $position === 'Senior Instructor'): ?>
                <div class="lg:ml-4">
                    <a href="instructor_courses.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg transition-colors shadow-md text-sm sm:text-base font-medium">
                        <i class="fas fa-edit mr-2"></i>Update Course Details
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (($position === 'Instructor' || $position === 'Senior Instructor') && count($assigned_courses) > 0): ?>
        
        <!-- Course Tabs -->
        <div class="bg-white rounded-lg shadow-md mb-6 overflow-x-auto">
            <div class="flex border-b border-gray-200">
                <?php foreach ($assigned_courses as $course): ?>
                    <a href="?course=<?php echo urlencode($course['course_no']); ?>" 
                       class="tab-button <?php echo ($course['course_no'] === $active_course_no) ? 'active' : 'text-gray-600'; ?>">
                        <i class="fas fa-book mr-2"></i>
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <?php
        $total_students = count($students);
        $pending_applications = count(array_filter($students, function($s) { return $s['is_processed'] == 0; }));
        $processed_applications = count(array_filter($students, function($s) { return $s['is_processed'] == 1; }));
        ?>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-2xl sm:text-3xl text-blue-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs sm:text-sm font-medium text-gray-500">Total Students</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900"><?php echo $total_students; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-2xl sm:text-3xl text-yellow-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs sm:text-sm font-medium text-gray-500">Pending Applications</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900"><?php echo $pending_applications; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 sm:col-span-2 lg:col-span-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-2xl sm:text-3xl text-green-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs sm:text-sm font-medium text-gray-500">Processed Applications</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900"><?php echo $processed_applications; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Student Management Tools -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-tools mr-2"></i>Student Management Options
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <form method="GET" class="col-span-1 sm:col-span-2">
                    <input type="hidden" name="course" value="<?php echo htmlspecialchars($active_course_no); ?>">
                    <div class="flex">
                        <input type="text" 
                               name="search_nic" 
                               value="<?php echo htmlspecialchars($search_nic); ?>" 
                               placeholder="Search by NIC..." 
                               class="flex-1 px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="px-4 sm:px-6 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <a href="?course=<?php echo urlencode($active_course_no); ?>" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors text-center text-sm flex items-center justify-center">
                    <i class="fas fa-refresh mr-2"></i>Clear Search
                </a>
                
                <?php if (!empty($students)): ?>
                <!-- PDF Export Button -->
                <button onclick="exportPDF()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm flex items-center justify-center">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </button>
                
                <button onclick="acceptAllPending()" 
                        class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors text-sm flex items-center justify-center">
                    <i class="fas fa-check-double mr-2"></i>Accept All Pending
                </button>
                
                <button onclick="rejectAllPending()" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors text-sm flex items-center justify-center">
                    <i class="fas fa-times-circle mr-2"></i>Reject All Pending
                </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Students Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800">
                    Student Applications - <?php echo htmlspecialchars($active_course_name); ?>
                </h2>
            </div>
            
            <?php if (!empty($students)): ?>
            
            <!-- Desktop Table -->
            <div class="hidden sm:block overflow-x-auto desktop-table">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()" class="mr-2">
                                Student Info
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIC</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Options</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($students as $student): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <input type="checkbox" class="student-checkbox mr-3" value="<?php echo $student['id']; ?>">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($student['full_name']); ?></div>
                                        <div class="text-sm text-gray-500">ID: <?php echo htmlspecialchars($student['Student_id']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($student['nic']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div>
                                    <a href="tel:<?php echo htmlspecialchars($student['contact_no']); ?>" class="text-blue-600 hover:text-blue-800">
                                        <?php echo htmlspecialchars($student['contact_no']); ?>
                                    </a>
                                </div>
                                <?php if ($student['whatsapp_no']): ?>
                                <div>
                                    <a href="https://wa.me/<?php echo htmlspecialchars($student['whatsapp_no']); ?>" target="_blank" class="text-green-600 hover:text-green-800">
                                        <i class="fab fa-whatsapp mr-1"></i><?php echo htmlspecialchars($student['whatsapp_no']); ?>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="max-w-xs truncate">
                                    <?php echo htmlspecialchars($student['address'] ?? 'Not provided'); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="mb-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        1st: <?php echo htmlspecialchars($student['course_option_one']); ?>
                                    </span>
                                </div>
                                <?php if ($student['course_option_two']): ?>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        2nd: <?php echo htmlspecialchars($student['course_option_two']); ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($student['is_processed']): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Processed
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="viewStudentDetails(<?php echo $student['id']; ?>)" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </button>
                                    <?php if (!$student['is_processed']): ?>
                                    <button onclick="handleStudentAction(<?php echo $student['id']; ?>, 'accept')" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                        <i class="fas fa-check mr-1"></i>Accept
                                    </button>
                                    <?php endif; ?>
                                    <button onclick="handleStudentAction(<?php echo $student['id']; ?>, 'reject')" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                        <i class="fas fa-times mr-1"></i>Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards -->
            <div class="sm:hidden">
                <?php foreach ($students as $student): ?>
                <div class="mobile-card border-b border-gray-200 last:border-b-0 p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($student['full_name']); ?></h3>
                            <p class="text-sm text-gray-500">ID: <?php echo htmlspecialchars($student['Student_id']); ?></p>
                            <p class="text-sm text-gray-500">NIC: <?php echo htmlspecialchars($student['nic']); ?></p>
                        </div>
                        <div class="text-right">
                            <?php if ($student['is_processed']): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Processed
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="space-y-2 mb-3">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-phone text-blue-500 mr-2"></i>
                            <a href="tel:<?php echo htmlspecialchars($student['contact_no']); ?>" class="text-blue-600">
                                <?php echo htmlspecialchars($student['contact_no']); ?>
                            </a>
                        </div>
                        
                        <?php if ($student['whatsapp_no']): ?>
                        <div class="flex items-center text-sm">
                            <i class="fab fa-whatsapp text-green-500 mr-2"></i>
                            <a href="https://wa.me/<?php echo htmlspecialchars($student['whatsapp_no']); ?>" target="_blank" class="text-green-600">
                                <?php echo htmlspecialchars($student['whatsapp_no']); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <div class="flex items-start text-sm">
                            <i class="fas fa-map-marker-alt text-gray-500 mr-2 mt-1"></i>
                            <span class="text-gray-600"><?php echo htmlspecialchars($student['address'] ?? 'Not provided'); ?></span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="flex flex-wrap gap-1">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                1st: <?php echo htmlspecialchars($student['course_option_one']); ?>
                            </span>
                            <?php if ($student['course_option_two']): ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                2nd: <?php echo htmlspecialchars($student['course_option_two']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <button onclick="viewStudentDetails(<?php echo $student['id']; ?>)" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm transition-colors text-center">
                            <i class="fas fa-eye mr-1"></i>View
                        </button>
                        <?php if (!$student['is_processed']): ?>
                        <button onclick="handleStudentAction(<?php echo $student['id']; ?>, 'accept')" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm transition-colors text-center">
                            <i class="fas fa-check mr-1"></i>Accept
                        </button>
                        <?php endif; ?>
                        <button onclick="handleStudentAction(<?php echo $student['id']; ?>, 'reject')" 
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm transition-colors text-center">
                            <i class="fas fa-times mr-1"></i>Reject
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php else: ?>
            <div class="px-6 py-12 text-center">
                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Student Applications Found</h3>
                <?php if (!empty($search_nic)): ?>
                    <p class="text-gray-500">No students found matching the NIC "<?php echo htmlspecialchars($search_nic); ?>"</p>
                    <a href="?course=<?php echo urlencode($active_course_no); ?>" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Clear search</a>
                <?php else: ?>
                    <p class="text-gray-500">Students will appear here when they apply for this course.</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php else: ?>
        
        <!-- Non-Instructor or No Courses Assigned -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Staff Dashboard</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                    <h3 class="text-base sm:text-lg font-medium text-blue-800 mb-2">Your Role</h3>
                    <p class="text-blue-700"><?php echo htmlspecialchars($position); ?></p>
                    <p class="text-sm text-blue-600 mt-2"><?php echo htmlspecialchars($staff_type); ?></p>
                </div>
                <div class="bg-green-50 border-l-4 border-green-400 p-4">
                    <h3 class="text-base sm:text-lg font-medium text-green-800 mb-2">Quick Actions</h3>
                    <button onclick="openProfileModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm transition-colors">
                        <i class="fas fa-user-edit mr-2"></i>Update Profile
                    </button>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
    
    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
    
    <!-- Profile Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeProfileModal()">&times;</span>
            <h2 class="text-xl sm:text-2xl font-bold mb-4">Update Profile</h2>
            <form id="profileForm" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="profile_photo" class="block text-sm font-medium text-gray-700">Profile Photo:</label>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <?php if (!empty($current_staff['profile_photo'])): ?>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">Current photo:</p>
                            <img src="../uploads/profile_photos/<?php echo htmlspecialchars($current_staff['profile_photo']); ?>" 
                                 alt="Current Profile" class="w-20 h-20 rounded-full object-cover mt-1">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name:</label>
                        <input type="text" id="first_name" name="first_name" required
                               value="<?php echo htmlspecialchars($current_staff['first_name']); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" required
                               value="<?php echo htmlspecialchars($current_staff['last_name']); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label for="contact_no" class="block text-sm font-medium text-gray-700">Contact Number:</label>
                    <input type="text" id="contact_no" name="contact_no"
                           value="<?php echo htmlspecialchars($current_staff['contact_no']); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($current_staff['email']); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password (leave blank to keep current):</label>
                    <input type="password" id="new_password" name="new_password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                    Update Profile
                </button>
            </form>
        </div>
    </div>
    
    <!-- Student Details Modal -->
    <div id="studentDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeStudentDetailsModal()">&times;</span>
            <h2 class="text-xl sm:text-2xl font-bold mb-4">Student Details</h2>
            <div id="studentDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
    
    <!-- First Login Password Modal -->
    <?php if ($show_password_modal): ?>
    <div id="passwordModal" class="modal" style="display: block;">
        <div class="modal-content">
            <h2 class="text-xl sm:text-2xl font-bold mb-4">Welcome! First Time Login</h2>
            <p class="text-gray-600 mb-6">Would you like to change your password or keep the current one?</p>
            <form id="passwordForm" class="space-y-4">
                <div>
                    <label for="modal_new_password" class="block text-sm font-medium text-gray-700">New Password:</label>
                    <input type="password" id="modal_new_password" name="new_password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="modal_confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password:</label>
                    <input type="password" id="modal_confirm_password" name="confirm_password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        Change Password
                    </button>
                    <button type="button" onclick="keepCurrentPassword()" class="flex-1 bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition-colors">
                        Keep Current
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <script>
        // PDF Export Function
        function exportPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            doc.setFontSize(16);
            doc.text('NVTI Baddegama - Student Applications Report', 20, 20);
            
            doc.setFontSize(10);
            doc.text('Generated on: ' + new Date().toLocaleString(), 20, 30);
            doc.text('Generated by: <?php echo addslashes($staff_name); ?> (<?php echo addslashes($position); ?>)', 20, 35);
            doc.text('Course: <?php echo addslashes($active_course_name); ?>', 20, 40);
            
            const tableData = [];
            <?php foreach ($students as $student): ?>
                tableData.push([
                    '<?php echo addslashes($student["full_name"]); ?>',
                    '<?php echo addslashes($student["Student_id"]); ?>',
                    '<?php echo addslashes($student["nic"]); ?>',
                    '<?php echo addslashes($student["contact_no"]); ?>',
                    '<?php echo addslashes($student["whatsapp_no"] ?: "N/A"); ?>',
                    '<?php echo addslashes($student["address"] ?? "Not provided"); ?>',
                    '<?php echo addslashes($student["course_option_one"]); ?>',
                    '<?php echo addslashes($student["course_option_two"] ?: "N/A"); ?>',
                    '<?php echo $student["is_processed"] ? "Processed" : "Pending"; ?>'
                ]);
            <?php endforeach; ?>
            
            doc.autoTable({
                head: [['Name', 'Student ID', 'NIC', 'Contact', 'WhatsApp', 'Address', 'Course 1', 'Course 2', 'Status']],
                body: tableData,
                startY: 50,
                styles: { fontSize: 7 },
                headStyles: { fillColor: [37, 99, 235] }
            });
            
            const filename = 'NVTI_Students_<?php echo addslashes($active_course_name); ?>_' + new Date().toISOString().split('T')[0] + '.pdf';
            doc.save(filename);
        }

        // Profile Modal Functions
        function openProfileModal() {
            document.getElementById('profileModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeProfileModal() {
            document.getElementById('profileModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Student Details Modal Functions
        function viewStudentDetails(studentId) {
            document.getElementById('studentDetailsModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
            
            fetch('get_student_details.php?id=' + studentId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('studentDetailsContent').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('studentDetailsContent').innerHTML = '<p class="text-red-600">Error loading student details.</p>';
                });
        }

        function closeStudentDetailsModal() {
            document.getElementById('studentDetailsModal').style.display = 'none';
            document.body.style.overflow = 'auto';
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

        <?php if (($position === 'Instructor' || $position === 'Senior Instructor') && count($assigned_courses) > 0): ?>
        // Handle student actions
        function handleStudentAction(studentId, action) {
            let confirmMessage;
            if (action === 'accept') {
                confirmMessage = 'Are you sure you want to accept this student application?';
            } else {
                confirmMessage = 'Are you sure you want to reject this student application?\n\n' +
                               'Note: If this is their Course Choice 1, they will be moved to Course Choice 2 instructor. ' +
                               'If this is their final choice, they will be removed from the database.';
            }
            
            if (confirm(confirmMessage)) {
                const formData = new FormData();
                formData.append('student_id', studentId);
                formData.append('action', action);
                formData.append('course_name', '<?php echo addslashes($active_course_name); ?>');
                
                fetch('student_action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing the request.');
                });
            }
        }

        // Bulk operations
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        }

        // Accept all pending students without selection
        function acceptAllPending() {
            const pendingCount = <?php echo $pending_applications; ?>;
            
            if (pendingCount === 0) {
                alert('No pending students found for this course.');
                return;
            }
            
            if (confirm(`Are you sure you want to accept all ${pendingCount} pending student(s) for this course?\n\nThis action will accept all students without requiring individual selection.`)) {
                const formData = new FormData();
                formData.append('action', 'bulk_accept_all_pending');
                formData.append('course_name', '<?php echo addslashes($active_course_name); ?>');
                
                fetch('student_action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while accepting pending students.');
                });
            }
        }

        // Reject all pending students without selection
        function rejectAllPending() {
            const pendingCount = <?php echo $pending_applications; ?>;
            
            if (pendingCount === 0) {
                alert('No pending students found for this course.');
                return;
            }
            
            if (confirm(`Are you sure you want to reject all ${pendingCount} pending student(s) for this course?\n\nNote:\n- Students with Course Choice 2 will be moved to their second choice instructor\n- Students without Course Choice 2 will be removed from the database\n\nThis action cannot be undone!`)) {
                const formData = new FormData();
                formData.append('action', 'bulk_reject_all_pending');
                formData.append('course_name', '<?php echo addslashes($active_course_name); ?>');
                
                fetch('student_action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while rejecting pending students.');
                });
            }
        }
        <?php endif; ?>

        // First login password modal functions
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

        // Close modal when clicking outside
        window.onclick = function(event) {
            const profileModal = document.getElementById('profileModal');
            const studentModal = document.getElementById('studentDetailsModal');
            
            if (event.target == profileModal) {
                profileModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
            
            if (event.target == studentModal) {
                studentModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
    </script>
</body>
</html>