<?php
session_start();
include_once('../../include/connection.php'); // Database connection

// --- 1. Define Common Password ---
// ඔබට අවශ්‍ය පොදු මුරපදය මෙතන දාන්න.
define('DEFAULT_PASSWORD', 'NVTI@staff123'); 

// 2. Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 3. Retrieve data from the form
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $nic = trim($_POST['nic']);
    $service_id = trim($_POST['service_id']);
    $gender = $_POST['gender'];
    $position = $_POST['position'];
    $contact_no = trim($_POST['contact_no']); // <-- ADD THIS
    $email = trim($_POST['email']); // <-- ADD THIS
    
    // Handle course_id (it might be empty if Non-Academic)
    $course_no = !empty($_POST['course_no']) ? trim($_POST['course_no']) : NULL;

    // --- 4. Password Logic (NEW) ---
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    $password_to_hash = ''; // Initialize

    if (empty($password) && empty($confirm_password)) {
        // If both fields are empty, use the common (default) password
        $password_to_hash = DEFAULT_PASSWORD;
    } else {
        // If user is typing a password, they must match
        if ($password !== $confirm_password) {
            $_SESSION['error'] = "Passwords do not match.";
            header("Location: ../pages/staff_register.php");
            exit();
        }
        // Check min length if password is not empty
        if (strlen($password) < 6) {
             $_SESSION['error'] = "Password must be at least 6 characters long.";
             header("Location: ../pages/staff_register.php");
             exit();
        }
        // Use the user-provided password
        $password_to_hash = $password;
    }


    // --- 5. Validation (Check for Duplicates) ---
    $check_query = "SELECT * FROM staff WHERE nic = ? OR service_id = ?";
    $stmt_check = $con->prepare($check_query);
    $stmt_check->bind_param("ss", $nic, $service_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $_SESSION['error'] = "A user with this NIC or Service ID already exists.";
        header("Location:../pages/staff_register.php");
        exit();
    }

    // --- 6. Secure Password Hashing ---
    // Hash the password (either default or user-provided)
    $hashed_password = password_hash($password_to_hash, PASSWORD_DEFAULT);

    
    // --- 7. Handle Profile Photo Upload ---
    $profile_photo_name = NULL; // Default is NULL (no photo)

    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $upload_dir = '../../uploads/profile_photos/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed_exts)) {
            $profile_photo_name = 'NVTI-STAFF-' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $profile_photo_name;

            if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
                 $profile_photo_name = NULL; // Reset to NULL if upload fails
            }
        }
    }
    
    // --- 8. Generate Staff ID ---
    $year = date("Y");
    $rand_num = rand(1000, 9999);
    $staff_id = "NVTI-$year-$rand_num";

    // --- 9. Insert data into the database ---
    $insert_query = "INSERT INTO staff (staff_id, service_id, first_name, last_name, nic, contact_no, email, gender, password, position, course_no, profile_photo, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
                     
    $stmt_insert = $con->prepare($insert_query);
    
    $stmt_insert->bind_param(
    "ssssssssssss", // 'i' එක 's' (string) කළා
    $staff_id, 
    $service_id, 
    $first_name, 
    $last_name, 
    $nic,
    $contact_no,
    $email,
    $gender,
    $hashed_password, 
    $position, 
    $course_no, // $course_id වෙනුවට $course_no
    $profile_photo_name
);

    if ($stmt_insert->execute()) {
        $_SESSION['success'] = "Registration successful! You can now log in.";
        header("Location: ../pages/staff_register.php");
        exit();
    } else {
        $_SESSION['error'] = "Database error. Registration failed. " . $stmt_insert->error;
        header("Location: ../pages/staff_register.php");
        exit();
    }

} else {
    // If accessed directly
    header("Location: ../pages/staff_register.php");
    exit();
}
?>