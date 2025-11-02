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
    $staff_id = $_SESSION['staff_id'];
    
    if (isset($_POST['keep_current'])) {
        // User chose to keep current password
        $update_query = "UPDATE staff SET login_status = 1 WHERE staff_id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("s", $staff_id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Welcome! You can change your password anytime from your profile.';
        } else {
            $response['message'] = 'Error updating login status.';
        }
    } else {
        // User wants to change password
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);
        
        // Validate passwords
        if (empty($new_password) || empty($confirm_password)) {
            $response['message'] = 'Please fill in both password fields.';
        } elseif ($new_password !== $confirm_password) {
            $response['message'] = 'Passwords do not match.';
        } elseif (strlen($new_password) < 6) {
            $response['message'] = 'Password must be at least 6 characters long.';
        } else {
            // Update password and login status
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE staff SET password = ?, login_status = 1 WHERE staff_id = ?";
            $stmt = $con->prepare($update_query);
            $stmt->bind_param("ss", $hashed_password, $staff_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Password changed successfully! Welcome to NVTI Baddegama.';
            } else {
                $response['message'] = 'Error updating password. Please try again.';
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>