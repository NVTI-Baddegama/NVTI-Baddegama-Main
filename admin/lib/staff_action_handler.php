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
    $contact_no = trim($_POST['contact_no']); // NEW
    $email = trim($_POST['email']); // NEW
    $status = trim($_POST['status']);
    
    $is_instructor = ($position === 'Instructors');
    $course_no = ($is_instructor && !empty($_POST['course_no'])) ? trim($_POST['course_no']) : NULL;
    
    $old_image_name = isset($_POST['old_image_name']) ? trim($_POST['old_image_name']) : null;
    $remove_image = isset($_POST['remove_image']) ? true : false;
    
    $upload_dir = '../../uploads/profile_photos/'; // Path relative to admin/lib
    
    // Set redirect page back to the edit form in case of errors
    $redirect_page_edit = "../pages/view_staff_details.php?staff_id=" . urlencode($staff_id);

    // Basic Validation
    if (empty($staff_id) || empty($position) || empty($status) || empty($contact_no) || empty($email)) {
        $_SESSION['staff_error_msg'] = "Required fields (Position, Contact, Email, Status) cannot be empty.";
        header("Location: $redirect_page_edit");
        exit();
    }
    
    $new_image_name = $old_image_name; // Keep old image by default
    $image_update_sql_part = ""; // SQL part for image update
    $types_for_update = "";    // String for bind_param types
    $params_for_update = []; // Array to hold parameters for binding

    // --- 2. Handle Image Removal ---
    if ($remove_image && !empty($old_image_name)) {
        $image_update_sql_part = ", profile_photo = NULL"; // Set DB field to NULL
        $new_image_name = NULL; // Mark that image should be NULL

        $old_image_path = $upload_dir . $old_image_name;
        if (file_exists($old_image_path)) {
            @unlink($old_image_path); // Attempt to delete
        }
    }

    // --- 3. Handle NEW File Upload (only if remove checkbox wasn't checked) ---
    if (!$remove_image && isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        
        $file_tmp = $_FILES['profile_photo']['tmp_name'];
        $file_size = $_FILES['profile_photo']['size'];
        $file_ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

        // Client-side 1MB validation is already done, but double-check server-side
        if ($file_size > 1 * 1024 * 1024) { // 1MB
             $_SESSION['staff_error_msg'] = "Error: File size exceeds 1MB limit.";
             header("Location: $redirect_page_edit"); exit();
        }

        if (in_array($file_ext, $allowed_exts)) {
            // Create unique name
            $uploaded_image_name = 'NVTI-STAFF-' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $uploaded_image_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Successfully uploaded new image
                $image_update_sql_part = ", profile_photo = ?"; // SQL part
                $new_image_name = $uploaded_image_name; // Set new image name for binding

                // Delete the old image if it existed and is different
                if (!empty($old_image_name) && $old_image_name !== $new_image_name && file_exists($upload_dir . $old_image_name)) {
                    @unlink($upload_dir . $old_image_name);
                }
            } else {
                $_SESSION['staff_error_msg'] = "Failed to move uploaded file.";
                header("Location: $redirect_page_edit"); exit();
            }
        } else {
             $_SESSION['staff_error_msg'] = "Invalid new image file type.";
             header("Location: $redirect_page_edit"); exit();
        }
    }
    // If no new image and remove checkbox not checked, $image_update_sql_part remains empty and $new_image_name remains $old_image_name


    // --- 4. Prepare Update Query ---
    $update_query = "UPDATE staff SET 
                        position = ?, 
                        course_no = ?, 
                        status = ?,
                        contact_no = ?,  -- NEW
                        email = ?        -- NEW
                        $image_update_sql_part
                     WHERE staff_id = ?";

    $stmt = $con->prepare($update_query);

    if (!$stmt) {
         $_SESSION['staff_error_msg'] = "Database error preparing update statement: " . $con->error;
         header("Location: $redirect_page_edit"); exit();
    }

    // --- 5. Build Parameters and Types for Binding ---
    $types_for_update = "ssssss"; // s=position, s=course_no, s=status, s=contact, s=email, s=staff_id
    $params_for_update = [
        $position, $course_no, $status,
        $contact_no, $email, $staff_id
    ];

    // If image was updated (or removed), add it to the parameters
    if (!empty($image_update_sql_part)) {
        $types_for_update = "s" . $types_for_update; // Add 's' for image name at the beginning
        array_unshift($params_for_update, $new_image_name); // Add image name to the beginning of params
        // Fix query to match this order
        $update_query = "UPDATE staff SET 
                        profile_photo = ?, 
                        position = ?, 
                        course_no = ?, 
                        status = ?,
                        contact_no = ?,
                        email = ?
                     WHERE staff_id = ?";
        // Re-prepare the statement with correct order
        $stmt->close();
        $stmt = $con->prepare($update_query);
        if (!$stmt) {
             $_SESSION['staff_error_msg'] = "Database error re-preparing update statement: " . $con->error;
             header("Location: $redirect_page_edit"); exit();
        }
    }


    // --- 6. Bind Parameters & Execute ---
    $stmt->bind_param($types_for_update, ...$params_for_update);

    if ($stmt->execute()) {
        $_SESSION['staff_success_msg'] = "Staff member (ID: $staff_id) details updated successfully.";
    } else {
        // Check for duplicate email error
        if (strpos($con->error, 'Duplicate entry') !== false && strpos($con->error, 'email') !== false) {
             $_SESSION['staff_error_msg'] = "Update failed: The email address '$email' is already in use by another staff member.";
        } else {
             $_SESSION['staff_error_msg'] = "Database error executing update: " . $stmt->error;
        }
    }
    $stmt->close();
    header("Location: $redirect_page_edit"); // Redirect back to edit page
    exit();


// --- DELETE ACTION (from GET request) ---
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'delete') {

    // (Delete logic remains the same as before)
    if (!isset($_GET['staff_id']) || empty($_GET['staff_id'])) {
         $_SESSION['staff_error_msg'] = "Invalid Staff ID for deletion.";
         header("Location: $redirect_page"); exit();
    }
    $staff_id_to_delete = trim($_GET['staff_id']);

    // Step 1: Get profile photo filename
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

    // Step 2: Delete DB record
    $delete_query = "DELETE FROM staff WHERE staff_id = ?";
    $stmt = $con->prepare($delete_query);
     if (!$stmt) {
         $_SESSION['staff_error_msg'] = "Database error preparing delete statement.";
         header("Location: $redirect_page"); exit();
    }
    $stmt->bind_param("s", $staff_id_to_delete);

    // Step 3: Execute DB deletion AND attempt file deletion
    if ($stmt->execute()) {
        $_SESSION['staff_success_msg'] = "Staff member (ID: $staff_id_to_delete) database record deleted successfully.";

        // Step 4: Attempt to delete the photo file
        if ($photo_filename) {
            $photo_path = '../../uploads/profile_photos/' . $photo_filename;
            if (file_exists($photo_path)) {
                if (@unlink($photo_path)) {
                    $_SESSION['staff_success_msg'] .= " Profile photo also deleted.";
                } else {
                    $_SESSION['staff_error_msg'] = "Staff record deleted, but failed to delete the profile photo file '$photo_filename'. Check file permissions.";
                }
            }
        }
    } else {
        $_SESSION['staff_error_msg'] = "Database error deleting staff member. Please try again.";
    }
    $stmt->close();
    header("Location: $redirect_page"); // Redirect to manage page
    exit();

} else {
    // Invalid action
    $_SESSION['staff_error_msg'] = "Invalid action.";
    header("Location: $redirect_page");
    exit();
}

// Final Redirect (should not be reached, but as a fallback)
$con->close();
header("Location: " . $redirect_page);
exit();
?>