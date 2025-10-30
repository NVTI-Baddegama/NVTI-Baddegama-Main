<?php
// 1. Include the database connection
include_once('../../include/connection.php');

// --- NEW: Define the redirect URL for convenience ---
$redirect_url = "../pages/manage images.php";

// 2. Check if an ID is provided in the URL
if (!isset($_GET['id']) || empty((int)$_GET['id'])) {
    // --- MODIFIED: Replaced die() with redirect ---
    header("Location: " . $redirect_url . "?status=delete_error");
    exit;
}

$image_id = (int)$_GET['id'];

// 3. First, fetch the image path
$sql_select = "SELECT image_path FROM gallery WHERE id = ?";
$stmt_select = $con->prepare($sql_select);
if ($stmt_select === false) {
     // --- MODIFIED: Replaced die() with redirect ---
     header("Location: " . $redirect_url . "?status=delete_error");
     exit;
}

$stmt_select->bind_param("i", $image_id);
$stmt_select->execute();
$result = $stmt_select->get_result();

if ($result->num_rows > 0) {
    $image = $result->fetch_assoc();
    $image_path = $image['image_path'];
    
    // 4. Now, delete the record from the database
    // --- MODIFIED: Added LIMIT 1 as an extra safety check ---
    $sql_delete = "DELETE FROM gallery WHERE id = ? LIMIT 1";
    $stmt_delete = $con->prepare($sql_delete);
    
    if ($stmt_delete === false) {
         // --- MODIFIED: Replaced die() with redirect ---
         header("Location: " . $redirect_url . "?status=delete_error");
         exit;
    }
    
    $stmt_delete->bind_param("i", $image_id);
    
    if ($stmt_delete->execute()) {
        // 5. If DB delete is successful, delete the actual file
        // --- NEW: Added a check if $image_path is not null/empty ---
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }
        
        // 6. Redirect with a SUCCESS status
        // --- MODIFIED: Added status=deleted ---
        header("Location: " . $redirect_url . "?status=deleted");
        exit;
        
    } else {
        // 6. Redirect with an ERROR status
        // --- MODIFIED: Replaced die() with redirect ---
        header("Location: " . $redirect_url . "?status=delete_error");
        exit;
    }
    
} else {
    // 6. Redirect with an ERROR status (image not found)
    // --- MODIFIED: Replaced die() with redirect ---
    header("Location: " . $redirect_url . "?status=delete_error");
    exit;
}
?>