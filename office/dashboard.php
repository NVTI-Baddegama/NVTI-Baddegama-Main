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
$staff_id = $_SESSION['staff_id'];

// Check if login_status column exists, if not add it
$check_column = "SHOW COLUMNS FROM staff LIKE 'login_status'";
$column_result = $con->query($check_column);

if ($column_result->num_rows == 0) {
    // Add the column if it doesn't exist
    $add_column = "ALTER TABLE staff ADD COLUMN login_status TINYINT(1) DEFAULT 0";
    $con->query($add_column);
}

// Check if this is the first login
$first_login_check = "SELECT login_status FROM staff WHERE staff_id = ?";
$login_stmt = $con->prepare($first_login_check);
$login_stmt->bind_param("s", $staff_id);
$login_stmt->execute();
$login_result = $login_stmt->get_result();

$show_password_modal = false;
if ($login_result->num_rows > 0) {
    $login_data = $login_result->fetch_assoc();
    $show_password_modal = ($login_data['login_status'] == 0);
} else {
    // If no result, assume first login
    $show_password_modal = true;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - NVTI Baddegama</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
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
                <div class="profile-menu">
                    <?php if ($profile_photo): ?>
                        <img src="../uploads/profile_photos/<?php echo $profile_photo; ?>" alt="Profile" class="profile-photo">
                    <?php endif; ?>
                    <div>
                        <strong><?php echo $staff_name; ?></strong><br>
                        <small><?php echo $position; ?></small>
                    </div>
                    <div class="profile-dropdown">
                        <a href="#" onclick="openProfileModal()">Profile</a>
                        <a href="../admin/lib/logout.php">Logout</a>
                    </div>
                </div>
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
                            <th>Full Name</th>
                            <th>NIC</th>
                            <th>Contact</th>
                            <th>WhatsApp</th>
                            <th>Course Option 1</th>
                            <th>Course Option 2</th>
                            <th>Status</th>
                            <?php if ($position === 'Instructors'): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['nic']); ?></td>
                                <td><?php echo htmlspecialchars($student['contact_no']); ?></td>
                                <td><?php echo htmlspecialchars($student['whatsapp_no'] ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($student['course_option_one']); ?></td>
                                <td><?php echo htmlspecialchars($student['course_option_two'] ?: 'N/A'); ?></td>
                                <td>
                                    <?php if ($student['is_processed']): ?>
                                        <span class="status-badge status-processed">Processed</span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($position === 'Instructors'): ?>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if (!$student['is_processed']): ?>
                                                <button onclick="handleStudentAction(<?php echo $student['id']; ?>, 'accept')" class="btn btn-success">Accept</button>
                                            <?php endif; ?>
                                            <button onclick="handleStudentAction(<?php echo $student['id']; ?>, 'reject')" class="btn btn-danger">Reject</button>
                                        </div>
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

    <!-- Profile Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeProfileModal()">&times;</span>
            <h2>Update Profile</h2>
            <form id="profileForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profile_photo">Profile Photo:</label>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($current_staff['first_name']); ?>">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($current_staff['last_name']); ?>">
                </div>
                <div class="form-group">
                    <label for="contact_no">Contact Number:</label>
                    <input type="text" id="contact_no" name="contact_no" value="<?php echo htmlspecialchars($current_staff['contact_no']); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($current_staff['email']); ?>">
                </div>
                <div class="form-group">
                    <label for="new_password">New Password (leave blank to keep current):</label>
                    <input type="password" id="new_password" name="new_password">
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <!-- First Login Password Modal -->
    <?php if ($show_password_modal): ?>
    <div id="passwordModal" class="modal" style="display: block;">
        <div class="modal-content">
            <h2>Welcome! First Time Login</h2>
            <p>Would you like to change your password or keep the current one?</p>
            <form id="passwordForm">
                <div class="form-group">
                    <label for="modal_new_password">New Password:</label>
                    <input type="password" id="modal_new_password" name="new_password">
                </div>
                <div class="form-group">
                    <label for="modal_confirm_password">Confirm Password:</label>
                    <input type="password" id="modal_confirm_password" name="confirm_password">
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Change Password</button>
                    <button type="button" onclick="keepCurrentPassword()" class="btn btn-secondary">Keep Current</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Profile Modal Functions
        function openProfileModal() {
            document.getElementById('profileModal').style.display = 'block';
        }

        function closeProfileModal() {
            document.getElementById('profileModal').style.display = 'none';
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

        // Handle student actions
        function handleStudentAction(studentId, action) {
            const confirmMessage = action === 'accept' ? 
                'Are you sure you want to accept this student application?' : 
                'Are you sure you want to reject this student application?';
            
            if (confirm(confirmMessage)) {
                const formData = new FormData();
                formData.append('student_id', studentId);
                formData.append('action', action);
                
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

        // First login password modal functions
        <?php if ($show_password_modal): ?>
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('modal_new_password').value;
            const confirmPassword = document.getElementById('modal_confirm_password').value;
            
            if (!newPassword || !confirmPassword) {
                alert('Please fill in both password fields.');
                return;
            }
            
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
                    location.reload(); // Reload to hide the modal
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
                    location.reload(); // Reload to hide the modal
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
            
            // Prepare table data (removed Student ID, Date, and Status)
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
            
            // Add table (removed Student ID, Date, and Status columns)
            doc.autoTable({
                head: [['Name', 'NIC', 'Contact', 'WhatsApp', 'Course 1', 'Course 2']],
                body: tableData,
                startY: 50,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [0, 123, 255] }
            });
            
            // Save the PDF
            const filename = 'NVTI_Students_' + new Date().toISOString().split('T')[0] + '.pdf';
            doc.save(filename);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const profileModal = document.getElementById('profileModal');
            if (event.target == profileModal) {
                profileModal.style.display = 'none';
            }
        }
    </script>
</body>
</html>