<?php
include('../../include/connection.php');
session_start();

// Optional: Add admin role check if needed
// if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php");
//     exit();
// }

// 1. Check if the form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Check if the required fields are set and not empty
    if (isset($_POST['module_id']) && isset($_POST['module_name']) && isset($_POST['course_id']) && 
        !empty($_POST['module_id']) && !empty($_POST['module_name']) && !empty($_POST['course_id'])) {

        // 3. Sanitize and retrieve the data
        $module_id = intval($_POST['module_id']);
        $module_name = $_POST['module_name']; // Handled by prepared statement
        $course_id = intval($_POST['course_id']);

        // Handle optional order_no (now a string).
        $order_no_input = $_POST['order_no'] ?? null;
        $order_no = ($order_no_input !== null && $order_no_input !== '') ? $order_no_input : null; // Save as string or NULL

        // 4. Prepare the SQL UPDATE statement to prevent SQL injection
        $query = "UPDATE modules SET module_name = ?, course_id = ?, order_no = ? WHERE id = ?";
        
        $stmt = mysqli_prepare($con, $query);

        if ($stmt) {
            // 5. Bind the parameters
            // "sisi" means:
            // s: module_name (string)
            // i: course_id (integer)
            // s: order_no (string)
            // i: module_id (integer)
            mysqli_stmt_bind_param($stmt, "sisi", $module_name, $course_id, $order_no, $module_id);

            // 6. Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // Success: Redirect back
                header("Location: ../pages/manage_modules.php?course_id=" . $course_id . "&status=update_success");
                exit();
            } else {
                // Error: Redirect back
                header("Location: ../pages/manage_modules.php?course_id=" . $course_id . "&status=update_error");
                exit();
            }

            // 7. Close the statement
            mysqli_stmt_close($stmt);

        } else {
            // SQL preparation failed
            header("Location: ../pages/manage_modules.php?course_id=" . $course_id . "&status=sql_error");
            exit();
        }

    } else {
        // Required fields were empty
        $redirect_course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : '';
        header("Location: ../pages/manage_modules.php?course_id=" . $redirect_course_id . "&status=invalid_input");
        exit();
    }

} else {
    // If not a POST request, just redirect back to the main page
    header("Location: ../pages/manage_modules.php");
    exit();
}

// 8. Close the database connection
mysqli_close($con);
?>
