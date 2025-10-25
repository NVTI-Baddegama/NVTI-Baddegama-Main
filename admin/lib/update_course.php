<?php
// Include your database connection file
include('../../include/connection.php');
session_start(); // Start the session if not already started

// if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php");
//     exit();
// }


// Check if the request is a POST request (i.e., the form was submitted)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Retrieve the submitted data from the form
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $course_fee = $_POST['course_fee'];
    $status = $_POST['status'];
    
    // --- NEW: Get instructor and course_no data ---
    $course_no = $_POST['course_no']; // The course's unique number
    $new_staff_id = $_POST['new_staff_id']; // The staff_id of the new instructor (or '' if unassigned)
    $old_staff_id = $_POST['old_staff_id']; // The staff_id of the old instructor (or '' if was unassigned)

    // Start a transaction
    mysqli_begin_transaction($con);

    try {
        // --- Step 1: Update the Course Details ---
        $query_course = "UPDATE course SET course_name = ?, course_fee = ?, status = ? WHERE id = ?";
        $stmt_course = mysqli_prepare($con, $query_course);
        if (!$stmt_course) throw new Exception("MySQLi Prepare Error: " . mysqli_error($con));
        
        mysqli_stmt_bind_param($stmt_course, "sssi", $course_name, $course_fee, $status, $course_id);
        if (!mysqli_stmt_execute($stmt_course)) throw new Exception("MySQLi Execute Error: " . mysqli_stmt_error($stmt_course));
        
        mysqli_stmt_close($stmt_course);

        // --- Step 2: Check if instructor was changed ---
        if ($new_staff_id !== $old_staff_id) {

            // --- Step 3: Unassign the OLD instructor (if one was assigned) ---
            if (!empty($old_staff_id)) {
                $query_unassign = "UPDATE staff SET course_no = NULL WHERE staff_id = ?";
                $stmt_unassign = mysqli_prepare($con, $query_unassign);
                if (!$stmt_unassign) throw new Exception("MySQLi Prepare Error: " . mysqli_error($con));

                mysqli_stmt_bind_param($stmt_unassign, "s", $old_staff_id);
                if (!mysqli_stmt_execute($stmt_unassign)) throw new Exception("MySQLi Execute Error: " . mysqli_stmt_error($stmt_unassign));
                
                mysqli_stmt_close($stmt_unassign);
            }

            // --- Step 4: Assign the NEW instructor (if one was selected) ---
            if (!empty($new_staff_id)) {
                $query_assign = "UPDATE staff SET course_no = ? WHERE staff_id = ?";
                $stmt_assign = mysqli_prepare($con, $query_assign);
                if (!$stmt_assign) throw new Exception("MySQLi Prepare Error: " . mysqli_error($con));

                mysqli_stmt_bind_param($stmt_assign, "ss", $course_no, $new_staff_id);
                if (!mysqli_stmt_execute($stmt_assign)) throw new Exception("MySQLi Execute Error: " . mysqli_stmt_error($stmt_assign));
                
                mysqli_stmt_close($stmt_assign);
            }
        }

        // --- Step 5: Commit the transaction ---
        mysqli_commit($con);
        
        // If successful, redirect back to the manage page with a success message
        header("Location: ../pages/manage_course.php?status=updated");
        exit();

    } catch (Exception $e) {
        // --- Step 6: Rollback on error ---
        mysqli_rollback($con);
        
        // Log the error (optional, but good for debugging)
        // error_log($e->getMessage());

        // Redirect with an error message
        header("Location: ../pages/manage_course.php?status=error");
        exit();
    }

} else {
    // If someone tries to access this file directly without submitting the form, redirect them
    header("Location: ../pages/manage_course.php");
    exit();
}

// Close the database connection
mysqli_close($con);
?>

