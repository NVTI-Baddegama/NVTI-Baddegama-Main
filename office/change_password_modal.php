<?php
session_start();
include '../include/connection.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$staff_id = $_SESSION['staff_id'];
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // First, check if login_status column exists, if not add it
        $check_column = "SHOW COLUMNS FROM staff LIKE 'login_status'";
        $column_result = $con->query($check_column);
        
        if ($column_result->num_rows == 0) {
            // Add the column if it doesn't exist
            $add_column = "ALTER TABLE staff ADD COLUMN login_status TINYINT(1) DEFAULT 0";
            $con->query($add_column);
        }
        
        $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
        $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
        $keep_current = isset($_POST['keep_current']) ? true : false;
        
        if ($keep_current) {
            // Update login status to 1 (logged in before)
            $update_query = "UPDATE staff SET login_status = 1 WHERE staff_id = ?";
            $stmt = $con->prepare($update_query);
            
            if ($stmt) {
                $stmt->bind_param("s", $staff_id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Password kept unchanged.';
                } else {
                    $response['message'] = 'Error updating login status: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $response['message'] = 'Error preparing statement: ' . $con->error;
            }
        } else {
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
                
                if ($stmt) {
                    $stmt->bind_param("ss", $hashed_password, $staff_id);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Password updated successfully!';
                    } else {
                        $response['message'] = 'Error updating password: ' . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $response['message'] = 'Error preparing statement: ' . $con->error;
                }
            }
        }
    } catch (Exception $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method';
}

header('Content-Type: application/json');
echo json_encode($response);
?>