<?php
// Include your database connection file
include('../../include/connection.php');
session_start();

// if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php");
//     exit();
// }

$upload_dir = '../../uploads/course_images/'; 
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Retrieve all data from the form
    $course_id = $_POST['course_id']; // The course table's primary key
    $course_name = $_POST['course_name'];
    $course_fee = $_POST['course_fee'];
    $status = $_POST['status'];
    
    $new_course_no = $_POST['course_no'];    // The (potentially updated) course number
    $course_duration = $_POST['course_duration'];
    $current_image = $_POST['current_image']; // The existing image filename
    
    // Get the original course number
    $old_course_no = $_POST['old_course_no']; // The course number *before* editing

    $qualifications = $_POST['qualifications'];
    $video = $_POST['course_video'];

    $new_image_filename = $current_image; // Default to old image

    // --- Handle File Upload ---
    if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] == 0) {
        $file = $_FILES['course_image'];
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        $new_image_filename = 'course_' . time() . '_' . uniqid() . '.' . $file_extension;
        $target_path = $upload_dir . $new_image_filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            // File upload success
            if (!empty($current_image) && file_exists($upload_dir . $current_image)) {
                unlink($upload_dir . $current_image);
            }
        } else {
            // File upload failed, keep the old image
            $new_image_filename = $current_image;
        }
    }

    // Start a database transaction
    mysqli_begin_transaction($con);

    try {
        // --- Step 1: Update the Course Details (name, fee, status, AND new fields) ---
       // Replace the Step 1 section in update_course.php with this:

// --- Step 1: Update the Course Details ---
            $query_course = "UPDATE course 
                             SET course_name = ?, 
                                 course_fee = ?, 
                                 status = ?, 
                                 course_no = ?, 
                                 course_duration = ?,
                                 qualifications = ?,
                                 course_video = ?,
                                 course_image = ? 
                             WHERE id = ?";
            
            $stmt_course = mysqli_prepare($con, $query_course);
            if (!$stmt_course) throw new Exception("MySQLi Prepare Error (Course): " . mysqli_error($con));
            
            // FIX: Correct binding - 8 strings (s) and 1 integer (i)
            mysqli_stmt_bind_param($stmt_course, "ssssisssi", 
                $course_name,        // s - string
                $course_fee,         // s - string
                $status,             // s - string
                $new_course_no,      // s - string
                $course_duration,    // i - integer
                $qualifications,     // s - string
                $video,              // s - string (THIS WAS THE ISSUE)
                $new_image_filename, // s - string
                $course_id           // i - integer
            );
            
            if (!mysqli_stmt_execute($stmt_course)) throw new Exception("MySQLi Execute Error (Course): " . mysqli_stmt_error($stmt_course));
            mysqli_stmt_close($stmt_course);


        // --- Step 2: Migrate existing staff if course_no changed ---
        // (This part remains, as it's necessary for data integrity if course_no changes)
        if ($new_course_no !== $old_course_no) {
            $query_migrate_staff = "UPDATE staff SET course_no = ? WHERE course_no = ?";
            $stmt_migrate_staff = mysqli_prepare($con, $query_migrate_staff);
            if (!$stmt_migrate_staff) throw new Exception("MySQLi Prepare Error (Migrate Staff): " . mysqli_error($con));
            
            mysqli_stmt_bind_param($stmt_migrate_staff, "ss", $new_course_no, $old_course_no);
            if (!mysqli_stmt_execute($stmt_migrate_staff)) throw new Exception("MySQLi Execute Error (Migrate Staff): " . mysqli_stmt_error($stmt_migrate_staff));
            
            mysqli_stmt_close($stmt_migrate_staff);
        }
        
        // --- Step 3: Commit the transaction --- (Original Step 5)
        mysqli_commit($con);
        
        // Redirect back with a success message
        header("Location: ../pages/manage_course.php?status=updated");
        exit();

    } catch (Exception $e) {
        // --- Step 4: Rollback on error --- (Original Step 6)
        mysqli_rollback($con);
        
        
        // Redirect with a generic error message
        header("Location: ../pages/manage_course.php?status=error");
        exit();
    }

} else {
    // If someone tries to access this file directly, redirect them
    header("Location: ../pages/manage_course.php");
    exit();
}

// Close the database connection
mysqli_close($con);
?>