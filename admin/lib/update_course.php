<?php
// Include your database connection file
include('../../include/connection.php');

// Check if the request is a POST request (i.e., the form was submitted)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Retrieve the submitted data from the form
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $course_fee = $_POST['course_fee'];
    $status = $_POST['status'];

    // 2. Prepare the SQL UPDATE statement with placeholders (?)
    $query = "UPDATE course SET course_name = ?, course_fee = ?, status = ? WHERE id = ?";
    
    // Initialize a prepared statement
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
        // 3. Bind the variables to the placeholders in the prepared statement
        mysqli_stmt_bind_param($stmt, "sssi", $course_name, $course_fee, $status, $course_id);
        
        // 4. Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // If successful, redirect back to the manage page with a success message
            header("Location: ../pages/manage_course.php?status=updated");
            exit();
        } else {
            // --- CHANGE HERE ---
            // If execution fails, redirect with an error message
            // echo "Error updating record: " . mysqli_stmt_error($stmt); // (Good for debugging)
            header("Location: ../pages/manage_course.php?status=error");
            exit();
        }
        
        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // echo "Error preparing statement: " . mysqli_error($con); // (Good for debugging)
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