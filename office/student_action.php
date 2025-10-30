<?php
session_start();
include '../include/connection.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: ../log/login.php");
    exit();
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $action = $_POST['action']; // 'accept' or 'reject'
    
    if ($action === 'accept') {
        // Update student status to processed (accepted)
        $update_query = "UPDATE student_enrollments SET is_processed = 1 WHERE id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("i", $student_id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Student application accepted successfully.';
        } else {
            $response['message'] = 'Error accepting student application.';
        }
    } elseif ($action === 'reject') {
        // Delete student application (reject)
        $delete_query = "DELETE FROM student_enrollments WHERE id = ?";
        $stmt = $con->prepare($delete_query);
        $stmt->bind_param("i", $student_id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Student application rejected and removed successfully.';
        } else {
            $response['message'] = 'Error rejecting student application.';
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>