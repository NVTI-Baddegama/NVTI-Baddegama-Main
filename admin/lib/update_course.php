<?php
// Include your database connection file
include('../../include/connection.php');
session_start();

// if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php");
//     exit();
// }

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Retrieve all data from the form
    $course_id = $_POST['course_id']; // The course table's primary key
    $course_name = $_POST['course_name'];
    $course_fee = $_POST['course_fee'];
    $status = $_POST['status'];
    
    // These are the key values for the instructor logic
    $course_no = $_POST['course_no'];       // The course's unique identifier (e.g., "ITD001")
    $new_staff_id = $_POST['new_staff_id']; // The newly selected staff_id (or "" if unassigned)
    $old_staff_id = $_POST['old_staff_id']; // The previously assigned staff_id (or "")

    // Start a database transaction
    mysqli_begin_transaction($con);

    try {
        // --- Step 1: Update the Course Details (name, fee, status) ---
        $query_course = "UPDATE course SET course_name = ?, course_fee = ?, status = ? WHERE id = ?";
        $stmt_course = mysqli_prepare($con, $query_course);
        if (!$stmt_course) throw new Exception("MySQLi Prepare Error (Course): " . mysqli_error($con));
        
        mysqli_stmt_bind_param($stmt_course, "sssi", $course_name, $course_fee, $status, $course_id);
        if (!mysqli_stmt_execute($stmt_course)) throw new Exception("MySQLi Execute Error (Course): " . mysqli_stmt_error($stmt_course));
        
        mysqli_stmt_close($stmt_course);

        // --- Step 2: Handle Instructor Assignment Changes ---
        // ALWAYS clear the course assignment first, then reassign if needed
        
        // Clear ALL staff assignments for this course
        $query_unassign_all = "UPDATE staff SET course_no = NULL WHERE course_no = ?";
        $stmt_unassign_all = mysqli_prepare($con, $query_unassign_all);
        if (!$stmt_unassign_all) throw new Exception("MySQLi Prepare Error (Unassign All): " . mysqli_error($con));
        
        mysqli_stmt_bind_param($stmt_unassign_all, "s", $course_no);
        if (!mysqli_stmt_execute($stmt_unassign_all)) throw new Exception("MySQLi Execute Error (Unassign All): " . mysqli_stmt_error($stmt_unassign_all));
        
        mysqli_stmt_close($stmt_unassign_all);

        // --- Step 3: Assign the new instructor (if one was selected) ---
        if (!empty($new_staff_id)) {
            // First, clear any existing course assignment for this staff member
            $query_clear_staff = "UPDATE staff SET course_no = NULL WHERE staff_id = ?";
            $stmt_clear_staff = mysqli_prepare($con, $query_clear_staff);
            if (!$stmt_clear_staff) throw new Exception("MySQLi Prepare Error (Clear Staff): " . mysqli_error($con));
            
            mysqli_stmt_bind_param($stmt_clear_staff, "s", $new_staff_id);
            if (!mysqli_stmt_execute($stmt_clear_staff)) throw new Exception("MySQLi Execute Error (Clear Staff): " . mysqli_stmt_error($stmt_clear_staff));
            
            mysqli_stmt_close($stmt_clear_staff);
            
            // Now assign the course to this staff member
            $query_assign = "UPDATE staff SET course_no = ? WHERE staff_id = ?";
            $stmt_assign = mysqli_prepare($con, $query_assign);
            if (!$stmt_assign) throw new Exception("MySQLi Prepare Error (Assign New): " . mysqli_error($con));

            mysqli_stmt_bind_param($stmt_assign, "ss", $course_no, $new_staff_id);
            if (!mysqli_stmt_execute($stmt_assign)) throw new Exception("MySQLi Execute Error (Assign New): " . mysqli_stmt_error($stmt_assign));
            
            mysqli_stmt_close($stmt_assign);
        }

        // --- Step 4: Commit the transaction ---
        mysqli_commit($con);
        
        // Redirect back with a success message
        header("Location: ../pages/manage_course.php?status=updated");
        exit();

    } catch (Exception $e) {
        // --- Step 5: Rollback on error ---
        mysqli_rollback($con);
        
        // Uncomment for debugging
        // echo "Transaction Failed: " . $e->getMessage();
        // die();
        
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