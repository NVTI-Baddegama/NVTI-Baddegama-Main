<?php
// 1. Include the database connection
include_once('../../include/connection.php');

// --- NEW: Define the redirect URL for convenience ---
$redirect_url = "../pages/manage images.php";

// 2. Check if an ID is provided in the URL
if (!isset($_GET['id']) || empty((int)$_GET['id'])) {
    header("Location: " . $redirect_url . "?status=delete_error");
    exit;
}

$image_id = (int)$_GET['id'];

// 3. First, fetch the image path
$sql_select = "SELECT image_path FROM gallery WHERE id = ?";
$stmt_select = $con->prepare($sql_select);
if ($stmt_select === false) {
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
    $sql_delete = "DELETE FROM gallery WHERE id = ? LIMIT 1";
    $stmt_delete = $con->prepare($sql_delete);
    
    if ($stmt_delete === false) {
         header("Location: " . $redirect_url . "?status=delete_error");
         exit;
    }
    
    $stmt_delete->bind_param("i", $image_id);
    
    if ($stmt_delete->execute()) {
        // 5. If DB delete is successful, delete the actual file
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }
        
        // 6. Redirect with a SUCCESS status
        header("Location: " . $redirect_url . "?status=deleted");
        exit;
        
    } else {
        // 6. Redirect with an ERROR status
        header("Location: " . $redirect_url . "?status=delete_error");
        exit;
    }
    
} else {
    // 6. Redirect with an ERROR status (image not found)
    header("Location: " . $redirect_url . "?status=delete_error");
    exit;
}
?>