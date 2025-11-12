<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: ../log/login.php");
    exit();
}

// Check if user is Instructor or Senior Instructor
$position = $_SESSION['position'] ?? '';

if (!in_array($position, ['Instructor', 'Senior Instructor'])) {
    // User is not authorized to access the office dashboard
    session_destroy();
    header("Location: ../log/login.php?error=" . urlencode("Access denied. Only Instructors and Senior Instructors can access this dashboard."));
    exit();
}

// If authorized, redirect to dashboard
header("Location: dashboard.php");
exit();
?>