<?php
session_start();
include '../include/connection.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: ../log/login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $contact_no = trim($_POST['contact_no'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    
    // Handle profile photo upload
    $profile_photo = null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $upload_dir = '../uploads/profile_photos/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_types)) {
            $new_filename = 'NVTI-STAFF-' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
                $profile_photo = $new_filename;
            } else {
                $response['message'] = 'Failed to upload profile photo.';
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }
        } else {
            $response['message'] = 'Invalid file type. Please upload JPG, JPEG, PNG, or GIF files only.';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }
    
    // Build update query
    $update_fields = [];
    $params = [];
    $param_types = '';
    
    if (!empty($first_name)) {
        $update_fields[] = "first_name = ?";
        $params[] = $first_name;
        $param_types .= 's';
    }
    
    if (!empty($last_name)) {
        $update_fields[] = "last_name = ?";
        $params[] = $last_name;
        $param_types .= 's';
    }
    
    if (!empty($contact_no)) {
        $update_fields[] = "contact_no = ?";
        $params[] = $contact_no;
        $param_types .= 's';
    }
    
    if (!empty($email)) {
        $update_fields[] = "email = ?";
        $params[] = $email;
        $param_types .= 's';
    }
    
    if ($profile_photo) {
        $update_fields[] = "profile_photo = ?";
        $params[] = $profile_photo;
        $param_types .= 's';
    }
    
    if (!empty($new_password)) {
        $update_fields[] = "password = ?";
        $params[] = password_hash($new_password, PASSWORD_DEFAULT);
        $param_types .= 's';
    }
    
    if (!empty($update_fields)) {
        $params[] = $staff_id;
        $param_types .= 's';
        
        $query = "UPDATE staff SET " . implode(', ', $update_fields) . " WHERE staff_id = ?";
        
        try {
            $stmt = $con->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $con->error);
            }
            
            $stmt->bind_param($param_types, ...$params);
            
            if ($stmt->execute()) {
                // Update session variables
                if (!empty($first_name) && !empty($last_name)) {
                    $_SESSION['staff_name'] = $first_name . ' ' . $last_name;
                }
                if ($profile_photo) {
                    $_SESSION['profile_photo'] = $profile_photo;
                }
                
                $response['success'] = true;
                $response['message'] = 'Profile updated successfully!';
            } else {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $response['message'] = 'Error updating profile: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'No changes to update.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>