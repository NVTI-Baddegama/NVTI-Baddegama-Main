<?php
session_start();
include_once '../../include/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method";
    header("Location: ../pages/staff_register.php");
    exit();
}

// Get form data
$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$nic = trim($_POST['nic']);
$service_id = trim($_POST['service_id']);
$gender = trim($_POST['gender']);
$contact_no = trim($_POST['contact_no']);
$email = trim($_POST['email']);
$position = trim($_POST['position']);
$password = !empty(trim($_POST['password'])) ? trim($_POST['password']) : 'NVTI@staff123';

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($nic) || empty($service_id) || 
    empty($gender) || empty($contact_no) || empty($email) || empty($position)) {
    $_SESSION['error'] = "All required fields must be filled";
    header("Location: ../pages/staff_register.php");
    exit();
}

// Validate NIC format
if (!preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $nic)) {
    $_SESSION['error'] = "Invalid NIC format";
    header("Location: ../pages/staff_register.php");
    exit();
}

// Validate contact number
if (!preg_match('/^[0-9]{10}$/', $contact_no)) {
    $_SESSION['error'] = "Invalid contact number format";
    header("Location: ../pages/staff_register.php");
    exit();
}

// Check for duplicate NIC, Service ID, or Email
$check_query = "SELECT * FROM staff WHERE nic = ? OR service_id = ? OR email = ?";
$check_stmt = $con->prepare($check_query);
$check_stmt->bind_param("sss", $nic, $service_id, $email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $_SESSION['error'] = "Staff member with this NIC, Service ID, or Email already exists";
    $check_stmt->close();
    header("Location: ../pages/staff_register.php");
    exit();
}
$check_stmt->close();

// Generate unique staff_id
do {
    $staff_id = 'NVTI-' . date('Y') . '-' . rand(1000, 9999);
    $id_check = $con->prepare("SELECT staff_id FROM staff WHERE staff_id = ?");
    $id_check->bind_param("s", $staff_id);
    $id_check->execute();
    $id_result = $id_check->get_result();
    $id_check->close();
} while ($id_result->num_rows > 0);

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Handle profile photo upload
$profile_photo = null;
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
    $file_tmp = $_FILES['profile_photo']['tmp_name'];
    $file_size = $_FILES['profile_photo']['size'];
    $file_ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
    $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

    if ($file_size > 1 * 1024 * 1024) {
        $_SESSION['error'] = "Profile photo size exceeds 1MB limit";
        header("Location: ../pages/staff_register.php");
        exit();
    }

    if (in_array($file_ext, $allowed_exts)) {
        $profile_photo = 'NVTI-STAFF-' . time() . '.' . $file_ext;
        $upload_path = '../../uploads/profile_photos/' . $profile_photo;
        
        if (!move_uploaded_file($file_tmp, $upload_path)) {
            $_SESSION['error'] = "Failed to upload profile photo";
            header("Location: ../pages/staff_register.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid file type for profile photo";
        header("Location: ../pages/staff_register.php");
        exit();
    }
}

// Insert staff member
$insert_query = "INSERT INTO staff (staff_id, service_id, first_name, last_name, nic, contact_no, email, gender, password, position, profile_photo, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
$insert_stmt = $con->prepare($insert_query);
$insert_stmt->bind_param("sssssssssss", $staff_id, $service_id, $first_name, $last_name, $nic, $contact_no, $email, $gender, $hashed_password, $position, $profile_photo);

if (!$insert_stmt->execute()) {
    $_SESSION['error'] = "Failed to register staff member: " . $insert_stmt->error;
    $insert_stmt->close();
    header("Location: ../pages/staff_register.php");
    exit();
}
$insert_stmt->close();

// Handle course assignments for Instructors/Senior Instructors
$is_instructor = in_array($position, ['Instructor', 'Senior Instructor']);
if ($is_instructor && isset($_POST['course_nos']) && is_array($_POST['course_nos'])) {
    $course_nos = array_filter($_POST['course_nos']); // Remove empty values
    
    if (!empty($course_nos)) {
        $course_insert_query = "INSERT INTO instructor_courses (staff_id, course_no, assigned_date, status) VALUES (?, ?, NOW(), 'active')";
        $course_stmt = $con->prepare($course_insert_query);
        
        foreach ($course_nos as $course_no) {
            $course_no = trim($course_no);
            if (!empty($course_no)) {
                // Check if already assigned (shouldn't happen, but safety check)
                $check_course = $con->prepare("SELECT id FROM instructor_courses WHERE staff_id = ? AND course_no = ?");
                $check_course->bind_param("ss", $staff_id, $course_no);
                $check_course->execute();
                $check_result = $check_course->get_result();
                $check_course->close();
                
                if ($check_result->num_rows == 0) {
                    $course_stmt->bind_param("ss", $staff_id, $course_no);
                    $course_stmt->execute();
                }
            }
        }
        $course_stmt->close();
    }
}

$_SESSION['success'] = "Staff member registered successfully! Staff ID: " . $staff_id;
$con->close();
header("Location: ../pages/staff_register.php");
exit();
?>