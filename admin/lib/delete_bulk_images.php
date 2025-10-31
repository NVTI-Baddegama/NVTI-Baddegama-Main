<?php
include('../../include/connection.php');

// Define the redirect URL
$redirect_url = "../pages/manage images.php";

// 1. Check if it's a POST request and 'image_ids' is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['image_ids'])) {
    
    $image_ids = $_POST['image_ids'];

    // 2. Check if the array is empty
    if (empty($image_ids) || !is_array($image_ids)) {
        header("Location: $redirect_url?status=bulk_delete_error&msg=no_ids");
        exit;
    }

    // 3. Sanitize all IDs to be integers
    $sanitized_ids = array_map('intval', $image_ids);
    $count = count($sanitized_ids);

    // 4. Create placeholders for the IN clause (e.g., ?, ?, ?)
    $placeholders = rtrim(str_repeat('?,', $count), ',');
    
    // 5. First, fetch the paths of all images to be deleted
    $sql_select = "SELECT image_path FROM gallery WHERE id IN ($placeholders)";
    $stmt_select = $con->prepare($sql_select);
    
    if ($stmt_select === false) {
        header("Location: $redirect_url?status=bulk_delete_error&msg=sql_select_prepare");
        exit;
    }
    
    // Bind all integer IDs
    $types = str_repeat('i', $count); 
    $stmt_select->bind_param($types, ...$sanitized_ids);
    
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    
    $image_paths = [];
    while ($row = $result->fetch_assoc()) {
        $image_paths[] = $row['image_path'];
    }
    $stmt_select->close();


    // 6. Now, delete the records from the database
    $sql_delete = "DELETE FROM gallery WHERE id IN ($placeholders)";
    $stmt_delete = $con->prepare($sql_delete);
    
    if ($stmt_delete === false) {
        header("Location: $redirect_url?status=bulk_delete_error&msg=sql_delete_prepare");
        exit;
    }

    // Bind all integer IDs again
    $stmt_delete->bind_param($types, ...$sanitized_ids);

    // 7. Execute deletion
    if ($stmt_delete->execute()) {
        // 8. If DB delete is successful, delete the actual files
        foreach ($image_paths as $path) {
            if (!empty($path) && file_exists($path)) {
                unlink($path);
            }
        }
        
        // 9. Redirect with a SUCCESS status
        header("Location: $redirect_url?status=bulk_deleted&count=$count");
        exit;
        
    } else {
        // 9. Redirect with an ERROR status
        header("Location: $redirect_url?status=bulk_delete_error&msg=db_delete_failed");
        exit;
    }

} else {
    // 6. Redirect if not a POST request or no IDs
    header("Location: $redirect_url?status=bulk_delete_error&msg=invalid_request");
    exit;
}
?>