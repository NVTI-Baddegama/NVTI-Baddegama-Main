<?php
session_start();
include_once('../../include/connection.php'); // Go back two folders

// Default redirect location
$redirect_page = "../pages/manage_staff.php";

// --- UPDATE ACTION (from POST request) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {

    // 1. Get data from the form
    $staff_id = trim($_POST['staff_id']);
    $position = trim($_POST['position']);
    // Handle course_no based on position logic (if Instructor, use value, else NULL)
    $is_instructor = ($position === 'Instructors');
    $course_no = ($is_instructor && !empty($_POST['course_no'])) ? trim($_POST['course_no']) : NULL;
    $status = trim($_POST['status']);


    // Basic Validation
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

    // 3. Execute and Redirect
    if ($stmt->execute()) {
        $_SESSION['staff_success_msg'] = "Staff member (ID: $staff_id) details updated successfully.";
        // Redirect to view page after successful update
        $redirect_page = "../pages/view_staff_details.php?staff_id=" . urlencode($staff_id);
    } else {
        $_SESSION['staff_error_msg'] = "Database error executing update. Please try again.";
        $redirect_page = "../pages/view_staff_details.php?staff_id=" . urlencode($staff_id); // Redirect back to edit page on error
    }
    $stmt->close();

// --- DELETE ACTION (from GET request) ---
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'delete') {

    if (!isset($_GET['staff_id']) || empty($_GET['staff_id'])) {
         $_SESSION['staff_error_msg'] = "Invalid Staff ID for deletion.";
         header("Location: $redirect_page"); exit(); // Redirect to manage page
    }

    $staff_id_to_delete = trim($_GET['staff_id']);

    // --- NEW: Step 1: Get the profile photo filename BEFORE deleting the record ---
    $photo_filename = null;
    $photo_query = "SELECT profile_photo FROM staff WHERE staff_id = ?";
    $stmt_photo = $con->prepare($photo_query);
    if ($stmt_photo) {
        $stmt_photo->bind_param("s", $staff_id_to_delete);
        $stmt_photo->execute();
        $result_photo = $stmt_photo->get_result();
        if ($result_photo->num_rows == 1) {
            $row = $result_photo->fetch_assoc();
            if (!empty($row['profile_photo'])) {
                $photo_filename = $row['profile_photo'];
            }
        }
        $stmt_photo->close();
    }
    // --- END NEW ---

    // --- Step 2: Prepare Delete Query for database ---
    $delete_query = "DELETE FROM staff WHERE staff_id = ?";
    $stmt = $con->prepare($delete_query);

     if (!$stmt) {
         $_SESSION['staff_error_msg'] = "Database error preparing delete statement.";
         header("Location: $redirect_page"); exit();
    }

    $stmt->bind_param("s", $staff_id_to_delete);

    // --- Step 3: Execute DB deletion AND attempt file deletion ---
    if ($stmt->execute()) {
        $_SESSION['staff_success_msg'] = "Staff member (ID: $staff_id_to_delete) database record deleted successfully.";

        // --- NEW: Step 4: Attempt to delete the photo file ---
        if ($photo_filename) {
            $photo_path = '../../uploads/profile_photos/' . $photo_filename; // Path relative to admin/lib
            if (file_exists($photo_path)) {
                if (unlink($photo_path)) {
                    // Optionally add a success message part for file deletion
                    $_SESSION['staff_success_msg'] .= " Profile photo also deleted.";
                } else {
                    // File exists but couldn't be deleted (permissions?)
                    $_SESSION['staff_error_msg'] = "Staff record deleted, but failed to delete the profile photo file '$photo_filename'. Check file permissions.";
                }
            }
            // If file doesn't exist, we don't need to do anything or show an error
        }
        // --- END NEW ---

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