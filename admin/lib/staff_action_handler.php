<?php
session_start();
include_once('../../include/connection.php'); // Go back two folders


$redirect_page = "../pages/manage_staff.php";


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    
    
    $staff_id = trim($_POST['staff_id']);
    $position = trim($_POST['position']);
    $course_no = !empty($_POST['course_no']) ? trim($_POST['course_no']) : NULL;
    $status = trim($_POST['status']);

    
    if (empty($staff_id) || empty($position) || empty($status)) {
        $_SESSION['staff_error_msg'] = "Required fields (Position, Status) cannot be empty.";
        header("Location: ../pages/view_staff_details.php?staff_id=" . urlencode($staff_id)); // Redirect back to edit page
        exit();
    }
    
    // 2. Prepare Update Query
    $update_query = "UPDATE staff SET position = ?, course_no = ?, status = ? WHERE staff_id = ?";
    $stmt = $con->prepare($update_query);
    
    if (!$stmt) {
         $_SESSION['staff_error_msg'] = "Database error preparing update statement.";
         header("Location: ../pages/view_staff_details.php?staff_id=" . urlencode($staff_id)); exit();
    }

    $stmt->bind_param("ssss", $position, $course_no, $status, $staff_id);

    if ($stmt->execute()) {
        $_SESSION['staff_success_msg'] = "Staff member (ID: $staff_id) details updated successfully.";
        
        $redirect_page = "../pages/view_staff_details.php?staff_id=" . urlencode($staff_id); 
    } else {
        $_SESSION['staff_error_msg'] = "Database error executing update. Please try again.";
        $redirect_page = "../pages/view_staff_details.php?staff_id=" . urlencode($staff_id); 
    }
    $stmt->close();

// --- DELETE ACTION (from GET request) ---
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'delete') {
    
    if (!isset($_GET['staff_id']) || empty($_GET['staff_id'])) {
         $_SESSION['staff_error_msg'] = "Invalid Staff ID for deletion.";
         header("Location: $redirect_page"); exit(); 
    }
    
    $staff_id_to_delete = trim($_GET['staff_id']);
    $delete_query = "DELETE FROM staff WHERE staff_id = ?";
    $stmt = $con->prepare($delete_query);
    
     if (!$stmt) {
         $_SESSION['staff_error_msg'] = "Database error preparing delete statement.";
         header("Location: $redirect_page"); exit();
    }

    $stmt->bind_param("s", $staff_id_to_delete);

    // Execute and set message
    if ($stmt->execute()) {
        $_SESSION['staff_success_msg'] = "Staff member (ID: $staff_id_to_delete) has been deleted.";
    } else {
        $_SESSION['staff_error_msg'] = "Database error deleting staff member. Please try again.";
    }
    $stmt->close();
    // Redirect is handled outside the if/else block for delete

} else {
    // Invalid action or method
    $_SESSION['staff_error_msg'] = "Invalid action.";
}

// Final Redirect
$con->close();
header("Location: " . $redirect_page);
exit();
?>