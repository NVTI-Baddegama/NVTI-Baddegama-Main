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
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $contact_no = trim($_POST['contact_no']);
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    
    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $response['message'] = 'First name, last name, and email are required.';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address.';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Check if email already exists for another user
    $email_check = "SELECT id FROM staff WHERE email = ? AND staff_id != ?";
    $email_stmt = $con->prepare($email_check);
    $email_stmt->bind_param("ss", $email, $staff_id);
    $email_stmt->execute();
    $email_result = $email_stmt->get_result();
    
    if ($email_result->num_rows > 0) {
        $response['message'] = 'Email address is already in use by another staff member.';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Handle profile photo upload
    $profile_photo = null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/profile_photos/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_info = pathinfo($_FILES['profile_photo']['name']);
        $extension = strtolower($file_info['extension']);
        
        // Validate file type
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($extension, $allowed_extensions)) {
            $response['message'] = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        // Validate file size (5MB max)
        if ($_FILES['profile_photo']['size'] > 5 * 1024 * 1024) {
            $response['message'] = 'File size must be less than 5MB.';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        // Generate unique filename
        $profile_photo = 'NVTI-STAFF-' . time() . '.' . $extension;
        $upload_path = $upload_dir . $profile_photo;
        
        if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
            $response['message'] = 'Failed to upload profile photo.';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        // Delete old profile photo if exists
        $old_photo_query = "SELECT profile_photo FROM staff WHERE staff_id = ?";
        $old_photo_stmt = $con->prepare($old_photo_query);
        $old_photo_stmt->bind_param("s", $staff_id);
        $old_photo_stmt->execute();
        $old_photo_result = $old_photo_stmt->get_result();
        $old_photo_data = $old_photo_result->fetch_assoc();
        
        if (!empty($old_photo_data['profile_photo'])) {
            $old_photo_path = $upload_dir . $old_photo_data['profile_photo'];
            if (file_exists($old_photo_path)) {
                unlink($old_photo_path);
            }
        }
    }
    
    // Prepare update query
    $update_fields = [];
    $params = [];
    $param_types = '';
    
    $update_fields[] = "first_name = ?";
    $params[] = $first_name;
    $param_types .= 's';
    
    $update_fields[] = "last_name = ?";
    $params[] = $last_name;
    $param_types .= 's';
    
    $update_fields[] = "contact_no = ?";
    $params[] = $contact_no;
    $param_types .= 's';
    
    $update_fields[] = "email = ?";
    $params[] = $email;
    $param_types .= 's';
    
    if ($profile_photo) {
        $update_fields[] = "profile_photo = ?";
        $params[] = $profile_photo;
        $param_types .= 's';
    }
    
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $response['message'] = 'Password must be at least 6 characters long.';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        $update_fields[] = "password = ?";
        $params[] = password_hash($new_password, PASSWORD_DEFAULT);
        $param_types .= 's';
    }
    
    $params[] = $staff_id;
    $param_types .= 's';
    
    $update_query = "UPDATE staff SET " . implode(", ", $update_fields) . " WHERE staff_id = ?";
    $update_stmt = $con->prepare($update_query);
    $update_stmt->bind_param($param_types, ...$params);
    
    if ($update_stmt->execute()) {
        // Update session data
        $_SESSION['staff_name'] = $first_name . ' ' . $last_name;
        if ($profile_photo) {
            $_SESSION['profile_photo'] = $profile_photo;
        }
        
        $response['success'] = true;
        $response['message'] = 'Profile updated successfully.';
    } else {
        $response['message'] = 'Error updating profile. Please try again.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>