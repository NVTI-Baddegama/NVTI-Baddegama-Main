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

// Get courses for tabs (Non-Academic Staff can see all, Academic Staff only their course)
if ($position === 'Non-Academic Staff') {
    $courses_query = "SELECT * FROM course WHERE status = 'active' ORDER BY course_name";
} else {
    $courses_query = "SELECT * FROM course WHERE course_no = ? AND status = 'active'";
}

$courses_stmt = $con->prepare($courses_query);
if ($position === 'Academic Staff') {
    $courses_stmt->bind_param("s", $course_no);
}
$courses_stmt->execute();
$courses_result = $courses_stmt->get_result();

// Get selected course (default to first available course)
$selected_course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;
if (!$selected_course_id && $courses_result->num_rows > 0) {
    $courses_result->data_seek(0);
    $first_course = $courses_result->fetch_assoc();
    $selected_course_id = $first_course['id'];
    $courses_result->data_seek(0);
}

// Get students for selected course
$students = [];
if ($selected_course_id) {
    // Map course names to enrollment options
    $course_mapping = [
        'Graphic Design' => 'graphic_design',
        'HND Business Management' => 'hnd_business',
        'IT Diploma' => 'it_diploma',
        'Automotive Mechanics' => 'automotive',
        'Electrical Technology' => 'electrical',
        'Welding & Fabrication' => 'welding'
    ];
    
    $course_name_query = "SELECT course_name FROM course WHERE id = ?";
    $course_name_stmt = $con->prepare($course_name_query);
    $course_name_stmt->bind_param("i", $selected_course_id);
    $course_name_stmt->execute();
    $course_name_result = $course_name_stmt->get_result();
    
    if ($course_name_result->num_rows > 0) {
        $course_data = $course_name_result->fetch_assoc();
        $course_name = $course_data['course_name'];
        $course_option = isset($course_mapping[$course_name]) ? $course_mapping[$course_name] : '';
        
        if ($course_option) {
            $students_query = "SELECT * FROM student_enrollments WHERE course_option_one = ? OR course_option_two = ? ORDER BY application_date DESC";
            $students_stmt = $con->prepare($students_query);
            $students_stmt->bind_param("ss", $course_option, $course_option);
            $students_stmt->execute();
            $students_result = $students_stmt->get_result();
            
            while ($student = $students_result->fetch_assoc()) {
                $students[] = $student;
            }
        }
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
        
        .course-tabs {
            display: flex;
            background: white;
            border-radius: 10px 10px 0 0;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .course-tab {
            flex: 1;
            padding: 15px 20px;
            background: #f8f9fa;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            text-align: center;
            transition: background 0.3s;
            border-right: 1px solid #ddd;
        }
        
        .course-tab:last-child {
            border-right: none;
        }
        
        .course-tab.active {
            background: #007bff;
            color: white;
        }
        
        .course-tab:hover:not(.active) {
            background: #e9ecef;
        }
        
        .students-section {
            background: white;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .students-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #ddd;
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="welcome-section">
            <h2>Welcome, <?php echo $staff_name; ?>!</h2>
            <p>Position: <strong><?php echo $position; ?></strong></p>
            <?php if ($position === 'Academic Staff' && $course_id): ?>
                <?php
                $course_query = "SELECT course_name FROM course WHERE course_no = ?";;
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

        <?php if ($courses_result->num_rows > 0): ?>
            <div class="course-tabs">
                <?php
                $courses_result->data_seek(0);
                while ($course = $courses_result->fetch_assoc()):
                    $is_active = ($course['id'] == $selected_course_id) ? 'active' : '';
                ?>
                    <a href="?course_id=<?php echo $course['id']; ?>" class="course-tab <?php echo $is_active; ?>">
                        <?php echo $course['course_name']; ?>
                    </a>
                <?php endwhile; ?>
            </div>

            <div class="students-section">
                <div class="students-header">
                    <h3>Student Enrollments</h3>
                    <?php
                    if ($selected_course_id) {
                        $course_name_query = "SELECT course_name FROM course WHERE id = ?";
                        $course_name_stmt = $con->prepare($course_name_query);
                        $course_name_stmt->bind_param("i", $selected_course_id);
                        $course_name_stmt->execute();
                        $course_name_result = $course_name_stmt->get_result();
                        if ($course_name_result->num_rows > 0) {
                            $course_data = $course_name_result->fetch_assoc();
                            echo "<p>Course: <strong>" . $course_data['course_name'] . "</strong></p>";
                        }
                    }
                    ?>
                </div>

                <?php if (!empty($students)): ?>
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>NIC</th>
                                <th>Contact</th>
                                <th>Application Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['Student_id']; ?></td>
                                    <td><?php echo $student['full_name']; ?></td>
                                    <td><?php echo $student['nic']; ?></td>
                                    <td><?php echo $student['contact_no']; ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($student['application_date'])); ?></td>
                                    <td>
                                        <?php if ($student['is_processed']): ?>
                                            <span class="status-badge status-processed">Processed</span>
                                        <?php else: ?>
                                            <span class="status-badge status-pending">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-students">
                        <p>No student enrollments found for this course.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="welcome-section">
                <h3>No courses available</h3>
                <p>There are no active courses assigned to your account.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>