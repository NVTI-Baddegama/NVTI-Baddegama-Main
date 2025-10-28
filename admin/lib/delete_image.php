<?php
// 1. Include the database connection
// Path is relative to the 'lib' folder
include_once('../../include/connection.php');

// 2. Check if an ID is provided in the URL
if (!isset($_GET['id'])) {
    die("Error: No image ID specified.");
}

$image_id = (int)$_GET['id'];

// 3. First, fetch the image path from the database BEFORE deleting the record
$sql_select = "SELECT image_path FROM gallery WHERE id = ?";
$stmt_select = $con->prepare($sql_select);
if ($stmt_select === false) {
    die("Prepare failed: " . $con->error);
}

$stmt_select->bind_param("i", $image_id);
$stmt_select->execute();
$result = $stmt_select->get_result();

if ($result->num_rows > 0) {
    $image = $result->fetch_assoc();
    $image_path = $image['image_path'];
    
    // 4. Now, delete the record from the database
    $sql_delete = "DELETE FROM gallery WHERE id = ?";
    $stmt_delete = $con->prepare($sql_delete);
    if ($stmt_delete === false) {
        die("Prepare failed: " . $con->error);
    }
    
    $stmt_delete->bind_param("i", $image_id);
    
    if ($stmt_delete->execute()) {
        // 5. If DB delete is successful, delete the actual file from the server
        // The path stored in the DB (e.g., ../uploads/file.png) is relative
        // to the 'lib' folder, so this should work.
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        
        // 6. Redirect back to the manage images page
        // Path is relative from 'lib' up to 'admin' and down to 'pages'
        header("Location: ../pages/manage images.php");
        exit;
        
    } else {
        die("Error deleting record: " . $stmt_delete->error);
    }
    
} else {
    die("Error: Image not found.");
}
?>