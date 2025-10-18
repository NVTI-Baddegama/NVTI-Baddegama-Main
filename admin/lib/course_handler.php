<?php
session_start();
include '../../include/connection.php'; // $con යනු mysqli object එකකි

if (!$con) {
    // Connection error is handled in connection.php
    die("Database connection failed.");
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. Retrieve Text Data ---
    $course_no = trim($_POST['course_no']);
    $course_name = trim($_POST['course_name']);
    $nvq_level = trim($_POST['nvq_level']);
    $course_type = trim($_POST['course_type']);
    $qualifications = trim($_POST['qualifications']);
    $course_duration = trim($_POST['course_duration']); // This is a number
    $course_fee = trim($_POST['course_fee']);
    $course_description = trim($_POST['course_description']);

    $new_file_name = null; // Initialize image file name as null

    // --- 2. Handle File Upload ---
    if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] == 0) {
        
        $upload_dir = '../../uploads/course_images/'; // Path from 'admin/lib' to 'uploads/course_images'
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = basename($_FILES['course_image']['name']);
        $file_tmp = $_FILES['course_image']['tmp_name'];
        $file_size = $_FILES['course_image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
        
        // Validate file type
        if (in_array($file_ext, $allowed_exts)) {
            // Validate file size (e.g., 5MB limit)
            if ($file_size < 5 * 1024 * 1024) {
                // Create a unique file name
                $new_file_name = 'course_img_' . uniqid() . '_' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_file_name;

                // Move the file
                if (!move_uploaded_file($file_tmp, $upload_path)) {
                    $_SESSION['error'] = "Failed to move uploaded file.";
                    header("Location: ../pages/add_course.php?error=upload_move_failed");
                    exit();
                }
            } else {
                $_SESSION['error'] = "File is too large. Max 5MB allowed.";
                header("Location: ../pages/add_course.php?error=file_too_large");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, PNG, WEBP allowed.";
            header("Location: ../pages/add_course.php?error=invalid_file_type");
            exit();
        }
    }

    // --- 3. Validation (Basic) ---
    if (empty($course_no) || empty($course_name) || empty($nvq_level) || empty($course_type) || empty($qualifications) || empty($course_duration)) {
        $_SESSION['error'] = "All required fields must be filled.";
        header("Location: ../pages/add_course.php?error=emptyfields");
        exit();
    }

    // --- 4. Check for Duplicate Course Number ---
    $check_query = "SELECT id FROM course WHERE course_no = ?";
    $stmt_check = $con->prepare($check_query);
    $stmt_check->bind_param("s", $course_no);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $_SESSION['error'] = "A course with this Course Number already exists.";
        header("Location: ../pages/add_course.php?error=duplicate_course_no");
        exit();
    }
    $stmt_check->close();


    // --- 5. Prepare SQL Insert Statement ---
    // Added 'course_image' column and 'status'
    $insert_query = "INSERT INTO course (
        course_no, course_name, nvq_level, course_type, 
        qualifications, course_duration, course_fee, course_description, 
        course_image, status
    ) VALUES (
        ?, ?, ?, ?, 
        ?, ?, ?, ?, 
        ?, 'active'
    )";

    $stmt_insert = $con->prepare($insert_query);

    if (!$stmt_insert) {
        $_SESSION['error'] = "Database query preparation failed (insert).";
        header("Location: ../pages/add_course.php?error=prepare_failed_insert");
        exit();
    }

    // --- 6. Bind Parameters ---
    // bind_param string changed from "ssisssss" to "ssissssss" (added 's' for course_image)
    $stmt_insert->bind_param(
        "ssissssss",
        $course_no,
        $course_name,
        $nvq_level,
        $course_type,
        $qualifications,
        $course_duration,
        $course_fee,
        $course_description,
        $new_file_name // This will be the file name or NULL
    );

    // --- 7. Execute Statement and Redirect ---
    if ($stmt_insert->execute()) {
        $_SESSION['success'] = "New course added successfully!";
        header("Location: ../pages/add_course.php?success=added");
        exit();
    } else {
        $_SESSION['error'] = "Failed to add the new course. Please try again.";
        header("Location: ../pages/add_course.php?error=insert_failed");
        exit();
    }

    // --- 8. Close connections ---
    $stmt_insert->close();
    $con->close();

} else {
    // If accessed directly without POST
    $_SESSION['error'] = "Invalid access method.";
    header("Location: ../pages/add_course.php?error=invalid_access");
    exit();
}
?>