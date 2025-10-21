<?php
session_start();
include_once('../../include/connection.php'); 


if (!isset($_GET['nic']) || empty($_GET['nic']) || !isset($_GET['action']) || empty($_GET['action'])) {
    $_SESSION['error_msg'] = "Invalid action or student NIC.";
    header("Location: ../pages/manage_students.php");
    exit();
}

$nic = trim($_GET['nic']);
$action = trim($_GET['action']);

if ($action == 'process') {
    $update_query = "UPDATE student_enrollments SET is_processed = 1 WHERE nic = ?";
    $stmt = $con->prepare($update_query);
    $stmt->bind_param("s", $nic);
    
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Student (NIC: $nic) has been marked as 'Processed'.";
    } else {
        $_SESSION['error_msg'] = "Database error. Could not update student status.";
    }
    $stmt->close();

} elseif ($action == 'delete') {
    $delete_query = "DELETE FROM student_enrollments WHERE nic = ?";
    $stmt = $con->prepare($delete_query);
    $stmt->bind_param("s", $nic);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Student application (NIC: $nic) has been deleted.";
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