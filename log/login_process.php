<?php
session_start();
include '../include/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = trim($_POST['staff_id']);
    $password = $_POST['password'];
    
    if (empty($staff_id) || empty($password)) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: login.php");
        exit();
    }
    
    // Check if login is by staff_id or service_id
    $query = "SELECT * FROM staff WHERE (staff_id = ? OR service_id = ?) AND status = 'active'";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ss", $staff_id, $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $staff = $result->fetch_assoc();
        
        if (password_verify($password, $staff['password'])) {
            // Login successful
            $_SESSION['staff_id'] = $staff['staff_id'];
            $_SESSION['staff_name'] = $staff['first_name'] . ' ' . $staff['last_name'];
            $_SESSION['position'] = $staff['position'];
            $_SESSION['course_id'] = $staff['course_id'];
            $_SESSION['profile_photo'] = $staff['profile_photo'];
            
            header("Location: ../office/dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password.";
        }
    } else {
        $_SESSION['error'] = "Invalid staff ID or service ID.";
    }
    
    header("Location: login.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>