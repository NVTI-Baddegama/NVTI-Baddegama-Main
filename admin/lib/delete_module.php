<?php
include('../../include/connection.php');
session_start();

// Optional: Add admin role check if needed
// if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php");
//     exit();
// }

$redirect_course_id = ''; // Default redirect if things go wrong
$module_id = 0;

// 1. Check if the request is a GET request and if ID is set
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && !empty($_GET['id'])) {

    // 2. Sanitize the Module ID
    $module_id = intval($_GET['id']);

    // 3. --- Important: Get the course_id BEFORE deleting ---
    // We need this so we can redirect back to the correct filtered course page.
    $query_get_course = "SELECT course_id FROM modules WHERE id = ?";
    $stmt_get_course = mysqli_prepare($con, $query_get_course);
    
    if ($stmt_get_course) {
        mysqli_stmt_bind_param($stmt_get_course, "i", $module_id);
        mysqli_stmt_execute($stmt_get_course);
        $result = mysqli_stmt_get_result($stmt_get_course);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $redirect_course_id = $row['course_id'];
        }
        mysqli_stmt_close($stmt_get_course);
    }

    // If we couldn't find a course_id, we can't redirect properly,
    // but we can still proceed with deletion if the ID is valid.
    // A redirect_course_id of '' will just send back to the main manage_modules page.

    // 4. Prepare the SQL DELETE statement
    $query_delete = "DELETE FROM modules WHERE id = ?";
    $stmt_delete = mysqli_prepare($con, $query_delete);

    if ($stmt_delete) {
        // 5. Bind the Module ID parameter
        mysqli_stmt_bind_param($stmt_delete, "i", $module_id);

        // 6. Execute the deletion
        if (mysqli_stmt_execute($stmt_delete)) {
            // Success
            header("Location: ../pages/manage_modules.php?course_id=" . $redirect_course_id . "&status=delete_success");
            exit();
        } else {
            // Deletion failed
            header("Location: ../pages/manage_modules.php?course_id=" . $redirect_course_id . "&status=delete_error");
            exit();
        }

        // 7. Close the statement
        mysqli_stmt_close($stmt_delete);

    } else {
        // SQL preparation failed
        header("Location: ../pages/manage_modules.php?course_id=" . $redirect_course_id . "&status=sql_error");
        exit();
    }

} else {
    // If not a GET request or no ID, just redirect back to the main page
    header("Location: ../pages/manage_modules.php");
    exit();
}

// 8. Close the database connection
mysqli_close($con);
?>
