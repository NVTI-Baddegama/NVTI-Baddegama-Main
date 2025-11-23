<?php
include_once '../include/connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $redirect_page = '../index.php';
    // 1. Get and sanitize data
    // Use trim() to remove accidental whitespace
    $user_name = trim($_POST['name']);
    $feedback = trim($_POST['feedback']);
    $rating = (int)$_POST['rating']; // Cast to integer

    // 2. Validate data
    if (!empty($user_name) && !empty($feedback) && $rating >= 1 && $rating <= 5) {
        
        // 3. Prepare SQL statement (to prevent SQL injection)
        // We set 'approve' to 0 by default (meaning "pending approval")
        $sql = "INSERT INTO feedback (user_name, feedback, rating, approve) VALUES (?, ?, ?, 0)";
        
        $stmt = $con->prepare($sql);
        
        if ($stmt === false) {
            // Handle query preparation error
            error_log("Prepare failed: (" . $con->errno . ") " . $con->error);
            header("Location: $redirect_page?status=dberror");
            exit();
        }

        // 4. Bind parameters
        // "ssi" = string, string, integer
        $stmt->bind_param("ssi", $user_name, $feedback, $rating);

        // 5. Execute and check for success
        if ($stmt->execute()) {
            // Success!
            header("Location: $redirect_page?status=success");
        } else {
            // Handle execution error
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            header("Location: $redirect_page?status=error");
        }
        
        // 6. Close statement
        $stmt->close();

    } else {
        // Data was invalid (e.g., empty fields or no rating)
        header("Location: $redirect_page?status=validation_failed");
    }
} else {
    // If someone tries to access this file directly, just redirect them
    header("Location: $redirect_page");
}

// 7. Close connection
$conn->close();
exit();
?>