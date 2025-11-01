<?php
include('../../include/connection.php');

// Define the redirect URL.
$redirect_url = "../pages/manage images.php";

// Define the target directory for uploads
$target_dir = "../uploads/gallery/"; 
// This is the path we will store in the DB
$db_path_prefix = "../uploads/gallery/";

// Allowed file types
$allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Counters for success/failure
$success_count = 0;
$failed_count = 0;

// --- 1. Check if form is POST and files are uploaded ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image_file']['name']) && is_array($_FILES['image_file']['name'])) {
    
    // --- Get the optional image name prefix ---
    $image_name_prefix = isset($_POST['image_name']) ? trim($_POST['image_name']) : '';

    // Ensure the upload directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $total_files = count($_FILES['image_file']['name']);

    // --- 2. Prepare the SQL statement ONCE for efficiency ---
    $sql = "INSERT INTO gallery (image_name, image_path) VALUES (?, ?)";
    $stmt = $con->prepare($sql);
    
    if ($stmt === false) {
        // SQL preparation failed badly
        header("Location: $redirect_url?status=upload_error&msg=sql_prepare");
        exit;
    }

    // --- 3. Loop through each uploaded file ---
    for ($i = 0; $i < $total_files; $i++) {
        
        // Check for individual file upload errors
        if ($_FILES['image_file']['error'][$i] !== UPLOAD_ERR_OK) {
            $failed_count++;
            continue; // Skip to the next file
        }

        $original_name = $_FILES['image_file']['name'][$i];
        $tmp_name = $_FILES['image_file']['tmp_name'][$i];
        $file_size = $_FILES['image_file']['size'][$i];
        
        $safe_original_name = basename($original_name);
        $file_extension = strtolower(pathinfo($safe_original_name, PATHINFO_EXTENSION));

        // --- 4. Perform validations on *each* file ---

        // A. Check file type
        if (!in_array($file_extension, $allowed_types)) {
            $failed_count++;
            continue; 
        }

        // B. Check file size (5MB limit from your script)
        if ($file_size > 5000000) {
            $failed_count++;
            continue; 
        }
        
        // C. Check if it's a real image
        if (getimagesize($tmp_name) === false) {
            $failed_count++;
            continue;
        }

        // --- 5. Process the valid file ---
        $unique_file_name = uniqid() . '_' . time() . '.' . $file_extension;
        $target_file_path = $target_dir . $unique_file_name;
        $db_path = $db_path_prefix . $unique_file_name; 

        // --- Determine the final image name ---
        $final_image_name = $safe_original_name; // Default to original filename
        
        if (!empty($image_name_prefix)) {
            if ($total_files > 1) {
                // If bulk upload, use prefix + original name
                $final_image_name = $image_name_prefix . " - " . $safe_original_name;
            } else {
                // If single file upload, use the exact name given
                $final_image_name = $image_name_prefix;
            }
        }
        
        if (move_uploaded_file($tmp_name, $target_file_path)) {
            
            // File moved successfully, now insert into DB
            $stmt->bind_param("ss", $final_image_name, $db_path);
            
            if ($stmt->execute()) {
                $success_count++;
            } else {
                unlink($target_file_path);
                $failed_count++;
            }

        } else {
            $failed_count++;
        }
    } // --- End of for-loop ---

    $stmt->close();

} else {
    // Not a POST request or no files uploaded
    header("Location: $redirect_url?status=upload_error&msg=no_files");
    exit;
}

$con->close();

// --- 6. Redirect back with a summary status ---
if ($failed_count == 0 && $success_count > 0) {
    header("Location: $redirect_url?status=uploaded&count=$success_count");
} elseif ($success_count > 0 && $failed_count > 0) {
    header("Location: $redirect_url?status=some_failed&s=$success_count&f=$failed_count");
} else {
    header("Location: $redirect_url?status=upload_error&msg=all_failed");
}
exit;
?>