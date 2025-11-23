<?php
session_start();
include_once('../../include/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_type = $_POST['user_type'];
    $identifier = $_POST['id']; // This is staff_id OR old_username
    $redirect_url = "../pages/profile_update.php";

    // Password validation logic
    $password_sql = "";
    $params = [];
    $types = "";

    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            $_SESSION['update_error'] = "New password and confirmation do not match.";
            header("Location: $redirect_url");
            exit();
        }
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $password_sql = ", password = ?";
    }

    // === HANDLE ORIGINAL ADMIN UPDATE ===
    if ($user_type === 'original_admin') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        if (empty($username) || empty($email)) {
            $_SESSION['update_error'] = "Username and Email are required.";
            header("Location: $redirect_url"); exit();
        }

        // UPDATE QUERY USING USERNAME AS KEY
        $query = "UPDATE admin SET username = ?, email = ? $password_sql WHERE username = ?";
        
        $params[] = $username;
        $params[] = $email;
        $types = "ss";

        if (!empty($password_sql)) {
            $params[] = $hashed_password;
            $types .= "s";
        }
        
        $params[] = $identifier; // This is the old username
        $types .= "s";

        // Update Session Name immediately
        $_SESSION['admin_username'] = $username;

    // === HANDLE STAFF ADMIN UPDATE ===
    } elseif ($user_type === 'staff_admin') {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $contact_no = trim($_POST['contact_no']);
        
        // Photo Upload Handling
        $profile_photo_sql = "";
        $old_photo = $_POST['old_profile_photo'];
        $new_photo_name = $old_photo;

        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['profile_photo']['name'];
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($file_ext, $allowed)) {
                $new_photo_name = 'NVTI-STAFF-' . time() . '.' . $file_ext;
                $destination = '../../uploads/profile_photos/' . $new_photo_name;

                if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $destination)) {
                    $profile_photo_sql = ", profile_photo = ?";
                    // Delete old photo if exists and different
                    if (!empty($old_photo) && file_exists('../../uploads/profile_photos/' . $old_photo)) {
                        @unlink('../../uploads/profile_photos/' . $old_photo);
                    }
                }
            } else {
                $_SESSION['update_error'] = "Invalid file type. Only JPG, PNG, WEBP allowed.";
                header("Location: $redirect_url"); exit();
            }
        }

        $query = "UPDATE staff SET first_name = ?, last_name = ?, email = ?, contact_no = ? $profile_photo_sql $password_sql WHERE staff_id = ?";
        
        $params[] = $first_name;
        $params[] = $last_name;
        $params[] = $email;
        $params[] = $contact_no;
        $types = "ssss";

        if (!empty($profile_photo_sql)) {
            $params[] = $new_photo_name;
            $types .= "s";
        }
        
        if (!empty($password_sql)) {
            $params[] = $hashed_password;
            $types .= "s";
        }

        $params[] = $identifier; // This is the staff_id
        $types .= "s";

        // Update Session Name immediately if first name changed
        if (!empty($first_name)) {
             $_SESSION['admin_username'] = $first_name;
        }
        
    } else {
        $_SESSION['update_error'] = "Invalid user type.";
        header("Location: $redirect_url"); exit();
    }

    // Execute Query
    $stmt = $con->prepare($query);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            $_SESSION['update_success'] = "Profile updated successfully!";
        } else {
            $_SESSION['update_error'] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['update_error'] = "Query preparation failed: " . $con->error;
    }

    header("Location: $redirect_url");
    exit();

} else {
    header("Location: ../pages/profile_update.php");
    exit();
}
?>