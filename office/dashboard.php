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
$profile_photo = $_SESSION['profile_photo'];

// Handle student rejection
if (isset($_POST['reject_student'])) {
    $student_id = $_POST['student_id'];
    $delete_query = "DELETE FROM student_enrollments WHERE id = ?";
    $delete_stmt = $con->prepare($delete_query);
    $delete_stmt->bind_param("i", $student_id);
    if ($delete_stmt->execute()) {
        $_SESSION['success'] = "Student application rejected and removed successfully.";
    } else {
        $_SESSION['error'] = "Error rejecting student application.";
    }
    header("Location: dashboard.php");
    exit();
}

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
        $where_conditions[] = "(se.course_option_one = ? OR se.course_option_two = ?)";
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
    $where_conditions[] = "se.nic LIKE ?";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - NVTI Baddegama</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-left img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }
        
        .profile-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border: 1px solid white;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .welcome-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .controls-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-box, .filter-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .search-box input, .filter-box select {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .students-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .students-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .students-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .students-table th,
        .students-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .students-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .students-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-processed {
            background: #d4edda;
            color: #155724;
        }
        
        .no-students {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="header-content">
            <div class="header-left">
                <img src="../images/logo/NVTI_logo.png" alt="NVTI Logo">
                <div>
                    <h1>Staff Dashboard</h1>
                    <p>National Vocational Training Institute - Baddegama</p>
                </div>
            </div>
            <div class="header-right">
                <?php if ($profile_photo): ?>
                    <img src="../uploads/profile_photos/<?php echo $profile_photo; ?>" alt="Profile" class="profile-photo">
                <?php endif; ?>
                <div>
                    <strong><?php echo $staff_name; ?></strong><br>
                    <small><?php echo $position; ?></small>
                </div>
                <a href="../admin/lib/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        
        <div class="welcome-section">
            <h2>Welcome, <?php echo $staff_name; ?>!</h2>
            <p>Position: <strong><?php echo $position; ?></strong></p>
            <?php if ($position === 'Instructors' && $course_no): ?>
                <?php
                $course_query = "SELECT course_name FROM course WHERE course_no = ?";
                $course_stmt = $con->prepare($course_query);
                $course_stmt->bind_param("s", $course_no);
                $course_stmt->execute();
                $course_result = $course_stmt->get_result();
                if ($course_result->num_rows > 0) {
                    $course = $course_result->fetch_assoc();
                    echo "<p>Assigned Course: <strong>" . $course['course_name'] . "</strong></p>";
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

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_students; ?></div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $pending_applications; ?></div>
                <div class="stat-label">Pending Applications</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $processed_applications; ?></div>
                <div class="stat-label">Processed Applications</div>
            </div>
        </div>

        <div class="controls-section">
            <div class="search-box">
                <label for="search_nic">Search by NIC:</label>
                <form method="GET" style="display: flex; gap: 10px;">
                    <input type="text" id="search_nic" name="search_nic" value="<?php echo htmlspecialchars($search_nic); ?>" placeholder="Enter NIC number">
                    <?php if ($position === 'Non-Academic Staff'): ?>
                        <input type="hidden" name="course_filter" value="<?php echo htmlspecialchars($selected_course_filter); ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="dashboard.php" class="btn btn-secondary">Clear</a>
                </form>
            </div>
            
            <?php if ($position === 'Non-Academic Staff'): ?>
            <div class="filter-box">
                <label for="course_filter">Filter by Course:</label>
                <form method="GET" style="display: flex; gap: 10px;">
                    <select name="course_filter" id="course_filter" onchange="this.form.submit()">
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
            
            <div>
                <button onclick="exportToPDF()" class="btn btn-success">Export to PDF</button>
            </div>
        </div>

        <div class="students-section">
            <div class="students-header">
                <h3>Student Applications</h3>
            </div>

            <?php if (!empty($students)): ?>
                <table class="students-table" id="studentsTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>NIC</th>
                            <th>Contact</th>
                            <th>WhatsApp</th>
                            <th>Course Option 1</th>
                            <th>Course Option 2</th>
                            <th>Application Date</th>
                            <th>Status</th>
                            <?php if ($position === 'Instructors'): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['Student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['nic']); ?></td>
                                <td><?php echo htmlspecialchars($student['contact_no']); ?></td>
                                <td><?php echo htmlspecialchars($student['whatsapp_no'] ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($student['course_option_one']); ?></td>
                                <td><?php echo htmlspecialchars($student['course_option_two'] ?: 'N/A'); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($student['application_date'])); ?></td>
                                <td>
                                    <?php if ($student['is_processed']): ?>
                                        <span class="status-badge status-processed">Processed</span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($position === 'Instructors'): ?>
                                    <td>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to reject this student application?');">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <button type="submit" name="reject_student" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;">Reject</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-students">
                    <p>No student applications found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Add title
            doc.setFontSize(16);
            doc.text('NVTI Baddegama - Student Applications Report', 20, 20);
            
            // Add generation info
            doc.setFontSize(10);
            doc.text('Generated on: ' + new Date().toLocaleString(), 20, 30);
            doc.text('Generated by: <?php echo $staff_name; ?> (<?php echo $position; ?>)', 20, 35);
            
            <?php if ($position === 'Non-Academic Staff' && $selected_course_filter !== 'all'): ?>
                doc.text('Filtered by Course: <?php echo htmlspecialchars($selected_course_filter); ?>', 20, 40);
            <?php endif; ?>
            
            // Prepare table data
            const tableData = [];
            <?php foreach ($students as $student): ?>
                tableData.push([
                    '<?php echo htmlspecialchars($student["Student_id"]); ?>',
                    '<?php echo htmlspecialchars($student["full_name"]); ?>',
                    '<?php echo htmlspecialchars($student["nic"]); ?>',
                    '<?php echo htmlspecialchars($student["contact_no"]); ?>',
                    '<?php echo htmlspecialchars($student["whatsapp_no"] ?: "N/A"); ?>',
                    '<?php echo htmlspecialchars($student["course_option_one"]); ?>',
                    '<?php echo htmlspecialchars($student["course_option_two"] ?: "N/A"); ?>',
                    '<?php echo date("Y-m-d", strtotime($student["application_date"])); ?>',
                    '<?php echo $student["is_processed"] ? "Processed" : "Pending"; ?>'
                ]);
            <?php endforeach; ?>
            
            // Add table
            doc.autoTable({
                head: [['Student ID', 'Name', 'NIC', 'Contact', 'WhatsApp', 'Course 1', 'Course 2', 'Date', 'Status']],
                body: tableData,
                startY: 50,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [0, 123, 255] }
            });
            
            // Save the PDF
            const filename = 'NVTI_Students_' + new Date().toISOString().split('T')[0] + '.pdf';
            doc.save(filename);
        }
    </script>
</body>
</html>