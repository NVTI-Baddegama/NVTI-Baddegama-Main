<?php
include('../../include/connection.php');

// Define the redirect URL.
// NOTE: Using a URL with a space is unusual, but matching your provided code.
$redirect_url = "../pages/manage images.php";

// Define the target directory for uploads
// This path is relative to *this file* (which is in 'lib/')
// So, it points to PROJECT_ROOT/uploads/gallery/
$target_dir = "../uploads/gallery/"; 

// This is the path we will store in the DB
// It is relative to the *pages* directory (e.g., 'pages/manage images.php')
// So it can be used in an <img src="..."> tag
$db_path_prefix = "../uploads/gallery/";

// Allowed file types
$allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Counters for success/failure
$success_count = 0;
$failed_count = 0;

// --- 1. Check if form is POST and files are uploaded ---
// We check for 'image_file[name]' and 'is_array'
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image_file']['name']) && is_array($_FILES['image_file']['name'])) {
    
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
        
        // Sanitize the original name for security and for the 'image_name' column
        $safe_original_name = basename($original_name);
        
        // Get file extension
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
        
        // Create a unique new name to prevent overwriting
        $unique_file_name = uniqid() . '_' . time() . '.' . $file_extension;
        
        // Full path for saving the file to the server's filesystem
        $target_file_path = $target_dir . $unique_file_name;
        
        // Path to store in the database (for the <img> tag)
        $db_path = $db_path_prefix . $unique_file_name; 

        // Try to move the file
        if (move_uploaded_file($tmp_name, $target_file_path)) {
            
            // File moved successfully, now insert into DB
            // We use the sanitized original file name as the 'image_name'
            $stmt->bind_param("ss", $safe_original_name, $db_path);
            
            if ($stmt->execute()) {
                // Success!
                $success_count++;
            } else {
                // DB insert failed, delete the file we just uploaded
                unlink($target_file_path);
                $failed_count++;
            }

        } else {
            // File move failed (permissions issue?)
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
    // All success
    header("Location: $redirect_url?status=uploaded&count=$success_count");
} elseif ($success_count > 0 && $failed_count > 0) {
    // Partial success
    header("Location: $redirect_url?status=some_failed&s=$success_count&f=$failed_count");
} else {
    // All failed
    header("Location: $redirect_url?status=upload_error&msg=all_failed");
}
exit;
?>