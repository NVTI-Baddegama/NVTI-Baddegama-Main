<?php
session_start();
include_once('../../include/connection.php');

// Security check: Ensure only logged-in admins can access this
if (!isset($_SESSION['admin_username'])) {
    $_SESSION['mail_error_msg'] = "Access Denied. Please log in.";
    header("Location: ../pages/mail_management.php");
    exit();
}

// Check if form was submitted with the correct action
if (isset($_POST['save_settings']) && isset($_POST['action']) && $_POST['action'] == 'update_settings') {

    // 1. Get data from the form
    // We use trim() to remove any accidental whitespace from start/end
    $send_mail = trim($_POST['send_mail']);
    $cc_mail = trim($_POST['cc_mail']);
    $bcc_mail = trim($_POST['bcc_mail']);

    // 2. Prepare Update Query
    // We always update the record with id = 1
    $update_query = "UPDATE mail_settings SET 
                        send_mail = ?, 
                        cc_mail = ?, 
                        bcc_mail = ? 
                    WHERE id = 1";
    
    $stmt = $con->prepare($update_query);
    
    if (!$stmt) {
        $_SESSION['mail_error_msg'] = "Database error preparing statement: " . $con->error;
    } else {
        // 3. Bind parameters and Execute
        $stmt->bind_param("sss", $send_mail, $cc_mail, $bcc_mail);
        
        if ($stmt->execute()) {
            $_SESSION['mail_success_msg'] = "Email settings updated successfully!";
        } else {
            $_SESSION['mail_error_msg'] = "Database error executing update: " . $stmt->error;
        }
        $stmt->close();
    }

} else {
    // If accessed directly or with wrong action
    $_SESSION['mail_error_msg'] = "Invalid access method.";
}

// Redirect back to the settings page
$con->close();
header("Location: ../pages/mail_management.php");
exit();
?>