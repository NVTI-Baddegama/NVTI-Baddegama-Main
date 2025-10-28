<?php
session_start();
include_once('../../include/connection.php'); 

// 1. Check if ID and Action are provided
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['action']) || empty($_GET['action'])) {
    $_SESSION['error_msg'] = "Invalid action or student ID.";
    header("Location: ../pages/manage_students.php");
    exit();
}

$id = (int)$_GET['id']; // Cast to integer
$action = trim($_GET['action']);

// 2. Perform the action
if ($action == 'process') {
    // --- Mark as Processed ---
    $update_query = "UPDATE student_enrollments SET is_processed = 1 WHERE id = ?"; // Use id
    $stmt = $con->prepare($update_query);
    $stmt->bind_param("i", $id); // Bind integer 'i'
    
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Student (ID: $id) has been marked as 'Processed'.";
    } else {
        $_SESSION['error_msg'] = "Database error. Could not update student status.";
    }
    $stmt->close();

} elseif ($action == 'delete') {
    // --- Delete the Application ---
    $delete_query = "DELETE FROM student_enrollments WHERE id = ?"; // Use id
    $stmt = $con->prepare($delete_query);
    $stmt->bind_param("i", $id); // Bind integer 'i'

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Student application (ID: $id) has been deleted.";
    } else {
        $_SESSION['error_msg'] = "Database error. Could not delete student application.";
    }
    $stmt->close();

} else {
    $_SESSION['error_msg'] = "Unknown action.";
}

$con->close();
header("Location: ../pages/manage_students.php");
exit();
?>