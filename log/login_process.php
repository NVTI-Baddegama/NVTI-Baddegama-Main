<?php
session_start();
include '../include/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Input eka staff_id, service_id, username, ho email wennapuluwan
    $username = trim($_POST['staff_id']); 
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: login.php");
        exit();
    }

    // Mulimma staff table eke check karala balamu
    $query_staff = "SELECT * FROM staff WHERE (staff_id = ? OR service_id = ?) AND status = 'active'";
    $stmt_staff = $con->prepare($query_staff);
    $stmt_staff->bind_param("ss", $username, $username);
    $stmt_staff->execute();
    $result_staff = $stmt_staff->get_result();

    if ($result_staff->num_rows == 1) {
        $staff = $result_staff->fetch_assoc();
        if (password_verify($password, $staff['password'])) {
            // Staff login eka success!
            $_SESSION['staff_id'] = $staff['staff_id'];
            $_SESSION['staff_name'] = $staff['first_name'] . ' ' . $staff['last_name'];
            $_SESSION['position'] = $staff['position'];
            $_SESSION['course_id'] = $staff['course_id'];
            $_SESSION['profile_photo'] = $staff['profile_photo'];

            // Office dashboard ekata redirect karamu
            header("Location: ../office/dashboard.php");
            exit();
        }
    }

    // Staff table eke nattan, admin table eke check karamu 
    // (oyage database eke 'admin' kiyala table ekak thiyenawa kiyala mama hithanawa)
    $query_admin = "SELECT * FROM admin WHERE username = ? OR email = ?";
    $stmt_admin = $con->prepare($query_admin);
    $stmt_admin->bind_param("ss", $username, $username);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_admin->num_rows == 1) {
        $admin = $result_admin->fetch_assoc();
        // admin table eke password eka hash karala thiyenna ona
        if (password_verify($password, $admin['password'])) {
            // Admin login eka success!
            $_SESSION['admin_username'] = $admin['username'];
            // Admin dashboard ekata redirect karamu
            header("Location: ../admin/pages/Dashboard.php");
            exit();
        }
    }

    // Staffwath, Adminwath nemei nam, error ekak pennamu
    $_SESSION['error'] = "Invalid credentials.";
    header("Location: login.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>