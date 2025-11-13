<?php
session_start();
include_once('../../include/connection.php'); // Go back two folders

// Default redirect location
$redirect_page = "../pages/manage_staff.php";

// --- UPDATE ACTION (from POST request) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {

    // 1. Get data from the form
    $staff_id = trim($_POST['staff_id']);
    
    // **** START: ADDED FIELDS ****
    $first_name = mysqli_real_escape_string($con, trim($_POST['first_name']));
    $last_name = mysqli_real_escape_string($con, trim($_POST['last_name']));
    // **** END: ADDED FIELDS ****
    
    $position = trim($_POST['position']);
    $contact_no = trim($_POST['contact_no']);
    $email = trim($_POST['email']);
    $status = trim($_POST['status']);
    
    // --- NEW: Check for admin type ---
    $new_type = (isset($_POST['admin_type']) && $_POST['admin_type'] === 'admin') ? 'admin' : NULL;
    // --- END NEW ---

    $is_instructor = ($position === 'Instructor') || ($position === 'Senior Instructor');
    $course_no = ($is_instructor && !empty($_POST['course_no'])) ? trim($_POST['course_no']) : NULL;
    
    $old_image_name = isset($_POST['old_image_name']) ? trim($_POST['old_image_name']) : null;
    $remove_image = isset($_POST['remove_image']) ? true : false;
    
    $upload_dir = '../../uploads/profile_photos/';
    
    $redirect_page_edit = "../pages/view_staff_details.php?staff_id=" . urlencode($staff_id);

    // Validation
    // **** ADDED first_name and last_name to validation ****
    if (empty($staff_id) || empty($first_name) || empty($last_name) || empty($position) || empty($status) || empty($contact_no) || empty($email)) {
        $_SESSION['staff_error_msg'] = "Required fields (First Name, Last Name, Position, Contact, Email, Status) cannot be empty.";
        header("Location: $redirect_page_edit");
        exit();
    }
    
    $new_image_name = $old_image_name;
    $image_update_sql_part = "";
    $types_for_update = "";
    $params_for_update = [];

    // --- 2. Handle Image Removal ---
    if ($remove_image && !empty($old_image_name)) {
        $image_update_sql_part = ", profile_photo = NULL";
        $new_image_name = NULL;
        $old_image_path = $upload_dir . $old_image_name;
        if (file_exists($old_image_path)) {
            @unlink($old_image_path);
        }
    }

    // --- 3. Handle NEW File Upload ---
    if (!$remove_image && isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        
        $file_tmp = $_FILES['profile_photo']['tmp_name'];
        $file_size = $_FILES['profile_photo']['size'];
        $file_ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

        if ($file_size > 1 * 1024 * 1024) { // 1MB Server-side check
             $_SESSION['staff_error_msg'] = "Error: File size exceeds 1MB limit.";
             header("Location: $redirect_page_edit"); exit();
        }

        if (in_array($file_ext, $allowed_exts)) {
            $uploaded_image_name = 'NVTI-STAFF-' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $uploaded_image_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $image_update_sql_part = ", profile_photo = ?";
                $new_image_name = $uploaded_image_name;
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


    // --- 4. Prepare Update Query ---
    // **** ADDED `first_name = ?` and `last_name = ?` to the query ****
    $update_query = "UPDATE staff SET 
                        first_name = ?,
                        last_name = ?,
                        position = ?, 
                        course_no = ?, 
                        status = ?,
                        contact_no = ?,
                        email = ?,
                        type = ?
                        $image_update_sql_part
                     WHERE staff_id = ?";

    $stmt = $con->prepare($update_query);
    if (!$stmt) {
         $_SESSION['staff_error_msg'] = "Database error preparing update statement: " . $con->error;
         header("Location: $redirect_page_edit"); exit();
    }

    // --- 5. Build Parameters and Types for Binding ---
    // Base parameters (without image)
    // **** ADDED "ss" to types and variables for names ****
    $types_for_update = "sssssssss"; // ss=names, s=position, s=course_no, s=status, s=contact, s=email, s=type, s=staff_id
    $params_for_update = [
        $first_name, 
        $last_name,
        $position, $course_no, $status,
        $contact_no, $email, 
        $new_type,
        $staff_id
    ];

    // If image was updated (or removed), adjust query and params
    if (!empty($image_update_sql_part)) {
        // **** ADDED `first_name = ?` and `last_name = ?` to the query ****
        $update_query = "UPDATE staff SET 
                        first_name = ?,
                        last_name = ?,
                        position = ?, 
                        course_no = ?, 
                        status = ?,
                        contact_no = ?,
                        email = ?,
                        type = ?,
                        profile_photo = ?    -- Image param
                     WHERE staff_id = ?";
        
        // **** ADDED "ss" to types and variables for names ****
        $types_for_update = "ssssssssss"; // Added 'ss' for names
        $params_for_update = [
            $first_name,
            $last_name,
            $position, $course_no, $status,
            $contact_no, $email, 
            $new_type,
            $new_image_name, // Image name parameter
            $staff_id
        ];
        
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
        if (strpos($con->error, 'Duplicate entry') !== false && strpos($con->error, 'email') !== false) {
             $_SESSION['staff_error_msg'] = "Update failed: The email address '$email' is already in use by another staff member.";
        } else {
             $_SESSION['staff_error_msg'] = "Database error executing update: " . $stmt->error;
        }
    }
    $stmt->close();
    header("Location: $redirect_page_edit");
    exit();


// --- DELETE ACTION (from GET request) ---
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'delete') {

    // (Delete logic remains the same)
    if (!isset($_GET['staff_id']) || empty($_GET['staff_id'])) {
         $_SESSION['staff_error_msg'] = "Invalid Staff ID for deletion.";
         header("Location: $redirect_page"); exit();
    }
    $staff_id_to_delete = trim($_GET['staff_id']);

    // Step 1: Get photo filename
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

    // Step 3: Execute
    if ($stmt->execute()) {
        $_SESSION['staff_success_msg'] = "Staff member (ID: $staff_id_to_delete) database record deleted successfully.";

        // Step 4: Delete file
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
    header("Location: $redirect_page");
    exit();

} else {
    $_SESSION['staff_error_msg'] = "Invalid action.";
    header("Location: $redirect_page");
    exit();
}

$con->close();
header("Location: " . $redirect_page);
exit();
?>