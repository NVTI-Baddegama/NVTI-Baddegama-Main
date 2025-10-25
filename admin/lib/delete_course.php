<?php
// Include your database connection file
include('../../include/connection.php');

// 1. Check if an ID is present in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    // Sanitize the ID to be sure it's an integer
    $course_id = intval($_GET['id']);

    // 2. Prepare the SQL DELETE statement with a placeholder
    $query = "DELETE FROM course WHERE id = ?";

    // Initialize a prepared statement
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        // 3. Bind the integer ID to the placeholder
        mysqli_stmt_bind_param($stmt, "i", $course_id);

        // 4. Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // If successful, redirect back to the manage page with a success message
            header("Location: ../pages/manage_course.php?status=deleted");
            exit();
        } else {
            // --- CHANGE HERE ---
            // If execution fails, redirect with an error message
            // echo "Error deleting record: " . mysqli_stmt_error($stmt); // (Good for debugging)
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
    // If no ID is provided in the URL, just redirect back
    header("Location: ../pages/manage_course.php");
    exit();
}

// Close the database connection
mysqli_close($con);
?>