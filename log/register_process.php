<?php
session_start();
include '../include/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $nic = trim($_POST['nic']);
    $service_id = trim($_POST['service_id']);
    $gender = $_POST['gender'];
    $position = $_POST['position'];
    $course_id = !empty($_POST['course_id']) ? $_POST['course_id'] : null;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($nic) || empty($service_id) || 
        empty($gender) || empty($position) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header("Location: register.php");
        exit();
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: register.php");
        exit();
    }
    
    if ($position === 'Academic Staff' && empty($course_id)) {
        $_SESSION['error'] = "Please select a course for Academic Staff position.";
        header("Location: register.php");
        exit();
    }
    
    // Check if NIC or Service ID already exists
    $check_query = "SELECT * FROM staff WHERE nic = ? OR service_id = ?";
    $check_stmt = $con->prepare($check_query);
    $check_stmt->bind_param("ss", $nic, $service_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "NIC or Service ID already exists.";
        header("Location: register.php");
        exit();
    }
    
    // Generate staff ID
    $staff_id = 'NVTI-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
    
    // Handle profile photo upload
    $profile_photo = null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $upload_dir = '../uploads/profile_photos/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $profile_photo = $staff_id . '.' . $file_extension;
        $upload_path = $upload_dir . $profile_photo;
        
        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($file_extension), $allowed_types)) {
            $_SESSION['error'] = "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
            header("Location: register.php");
            exit();
        }
        
        // Validate file size (max 5MB)
        if ($_FILES['profile_photo']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "File size too large. Maximum 5MB allowed.";
            header("Location: register.php");
            exit();
        }
        
        if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
            $_SESSION['error'] = "Failed to upload profile photo.";
            header("Location: register.php");
            exit();
        }
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert staff record
    $insert_query = "INSERT INTO staff (staff_id, profile_photo, first_name, last_name, nic, service_id, gender, position, course_id, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $con->prepare($insert_query);
    $insert_stmt->bind_param("ssssssssss", $staff_id, $profile_photo, $first_name, $last_name, $nic, $service_id, $gender, $position, $course_id, $hashed_password);
    
    if ($insert_stmt->execute()) {
        $_SESSION['success'] = "Registration successful! You can now login with your Staff ID: " . $staff_id;
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: register.php");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
?>