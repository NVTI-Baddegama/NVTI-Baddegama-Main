<?php
session_start();
include '../include/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $login_identifier = trim($_POST['staff_id']); // This can be staff_id, service_id, or username/email
    $password = $_POST['password'];

    if (empty($login_identifier) || empty($password)) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: login.php");
        exit();
    }
    
    // --- 1. Staff Check ---
    // Try to log in as a Staff member first
    $query_staff = "SELECT * FROM staff WHERE (staff_id = ? OR service_id = ? OR email = ?) AND status = 'active'";
    $stmt_staff = $con->prepare($query_staff);
    $stmt_staff->bind_param("sss", $login_identifier, $login_identifier, $login_identifier);
    $stmt_staff->execute();
    $result_staff = $stmt_staff->get_result();

    if ($result_staff->num_rows == 1) {
        $staff = $result_staff->fetch_assoc();
        
        // Check Password
        if (password_verify($password, $staff['password'])) {
            
            // Check if this staff member is ALSO an 'admin' type
            if (isset($staff['type']) && $staff['type'] === 'admin') {
                 // --- LOGIN AS ADMIN (from staff table) ---
                $_SESSION['admin_username'] = $staff['first_name']; // Use first_name
                $_SESSION['admin_type'] = $staff['type'];
                header("Location: ../admin/pages/Dashboard.php");
                exit();
            } else {
                 // --- LOGIN AS NORMAL STAFF (from staff table) ---
                $_SESSION['staff_id'] = $staff['staff_id'];
                $_SESSION['staff_name'] = $staff['first_name'] . ' ' . $staff['last_name'];
                $_SESSION['position'] = $staff['position'];
                $_SESSION['course_no'] = $staff['course_no'];
                $_SESSION['profile_photo'] = $staff['profile_photo'];
                
                // Determine staff type based on position
                $staff_type = '';
                if (in_array($staff['position'], ['Instructor', 'Senior Instructor'])) {
                    $staff_type = 'Academic Staff';
                } elseif (in_array($staff['position'], ['Non-Academic Staff', 'Management Assistant', 'Program Officer', 'Finance Officer', 'Training Officer', 'Driver'])) {
                    $staff_type = 'Non-Academic Staff';
                }
                $_SESSION['staff_type'] = $staff_type;
                
                header("Location: ../office/dashboard.php");
                exit();
            }
        }
    }

    // --- 2. Admin Table Check (Fallback) ---
    // If login failed as Staff, check the separate 'admin' table
    $query_admin = "SELECT * FROM admin WHERE (username = ? OR email = ?)";
    $stmt_admin = $con->prepare($query_admin);
    $stmt_admin->bind_param("ss", $login_identifier, $login_identifier);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_admin->num_rows == 1) {
        $admin = $result_admin->fetch_assoc();

        // Check Password
        if (password_verify($password, $admin['password'])) {
            // --- LOGIN AS ADMIN (from admin table) ---
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_type'] = $admin['type']; // Get type from admin table
            header("Location: ../admin/pages/Dashboard.php");
            exit();
        }
    }

    // --- Login Failed ---
    // If both checks fail, show error
    $_SESSION['error'] = "Invalid Login ID, Password, or account is inactive.";
    header("Location: login.php");
    exit();

} else {
    header("Location: login.php");
    exit();
}
?>