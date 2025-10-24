<?php
session_start();
include '../include/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $login_identifier = trim($_POST['staff_id']);
    $password = $_POST['password'];

    if (empty($login_identifier) || empty($password)) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: login.php");
        exit();
    }
    

    // --- Staff Check ---
    $query_staff = "SELECT * FROM staff WHERE (staff_id = ? OR service_id = ?) AND status = 'active'";
    $stmt_staff = $con->prepare($query_staff);
    $stmt_staff->bind_param("ss", $login_identifier, $login_identifier);
    $stmt_staff->execute();
    $result_staff = $stmt_staff->get_result();

    if ($result_staff->num_rows == 1) {
    $staff = $result_staff->fetch_assoc();
    
    // NEW Check:
    if (password_verify($password, $staff['password'])) {
        $_SESSION['staff_id'] = $staff['staff_id'];
         $_SESSION['staff_name'] = $staff['first_name'] . ' ' . $staff['last_name'];
            $_SESSION['position'] = $staff['position'];
            $_SESSION['course_no'] = $staff['course_no'];
            $_SESSION['profile_photo'] = $staff['profile_photo'];
        header("Location: ../office/dashboard.php");
        exit();
    }
}


  


    // --- Admin Check ---
    $query_admin = "SELECT * FROM admin WHERE (username = ? OR email = ?)";
    $stmt_admin = $con->prepare($query_admin);
    $stmt_admin->bind_param("ss", $login_identifier, $login_identifier);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_admin->num_rows == 1) {
    $admin = $result_admin->fetch_assoc();

    // NEW Check:
    if (password_verify($password, $admin['password'])) {
        $_SESSION['type']=$admin['type'];

        $_SESSION['admin_username'] = $admin['username'];
        header("Location: ../admin/pages/Dashboard.php");
        exit();
    }
}

   

   

    // --- Login Failed ---
    $_SESSION['error'] = "Invalid Staff ID, Service ID, or Password.";
    header("Location: login.php");
    exit();

} else {
    header("Location: login.php");
    exit();
}
?>