<?php
include('../../include/connection.php');

// --- NEW: Define the redirect URL for convenience ---
$redirect_url = "../pages/manage images.php";

// 1. Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- MODIFIED: Added check for 'name' and 'error' to ensure a file was actually uploaded ---
    if (isset($_POST['image_name']) && isset($_FILES['image_file']) && $_FILES['image_file']['error'] == UPLOAD_ERR_OK) {
        
        $image_name = trim($_POST['image_name']);
        $file = $_FILES['image_file'];

        // 2. Define upload directory
        $target_dir = "../uploads/gallery/";

        // --- NEW: Check if upload directory exists, if not, create it ---
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $unique_file_name = uniqid() . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $unique_file_name;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        // 3. Perform file validation
        
        // Check if image file is a actual image
        $check = getimagesize($file["tmp_name"]);
        if($check === false) {
            // --- MODIFIED: Replaced die() with redirect ---
            header("Location: " . $redirect_url . "?status=upload_error");
            exit;
        }

        // Check file size (e.g., 5MB limit)
        if ($file["size"] > 5000000) {
            // --- MODIFIED: Replaced die() with redirect ---
            header("Location: " . $redirect_url . "?status=upload_error");
            exit;
        }

        // Allow certain file formats
        if (!in_array($file_extension, $allowed_types)) {
            // --- MODIFIED: Replaced die() with redirect ---
            header("Location: " . $redirect_url . "?status=upload_error");
            exit;
        }

        // 4. Try to upload file
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            
            // 5. Insert into database
            $sql = "INSERT INTO gallery (image_name, image_path) VALUES (?, ?)";
            $stmt = $con->prepare($sql);
            
            if ($stmt === false) {
                // --- MODIFIED: Replaced die() with redirect ---
                // --- ALSO: Delete the file we just uploaded, since the DB failed ---
                unlink($target_file);
                header("Location: " . $redirect_url . "?status=upload_error");
                exit;
            }
            
            $stmt->bind_param("ss", $image_name, $target_file);
            
            if ($stmt->execute()) {
                // 6. Redirect back to the manage page with SUCCESS
                // --- MODIFIED: Added success status ---
                header("Location: " . $redirect_url . "?status=uploaded");
                exit;
            } else {
                // --- MODIFIED: Replaced die() with redirect ---
                // --- ALSO: Delete the file we just uploaded, since the DB failed ---
                unlink($target_file);
                header("Location: " . $redirect_url . "?status=upload_error");
                exit;
            }

        } else {
            // --- NEW: Handle failure of move_uploaded_file ---
            header("Location: " . $redirect_url . "?status=upload_error");
            exit;
        }

    } else {
        // --- MODIFIED: Replaced die() with redirect (for incomplete form) ---
        header("Location: " . $redirect_url . "?status=upload_error");
        exit;
    }
} else {
    // Not a POST request
    // --- MODIFIED: Replaced redirect to wrong page with correct one ---
    header("Location: " . $redirect_url);
    exit;
}
// --- MODIFIED: Removed the extra "}" that was at the end of your original file ---
?>